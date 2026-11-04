<?php
namespace App\Console\Commands;
use App\Models\{TiTeam,TiObject,User};
use App\Services\AuditTrailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class ImportTimcareTiReference extends Command {
    protected $signature='timcare:import-ti-reference {--apply : Simpan perubahan; tanpa opsi ini hanya pemeriksaan}';
    protected $description='Impor referensi tim, keanggotaan berdasarkan email, dan master objek TI tanpa mengubah akun/role/password.';
    public function handle(): int {
        $ref=json_decode(file_get_contents(database_path('data/timcare-teams.json')),true,512,JSON_THROW_ON_ERROR);
        $objects=json_decode(file_get_contents(database_path('data/timcare-ti-objects.json')),true,512,JSON_THROW_ON_ERROR);
        $apply=(bool)$this->option('apply');
        $this->info($apply?'MODE SIMPAN (additive, tidak mereset data lama)':'MODE PEMERIKSAAN — tidak ada perubahan');
        $accounts=User::get()->groupBy(fn($u)=>mb_strtolower(trim($u->email)));
        $matches=[]; $missing=[]; $ambiguous=[];
        foreach ($ref['users'] as $row) {
            $email=mb_strtolower(trim($row['email'])); $found=$accounts->get($email,collect());
            if ($found->count()===1) $matches[$email]=['user'=>$found->first(),'memberships'=>$row['memberships']];
            elseif ($found->isEmpty()) $missing[]=$email;
            else $ambiguous[]=$email;
        }
        $this->line(count($ref['teams']).' tim referensi; '.count($objects).' objek TI; '.count($matches).' akun cocok; '.count($missing).' email tidak ditemukan; '.count($ambiguous).' email ambigu.');
        foreach ($missing as $email) $this->warn('Dilewati (akun belum ada/soft-deleted): '.$email);
        foreach ($ambiguous as $email) $this->warn('Dilewati (email ambigu): '.$email);
        // Never trust role, password, username, or capability flags from the source JSON.
        if (!$apply) { $this->comment('Jalankan kembali dengan --apply setelah memeriksa kecocokan. Akun yang dilewati TIDAK dibuat.'); return self::SUCCESS; }
        DB::transaction(function () use ($ref,$objects,$matches) {
            $teamMap=[];
            foreach ($ref['teams'] as $row) {
                $team=TiTeam::firstOrCreate(['name'=>$row['name']]); $teamMap[$row['id']]=$team;
                if ($team->wasRecentlyCreated) app(AuditTrailService::class)->record(null,null,'ti_team',$team->id,'ti_reference_imported',['name'=>$team->name]);
            }
            $leaders=[];
            foreach ($matches as $entry) {
                $ids=[];
                foreach ($entry['memberships'] as $m) {
                    if (!isset($teamMap[$m['teamId']])) continue;
                    $team=$teamMap[$m['teamId']]; $ids[]=$team->id;
                    if ($m['isKetua']===true) $leaders[$team->id][]=$entry['user']->id;
                }
                $change=$entry['user']->teams()->syncWithoutDetaching($ids);
                if ($change['attached']) app(AuditTrailService::class)->record(null,null,'user',$entry['user']->id,'ti_memberships_imported',['added'=>$change['attached']]);
            }
            foreach ($teamMap as $team) {
                $ids=array_values(array_unique($leaders[$team->id]??[]));
                if (!$team->leader_user_id && count($ids)===1) {
                    $team->update(['leader_user_id'=>$ids[0]]);
                    app(AuditTrailService::class)->record(null,null,'ti_team',$team->id,'ti_leader_imported',['leader_user_id'=>$ids[0]]);
                } elseif (!$team->leader_user_id) $this->warn('Ketua perlu ditetapkan manual: '.$team->name);
            }
            foreach ($objects as $row) {
                $object=TiObject::firstOrCreate(['code'=>$row['code']], [
                    'name'=>$row['name'],'scope'=>$row['scope'],'aspect'=>$row['aspect'],'manager_label'=>$row['manager_label'],
                    'audience'=>$row['audience'],'technical_type'=>$row['technical_type'],'programmer_label'=>$row['programmer_label'],
                    'is_active'=>$row['status']==='Aktif',
                ]);
                if ($object->wasRecentlyCreated) app(AuditTrailService::class)->record(null,null,'ti_object',$object->id,'ti_reference_imported',['code'=>$object->code]);
            }
        });
        $this->info('Selesai. Atur PIC dan tim pengelola setiap objek melalui Monitoring TI. Pemetaan tidak ditebak dari teks Excel.');
        $this->comment('Role, password, email, dan keanggotaan yang sudah ada tidak ditimpa. Contoh laporan Excel tidak diimpor.');
        return self::SUCCESS;
    }
}
