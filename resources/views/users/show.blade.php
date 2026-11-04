<x-app-layout><div class="tc">
@include('ti.partials.assets')
<header class="tc-header"><h1>Detail pengguna</h1><p class="tc-muted">Informasi akun dan keanggotaan tim {{ $user->name }}.</p></header>
@if(session('success'))<div class="tc-notice tc-success" role="status">{{ session('success') }}</div>@endif
<div class="tc-toolbar"><a class="tc-link" href="{{ route('users.index') }}">Kembali ke daftar pengguna</a><div class="tc-actions"><a class="tc-btn" href="{{ route('users.editPassword',$user) }}">Ubah password</a><a class="tc-btn tc-btn-primary" href="{{ route('users.edit',$user) }}">Edit pengguna</a></div></div>
<section class="tc-panel"><h2>Informasi akun</h2><dl class="tc-grid"><div><dt>Nama lengkap</dt><dd>{{ $user->name }}</dd></div><div><dt>Email</dt><dd>{{ $user->email }}</dd></div><div><dt>No. handphone</dt><dd>{{ $user->phone_number ?: 'Belum diisi' }}</dd></div><div><dt>Role Timcare</dt><dd>{{ $user->roles->pluck('name')->join(', ') ?: 'Belum ditetapkan' }}</dd></div><div><dt>Tanggal dibuat</dt><dd>{{ $user->created_at?->format('d/m/Y H:i') ?? '—' }}</dd></div></dl></section>
<section class="tc-panel"><h2>Tim kerja</h2><p class="tc-muted">Keanggotaan tim bersifat opsional dan tidak menggantikan role akun Timcare.</p>
@if($user->teams->isEmpty())<div class="tc-notice">Pegawai ini belum memiliki tim. Akun tetap dapat digunakan.</div>
@else<div class="tc-table-wrap"><table><thead><tr><th>Nama tim</th><th>Kedudukan</th><th>Ketua tim</th></tr></thead><tbody>
@foreach($user->teams as $team)<tr><td>{{ $team->name }}</td><td>{{ (int)$team->leader_user_id===(int)$user->id?'Ketua':'Anggota' }}</td><td>{{ $team->leader?->name ?? 'Belum ditetapkan' }}</td></tr>@endforeach
</tbody></table></div>@endif</section>
</div></x-app-layout>
