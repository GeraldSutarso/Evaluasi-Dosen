@extends('layout.main')

@section('content')
<main class="login-form">
  <div class="container mt-4">
      <div class="row justify-content-center">
          <div class="col-md-8">
              <div class="card">
                  <div class="card-header" style="background-color: #992424"><center><b style="color: aliceblue">Verifikasi</b></center></div>
                  <div class="card-body">
  
                      <form action="{{ route('2fa.verify') }}" method="POST">
                          @csrf
                          <div class="form-group row">
                            <label for="2fa_code" class="col-md-4 col-form-label text-md-right">Kode Verifikasi:</label>
                            <div class="col-md-6">
                                <input type="text" id="2fa_code" class="form-control" placeholder="Masukkan Kode Verifikasi" name="2fa_code" required autofocus>
                            </div>
                            <br>
                            <br>
                            <label class="col-md-4 col-form-label text-md-right">&nbsp;</label>
                            @if ($errors->any())
                            <div class="col-md-6 alert alert-danger">
                                    @foreach ($errors->all() as $error)
                                        {{ $error }}
                                    @endforeach
                            </div>
                            @endif
                          </div>                          
                          <div class="col-md-6 offset-md-4">
                              <button type="submit" class="btn btn-primary">
                                  Masuk
                              </button>
                          </div>
                      </form>
                        
                  </div>
              </div>
          </div>
      </div>
  </div>
</main>
@endsection