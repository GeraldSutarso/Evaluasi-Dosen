@if(!isset($excludeNavbar))
<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #992424">
  <div class="container-fluid">
    <a class="navbar-brand" href="/">
      <h4><img src="{{ asset('img/Logo (3295x1171).png') }}" style="width: 150px; height: auto;"></h4>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        @guest
          <li class="nav-item active">
            <b style="color: aliceblue">AKTI | EDOM</b>
          </li>
        @else
          <li class="nav-item">
           <a class="nav-link text-light" href="{{ route('home') }}"><span><h5>Welcome, {{ auth()->user()->name }}</h5></span></a>
          </li>
          <li class="nav-item">&nbsp;&nbsp;&nbsp;
            {{-- <a class="nav-link" href="{{ route('home') }}">
              <button id="home" class="btn btn-warning">Home</button>
            </a> --}}
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="{{ route('logout') }}">
              <button class="btn btn-danger"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
              </svg>&nbsp;Keluar</button>
            </a>
          </li>
        @endguest
      </ul>
    </div>
  </div>
</nav>
@endif
