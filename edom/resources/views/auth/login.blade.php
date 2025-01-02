@extends('layout.main')

@section('content')
<main class="login-form">
  <div class="container mt-4">
      <div class="row justify-content-center">
          <div class="col-md-8">
              <div class="card">
                  <div class="card-header" style="background-color: #992424"><center><b style="color: aliceblue">Masuk</b></center></div>
                  <div class="card-body">
  
                      <form action="{{ route('login.post') }}" method="POST">
                          @csrf
                          <div class="form-group row">
                            <label for="student_id" class="col-md-4 col-form-label text-md-right">NIM:</label>
                            <div class="col-md-6">
                                <input type="text" id="student_id" class="form-control" placeholder="Masukkan NIM Anda" name="student_id" required autofocus>
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