<x-app-layout><div class="tc">
@include('ti.partials.header',['title'=>$report->exists?'Edit laporan':'Isi laporan'])
<a class="tc-report-back" href="{{ route('ti.reports.index',['object_id'=>$object->id]) }}">← Kembali ke daftar laporan</a>
<section class="tc-form-context" aria-label="Ringkasan laporan">
    <div class="tc-form-context-object">
        <span class="tc-eyebrow">APLIKASI / OBJEK</span>
        <strong>{{ $object->name }}</strong>
        <small>{{ $object->code }} · {{ $object->scope }} · {{ $object->aspect }}</small>
    </div>
    <div class="tc-form-context-type">
        <span class="tc-eyebrow">JENIS LAPORAN</span>
        <strong>{{ \App\Support\TiForm::TYPES[$type] }}</strong>
        <small>{{ $type==='followup'?'Diisi setiap semester':'Diisi setiap bulan' }}</small>
    </div>
</section>
<form data-tc-native method="POST" action="{{ $report->exists?route('ti.reports.update',$report):route('ti.reports.store',[$object,$type]) }}">@csrf
@if($report->exists) @method('PUT') <input type="hidden" name="version" value="{{ old('version',$report->version) }}"> @endif
<section class="tc-panel tc-form-section tc-period-section" aria-labelledby="report-period-heading">
    <div class="tc-form-section-heading">
        <span class="tc-step">1</span>
        <div><h2 id="report-period-heading">Periode laporan</h2><p class="tc-muted">Pilih tahun dan {{ $type==='followup'?'semester':'bulan' }} yang dicatat.</p></div>
    </div>
    @if($report->exists)
        <div class="tc-period-summary"><span>{{ \App\Support\TiForm::period($type,$report->period) }}</span><strong>{{ $report->year }}</strong></div>
        <p class="tc-help">Periode tidak dapat dipindahkan setelah laporan dibuat.</p>
    @else
        @if($existingReport)
            <div class="tc-existing-report" role="status">
                <div><strong>Laporan untuk periode ini sudah tersedia.</strong><p>Buka laporan yang sudah ada, atau pilih periode berbeda sebelum mengisi data.</p></div>
                <a class="tc-btn" href="{{ route('ti.reports.show',$existingReport) }}">Buka laporan</a>
            </div>
        @endif
        <div class="tc-period-fields">
            <div class="tc-field">
                <label for="report-year">Tahun <span aria-hidden="true">*</span></label>
                <input id="report-year" type="number" name="year" required min="2000" max="2100" inputmode="numeric" value="{{ old('year',$report->year) }}" aria-invalid="{{ $errors->has('year')?'true':'false' }}" aria-describedby="report-year-help{{ $errors->has('year')?' report-year-error':'' }}">
                <small id="report-year-help">Contoh: {{ now()->year }}</small>
                @error('year')<p id="report-year-error" class="tc-error">{{ $message }}</p>@enderror
            </div>
            <div class="tc-field">
                <label for="report-period">{{ $type==='followup'?'Semester':'Bulan' }} <span aria-hidden="true">*</span></label>
                <select id="report-period" name="period" required aria-invalid="{{ $errors->has('period')?'true':'false' }}" aria-describedby="report-period-help{{ $errors->has('period')?' report-period-error':'' }}">
                    @foreach($type==='followup'?[1=>'Semester I',2=>'Semester II']:\App\Support\TiForm::MONTHS as $k=>$v)
                        <option value="{{ $k }}" @selected((int)old('period',$report->period)===$k)>{{ $v }}</option>
                    @endforeach
                </select>
                <small id="report-period-help">Pilih periode yang belum memiliki laporan.</small>
                @error('period')<p id="report-period-error" class="tc-error">{{ $message }}</p>@enderror
            </div>
        </div>
    @endif
</section>
<section class="tc-panel tc-form-section tc-data-section" aria-labelledby="report-data-heading">
    <div class="tc-form-section-heading">
        <span class="tc-step">2</span>
        <div>
            <h2 id="report-data-heading">Data {{ $report->exists?'laporan':'yang dilaporkan' }}</h2>
            <p class="tc-muted">Isi kolom bertanda <span class="tc-required-mark">*</span>. Kolom lain boleh dikosongkan.</p>
        </div>
    </div>
    @if($type==='monitoring')
        <div class="tc-form-guidance"><strong>Petunjuk singkat</strong><span>Jika operasional normal, tulis “Tidak ada kendala”. Efektivitas tindakan boleh dikosongkan bila tidak ada tindakan yang dievaluasi.</span></div>
    @endif
    <div class="tc-grid tc-report-fields">
        @foreach(\App\Support\TiForm::fields($type) as $key=>$field)
<x-ti-field :name="'data['.$key.']'" :label="$field[0]" :kind="$field[1]" :required="$field[2]" :options="$field[3]??[]" :value="old('data.'.$key,$report->data[$key]??'')" :help="$key==='update_notes'?'Wajib diisi jika update fitur = Ya.':null"/>
        @endforeach
    </div>
</section>
@if($errors->has('version'))<div class="tc-notice tc-errors">{{ $errors->first('version') }} <a class="tc-link" href="{{ route('ti.reports.edit',$report) }}">Muat ulang versi terbaru</a></div>@endif
<div class="tc-form-actions" id="report-version">
    <span class="tc-form-actions-note"><strong>{{ $report->exists?'Perubahan belum tersimpan':'Langkah terakhir' }}</strong><small>{{ $report->exists?'Pastikan perubahan sudah benar.':'Periksa periode dan data sebelum menyimpan laporan.' }}</small></span>
    <div class="tc-actions"><a class="tc-btn" href="{{ $report->exists?route('ti.reports.show',$report):route('ti.reports.index',['object_id'=>$object->id]) }}">Batal</a><button type="submit" class="tc-btn tc-btn-primary">{{ $report->exists?'Simpan perubahan':'Simpan laporan' }}</button></div>
</div>
</form>
</div></x-app-layout>
