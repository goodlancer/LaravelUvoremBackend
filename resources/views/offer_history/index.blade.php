@extends('layouts.app', ['activePage' => 'offer_history', 'titlePage' => __('Offer History')])
@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="card registration">
      <div class="card-header card-header-primary">
        <h4 class="card-title">{{ __('History') }}</h4>
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
            </div>
            @if(Session::get('role') == 1)
            <form class="col-md-8" action="{{ route('offer_history.destroy', 0) }}" method="post"  style="text-align:right; margin-bottom:20px">
              @csrf
              @method('delete')
              <button rel="tooltip" type="button" class="btn btn-primary btn-sm" data-original-title="Delete" title="Delete" 
              onclick="confirm('{{ __("Are you sure you want to delete checked offer history?") }}') ? document.getElementById(`checkbox_form`).submit() : ''">
                  Delete checked history
              </button>

              <button rel="tooltip" type="button" class="btn btn-danger btn-sm" data-original-title="Delete All" title="Delete All" onclick="confirm('{{ __("Are you sure you want to delete this all offer history?") }}') ? this.parentElement.submit() : ''">
                  Delete all
              </button>
            </form>
            @endif
          </div>
          <form action="{{ route('offer_history.destroy', -1) }}" method="post" id="checkbox_form">
            @csrf
            @method('delete')
            <table id="datatables" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%"  style='text-align:center'>
            <thead class=" text-primary">
            <tr >
              <th style="width:4%"> 
                <div class="form-check mr-auto">
                  <label class="form-check-label">
                    <input class="form-check-input" name='check_all' id="check_all" type="checkbox" value="checked">
                    <span class="form-check-sign">
                      <span class="check"></span>
                    </span>
                  </label>
                </div>
              </th>
                <th style="width: 4%"> {{ __('No ') }} </th>
                <th style="width: 10%"> {{ __('Name') }} </th>
                <th style="width: 10%"> {{ __('Email') }} </th>
                <th style="width: 10%"> {{ __('Country') }} </th>
                <th style="width: 10%"> {{ __('Type') }} </th>
                <th style="width: 10%"> {{ __('Image_1') }} </th>
                <th style="width: 10%"> {{ __('Image_2') }} </th>
                <th style="width: 10%"> {{ __('Image_3') }} </th>
                <th style="width: 10%"> {{ __('title') }} </th>
                <th style="width: 17%"> {{ __('Description') }} </th>
                @if(Session::get('role') == 1)
                <th style="width: 5%"> {{ __('Publish State') }} </th>
                @endif
              </tr>
            </thead>
            <tbody >
                @foreach($data as  $index => $item)
                <tr>
                  <td>
                    <div class="form-check mr-auto">
                      <label class="form-check-label">
                        <input class="form-check-input" name='check[]' type="checkbox" value="{{$item->article_id}}">
                        <span class="form-check-sign">
                          <span class="check"></span>
                        </span>
                      </label>
                    </div>
                  </td>
                  <td> {{$index+1}}</td>
                  <td> {{ $item->name }} </td>
                  <td> {{ $item->email }} </td>
                  <td> {{ $item->country }} </td>
                  @if($item->offerType == 0)
                  <td> DRAWINGS </td>
                  @elseif($item->offerType == 1)
                  <td> PAINTINGS </td>
                  @else
                  <td> ARTISTIC OBJECTS </td>
                  @endif
                  @foreach ($images[$index] as $imgIndex => $image)
                  <td>
                    <img src="{{$image->imagePath}}?{{time()}}" style="width:100px; height:100px;"  data-toggle="modal" data-target="#exampleModal{{$index}}_{{$imgIndex}}">
                  </td>
                  <div class="modal fade" id="exampleModal{{$index}}_{{$imgIndex}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          {{-- <h5 class="modal-title" id="exampleModalLabel">Modal title</h5> --}}
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <img src="{{$image->imagePath}}?{{time()}}" style="width:100%; height:100%;">
                        </div>
                        {{-- <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                          <button type="button" class="btn btn-primary">Save changes</button>
                        </div> --}}
                      </div>
                    </div>
                  </div>
                  @endforeach
                  <td> {{ $item->title }} </td>
                  <td> {{ $item->description }} </td>
                  @if(Session::get('role') == 1)
                  <td>
                    <form action="{{ route('offer_history.destroy', $item->article_id) }}" method="post">
                      @csrf
                      @method('delete')
                      <button rel="tooltip" type="button" class="btn btn-danger btn-link" data-original-title="Delete" title="Delete" onclick="confirm('{{ __("Are you sure you want to delete this offer history?") }}') ? this.parentElement.submit() : ''">
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

@endsection
@push('js')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
  <script src="{{ asset('pages') }}/offerSetting.js"></script>
  <script>
    var setType = function(){
      var type = $('#offerType').val();
      $('#offer_type').val(type);
    }
  </script>
@endpush
