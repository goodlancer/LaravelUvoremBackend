@extends('layouts.app', ['activePage' => 'admin', 'titlePage' => __('Admin Management')])

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          @if ($errors->has('email'))
            <div class="row">
              <div class="col-sm-12">
                <div class="alert alert-danger">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <i class="material-icons">close</i>
                  </button>
                  <span>{{ $errors->first('email') }}</span>
                </div>
              </div>
            </div>
          @endif
          <form method="post" action="{{ route('admin.update', $user) }}" autocomplete="off" class="form-horizontal" enctype="multipart/form-data">
            @csrf
            @method('put')
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('Edit Admin') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body row">
                <div class="col-md-12 text-right">
                    <a href="{{ route('admin.index', 'country=0') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                  <div class="row">
                    <div class="col-md-4 row" style="justify-content: center; align-content: center;">
                      <div class="fileinput text-center fileinput-new" data-provides="fileinput">
                        <div class="fileinput-new thumbnail img-circle">
                          @if($user->avatar != null)
                          <img src="{{$user->avatar}}?{{time()}}" alt="...">
                          @else
                            <img src="{{ asset('material') }}/img/default.png" alt="...">
                          @endif
                        </div>
                        <div class="fileinput-preview fileinput-exists thumbnail img-circle" style="">\
                        <input value="{{$user->id}}" id="userId" hidden>
                        </div>
                        <div>
                          <span class="btn btn-round btn-rose btn-file">
                            <span class="fileinput-new"> Photo</span>
                            <span class="fileinput-exists">Change</span>
                            <input type="file" name="photo_path">
                          <div class="ripple-container"></div></span>
                          <br>
                          <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove<div class="ripple-container"><div class="ripple-decorator ripple-on ripple-out" style="left: 80.0156px; top: 18px; background-color: rgb(255, 255, 255); transform: scale(15.5098);"></div><div class="ripple-decorator ripple-on ripple-out" style="left: 80.0156px; top: 18px; background-color: rgb(255, 255, 255); transform: scale(15.5098);"></div><div class="ripple-decorator ripple-on ripple-out" style="left: 80.0156px; top: 18px; background-color: rgb(255, 255, 255); transform: scale(15.5098);"></div></div></a>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-8 row">
                      <label class="col-sm-2 col-form-label">{{ __('Activate') }}</label>
                      <div class="col-sm-4">
                        <div class="togglebutton" style="margin-top: 14px">
                          <label>
                            <input type="checkbox" @if($user->active == 1) checked @else @endif id="activate" onclick="userActivate({{$user->id}})">
                              <span class="toggle"></span>
                          </label>
                        </div>
                      </div>
                      
                      <label class="col-sm-2 col-form-label">{{ __('Email') }}</label>
                      <div class="col-sm-4">
                        <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                          <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" disabled name="email" id="input-email" type="email" placeholder="{{ __('Email') }}" value="{{ old('email', $user->email) }}" required />
                          @if ($errors->has('email'))
                            <span id="email-error" class="error text-danger" for="input-email">{{ $errors->first('email') }}</span>
                          @endif
                        </div>
                      </div>
                      <label class="col-md-2 col-form-label">{{ __('Name') }}</label>
                      <div class="col-md-4">
                        <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                          <input class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" disabled name="name" id="input-name" type="text" placeholder="{{ __('Name') }}" value="{{ old('name', $user->name) }}" required="true" aria-required="true"/>
                          @if ($errors->has('name'))
                            <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name') }}</span>
                          @endif
                        </div>
                      </div>
                      
       
                      <label class="col-md-2 col-form-label">{{ __('First name') }}</label>
                      <div class="col-md-4">
                        <div class="form-group{{ $errors->has('firstname') ? ' has-danger' : '' }}">
                          <input class="form-control{{ $errors->has('firstname') ? ' is-invalid' : '' }}" disabled name="firstname" id="input-firstname" type="text" placeholder="{{ __('First name') }}" value="{{ old('firstname', $user->firstname) }}" required="true" aria-required="true"/>
                          @if ($errors->has('firstname'))
                            <span id="name-error" class="error text-danger" for="input-firstname">{{ $errors->first('firstname') }}</span>
                          @endif
                        </div>
                      </div>
                      
                      <label class="col-sm-2 col-form-label">{{ __('Phone number') }}</label>
                      <div class="col-sm-4">
                        <div class="form-group{{ $errors->has('phonenumber') ? ' has-danger' : '' }}">
                          <input class="form-control{{ $errors->has('phonenumber') ? ' is-invalid' : '' }}" disabled name="phonenumber" id="input-phonenumber" type="phonenumber" placeholder="{{ __('Phone number') }}" value="{{ old('phonenumber', $user->phonenumber) }}" required />
                          @if ($errors->has('phonenumber'))
                            <span id="phonenumber-error" class="error text-danger" for="input-phonenumber">{{ $errors->first('phonenumber') }}</span>
                          @endif
                        </div>
                      </div>
                      <label class="col-md-2 col-form-label">{{ __('Last name') }}</label>
                      <div class="col-md-4">
                        <div class="form-group{{ $errors->has('lastname') ? ' has-danger' : '' }}">
                          <input class="form-control{{ $errors->has('lastname') ? ' is-invalid' : '' }}" disabled name="lastname" id="input-lastname" type="text" placeholder="{{ __('Last name') }}" value="{{ old('lastname', $user->lastname) }}" required="true" aria-required="true"/>
                          @if ($errors->has('lastname'))
                            <span id="name-error" class="error text-danger" for="input-lastname">{{ $errors->first('lastName') }}</span>
                          @endif
                        </div>
                      </div>
                      <label class="col-sm-2 col-form-label" for="input-password-confirmation">{{ __('Gender') }}</label>
                      <div class="col-sm-4">
                        <div class="form-group">
                          <select class="selectpicker" name="gender" data-style="btn btn-primary" value="0">
                            <option value="0" disabled <?php echo ($user->gender == 0 ? 'selected' : '')?>>Male</option>
                            <option value="1" disabled <?php echo ($user->gender == 1 ? 'selected' : '')?>>Female</option>
                          </select>
                        </div>
                      </div>
                      
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary" hidden>Register</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
@push('js')
  <script src="{{ asset('material') }}/js/plugins/jasny-bootstrap.min.js"></script>
  <script src="{{ asset('pages') }}/edit_user.js"></script>
@endpush
