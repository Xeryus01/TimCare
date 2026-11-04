<x-app-layout><div class="tc">
@include('ti.partials.header',['title'=>'Tim kerja','description'=>'Atur nama dan ketua semua tim sekaligus.'])
<div class="tc-notice"><strong>Satu kali simpan untuk semua tim.</strong> Ketua otomatis menjadi anggota tim. Untuk mengatur anggota, gunakan tombol pada tim yang dituju; anggota tim lain tidak akan berubah.</div>
<form data-tc-native method="POST" action="{{ route('ti.teams.bulk-update') }}">@csrf @method('PUT')
<section class="tc-panel tc-team-panel">
<div class="tc-team-heading"><div><h2>Daftar tim</h2><p class="tc-muted">{{ $teams->count() }} tim · Ubah nama atau ketua, lalu simpan sekali.</p></div></div>
<div class="tc-table-wrap tc-team-table-wrap"><table class="tc-team-table"><thead><tr><th>Nama tim</th><th>Ketua tim</th><th>Anggota</th><th>Aplikasi</th><th>Aksi</th></tr></thead><tbody>
@forelse($teams as $team)
<tr>
<td><input type="hidden" name="teams[{{ $team->id }}][id]" value="{{ $team->id }}"><x-ti-field :id="'ti-teams-'.$team->id.'-name'" :name="'teams['.$team->id.'][name]'" label="Nama tim" :value="old('teams.'.$team->id.'.name',$team->name)" required/></td>
<td><div class="tc-field"><label class="tc-visually-hidden" for="ti-teams-{{ $team->id }}-leader_user_id">Ketua {{ $team->name }}</label><select id="ti-teams-{{ $team->id }}-leader_user_id" name="teams[{{ $team->id }}][leader_user_id]"><option value="">Belum ditetapkan</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string)old('teams.'.$team->id.'.leader_user_id',$team->leader_user_id)===(string)$user->id)>{{ $user->name }} — {{ $user->email }}</option>@endforeach</select>@error('teams.'.$team->id.'.leader_user_id')<p class="tc-error">{{ $message }}</p>@enderror</div></td>
<td><span class="tc-team-count">{{ $team->users_count }}</span></td><td><span class="tc-team-count">{{ $team->objects_count }}</span></td>
<td><a class="tc-btn" href="{{ route('ti.teams.members.edit',$team) }}">Atur anggota</a></td>
</tr>
@empty
<tr><td colspan="5"><div class="tc-empty"><h2>Belum ada tim kerja</h2><p>Tambahkan tim pertama melalui bagian “Tambah tim” di bawah.</p></div></td></tr>
@endforelse
</tbody></table></div>
<details class="tc-add-team" @if($errors->has('new_team.name')||$errors->has('new_team.leader_user_id')) open @endif>
<summary>Tambah tim (opsional)</summary><div class="tc-grid tc-add-team-fields">
<x-ti-field id="ti-new_team-name" name="new_team[name]" label="Nama tim baru" :value="old('new_team.name')" help="Biarkan kosong jika tidak ingin menambah tim."/>
<div class="tc-field"><label for="ti-new_team-leader_user_id">Ketua tim baru (opsional)</label><select id="ti-new_team-leader_user_id" name="new_team[leader_user_id]"><option value="">Belum ditetapkan</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string)old('new_team.leader_user_id')===(string)$user->id)>{{ $user->name }} — {{ $user->email }}</option>@endforeach</select>@error('new_team.leader_user_id')<p class="tc-error">{{ $message }}</p>@enderror</div>
</div></details>
<div class="tc-team-save"><span class="tc-muted">Perubahan ketua berlaku untuk hak ubah laporan tim.</span><button type="submit" class="tc-btn tc-btn-primary">Simpan semua perubahan</button></div>
</section></form>
</div></x-app-layout>
