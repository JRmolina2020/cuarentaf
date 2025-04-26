@include('header')

<body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#"><i class="fi fi-nav-icon-grid-a"></i></a>
        </li>
      </ul>
    </nav>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="index3.html" class="brand-link">
        <img src="img/iconf.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">

      </a>

      <div class="sidebar">

        {{-- navegation  --}}
        @include('navegation')
        {{-- end  --}}
      </div>
    </aside>
    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row" id="app">
            @yield('measure')
            <div class="card card-outline card-primary">
              <div class="card-header">
                <h3 class="card-title">@yield('titlepanel')</h3>
              </div>
              <div class="card-body">
                @yield('content')
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <footer class="main-footer">

  </footer>
  </div>
  @include('footer')