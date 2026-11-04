<x-app-layout><div class="tc">
@include('ti.partials.header',['title'=>'Monitoring perangkat & aplikasi TI','description'=>'Catat pemanfaatan bulanan, evaluasi kendala, dan tindak lanjut semesteran.'])
<section class="tc-panel tc-start" aria-labelledby="start-report-heading">
<div class="tc-panel-heading">
    <div><span class="tc-eyebrow">MULAI</span><h2 id="start-report-heading">Buat laporan</h2></div>
    <p class="tc-section-caption">Pilih aplikasi dan jenis laporan. Periode akan ditentukan pada langkah berikutnya.</p>
</div>
@if($editableObjects->isNotEmpty())
<form data-tc-native method="GET" action="{{ route('ti.reports.start') }}" class="tc-start-form">
    <div class="tc-field">
        <label for="start-object">Aplikasi / objek TI</label>
        <select id="start-object" name="object_id" required>
            <option value="">Pilih aplikasi</option>
            @foreach($editableObjects as $item)
                <option value="{{ $item->id }}" @selected((string)request('object_id')===(string)$item->id)>{{ $item->code }} — {{ $item->name }}</option>
            @endforeach
        </select>
        <small>Pilihan hanya menampilkan aplikasi yang dapat Anda kelola.</small>
    </div>
    <fieldset class="tc-report-types">
        <legend>Pilih jenis laporan</legend>
        <div class="tc-report-type-grid">
            <button type="submit" name="type" value="utilization" class="tc-report-type"><span class="tc-report-type-icon" aria-hidden="true">01</span><span><strong>Pemanfaatan</strong><small>Rekap aktivitas bulanan</small></span><span class="tc-report-type-arrow" aria-hidden="true">→</span></button>
            <button type="submit" name="type" value="monitoring" class="tc-report-type"><span class="tc-report-type-icon" aria-hidden="true">02</span><span><strong>Monitoring</strong><small>Kondisi, kendala, dan solusi</small></span><span class="tc-report-type-arrow" aria-hidden="true">→</span></button>
            <button type="submit" name="type" value="followup" class="tc-report-type"><span class="tc-report-type-icon" aria-hidden="true">03</span><span><strong>Tindak lanjut</strong><small>Pemantauan per semester</small></span><span class="tc-report-type-arrow" aria-hidden="true">→</span></button>
        </div>
    </fieldset>
</form>
@else
<div class="tc-start-empty">
    <span class="tc-empty-mark" aria-hidden="true">!</span>
    <div>
        <h3>Belum ada aplikasi yang bisa Anda laporkan</h3>
        @if(auth()->user()->hasRole('Admin'))
            <p>Tetapkan PIC atau tim pengelola pada aplikasi aktif agar laporan dapat mulai diisi.</p>
            <a class="tc-btn tc-btn-primary" href="{{ route('ti.objects.index') }}">Atur aplikasi dan penanggung jawab</a>
        @else
            <p>Pengisian laporan tersedia untuk PIC atau ketua tim pengelola. Hubungi Admin untuk memeriksa penugasan Anda.</p>
        @endif
    </div>
</div>
@endif
@if($editableObjects->isNotEmpty())
    <div class="tc-actions tc-panel-footer"><a class="tc-link" href="{{ route('ti.objects.index') }}">Lihat aplikasi dan penanggung jawab</a></div>
@endif
</section>
@php($hasReportFilters = request()->hasAny(['search','object_id','type','year','period']))
<section class="tc-panel tc-report-history" aria-labelledby="report-history-heading">
    <div class="tc-panel-heading">
        <div><span class="tc-eyebrow">RIWAYAT</span><h2 id="report-history-heading">Laporan yang sudah dicatat</h2></div>
        <p class="tc-section-caption">Temukan laporan berdasarkan aplikasi, jenis, atau periode.</p>
    </div>
    <form data-tc-native method="GET" action="{{ route('ti.reports.index') }}" class="tc-filters tc-report-filters">
        <div class="tc-field tc-report-search"><label for="filter-search">Cari aplikasi</label><input id="filter-search" type="search" name="search" value="{{ request('search') }}" maxlength="200" placeholder="Nama atau kode aplikasi"></div>
        <div class="tc-field"><label for="filter-object">Objek TI</label><select id="filter-object" name="object_id"><option value="">Semua objek yang dapat diakses</option>@foreach($objects as $object)<option value="{{ $object->id }}" @selected((string)request('object_id')===(string)$object->id)>{{ $object->name }}</option>@endforeach</select></div>
        <div class="tc-field"><label for="filter-type">Jenis laporan</label><select id="filter-type" name="type"><option value="">Semua jenis</option>@foreach(\App\Support\TiForm::TYPES as $k=>$v)<option value="{{ $k }}" @selected(request('type')===$k)>{{ $v }}</option>@endforeach</select></div>
        <div class="tc-field"><label for="filter-year">Tahun</label><input id="filter-year" type="number" name="year" min="2000" max="2100" placeholder="Semua tahun" step="1" inputmode="numeric" value="{{ request('year') }}"></div>
        <div class="tc-field"><label for="filter-period">{{ request('type')==='followup'?'Semester':'Bulan' }}</label><select id="filter-period" name="period"><option value="">Semua periode</option>@foreach(request('type')==='followup'?[1=>'Semester I',2=>'Semester II']:\App\Support\TiForm::MONTHS as $k=>$v)<option value="{{ $k }}" @selected((string)request('period')===(string)$k)>{{ $v }}</option>@endforeach</select></div>
        <div class="tc-filter-actions">
            <button type="submit" class="tc-btn tc-btn-primary">Terapkan filter</button>
            @if($hasReportFilters)<a class="tc-btn tc-btn-reset" href="{{ route('ti.reports.index') }}">Hapus filter</a>@endif
        </div>
    </form>
    <div class="tc-report-export">
        @if(request('type') && isset(\App\Support\TiForm::TYPES[request('type')]))
            <p>Unduh hasil pencarian untuk jenis laporan yang dipilih.</p>
            <a class="tc-btn" href="{{ route('ti.reports.export',request()->only('type','year','period','object_id','search')) }}">Unduh CSV</a>
        @else
            <p>Pilih jenis laporan untuk mengaktifkan unduhan CSV.</p>
        @endif
    </div>
</section>
<div class="tc-results-heading"><h2>Hasil laporan</h2><span class="tc-muted">{{ $reports->total() }} laporan</span></div>
<div class="tc-table-wrap tc-report-table-wrap">
    <table class="tc-report-table">
        <caption class="tc-visually-hidden">Daftar laporan monitoring TI</caption>
        <thead><tr><th>Objek TI</th><th>Jenis / periode</th><th>Status laporan</th><th>Terakhir diubah</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse($reports as $report)
            <tr>
                <td><strong>{{ $report->object->name }}</strong><small>{{ $report->object->code }} · {{ $report->scope_snapshot }}</small></td>
                <td><strong>{{ \App\Support\TiForm::TYPES[$report->type] }}</strong><small>{{ \App\Support\TiForm::period($report->type,$report->period) }} {{ $report->year }}</small></td>
                <td><span class="tc-chip">{{ $report->data['obstacle_status'] ?? $report->data['status'] ?? 'Tercatat' }}</span></td>
                <td>{{ $report->updated_at->format('d/m/Y H:i') }}<small>{{ $report->editor?->name ?? 'Akun tidak tersedia' }}</small></td>
                <td><div class="tc-actions">
                    <a class="tc-btn tc-report-row-action" href="{{ route('ti.reports.show',$report) }}">Lihat</a>
                    @if(\App\Services\TiAccess::canEdit(auth()->user(),$report->object))
                        <a class="tc-btn tc-report-row-action" href="{{ route('ti.reports.edit',$report) }}">Edit</a>
                    @else
                        <span class="tc-chip">Baca saja</span>
                    @endif
                </div></td>
            </tr>
        @empty
            <tr><td colspan="5"><div class="tc-empty">
                <span class="tc-empty-mark" aria-hidden="true">—</span>
                <h2>{{ $hasReportFilters ? 'Tidak ada laporan yang cocok' : 'Belum ada laporan' }}</h2>
                <p>{{ $hasReportFilters ? 'Coba ubah filter, atau hapus filter untuk melihat semua laporan.' : 'Laporan yang sudah dicatat akan muncul di sini.' }}</p>
                @if($hasReportFilters)<a class="tc-btn" href="{{ route('ti.reports.index') }}">Hapus filter</a>@else<a class="tc-link" href="#start-report-heading">Buat laporan baru</a>@endif
            </div></td></tr>
        @endforelse
        </tbody>
    </table>
</div>
{{ $reports->links() }}
</div></x-app-layout>
