<nav class="navbar navbar-expand-md navbar-dark fixed-top">
    <div class="collapse navbar-collapse d-md-flex justify-content-between align-items-center" id="navbarMain">
        <ul class="navbar-nav d-flex align-items-center">
            <li class="nav-item text-center">
                <a class="navbar-brand mr-5" href="{{ route('home') }}">
                    <img class="img-fluid" src="{{ asset('/images/waben.png') }}"
                         style="width: 100%; height: 100%; max-width: 200px; max-height: 40px;">
                </a>
            </li>
            <li class="nav-item text-center">
                <a href="{{ route('home') }}" class="nav-link mx-0 mx-lg-3">
                    <i class="fas fa-globe"></i> <span>Alle Domains</span>
                </a>
            </li>
            <li class="nav-item text-center">
                <a href="{{ route('domain.active') }}" class="nav-link mx-0 mx-lg-3">
                    <i class="fas fa-list"></i> <span>Aktive Domains</span>
                </a>
            </li>
            <li class="nav-item text-center">
                <a href="{{ route('domain.incomplete') }}" class="nav-link mx-0 mx-lg-3">
                    <i class="fas fa-exclamation-triangle"></i> <span>Unvollständige/Fehlerhafte Daten</span>
                </a>
            </li>
            <li class="nav-item text-center">
                <a href="{{ route('domain.expiring') }}" class="nav-link mx-0 mx-lg-3">
                    <i class="far fa-calendar-alt"></i> <span>Auslaufende Verträge</span>
                </a>
            </li>
            <li class="nav-item text-center">
                <form method="GET" action="{{ route('domain.search') }}">
                    @csrf
                    <div class="input-group">
                        <input type="text" class="form-control" name="searchString" placeholder="Suche nach">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </li>
        </ul>
        <ul class="navbar-nav d-md-flex justify-content-center" style="min-width: 150px;">
            <li class="nav-item text-center mx-2">
                <a href="{{ route('import') }}" class="btn btn-primary">Daten-Import manuell starten</a>
            </li>
            @if (Auth::user())
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <li class="nav-item text-center text-center mx-2">
                        <a class="nav-link" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); this.closest('form').submit();">
                            <span class="d-none d-md-inline mr-2">Logout</span>
                            <span class="d-inline d-md-none mr-2">{{ __('Logout') }}</span><i
                                class="fa fa-sign-out-alt d-md-inline"></i>
                        </a>
                    </li>
                </form>
            @else
                <li class="nav-item text-center mx-2">
                    <a class="nav-link" href="{{ route('login') }}"><i class="fas fa-user"></i>
                        <span>{{ __('Login') }}</span></a>
                </li>
            @endif
        </ul>
    </div>
</nav>
