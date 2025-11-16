<div id="footer-bar" class="footer-bar-3">
    <a href="{{ route('home') }}" class="{{ Route::is('home') ? 'active-nav' : '' }}"><i class="fa fa-home"></i><span>Home</span></a>
    {{-- <a href="{{ route('schedule') }}" class="{{ Route::is('schedule') ? 'active-nav' : '' }}"><i class="fas fa-tasks"></i><span>Tugas</span></a> --}}
    <a href="{{ route('patrolls.index') }}" class="{{ Route::is('patrolls.index') ? 'active-nav' : '' }}"><i class="fas fa-user-shield"></i><span>Security Patrol</span></a>
    {{-- <a href="#"><i class="fa fa-search"></i><span>Search</span></a> --}}
    <a href="{{ route('setting') }}" class="{{ Route::is('setting') ? 'active-nav' : '' }}"><i class="fa fa-cog"></i><span>Settings</span></a>
</div>