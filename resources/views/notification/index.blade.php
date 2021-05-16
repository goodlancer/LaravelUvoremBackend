@extends('layouts.app', ['activePage' => 'notification', 'titlePage' => __('Notification History Management')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="card registration">
      <div class="card-header card-header-primary">
        <h4 class="card-title">{{ __('Notification History') }}</h4>
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
          <div class="row">
            <div class="col-md-4">
              <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#notification_modal">{{ __('Send Notification') }}</button>
            </div>
            @if(Session::get('role') == 1)
            <form class="col-md-8" action="{{ route('notification.destroy', 0) }}" method="post"  style="text-align:right; margin-bottom:20px">
              @csrf
              @method('delete')
              <button rel="tooltip" type="button" class="btn btn-primary btn-sm" data-original-title="Delete" title="Delete" 
              onclick="confirm('{{ __("Are you sure you want to delete checked notification history?") }}') ? document.getElementById(`checkbox_form`).submit() : ''">
                  Delete checked history
              </button>

              <button rel="tooltip" type="button" class="btn btn-danger btn-sm" data-original-title="Delete All" title="Delete All" onclick="confirm('{{ __("Are you sure you want to delete this all notification history?") }}') ? this.parentElement.submit() : ''">
                  Delete all
              </button>
            </form>
            @endif
          </div>
          <form action="{{ route('notification.destroy', -1) }}" method="post" id="checkbox_form">
            @csrf
            @method('delete')
            <table id="datatables" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%"  style='text-align:center'>
              <thead class=" text-primary">
                <tr>
                  <th style="width:80px"> 
                    <div class="form-check mr-auto">
                      <label class="form-check-label">
                        <input class="form-check-input" name='check_all' id="check_all" type="checkbox" value="checked">
                        <span class="form-check-sign">
                          <span class="check"></span>
                        </span>
                      </label>
                    </div>
                  </th>
                  <th style="width:80px"> {{ __('No') }} </th>
                  <th style="width:120px"> {{ __('Sender User') }} </th>
                  <th style="width:120px"> {{ __('Receive User') }} </th>
                  {{-- <th style="width:120px"> {{ __('Group') }} </th> --}}
                  <th style="width:120px"> {{ __('Title') }} </th>
                  <th> {{ __('Content') }} </th>
                  {{-- <th style="width:120px"> {{ __('Image') }} </th> --}}
                  {{-- <th style="width:80px"> {{ __('Answer') }} </th> --}}
                  <th style="width:120px"> {{ __('Date') }} </th>
                  @if(Session::get('role') == 1)
                  <th style="width:60px"> {{ __('Delete') }} </th>
                  @endif
                </tr>
              </thead>
              <tbody>
                @foreach($data as $index=>$item)
                  <tr>
                    <td>
                      <div class="form-check mr-auto">
                        <label class="form-check-label">
                          <input class="form-check-input" name='check[]' type="checkbox" value="{{$item->id}}">
                          <span class="form-check-sign">
                            <span class="check"></span>
                          </span>
                        </label>
                      </div>
                    </td>
                    <td>{{$index+1}}</td>
                    <td>
                      @if($item->send_user)
                        {{$item->send_user->name}} {{$item->send_user->surname}} 
                      @endif
                    </td>
                    <td>
                      @if($item->receive_user)
                        {{$item->receive_user->name}} {{$item->receive_user->surname}} 
                      @elseif($item->receiver_id == 0)
                      All
                      @endif
                    </td>
                    {{-- <td>
                      @if($item->group)
                        {{$item->group->name}}
                      @endif
                    </td> --}}
                    <td>{{$item->title}}</td>
                    <td>{{$item->content}}</td>
                    {{-- <td>
                      <img src="{{$item->image}}" alt="" style="max-width:120px; max-height:120px">
                    </td> --}}
                    {{-- <td>
                      @if($item->answer == 1)
                      Not answer
                      @elseif($item->answer == 2)
                      YES
                      @elseif($item->answer == 3)
                      NO
                      @endif
                    </td> --}}
                    <td>{{date('H:i d M Y', strtotime($item->created_at))}}</td>
                    @if(Session::get('role') == 1)
                    <td>
                      <form action="{{ route('notification.destroy', $item) }}" method="post">
                        @csrf
                        @method('delete')
                        <button rel="tooltip" type="button" class="btn btn-danger btn-link" data-original-title="Delete" title="Delete" onclick="confirm('{{ __("Are you sure you want to delete this notification history?") }}') ? this.parentElement.submit() : ''">
                            <i class="material-icons">close</i>
                            <div class="ripple-container"></div>
                        </button>
                      </form>
                    </td>
                    @endif
                  </tr>
                @endforeach
              </tbody>
            </table>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Theme -->
<div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="send_notification" aria-hidden="true" id="notification_modal">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Send Notification</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <form method="post" action="{{ route('notification.store') }}" autocomplete="off" class="form-horizontal" enctype="multipart/form-data">
        @csrf
        <div class="modal-body row">
          <div class="col-md-12">
            <div class="form-check form-check-radio form-check-inline">
              <label class="form-check-label">
                  <input class="form-check-input" type="radio" name="send_to" id="user" value="0" onchange="selectUser(value)" checked>
                  Send to User
                  <span class="circle">
                      <span class="check"></span>
                  </span>
              </label>
            </div>
            <div class="form-check form-check-radio form-check-inline" hidden>
              <label class="form-check-label">
                  <input class="form-check-input" type="radio" name="send_to" id="group" value="1" onchange="selectUser(value)" >
                  Send to Group
                  <span class="circle">
                      <span class="check"></span>
                  </span>
              </label>
            </div>
            <div class="form-check form-check-radio form-check-inline">
              <div id="send_user_id">
                <select class="selectpicker" name="send_user_id[]" multiple data-style="btn btn-primary">
                  <option value="0">{{ __('All') }}</option>
                  @foreach($users as $user)
                    <option value="{{$user->id}}">{{$user->firstname}}, {{$user->lastname}} <?php echo ($user->device_token || $user->iphone_device_token) ? '' : "( Offline )"?></option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          {{-- <div class="fileinput fileinput-new text-center col-md-4" data-provides="fileinput">
            <div class="fileinput-new thumbnail img-raised">
            <img src="\uploads\marker.png" alt="...">
            </div>
            <div class="fileinput-preview fileinput-exists thumbnail img-raised"></div>
            <div>
            <span class="btn btn-raised btn-round btn-rose btn-file">
              <span class="fileinput-new">Select image</span>
              <span class="fileinput-exists">Change</span>
              <input type="file" name="photo" id="photo"/>
            </span>
                <a href="#pablo" class="btn btn-danger btn-round fileinput-exists remove-image" data-dismiss="fileinput">
                  <i class="fa fa-times"></i> Remove
                </a>
            </div>
          </div> --}}

          <div class="col-md-8">
            <div class="form-group">
              <input class="form-control" name="title" type="text"  placeholder="{{ __('Title') }}" required/>
            </div>
            <div class="form-group">
            <textarea class="form-control" name="content" id="content" rows="8" placeholder="{{ __('Content') }}" required></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
@push('js')
<script src="{{ asset('material') }}/js/plugins/jasny-bootstrap.min.js"></script>
  <!-- <script src="{{ asset('material') }}/js/pages/matches.js"></script> -->
  <script>
  $(function () {
    $('#check_all').click(function() {
      if (this.checked) {
        $(':checkbox').each(function() {
          this.checked = true;
        });
      } else {
        $(':checkbox').each(function() {
          this.checked = false;
        });
      } 
    });
  });
  var selectUser = function(val){
    if(val == 0){
      $("#send_user_id").removeClass("hidden");
      $("#send_group_id").addClass("hidden");
    }else{
      $("#send_user_id").addClass("hidden");
      $("#send_group_id").removeClass("hidden");
    }
  }
</script>
@endpush