<x-app-layout><div class="tc">
@include('ti.partials.header',['title'=>'Anggota tim','description'=>'Atur keanggotaan untuk satu tim saja.'])
<div class="tc-form-context">
    <div><span class="tc-eyebrow">TIM TERPILIH</span><strong>{{ $team->name }}</strong><small>{{ $team->users_count }} anggota · {{ $team->objects_count }} aplikasi</small></div>
    <a class="tc-link" href="{{ route('ti.teams.index') }}">Kembali ke daftar tim</a>
</div>
<div class="tc-notice">Perubahan di halaman ini hanya berlaku untuk <strong>{{ $team->name }}</strong>. Keanggotaan pada tim lain tidak berubah. Ketua tim tetap menjadi anggota dan tidak dapat dilepas sebelum penugasan ketua diganti.</div>
<form data-tc-native method="POST" action="{{ route('ti.teams.members.update',$team) }}">@csrf @method('PUT')
<section class="tc-panel tc-members-panel">
    <div class="tc-panel-heading">
        <div><span class="tc-eyebrow">KEANGGOTAAN</span><h2>Pilih pegawai</h2></div>
        <p class="tc-section-caption"><span data-tc-member-count>{{ count($memberIds) }}</span> anggota dipilih. Cari nama/email lalu centang pegawai yang menjadi anggota tim.</p>
    </div>
    @if($errors->has('member_ids')||$errors->has('member_ids.*'))
        <div class="tc-notice tc-errors" role="alert">{{ $errors->first('member_ids') ?: $errors->first('member_ids.*') }}</div>
    @endif
    <div class="tc-field tc-member-search">
        <label for="member-search">Cari pegawai</label>
        <input id="member-search" type="search" data-tc-member-search placeholder="Ketik nama atau email" autocomplete="off">
    </div>
    <fieldset class="tc-member-list">
        <legend class="tc-visually-hidden">Anggota tim {{ $team->name }}</legend>
        @forelse($users as $user)
            @php($isLeader=(int)$team->leader_user_id===(int)$user->id)
            <label class="tc-member-option" data-member-search="{{ mb_strtolower($user->name.' '.$user->email) }}">
                <input type="checkbox" name="member_ids[]" value="{{ $user->id }}" @checked($isLeader||in_array((int)$user->id,$memberIds,true)) @disabled($isLeader)>
                <span class="tc-member-details"><strong>{{ $user->name }}</strong><small>{{ $user->email }}</small></span>
                @if($isLeader)<span class="tc-chip">Ketua tim</span>@endif
            </label>
        @empty
            <p class="tc-muted">Belum ada akun pegawai yang dapat ditambahkan.</p>
        @endforelse
        <p class="tc-member-no-results" data-tc-member-empty hidden>Tidak ada pegawai yang cocok dengan pencarian.</p>
    </fieldset>
    <div class="tc-team-save">
        <span class="tc-muted">Akses laporan mengikuti keanggotaan tim ini.</span>
        <div class="tc-actions"><a class="tc-btn" href="{{ route('ti.teams.index') }}">Batal</a><button type="submit" class="tc-btn tc-btn-primary">Simpan anggota</button></div>
    </div>
</section>
</form>
</div></x-app-layout>
