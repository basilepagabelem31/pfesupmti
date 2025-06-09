<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"{{ (!empty($htmlAttribute)) ? $htmlAttribute : '' }}>
<head>
    @include('partial.head')
</head>
<body class="{{ (!empty($bodyClass)) ? $bodyClass : '' }}">
    <!--pour la notification -->
  @if(Auth::check() && Auth::user()->unreadNotifications->count())
    <div class="alert alert-info text-center" id="notification-alert">
        <h5>Notifications :</h5>
        <ul>
            @foreach(Auth::user()->unreadNotifications as $notification)
                <li>
                    @if(isset($notification->data['type']) && $notification->data['type'] === 'ajout')
                        <span class="badge bg-success me-1">Ajout</span>
                    @elseif(isset($notification->data['type']) && $notification->data['type'] === 'modification')
                        <span class="badge bg-warning text-dark me-1">Modification</span>
                    @endif
                    {{ $notification->data['message'] }}
                    <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-link btn-sm p-0 align-baseline">
                            Voir
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>
@endif

    <!-- BEGIN #app -->
    <div id="app" class="app {{ (!empty($appClass)) ? $appClass : '' }}">
        @includeWhen(empty($appHeaderHide), 'partial.header')
        @includeWhen(empty($appSidebarHide), 'partial.sidebar')
        @includeWhen(!empty($appTopNav), 'partial.top-nav')

        @if (empty($appContentHide))
            <!-- BEGIN #content -->
            <div id="content" class="app-content  {{ (!empty($appContentClass)) ? $appContentClass : '' }}">
                @yield('content')
            </div>
            <!-- END #content -->
        @else
            @yield('content')
            {{-- Injection du JSON pré-calculé --}}
            <script>
                window._paysVilles = @json($paysVilles ?? []) ;
            </script>
        @endif

        @includeWhen(!empty($appFooter), 'partial.footer')
    </div>
    <!-- END #app -->

    @yield('outter_content')
    @include('partial.scroll-top-btn')
    @include('partial.theme-panel')
    @include('partial.scripts')
</body>
@yield('my_js')

</html>
