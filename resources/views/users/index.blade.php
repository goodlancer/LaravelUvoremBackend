@extends('layouts.app', ['activePage' => 'user', 'titlePage' => __('User Management')])

@section('content')

    <div class="content">
        <div class="container-fluid">
            <div class="card registration">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">{{ __('Users') }}</h4>
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
                    <div class="row">
                        <div class="col-sm-3">
                            <select class="selectpicker" name="countrys" data-style="btn btn-primary" id="countrys"
                                value="0" onchange="location.href='/user?country='+this.value">
                                <option value="0">All</option>
                                @foreach ($countrys as $item)
                                    <option value="{{ $item->country }}" <?php echo $cur_country==$item->
                                        country ? 'selected' : ''; ?>>{{ $item->country }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="col-sm-1">
                            @if (Session::get('role') == 1)
                                <a href="{{ route('user.create') }}" class="btn btn-primary">{{ __('Add User') }}</a>
                            @endif
                        </div> --}}
                        <div class="col-sm-8"></div>

                    </div>
                    <div class="fresh-datatables">
                        <table id="datatables" class="table table-striped table-no-bordered table-hover" cellspacing="0"
                            width="100%" style='text-align:center'>
                            <thead class=" text-primary">
                                <tr>
                                    <th style="width:80px"> {{ __('No ') }} </th>
                                    <th> {{ __('Avatar') }} </th>
                                    <th> {{ __('Offers') }} </th>
                                    <th> {{ __('User') }} </th>
                                    <th> {{ __('Nick Name') }} </th>
                                    <th> {{ __('Name') }} </th>
                                    <th> {{ __('Email') }} </th>
                                    <th> {{ __('Country') }} </th>
                                    <th> {{ __('Limit Date') }} </th>
                                    <th> {{ __('Active') }} </th>
                                    <th> {{ __('Action') }} </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $index => $user)
                                    @if ($user->role == 1 || $user->role == 4)
                                        <tr>
                                        @elseif($user->role == 2)
                                        <tr style="background-color: #d88420">
                                        @elseif($user->role == 3)
                                        <tr style="background-color: #00c68b">
                                    @endif
                                    <td> {{ $index + 1 }}</td>
                                    <td rel="tooltip" data-original-title="{{ $user->nickname }}"
                                        title="{{ $user->nickname }}">
                                        <img src="{{ $user->avatar }}?{{ time() }}"
                                            style="max-width:100px; max-height:100px; border-radius:50%">
                                        <!-- @if ($user->role != 1)
                                            <div class="row">
                                                <div class="col-sm-2"></div>
                                                <div class="col-sm-3" style="padding:0px; text-align: -webkit-right;">
                                                    <p
                                                        style="border-radius:50%; height: 20px;width: max-content; margin:0px; background-color: red; color: white; padding-right: 4px; padding-left: 4px;">
                                                        {{ $user->diactive_count }}</p>
                                                </div>
                                                <div class="col-sm-1"></div>
                                                <div class="col-sm-3" style="padding:0px">
                                                    <p
                                                        style="border-radius:50%; height: 20px; width: max-content; margin:0px; background-color: rgb(40, 179, 81); color: white; padding-right: 4px; padding-left: 4px;">
                                                        {{ $user->active_count }}</p>
                                                </div>
                                            </div>
                                        @endif -->
                                    </td>
                                    <td rel="tooltop" data-original-title="offer">
                                        <h4>{{ $user->active_count }}</h4>
                                    </td>
                                    <td>
                                        @if ($user->role == 1)
                                            Admin
                                        @elseif($user->role == 2)
                                            General
                                        @else
                                            Premium
                                        @endif
                                    </td>
                                    <td> {{ $user->name }} </td>
                                    <td> {{ $user->firstname }} </td>
                                    <td> {{ $user->email }} </td>
                                    <td> {{ $user->country }} </td>
                                    @if ($user->role == 1)
                                        <td></td>
                                    @elseif($user->role == 2)
                                        <td></td>
                                    @else
                                        <td>{{ $user->dt_limit }}</td>
                                    @endif
                                    <td>
                                        @if ($user->active)
                                            Allowed
                                        @else
                                            Blocked
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->role != 1)
                                            <form action="{{ route('user.destroy', $user) }}" method="post">
                                                @csrf
                                                @method('delete')
                                                <a rel="tooltip" class="btn btn-success btn-link"
                                                    href="{{ route('user.edit', [$user, 4]) }}" data-original-title="Edit"
                                                    title="Edit">
                                                    <i class="material-icons">edit</i>
                                                    <div class="ripple-container"></div>
                                                </a>
                                                @if (Session::get('role') == 1)
                                                    <button rel="tooltip" type="button" class="btn btn-danger btn-link"
                                                        data-original-title="Delete" title="Delete"
                                                        onclick="confirm('{{ __('Are you sure you want to delete this user?') }}') ? this.parentElement.submit() : ''">
                                                        <i class="material-icons">close</i>
                                                        <div class="ripple-container"></div>
                                                    </button>
                                                @endif
                                            </form>
                                        @elseif(Session::get('role') == 1 || Session::get('role') == 4 && $user->role !=
                                            1)
                                            <a rel="tooltip" class="btn btn-success btn-link"
                                                href="{{ route('profile.edit') }}" data-original-title="Edit" title="Edit">
                                                <i class="material-icons">edit</i>
                                                <div class="ripple-container"></div>
                                            </a>
                                        @endif
                                    </td>
                                    </tr>
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
@endpush
