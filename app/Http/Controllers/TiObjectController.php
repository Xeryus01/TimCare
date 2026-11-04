<?php
namespace App\Http\Controllers;
use App\Models\{TiObject,TiTeam,User};
use App\Services\{TiAccess,AuditTrailService};
use App\Support\TiForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class TiObjectController extends Controller {
    public function index(Request $r) {
        $visible=TiAccess::visible(TiObject::query(),$r->user());
        $total=(clone $visible)->count();
        $active=(clone $visible)->where('is_active',true)->count();
        $unassigned=(clone $visible)->whereNull('pic_user_id')->whereDoesntHave('teams')->count();
        $filters=$r->validate([
            'search'=>'nullable|string|max:200',
            'scope'=>['nullable',Rule::in(TiForm::SCOPES)],
            'aspect'=>['nullable',Rule::in(TiForm::ASPECTS)],
            'status'=>['nullable',Rule::in(['active','inactive','unassigned'])],
            'assignment'=>['nullable',Rule::in(['assigned','unassigned'])],
            'team_id'=>['nullable','integer','exists:ti_teams,id'],
            'per_page'=>'nullable|integer|in:10,20,50',
        ],TiForm::messages(),[
            'search'=>'pencarian','scope'=>'cakupan aplikasi','aspect'=>'aspek aplikasi',
            'status'=>'status aplikasi','assignment'=>'status penugasan','team_id'=>'tim pengelola',
            'per_page'=>'jumlah aplikasi per halaman',
        ]);
        $q=TiAccess::visible(TiObject::with('pic','teams.leader')->withCount('reports'),$r->user());
        if (!empty($filters['search'])) {
            $s=trim($filters['search']);
            $q->where(fn($q)=>$q->where('name','like',"%{$s}%")->orWhere('code','like',"%{$s}%"));
        }
        $status=$filters['status']??'';
        $assignment=$filters['assignment']??($status==='unassigned'?'unassigned':'');
        if ($status==='active') $q->where('is_active',true);
        elseif ($status==='inactive') $q->where('is_active',false);
        if (!empty($filters['scope'])) $q->where('scope',$filters['scope']);
        if (!empty($filters['aspect'])) $q->where('aspect',$filters['aspect']);
        if (!empty($filters['team_id'])) $q->whereHas('teams',fn($q)=>$q->where('ti_teams.id',$filters['team_id']));
        if ($assignment==='assigned') $q->where(fn($q)=>$q->whereNotNull('pic_user_id')->orWhereHas('teams'));
        elseif ($assignment==='unassigned') $q->whereNull('pic_user_id')->whereDoesntHave('teams');
        $perPage=(int)($filters['per_page']??20);
        $teamQuery=TiTeam::query()->orderBy('name');
        if (!$r->user()->hasRole('Admin')) {
            $teamQuery->whereHas('objects',fn($q)=>TiAccess::visible($q,$r->user()));
        }
        return view('ti.objects.index',[
            'objects'=>$q->orderBy('name')->paginate($perPage)->appends(array_merge(collect($filters)->except('per_page')->all(),['per_page'=>$perPage])),
            'summary'=>['total'=>$total,'active'=>$active,'unassigned'=>$unassigned],
            'status'=>$status==='unassigned'?'':$status,
            'assignment'=>$assignment,
            'scope'=>$filters['scope']??'',
            'aspect'=>$filters['aspect']??'',
            'teamId'=>(string)($filters['team_id']??''),
            'teams'=>$teamQuery->get(['ti_teams.id','ti_teams.name']),
        ]);
    }
    public function create(Request $r) { abort_unless($r->user()->hasRole('Admin'),403); return $this->form(new TiObject(['is_active'=>true])); }
    public function edit(Request $r, TiObject $object) { abort_unless($r->user()->hasRole('Admin'),403); return $this->form($object); }
    private function form(TiObject $object) { return view('ti.objects.form',['object'=>$object,'teams'=>TiTeam::orderBy('name')->get(),'users'=>User::orderBy('name')->get()]); }
    public function store(Request $r) { return $this->save($r,new TiObject); }
    public function update(Request $r, TiObject $object) { return $this->save($r,$object); }
    private function save(Request $r, TiObject $object) {
        abort_unless($r->user()->hasRole('Admin'),403);
        $data=$r->validate([
            'code'=>['required','string','max:50',Rule::unique('ti_objects')->ignore($object->id)],'name'=>'required|string|max:255',
            'scope'=>['required',Rule::in(TiForm::SCOPES)],'aspect'=>['required',Rule::in(TiForm::ASPECTS)],
            'manager_label'=>'nullable|string|max:255','audience'=>'nullable|string|max:255','technical_type'=>'nullable|string|max:100',
            'programmer_label'=>'nullable|string|max:255','pic_user_id'=>['nullable',Rule::exists('users','id')->whereNull('deleted_at')],
            'is_active'=>'required|boolean','team_ids'=>'nullable|array','team_ids.*'=>'integer|distinct|exists:ti_teams,id',
        ],TiForm::messages());
        DB::transaction(function () use ($r,$data,$object) {
            $old=$object->exists ? array_merge($object->toArray(),['team_ids'=>$object->teams()->pluck('ti_teams.id')->all()]) : [];
            $ids=$data['team_ids']??[]; unset($data['team_ids']); $object->fill($data)->save(); $object->teams()->sync($ids);
            app(AuditTrailService::class)->record($r,$r->user()->id,'ti_object',$object->id,'ti_object_saved',['before'=>$old,'after'=>$data,'team_ids'=>$ids]);
        });
        return redirect()->route('ti.objects.index')->with('success','Objek TI dan penanggung jawab berhasil disimpan.');
    }
}
