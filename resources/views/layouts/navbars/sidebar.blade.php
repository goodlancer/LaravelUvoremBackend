<div class="sidebar" data-color="purple" data-background-color="black" >
  <!--
      Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

      Tip 2: you can also add an image using data-image tag
  -->
  <div class="logo">
    <a href="/dashboard" class="simple-text logo-normal" style="text-transform: none;">
      {{ __('Uvorem') }}
    </a>
  </div>
  <div class="sidebar-wrapper">
    <ul class="nav">
      <li class="nav-item{{ $activePage == 'dashboard' ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.index') }}">
          <i class="material-icons">dashboard</i>
            <p>{{ __('Dashboard') }}</p>
        </a>
      </li>
      @if (Session::get('role') == 1)
      <li class="nav-item{{ $activePage == 'admin' ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('admin.index', 'country=0') }}">
          <i class="material-icons">account_circle</i>
            <p>{{ __('Admin Management') }}</p>
        </a>
      </li>
      @endif
      <li class="nav-item{{ $activePage == 'user' ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('user.index', 'country=0') }}">
          <i class="material-icons">account_circle</i>
            <p>{{ __('User Management') }}</p>
        </a>
      </li>
      <li class="nav-item{{ $activePage == 'offer' ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('offer.index', 'type=-1') }}">
          <i class="material-icons">card_travel</i>
            <p>{{ __('Offer Management') }}</p>
        </a>
      </li>
      <!--
      <li class="nav-item{{ $activePage == 'country' ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('country.index', 'id=0&state=2') }}">
          <i class="material-icons">card_travel</i>
            <p>{{ __('Country Management') }}</p>
        </a>
      </li>
      -->
      <li class="nav-item{{ $activePage == 'notification' ? ' active' : '' }}">
        <a class="nav-link"href="{{ route('notification.index') }}">
          <i class="material-icons">access_alarm</i>
            <p>{{ __('Notification History') }}</p>
        </a>
      </li>
      <li class="nav-item{{ $activePage == 'offer_history' ? ' active' : '' }}">
        <a class="nav-link"href="{{ route('offer_history.index') }}">
          <i class="material-icons">history</i>
            <p>{{ __('Offer History') }}</p>
        </a>
      </li>
      
    </ul>
  </div>
</div>

