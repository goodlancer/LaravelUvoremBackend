@extends('layouts.app', ['activePage' => 'offer', 'titlePage' => __('Offer Management')])
@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="card registration">
      <div class="card-header card-header-primary">
        <h4 class="card-title">{{ __('Offer') }}</h4>
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
                <th style="width: 4%"> {{ __('No ') }} </th>
                <th style="width: 8%"> {{ __('Name') }} </th>
                <th style="width: 10%"> {{ __('Email') }} </th>
                <th style="width: 10%"> {{ __('Country') }} </th>
                <th style="width: 6%"> {{ __('Type') }} </th>
                <th style="width: 10%"> {{ __('Image_1') }} </th>
                <th style="width: 10%"> {{ __('Image_2') }} </th>
                <th style="width: 10%"> {{ __('Image_3') }} </th>
                <th style="width: 10%"> {{ __('title') }} </th>
                <th style="width: 18%"> {{ __('Description') }} </th>
                <th style="width: 6%"> {{ __('Online/Offline State') }} </th>
                <th style="width: 8%"> {{ __('Publish State') }} </th>
                </tr>
            </thead>
            <tbody >
                @foreach($data as  $index => $item)
                <tr>
                  <td> {{$index+1}}</td>
                  <td> {{ $item->name }} </td>
                  <td> {{ $item->email }} </td>
                  <td> {{ $item->country }} </td>
                  @if($item->offerType == 0)
                  <td> DRAWINGS </td>
                  @elseif($item->offerType == 1)
                  <td> PAINTINGS </td>
                  @elseif($item->offerType == 2)
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
                  @if($item->art_active == 0)
                  <td>Offline</td>
                  @else
                  <td>Online</td>
                  @endif
                  <td>
                      <div class="togglebutton">
                          <label>
                          <input type="checkbox" @if($item->state == 1) checked @else @endif id="state{{$item->article_id}}" onclick="checkPublished({{$item->article_id}})">
                              <span class="toggle"></span>
                          </label>
                      </div>
                      <div class="row">
                        <div class="col-md-4" style="padding: 0px"> 
                          <a rel="tooltip" class="btn btn-success btn-link"  data-toggle="modal" data-target="#editOffer_{{$index}}" data-original-title="Edit" title="Edit" >
                            <i class="material-icons">edit</i>
                            <div class="ripple-container"></div>
                          </a>
                        </div>
                        <div class="col-md-4">
                          <form action="{{ route('article.delete', $item->article_id) }}" method="post">
                            @csrf
                            @method('delete')
                            <button rel="tooltip" type="button" class="btn btn-danger btn-link" data-original-title="Delete" title="Delete" onclick="confirm('{{ __("Are you sure you want to delete this user?") }}') ? this.parentElement.submit() : ''">
                                <i class="material-icons">close</i>
                                <div class="ripple-container"></div>
                            </button>
                          </form>
                        </div>
                      </div>
                  </td>
                </tr>
                <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="send_notification" aria-hidden="true" id="editOffer_{{$index}}">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Edit Offer</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      </div>
                      <form method="post" action="offer/edit" autocomplete="off" class="form-horizontal" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body row">
                          <div class="col-md-8">
                            <div class="form-group">
                              <input class="form-control" name="article_id" type="text"  value="{{ $item->article_id }}" required hidden/>
                            </div>
                            <div class="form-group">
                              <input class="form-control" name="offer_type" id="offer_type" type="text" value="{{$item->offerType}}" required hidden/>
                              {{-- $user->id, this.value --}}
                              <select class="selectpicker"  name="offerType" data-style="btn btn-primary" id="offerType" value="0" onchange="setType()" required>
                                <option value="0" <?php  echo $item->offerType == 0 ? 'selected' : '' ?>>DRAWINGS</option>
                                <option value="1" <?php  echo $item->offerType == 1 ? 'selected' : '' ?>>PAINTINGS</option>
                                <option value="2" <?php  echo $item->offerType == 2 ? 'selected' : '' ?>>ARTISTIC OBJECTS</option>
                                <option value="2" <?php  echo $item->offerType == 3 ? 'selected' : '' ?>>PICTURES</option>
                              </select>
                            </div>
                            <div class="form-group">
                              <input class="form-control" name="title" type="text"  placeholder="{{ __('Title') }}" value="{{ $item->title }}" required/>
                            </div>
                            <div class="form-group">
                            <textarea class="form-control" name="description" id="content" rows="8" placeholder="{{ __('Description') }}" required>{{$item->description}}</textarea>
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
  <script src="{{ asset('pages') }}/offerSetting.js"></script>
  <script>
    var setType = function(){
      var type = $('#offerType').val();
      $('#offer_type').val(type);
    }
  </script>
@endpush
