<?php
namespace App\Support;
use Illuminate\Validation\Rule;
class TiForm {
    public const TYPES = ['utilization'=>'Pemanfaatan bulanan','monitoring'=>'Monitoring bulanan','followup'=>'Tindak lanjut semesteran'];
    public const MONTHS = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
    public const SCOPES = ['Aplikasi & Layanan','Infrastruktur','Keamanan'];
    public const ASPECTS = ['Operasional SDM','Pengukuran Kinerja','Pelayanan Publik','Proses Bisnis (Produksi Statistik)','Lintas Aspek/Umum'];
    public static function scales(): array { return json_decode(file_get_contents(database_path('data/timcare-ti-scales.json')),true,512,JSON_THROW_ON_ERROR); }
    public static function fields(string $type): array {
        $s = self::scales();
        return match ($type) {
            'utilization' => [
                'user_count'=>['Jumlah pengguna','number',true], 'usage_count'=>['Jumlah pemanfaatan','number',true],
                'feature_update'=>['Update fitur','select',true,['Ya','Tidak']], 'update_notes'=>['Keterangan update','textarea',false],
            ],
            'monitoring' => [
                'operational_condition'=>['Kondisi / capaian operasional','textarea',true],
                'utilization_level'=>['Tingkat pemanfaatan','select',true,$s['utilization']],
                'obstacles'=>['Permasalahan / kendala','textarea',true],
                'obstacle_level'=>['Tingkat kendala','select',true,$s['obstacle']],
                'corrective_action'=>['Tindakan koreksi / solusi','textarea',true],
                'effectiveness_level'=>['Tingkat efektivitas tindakan','select',false,$s['effectiveness']],
                'obstacle_status'=>['Status kendala','select',true,['Open','Closed','Carry-over']],
                'notes'=>['Catatan','textarea',false],
            ],
            'followup' => [
                'obstacle_summary'=>['Ringkasan kendala','textarea',true], 'followup_detail'=>['Detail tindak lanjut','textarea',true],
                'evidence_url'=>['Link bukti','url',false], 'responsible_person'=>['Penanggung jawab tindak lanjut','text',true],
                'target_date'=>['Target selesai','date',true], 'status'=>['Status','select',true,['Belum','Proses','Selesai']],
                'meeting_notes'=>['Catatan hasil rapat','textarea',false],
            ], default => [],
        };
    }
    public static function rules(string $type): array {
        $rules = [];
        foreach (self::fields($type) as $key=>[$label,$kind,$required]) {
            $r = [$required ? 'required' : 'nullable'];
            if ($kind==='number') $r = array_merge($r,['integer','min:0','max:2147483647']);
            elseif ($kind==='select') $r[] = Rule::in(self::fields($type)[$key][3]);
            elseif ($kind==='date') $r[] = 'date_format:Y-m-d';
            elseif ($kind==='url') $r = array_merge($r,['url:http,https','max:2000']);
            else $r = array_merge($r,['string','max:'.($kind==='textarea' ? '10000' : '255')]);
            $rules['data.'.$key] = $r;
        }
        if ($type==='utilization') $rules['data.update_notes'] = ['nullable','required_if:data.feature_update,Ya','string','max:10000'];
        return $rules;
    }
    public static function messages(): array {
        return [
            'required'=>':attribute wajib diisi.', 'integer'=>':attribute harus berupa bilangan bulat.',
            'array'=>':attribute harus berisi daftar pilihan yang valid.', 'string'=>':attribute harus berupa teks.',
            'in'=>'Pilihan :attribute tidak valid.', 'exists'=>'Pilihan :attribute tidak ditemukan atau sudah tidak aktif.',
            'min.numeric'=>':attribute minimal :min.', 'max.numeric'=>':attribute maksimal :max.',
            'max.string'=>':attribute maksimal :max karakter.', 'date_format'=>':attribute harus berupa tanggal yang valid.',
            'url'=>':attribute harus berupa tautan HTTP atau HTTPS yang valid.',
            'period.unique'=>'Laporan jenis ini sudah ada untuk objek dan periode tersebut. Buka laporan yang sudah tersimpan.',
            'data.update_notes.required_if'=>'Keterangan update wajib diisi jika Update fitur dipilih Ya.',
            'name.unique'=>'Nama tim sudah digunakan.', 'code.unique'=>'Kode objek sudah digunakan.',
        ];
    }
    public static function period(string $type, int $period): string { return $type==='followup' ? 'Semester '.($period===1 ? 'I' : 'II') : (self::MONTHS[$period] ?? '-'); }
}
