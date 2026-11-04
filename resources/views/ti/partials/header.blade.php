@include('ti.partials.assets')
<header class="tc-header"><h1>{{ $title }}</h1><p class="tc-muted tc-subtitle">{{ $description ?? 'Pemanfaatan, monitoring, dan evaluasi perangkat serta aplikasi TI.' }}</p></header>
<nav class="tc-nav" aria-label="Menu monitoring TI">
    <a href="{{ route('ti.reports.index') }}" @if(request()->routeIs('ti.reports.*')) aria-current="page" @endif>Laporan monitoring</a>
    <a href="{{ route('ti.objects.index') }}" @if(request()->routeIs('ti.objects.*')) aria-current="page" @endif>Objek &amp; penanggung jawab</a>
    @if(auth()->user()->hasRole('Admin'))<a href="{{ route('ti.teams.index') }}" @if(request()->routeIs('ti.teams.*')) aria-current="page" @endif>Tim kerja</a>@endif
</nav>
@if(session('success'))<div class="tc-notice tc-success" role="status">{{ session('success') }}</div>@endif
@if($errors->any())
<div class="tc-notice tc-errors tc-summary-focus" role="alert" tabindex="-1" data-tc-errors><strong>Data belum disimpan. Periksa isian berikut:</strong><ul>
@foreach($errors->messages() as $key=>$messages)
@php($anchor=match($key){'year'=>request()->routeIs('ti.reports.index')?'filter-year':'report-year','period'=>request()->routeIs('ti.reports.index')?'filter-period':'report-period','version'=>'report-version','object_id'=>'start-object','type'=>'start-type','name'=>old('_team_id')?'team-name-'.old('_team_id'):'ti-name','leader_user_id'=>old('_team_id')?'team-leader-'.old('_team_id'):'new-team-leader',default=>'ti-'.str_replace('.','-',$key)})
@foreach($messages as $message)<li><a href="#{{ $anchor }}">{{ $message }}</a></li>@endforeach
@endforeach
</ul></div>
@endif
