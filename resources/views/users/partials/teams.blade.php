@include('ti.partials.assets')
<section class="tc tc-user-teams" aria-labelledby="user-teams-heading">
    <h2 id="user-teams-heading">Keanggotaan tim</h2>
    @if(isset($user))
        @if($user->teams->isNotEmpty())
            <p class="tc-muted">Keanggotaan saat ini. Untuk mengubahnya, buka tim terkait agar perubahan hanya berlaku pada tim tersebut.</p>
            <ul class="tc-user-team-list">
                @foreach($user->teams as $team)
                    <li>
                        <span>{{ $team->name }} @if((int)$team->leader_user_id===(int)$user->id)<strong>(Ketua)</strong>@endif</span>
                        <a class="tc-link" href="{{ route('ti.teams.members.edit',$team) }}">Atur anggota tim</a>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="tc-muted">Pengguna ini belum menjadi anggota tim.</p>
            <a class="tc-link" href="{{ route('ti.teams.index') }}">Pilih tim untuk menambahkan anggota</a>
        @endif
    @else
        <p class="tc-muted">Keanggotaan diatur dari tim yang dituju setelah akun dibuat, sehingga perubahan hanya memengaruhi satu tim.</p>
        <a class="tc-link" href="{{ route('ti.teams.index') }}">Buka pengelolaan tim</a>
    @endif
</section>
