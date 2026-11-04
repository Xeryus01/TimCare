<x-app-layout><div class="tc">
@include('ti.partials.header',['title'=>'Aplikasi & perangkat TI','description'=>'Lihat penanggung jawab, tim pengelola, dan laporan tiap aplikasi.'])
<div class="tc-object-toolbar">
    <div><span class="tc-eyebrow">MASTER APLIKASI</span><p class="tc-object-toolbar-caption">Cari dan saring aplikasi berdasarkan cakupan, tim, atau status.</p></div>
    @if(auth()->user()->hasRole('Admin'))<a class="tc-btn tc-btn-primary tc-object-add" href="{{ route('ti.objects.create') }}">Tambah aplikasi</a>@endif
</div>
<div class="tc-object-summary" aria-label="Ringkasan aplikasi">
    <a class="{{ !$status&&!$assignment&&!$scope&&!$aspect&&!$teamId&&!request()->filled('search')?'is-selected':'' }}" href="{{ route('ti.objects.index') }}"><span class="tc-object-summary-value">{{ $summary['total'] }}</span><span class="tc-object-summary-label">Semua aplikasi</span></a>
    <a class="{{ $status==='active'&&!$assignment&&!$scope&&!$aspect&&!$teamId&&!request()->filled('search')?'is-selected':'' }}" href="{{ route('ti.objects.index',['status'=>'active']) }}"><span class="tc-object-summary-value">{{ $summary['active'] }}</span><span class="tc-object-summary-label">Aktif</span></a>
    @if(auth()->user()->hasRole('Admin'))<a class="{{ $assignment==='unassigned'&&!$scope&&!$aspect&&!$teamId&&!request()->filled('search')?'is-selected':'' }}" href="{{ route('ti.objects.index',['assignment'=>'unassigned']) }}"><span class="tc-object-summary-value">{{ $summary['unassigned'] }}</span><span class="tc-object-summary-label">Belum ditugaskan</span></a>@endif
</div>
@if(auth()->user()->hasRole('Admin'))
    <div class="tc-notice"><strong>Alur penugasan:</strong> atur PIC dan tim pengelola aplikasi. PIC atau ketua tim dapat mengisi laporan; anggota tim dapat melihatnya.</div>
@endif
<section class="tc-panel tc-object-filter-panel" aria-labelledby="object-filter-heading">
    <div class="tc-object-filter-heading"><div><h2 id="object-filter-heading">Filter aplikasi</h2><p class="tc-muted">Kombinasikan filter untuk menemukan aplikasi yang dicari.</p></div>
        @if(request()->hasAny(['search','status','assignment','scope','aspect','team_id']))<a class="tc-btn tc-btn-reset" href="{{ route('ti.objects.index') }}">Hapus semua filter</a>@endif
    </div>
    <form data-tc-native method="GET" action="{{ route('ti.objects.index') }}" class="tc-object-filter-form">
        <div class="tc-field tc-object-filter-search"><label for="object-search">Nama atau kode aplikasi</label><input id="object-search" type="search" name="search" value="{{ request('search') }}" maxlength="200" placeholder="Contoh: SIMPEG atau APP-001"></div>
        <div class="tc-field"><label for="object-scope">Cakupan</label><select id="object-scope" name="scope"><option value="">Semua cakupan</option>@foreach(\App\Support\TiForm::SCOPES as $item)<option value="{{ $item }}" @selected($scope===$item)>{{ $item }}</option>@endforeach</select></div>
        <div class="tc-field"><label for="object-aspect">Aspek</label><select id="object-aspect" name="aspect"><option value="">Semua aspek</option>@foreach(\App\Support\TiForm::ASPECTS as $item)<option value="{{ $item }}" @selected($aspect===$item)>{{ $item }}</option>@endforeach</select></div>
        <div class="tc-field"><label for="object-team">Tim pengelola</label><select id="object-team" name="team_id"><option value="">Semua tim</option>@foreach($teams as $team)<option value="{{ $team->id }}" @selected($teamId===(string)$team->id)>{{ $team->name }}</option>@endforeach</select></div>
        <div class="tc-field"><label for="object-status">Status aplikasi</label><select id="object-status" name="status"><option value="">Semua status</option><option value="active" @selected($status==='active')>Aktif</option><option value="inactive" @selected($status==='inactive')>Nonaktif</option></select></div>
        <div class="tc-field"><label for="object-assignment">Status penugasan</label><select id="object-assignment" name="assignment"><option value="">Semua penugasan</option><option value="assigned" @selected($assignment==='assigned')>Sudah ditugaskan</option><option value="unassigned" @selected($assignment==='unassigned')>Belum ditugaskan</option></select></div>
        <div class="tc-object-filter-actions"><button class="tc-btn tc-btn-primary" type="submit">Tampilkan aplikasi</button></div>
    </form>
</section>
<div class="tc-object-results-heading"><h2>Daftar aplikasi</h2><span>{{ $objects->total() }} aplikasi cocok</span></div>
<div class="tc-object-list">
@forelse($objects as $object)
    @php($canEdit=\App\Services\TiAccess::canEdit(auth()->user(),$object))
    <article class="tc-panel tc-object-card">
        <div class="tc-object-card-heading">
            <div class="tc-object-title"><span class="tc-eyebrow">{{ $object->code }}</span><h2>{{ $object->name }}</h2></div>
            <div class="tc-object-badges">
                <span class="tc-chip {{ $object->is_active?'tc-chip-active':'tc-chip-inactive' }}">{{ $object->is_active?'Aktif':'Nonaktif' }}</span>
                @if(auth()->user()->hasRole('Admin') && !$object->pic_user_id && $object->teams->isEmpty())<span class="tc-chip tc-chip-warning">Belum ditugaskan</span>@endif
            </div>
        </div>
        <div class="tc-object-meta"><span>{{ $object->scope }}</span><span aria-hidden="true">·</span><span>{{ $object->aspect }}</span></div>
        <div class="tc-object-assignments">
            <div class="tc-object-assignment"><span class="tc-object-label">PIC</span><strong>{{ $object->pic?->name ?? 'Belum ditetapkan' }}</strong></div>
            <div class="tc-object-assignment"><span class="tc-object-label">Tim pengelola</span>
                @if($object->teams->isNotEmpty())
                    <div class="tc-object-teams">@foreach($object->teams as $team)<span class="tc-object-team">{{ $team->name }}<small>Ketua: {{ $team->leader?->name ?? 'Belum ditetapkan' }}</small></span>@endforeach</div>
                @else
                    <strong class="tc-muted">Belum dipetakan</strong>
                @endif
            </div>
        </div>
        <div class="tc-object-card-footer">
            <span class="tc-object-report-count">{{ $object->reports_count }} laporan tersimpan</span>
            <div class="tc-actions">
                @if(auth()->user()->hasRole('Admin'))<a class="tc-btn" href="{{ route('ti.objects.edit',$object) }}">Atur penanggung jawab</a>@endif
                <a class="tc-btn" href="{{ route('ti.reports.index',['object_id'=>$object->id]) }}">Lihat laporan</a>
                @if($canEdit)
                    <a class="tc-btn tc-btn-primary" href="{{ route('ti.reports.index',['object_id'=>$object->id]) }}#start-report-heading">Buat laporan</a>
                @elseif(!auth()->user()->hasRole('Admin'))
                    <span class="tc-chip">Baca saja</span>
                @endif
            </div>
        </div>
    </article>
@empty
    <section class="tc-panel tc-empty">
        <span class="tc-empty-mark" aria-hidden="true">—</span>
        <h2>{{ request()->hasAny(['search','status','assignment','scope','aspect','team_id'])?'Aplikasi tidak ditemukan':'Belum ada aplikasi untuk ditampilkan' }}</h2>
        <p>{{ request()->hasAny(['search','status','assignment','scope','aspect','team_id'])?'Coba ubah atau hapus sebagian filter.':'Admin dapat menambahkan aplikasi dan menetapkan PIC atau tim pengelolanya.' }}</p>
        @if(request()->hasAny(['search','status','assignment','scope','aspect','team_id']))<a class="tc-link" href="{{ route('ti.objects.index') }}">Tampilkan semua aplikasi</a>@endif
    </section>
@endforelse
</div>
<x-pagination :paginator="$objects" />
</div></x-app-layout>
