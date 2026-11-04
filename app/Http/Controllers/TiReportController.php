<?php
namespace App\Http\Controllers;
use App\Models\{TiObject,TiReport};
use App\Services\{TiAccess,AuditTrailService};
use App\Support\TiForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\{Rule,ValidationException};
class TiReportController extends Controller {
    private function filtered(Request $r) {
        $r->validate(['type'=>['nullable',Rule::in(array_keys(TiForm::TYPES))],'year'=>'nullable|integer|min:2000|max:2100',
            'object_id'=>'nullable|integer','period'=>'nullable|integer|min:1|max:'.($r->input('type')==='followup'?2:12),'search'=>'nullable|string|max:200'],TiForm::messages(),['year'=>'Tahun','period'=>'Periode','type'=>'Jenis formulir']);
        $q=TiReport::with('object.pic','object.teams.leader','editor')->whereHas('object',fn($q)=>TiAccess::visible($q,$r->user()));
        foreach (['type','year','object_id','period'] as $key) if ($r->filled($key)) $q->where($key,$r->input($key));
        if ($r->filled('search')) { $s=trim($r->input('search')); $q->whereHas('object',fn($q)=>$q->where('name','like',"%{$s}%")->orWhere('code','like',"%{$s}%")); }
        return $q->orderByDesc('year')->orderByDesc('period')->orderBy('type')->orderByDesc('id');
    }
    public function index(Request $r) {
        return view('ti.reports.index',['reports'=>$this->filtered($r)->paginate(20)->withQueryString(),
            'editableObjects'=>TiAccess::editable(TiObject::query(),$r->user())->orderBy('name')->get(),
            'objects'=>TiAccess::visible(TiObject::query(),$r->user())->orderBy('name')->get()]);
    }
    public function start(Request $r) {
        $v=$r->validate(['object_id'=>'required|integer|exists:ti_objects,id','type'=>['required',Rule::in(array_keys(TiForm::TYPES))]],TiForm::messages(),['object_id'=>'Objek TI','type'=>'Jenis formulir']);
        $object=TiObject::findOrFail($v['object_id']);
        abort_unless(TiAccess::canEdit($r->user(),$object),403);
        return redirect()->route('ti.reports.create',[$object,$v['type']]);
    }
    public function create(Request $r, TiObject $object, string $type) {
        abort_unless(isset(TiForm::TYPES[$type]),404); abort_unless(TiAccess::canEdit($r->user(),$object),403);
        $defaultYear=now()->year;
        $defaultPeriod=$type==='followup' ? (now()->month<=6 ? 1 : 2) : now()->month;
        $year=(int)$r->session()->getOldInput('year',$defaultYear);
        $period=(int)$r->session()->getOldInput('period',$defaultPeriod);
        $report=new TiReport(['type'=>$type,'year'=>$year,'period'=>$period,'data'=>[]]);
        $existingReport=TiReport::where('object_id',$object->id)->where('type',$type)
            ->where('year',$year)->where('period',$period)->first();
        return view('ti.reports.form',compact('object','type','report','existingReport'));
    }
    public function store(Request $r, TiObject $object, string $type) {
        abort_unless(isset(TiForm::TYPES[$type]),404); abort_unless(TiAccess::canEdit($r->user(),$object),403);
        $v=$r->validate(array_merge(['year'=>'required|integer|min:2000|max:2100',
            'period'=>['required','integer','min:1','max:'.($type==='followup'?2:12),Rule::unique('ti_reports')->where(fn($q)=>$q->where('object_id',$object->id)->where('type',$type)->where('year',$r->input('year')))],
            'data'=>'required|array'],TiForm::rules($type)),TiForm::messages(),self::attributes($type));
        $data=array_intersect_key($v['data'],TiForm::fields($type));
        try {
            $report=DB::transaction(function () use ($r,$object,$type,$v,$data) {
                $report=TiReport::create(['object_id'=>$object->id,'type'=>$type,'year'=>$v['year'],'period'=>$v['period'],
                    'scope_snapshot'=>$object->scope,'aspect_snapshot'=>$object->aspect,'data'=>$data,'version'=>1,'created_by'=>$r->user()->id,'updated_by'=>$r->user()->id]);
                app(AuditTrailService::class)->record($r,$r->user()->id,'ti_report',$report->id,'ti_report_created',['after'=>$report->toArray()]);
                return $report;
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if (TiReport::where('object_id',$object->id)->where('type',$type)->where('year',$v['year'])->where('period',$v['period'])->exists())
                throw ValidationException::withMessages(['period'=>'Laporan jenis ini sudah ada untuk objek dan periode tersebut. Buka laporan yang sudah ada.']);
            throw $e;
        }
        return redirect()->route('ti.reports.show',$report)->with('success','Laporan berhasil disimpan.');
    }
    public function show(Request $r, TiReport $report) {
        $report->load('object.pic','object.teams.leader','creator','editor');
        abort_unless(TiAccess::canView($r->user(),$report->object),403);
        return view('ti.reports.show',compact('report'));
    }
    public function edit(Request $r, TiReport $report) {
        abort_unless(TiAccess::canEdit($r->user(),$report->object),403);
        return view('ti.reports.form',['report'=>$report,'object'=>$report->object,'type'=>$report->type,'existingReport'=>null]);
    }
    public function update(Request $r, TiReport $report) {
        abort_unless(TiAccess::canEdit($r->user(),$report->object),403);
        $type=$report->type;
        $v=$r->validate(array_merge(['data'=>'required|array','version'=>'required|integer|min:1'],TiForm::rules($type)),TiForm::messages(),self::attributes($type));
        DB::transaction(function () use ($r,$report,$v,$type) {
            $locked=TiReport::whereKey($report->id)->lockForUpdate()->firstOrFail();
            if ($locked->version !== (int)$v['version']) throw ValidationException::withMessages(['version'=>'Laporan telah diubah pengguna lain. Muat ulang halaman sebelum menyimpan.']);
            $old=$locked->data; $locked->data=array_intersect_key($v['data'],TiForm::fields($type));
            $locked->version++; $locked->updated_by=$r->user()->id; $locked->save();
            app(AuditTrailService::class)->record($r,$r->user()->id,'ti_report',$locked->id,'ti_report_updated',['before'=>$old,'after'=>$locked->data,'version'=>$locked->version]);
        });
        return redirect()->route('ti.reports.show',$report)->with('success','Perubahan laporan berhasil disimpan.');
    }
    public function export(Request $r) {
        $r->validate(['type'=>['required',Rule::in(array_keys(TiForm::TYPES))]]); $q=$this->filtered($r); $type=$r->input('type');
        return response()->streamDownload(function () use ($q,$type) {
            $out=fopen('php://output','w'); fwrite($out,"\xEF\xBB\xBF");
            $safe=static function ($value) { $v=(string)($value??''); return preg_match('/^[\s]*[=+@\-\t\r\n]/u',$v) ? "'".$v : $v; };
            $write=static function ($row) use ($out,$safe) { fputcsv($out,array_map($safe,$row),',','"',''); };
            $fields=TiForm::fields($type);
            $head=['Kode objek','Nama objek','Tahun',$type==='followup'?'Semester':'Bulan'];
            if ($type==='monitoring') $head=array_merge($head,['Cakupan','Aspek']);
            $write(array_merge($head,array_column($fields,0),['Diubah oleh','Diubah pada']));
            foreach ($q->lazy(200) as $report) {
                $row=[$report->object->code,$report->object->name,$report->year,TiForm::period($type,$report->period)];
                if ($type==='monitoring') $row=array_merge($row,[$report->scope_snapshot,$report->aspect_snapshot]);
                foreach ($fields as $key=>$field) $row[]=$report->data[$key]??'';
                $write(array_merge($row,[$report->editor?->name,$report->updated_at->toDateTimeString()]));
            }
            fclose($out);
        },'timcare-'.$type.'-'.now()->format('Ymd-His').'.csv',['Content-Type'=>'text/csv; charset=UTF-8','Cache-Control'=>'no-store']);
    }
    private static function attributes(string $type): array {
        $out=['year'=>'tahun','period'=>'periode','version'=>'versi laporan'];
        foreach (TiForm::fields($type) as $key=>$field) $out['data.'.$key]=$field[0]; return $out;
    }
}
