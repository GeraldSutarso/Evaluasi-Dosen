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
           <a class="nav-link" href="#"><span>Welcome, {{ auth()->user()->username }}</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('home') }}">
              <button id="home" class="btn btn-warning">Home</button>
            </a>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="{{ route('logout') }}">
              <button class="btn btn-danger">Keluar</button>
            </a>
          </li>
        @endguest
      </ul>
    </div>
  </div>
</nav>
@endif
