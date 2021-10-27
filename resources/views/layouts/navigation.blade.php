<nav class="navbar navbar-expand-md navbar-dark fixed-top">
    {{--    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMain"--}}
    {{--            aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">--}}
    {{--        <span class="navbar-toggler-icon"></span>--}}
    {{--    </button>--}}

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
                    <i class="fas fa-list"></i> <span>Alle Domains</span>
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
                <form method="GET" action="{{route('domain.search')}}">
                    @csrf
                    <div class="input-group">
                        <input type="text" class="form-control" name="searchString" placeholder="Suche nach">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </li>
            <li class="nav-item text-center">
                {{--                <a href="{{ route('imprint') }}" class="nav-link mx-0 mx-lg-3">--}}
                {{--                    <i class="fas fa-stamp"></i> <span>{{ __('navigation.imprint') }}</span>--}}
                {{--                </a>--}}
            </li>
        </ul>
        <ul class="navbar-nav d-md-flex justify-content-center" style="min-width: 150px;">
            @if (\Auth::user())
                <li class="nav-item text-center mx-2">
                    {{--                    <a class="nav-link" href="{{ route('chat') }}">--}}
                    {{--                        Chat--}}
                    {{--                        @php--}}
                    {{--                            $numberOfUnseenMessages = \Auth::user()->getNumberOfUnseenMessages()--}}
                    {{--                        @endphp--}}
                    {{--                        @if ($numberOfUnseenMessages > 0)--}}
                    {{--                            <i class="fas fa-envelope-open-text d-md-inline"></i>--}}
                    {{--                            <span--}}
                    {{--                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">--}}
                    {{--                                {{ $numberOfUnseenMessages }}--}}
                    {{--                            <span class="d-none">unread messages</span>--}}
                    {{--                        </span>--}}
                    {{--                        @else--}}
                    {{--                            <i class="fas fa-envelope d-md-inline"></i>--}}
                    {{--                        @endif--}}
                    {{--                    </a>--}}
                </li>
                <li class="nav-item text-center mx-2">
                    {{--                    <a class="nav-link" href="{{ route('profile') }}">{{ \Auth::user()->name }} <i--}}
                    {{--                            class="fa fa-user d-md-inline"></i></a>--}}
                </li>
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
                    <a class="nav-link" href="{{ route('login') }}"><i class="fas fa-user"></i> <span>{{ __('Login') }}</span></a>
                </li>
            @endif
        </ul>
    </div>
</nav>
