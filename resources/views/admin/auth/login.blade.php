@extends('admin.auth.auth')

@section('content')
<section class="login-content">
   <div class="row m-0 align-items-center bg-white vh-100">
      <div class="col-md-6">
         <div class="row justify-content-center">
            <div class="col-md-10">
               <div class="card card-transparent shadow-none d-flex justify-content-center mb-0 auth-card">
                  <div class="card-body z-3 px-md-0 px-lg-4">
                     <a href="{{ url('/dashboard') }}" class="navbar-brand d-flex align-items-center mb-3">
                        <div class="logo-main">
                           <div class="logo-normal">
                              <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="E-zone Logo" class="img-fluid" loading="lazy" height="80px" width="80px"/>
                           </div>
                           <div class="logo-mini">
                              <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="E-zone Logo" loading="lazy"/>
                           </div>
                        </div>
                        <h4 class="logo-title ms-3">Admin</h4>
                     </a>

                     @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                           <ul class="mb-0">
                              @foreach($errors->all() as $error)
                                 <li>{{ $error }}</li>
                              @endforeach
                           </ul>
                           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                     @endif

                     <form method="POST" action="{{ route('admin.login') }}">
                        @csrf
                        <div class="row">
                           <div class="col-lg-12">
                              <div class="form-group">
                                 <label for="login" class="form-label">Email or Username</label>
                                 <input type="text" class="form-control @error('login') is-invalid @enderror" id="login" name="login" value="{{ old('login') }}" aria-describedby="login" placeholder=" " required>
                                 @error('login')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-lg-12">
                              <div class="form-group">
                                 <label for="password" class="form-label">Password</label>
                                 <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" aria-describedby="password" placeholder=" " required>
                                 @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                 @enderror
                              </div>
                           </div>
                           <div class="col-lg-12 d-flex justify-content-between">
                              <div class="form-check mb-3">
                                 <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                 <label class="form-check-label" for="remember">Remember Me</label>
                              </div>
                           </div>
                        </div>
                        <div class="d-flex justify-content-center">
                           <button type="submit" class="btn btn-primary">Sign In</button>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-6 d-md-block d-none bg-primary p-0 mt-n1 vh-100 overflow-hidden">
         <img src="{{ asset('admin_assets/images/auth/01.png') }}" class="img-fluid gradient-main animated-scaleX" alt="images">
      </div>
   </div>
</section>
@endsection
