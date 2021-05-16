@extends('layouts.app', ['activePage' => 'country', 'titlePage' => __('Country Management')])
@section('content')
<style type="text/css">
  #radioBtn .notActive{
    color: #3276b1;
    background-color: #fff;
  }
</style>
<div class="content">
  <div class="container-fluid">
    <div class="card registration">
      <div class="card-header card-header-primary">
        <h4 class="card-title">{{ __('Country') }}</h4>
      </div>
      <div class="card-body ">
        @if (session('status'))
          <div class="row">
            <div class="col-sm-12">
              <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <i class="material-icons">close</i>
                </button>
                <span>{{ session('status') }}</span>
              </div>
            </div>
          </div>
        @endif
        <div class="fresh-datatables">
          <table id="datatables" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%"  style='text-align:center'>
            <thead class=" text-primary">
            <tr >
              <th style="width:80px"> {{ __('No ') }} </th>
              <th> {{ __('Avatar') }} </th>
              <th> {{ __('NICK NAME') }} </th>
              <th> {{ __('Name') }} </th>
              <th> {{ __('Email') }} </th>
              <th> {{ __('Country') }} </th>
              <th> {{ __('New Country') }} </th>
              <th> {{ __('Create Date') }} </th>
              <th> {{ __('Attache Image') }} </th>
              <th> {{ __('Action') }} </th>
              </tr>
            </thead>
            <tbody >
              @foreach($users as  $index => $user)
              <tr>
                <td> {{$index+1}}</td>
                <td rel="tooltip"  data-original-title="{{$user->nickname}}" title="{{$user->nickname}}">
                  <img src="{{$user->avatar}}?{{time()}}" style="max-width:100px; max-height:100px; border-radius:50%">
                </td>
                <td> {{ $user->name }} </td>
                <td> {{ $user->firstname }} </td>
                <td> {{ $user->email }} </td>
                <td> {{ $user->country }} </td>
                <td> {{ $user->new_country }} </td>
                <td>{{date('M d Y', strtotime($user->updated_at))}}</td>
                <td>
                  <img src="{{$user->country_image}}?{{time()}}" style="max-width:100px; max-height:100px;" data-toggle="modal" data-target="#exampleModal{{$index}}">
                </td>
                <td>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <div class="col-sm-7 col-md-7">
                        <div class="input-group">
                          <div id="radioBtn" class="btn-group">
                            <a class="btn btn-primary btn-sm notActive" data-toggle="happy" data-title="N" onclick="location.href='/country?id='+{{$user->id}}+'&state=0'">refuse</a>
                            <a class="btn btn-primary btn-sm active" data-toggle="happy" data-title="P" onclick="location.href='/country?id='+{{$user->id}}+'&state=2'">PENDING</a>
                            <a class="btn btn-primary btn-sm notActive" data-toggle="happy" data-title="Y" onclick="location.href='/country?id='+{{$user->id}}+'&state=1'">approve</a>
                          </div>
                          <input type="hidden" name="happy" id="happy">
                        </div>
                    </div>
                    {{-- <div class="togglebutton">
                        <label>
                            <input type="checkbox" onchange="location.href='/country?id='+{{$user->id}}">
                            <span class="toggle"></span>
                        </label>
                    </div> --}}
                  </div>
                </td>
              </tr>
              <div class="modal fade" id="exampleModal{{$index}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      {{-- <h5 class="modal-title" id="exampleModalLabel">Modal title</h5> --}}
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <img src="{{$user->country_image}}?{{time()}}" style="width:100%; height:100%;">
                    </div>
                    {{-- <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <button type="button" class="btn btn-primary">Save changes</button>
                    </div> --}}
                  </div>
                </div>
              </div>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
@push('js')
  <script>
    $('#radioBtn a').on('click', function(){
    var sel = $(this).data('title');
    var tog = $(this).data('toggle');
    $('#'+tog).prop('value', sel);
     
    $('a[data-toggle="'+tog+'"]').not('[data-title="'+sel+'"]').removeClass('active').addClass('notActive');
    $('a[data-toggle="'+tog+'"][data-title="'+sel+'"]').removeClass('notActive').addClass('active');
  })
  
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
  <script src="{{ asset('pages') }}/edit_user.js"></script>
  @endpush
