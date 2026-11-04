<x-app-layout><div class="tc">
@include('ti.partials.header',['title'=>$object->exists?'Atur objek TI':'Tambah objek TI'])
<form data-tc-native method="POST" action="{{ $object->exists?route('ti.objects.update',$object):route('ti.objects.store') }}">@csrf @if($object->exists) @method('PUT') @endif
<section class="tc-panel"><h2>Identitas objek</h2><div class="tc-grid">
<x-ti-field name="code" label="Kode objek" :value="old('code',$object->code)" required help="Kode unik, misalnya APP-044 atau INF-020. Maksimal 50 karakter."/>
<x-ti-field name="name" label="Nama objek" :value="old('name',$object->name)" required/>
<x-ti-field name="scope" label="Cakupan" kind="select" :options="\App\Support\TiForm::SCOPES" :value="old('scope',$object->scope)" required/>
<x-ti-field name="aspect" label="Aspek" kind="select" :options="\App\Support\TiForm::ASPECTS" :value="old('aspect',$object->aspect)" required/>
<x-ti-field name="manager_label" label="PJ / pengelola (referensi)" :value="old('manager_label',$object->manager_label)" help="Teks referensi; bukan sumber pemberian hak akses."/>
<x-ti-field name="audience" label="Pengguna" :value="old('audience',$object->audience)"/>
<x-ti-field name="technical_type" label="Jenis teknis" :value="old('technical_type',$object->technical_type)"/>
<x-ti-field name="programmer_label" label="Programmer" :value="old('programmer_label',$object->programmer_label)"/>
<div class="tc-field"><label for="object-status">Status</label><select id="object-status" name="is_active"><option value="1" @selected((string)old('is_active',(int)$object->is_active)==='1')>Aktif</option><option value="0" @selected((string)old('is_active',(int)$object->is_active)==='0')>Nonaktif — riwayat tetap tersimpan</option></select></div>
</div></section>
<section class="tc-panel"><h2>PIC &amp; tim pengelola</h2><p class="tc-muted">Hanya PIC dan ketua tim yang dipilih di bawah ini yang dapat mengubah laporan. Jika keduanya kosong, objek belum dapat dilaporkan.</p><div class="tc-grid">
<div class="tc-field"><label for="object-pic">PIC aplikasi / perangkat (opsional)</label><select id="object-pic" name="pic_user_id"><option value="">Belum ditetapkan</option>@foreach($users as $u)<option value="{{ $u->id }}" @selected((string)old('pic_user_id',$object->pic_user_id)===(string)$u->id)>{{ $u->name }} — {{ $u->email }}</option>@endforeach</select>@error('pic_user_id')<p class="tc-error">{{ $message }}</p>@enderror</div>
<fieldset><legend>Tim pengelola (boleh lebih dari satu)</legend><div class="tc-checks">@foreach($teams as $team)<label class="tc-check"><input type="checkbox" name="team_ids[]" value="{{ $team->id }}" @checked(in_array($team->id,(session()->hasOldInput()?old('team_ids',[]):$object->teams->pluck('id')->all())))><span>{{ $team->name }}</span></label>@endforeach</div>@error('team_ids')<p class="tc-error">{{ $message }}</p>@enderror</fieldset>
</div></section><div class="tc-actions"><button type="submit" class="tc-btn tc-btn-primary">Simpan objek &amp; penanggung jawab</button><a class="tc-btn" href="{{ route('ti.objects.index') }}">Batal</a></div></form>
</div></x-app-layout>
