<?php
namespace App\Http\Controllers;
use App\Models\{TiTeam,User};
use App\Services\AuditTrailService;
use App\Support\TiForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class TiTeamController extends Controller {
    public function index() {
        return view('ti.teams',['teams'=>TiTeam::with('leader')->withCount('users','objects')->orderBy('name')->get(),'users'=>User::orderBy('name')->get()]);
    }
    public function editMembers(TiTeam $team) {
        $team->load('leader')->loadCount(['users','objects']);
        $users=User::select('id','name','email')->orderBy('name')->get();
        return view('ti.team-members',['team'=>$team,'users'=>$users,'memberIds'=>$team->users()->pluck('users.id')->map(fn($id)=>(int)$id)->all()]);
    }
    public function updateMembers(Request $r, TiTeam $team) {
        abort_unless($r->user()->hasRole('Admin'),403);
        $data=$r->validate([
            'member_ids'=>'nullable|array',
            'member_ids.*'=>['integer','distinct',Rule::exists('users','id')->whereNull('deleted_at')],
        ],TiForm::messages(),['member_ids'=>'daftar anggota','member_ids.*'=>'pegawai']);
        DB::transaction(function () use ($r,$team,$data) {
            $team=TiTeam::whereKey($team->id)->lockForUpdate()->firstOrFail();
            $before=$team->users()->pluck('users.id')->map(fn($id)=>(int)$id)->all();
            $memberIds=array_map('intval',$data['member_ids']??[]);
            if ($team->leader_user_id) $memberIds[]=(int)$team->leader_user_id;
            $memberIds=array_values(array_unique($memberIds));
            $team->users()->sync($memberIds);
            app(AuditTrailService::class)->record($r,$r->user()->id,'ti_team',$team->id,'ti_team_members_updated',[
                'before'=>$before,'after'=>$memberIds,'leader_user_id'=>$team->leader_user_id,
            ]);
        });
        return redirect()->route('ti.teams.members.edit',$team)->with('success','Anggota tim berhasil diperbarui.');
    }
    public function bulkUpdate(Request $r) {
        abort_unless($r->user()->hasRole('Admin'),403);
        $rows=$r->input('teams',[]);
        if (!is_array($rows)) $rows=[];
        $rules=[
            'teams'=>'nullable|array',
            'new_team'=>'nullable|array',
            'new_team.name'=>'nullable|string|max:255',
            'new_team.leader_user_id'=>['nullable',Rule::exists('users','id')->whereNull('deleted_at')],
        ];
        foreach ($rows as $key=>$row) {
            $rules["teams.$key.id"]=['required','integer','distinct','exists:ti_teams,id'];
            $rules["teams.$key.name"]=['required','string','max:255'];
            $rules["teams.$key.leader_user_id"]=['nullable',Rule::exists('users','id')->whereNull('deleted_at')];
        }
        $validator=Validator::make($r->all(),$rules,TiForm::messages(),[
            'teams.*.id'=>'tim kerja','teams.*.name'=>'nama tim','teams.*.leader_user_id'=>'ketua tim',
            'new_team.name'=>'nama tim baru','new_team.leader_user_id'=>'ketua tim baru',
        ]);
        $validator->after(function ($validator) use ($rows,$r) {
            $ids=[];
            $names=[];
            foreach ($rows as $key=>$row) {
                if (!is_array($row)) continue;
                if (isset($row['id']) && filter_var($row['id'],FILTER_VALIDATE_INT)!==false) $ids[]=(int)$row['id'];
                if (isset($row['name']) && is_string($row['name']) && trim($row['name'])!=='') {
                    $names['teams.'.$key.'.name'][]=mb_strtolower(trim($row['name']));
                }
            }
            $newName=$r->input('new_team.name');
            if (is_string($newName) && trim($newName)!=='') $names['new_team.name'][]=mb_strtolower(trim($newName));
            $seen=[];
            foreach ($names as $field=>$values) {
                $name=$values[0];
                if (isset($seen[$name])) {
                    $validator->errors()->add($field,'Nama tim harus berbeda.');
                    continue;
                }
                $seen[$name]=true;
                if (TiTeam::query()->whereRaw('LOWER(name) = ?',[$name])->when($ids,fn($q)=>$q->whereNotIn('id',$ids))->exists()) {
                    $validator->errors()->add($field,'Nama tim sudah digunakan.');
                }
            }
        });
        $data=$validator->validate();
        $rows=$data['teams']??[];
        $newTeam=$data['new_team']??[];
        $newName=isset($newTeam['name']) ? trim($newTeam['name']) : '';
        DB::transaction(function () use ($rows,$newTeam,$newName,$r) {
            $ids=array_map(fn($row)=>(int)$row['id'],$rows);
            $teams=TiTeam::whereIn('id',$ids)->get()->keyBy('id');
            $before=[];
            foreach ($rows as $row) {
                $team=$teams->get((int)$row['id']);
                $before[$team->id]=$team->toArray();
            }
            if (collect($rows)->contains(fn($row)=>$teams->get((int)$row['id'])->name!==trim($row['name']))) {
                foreach ($teams as $team) $team->update(['name'=>'__ti_bulk_'.Str::uuid()]);
            }
            foreach ($rows as $row) {
                $team=$teams->get((int)$row['id']);
                $team->fill(['name'=>trim($row['name']),'leader_user_id'=>$row['leader_user_id']??null])->save();
                if ($team->leader_user_id) $team->users()->syncWithoutDetaching([$team->leader_user_id]);
                app(AuditTrailService::class)->record($r,$r->user()->id,'ti_team',$team->id,'ti_team_saved',['before'=>$before[$team->id],'after'=>$team->toArray()]);
            }
            if ($newName!=='') {
                $team=TiTeam::create(['name'=>$newName,'leader_user_id'=>$newTeam['leader_user_id']??null]);
                if ($team->leader_user_id) $team->users()->syncWithoutDetaching([$team->leader_user_id]);
                app(AuditTrailService::class)->record($r,$r->user()->id,'ti_team',$team->id,'ti_team_saved',['before'=>[],'after'=>$team->toArray()]);
            }
        });
        return back()->with('success',$newName!==''?'Tim berhasil diperbarui dan tim baru ditambahkan.':'Semua tim berhasil diperbarui.');
    }
    public function store(Request $r) { return $this->save($r,new TiTeam); }
    public function update(Request $r, TiTeam $team) { return $this->save($r,$team); }
    private function save(Request $r, TiTeam $team) {
        abort_unless($r->user()->hasRole('Admin'),403);
        $data=$r->validate(['name'=>['required','string','max:255',Rule::unique('ti_teams')->ignore($team->id)],
            'leader_user_id'=>['nullable',Rule::exists('users','id')->whereNull('deleted_at')]],TiForm::messages());
        DB::transaction(function () use ($team,$data,$r) {
            $old=$team->toArray(); $team->fill($data)->save();
            if ($team->leader_user_id) $team->users()->syncWithoutDetaching([$team->leader_user_id]);
            app(AuditTrailService::class)->record($r,$r->user()->id,'ti_team',$team->id,'ti_team_saved',['before'=>$old,'after'=>$team->toArray()]);
        });
        return back()->with('success','Tim dan ketua tim berhasil disimpan.');
    }
}
