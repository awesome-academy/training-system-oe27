@extends('supervisor.layouts.app')
@section('css')
    <script src="{{ asset('js/message.js') }}"></script>
    <link rel="stylesheet" type="text/css"
        href="{{ asset('bower_components/bower_package/datatables.net-bs4/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/supervisor_list_courses.css') }}" >
    <link rel="stylesheet" type="text/css"
        href="{{ asset('bower_components/bower_package/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <script type="text/javascript" language="javascript"
        src="{{ asset('bower_components/bower_package/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" language="javascript"
        src="{{ asset('bower_components/bower_package/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/datatable.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/message.css') }}">
@endsection
@section('content')
    @if (session('messenger'))
        <div id="messenger" class="alert alert-success" role="alert">
            <i data-feather="check"></i>
            <span class="mx-2">{{ session('messenger') }}</span>
        </div>
    @endif
    <div class="navbar padding">
        <a href="{{ route('trainee.create') }}"
            class="btn btn-primary">
            {{ trans('supervisor.new_trainee.new_trainee') }}
        </a>
    </div>
    <div id="main" class="layout-column flex padding">
        <div id="content" class="flex ">
            <div class="tab-pane fade show active" id="all"
                role="tabpanel" aria-labelledby="v-pills-home-tab">
                <table id="example"
                    class="table table-theme table-row v-middle dataTable no-footer"
                    role="grid"
                    aria-describedby="datatable_info">
                    <thead>
                        <tr role="row">
                            <th class="column-width17" tabindex="0"
                                aria-controls="datatable"
                                rowspan="1" colspan="1" aria-sort="ascending"
                                aria-label="ID: activate to sort column descending">
                                <span class="text-muted">
                                    {{ trans('supervisor.list_subjects.id') }}
                                </span>
                            </th>
                            <th class="column-width200" tabindex="0"
                                aria-controls="datatable" rowspan="1" colspan="1"
                                aria-label="Owner: activate to sort column ascending">
                                <span class="text-muted">
                                    {{ trans('supervisor.list_trainees.fullname') }}
                                </span>
                            </th>
                            <th class="column-width200" tabindex="0"
                                aria-controls="datatable" rowspan="1" colspan="1"
                                aria-label="Project: activate to sort column ascending">
                                <span class="text-muted">
                                    {{ trans('supervisor.list_trainees.email') }}
                                </span>
                            </th>
                            <th class="column-width100" tabindex="0" rowspan="1"
                                aria-controls="datatable" colspan="1" aria-label="Tasks">
                                <span class="text-muted d-none d-sm-block">
                                    {{ trans('supervisor.list_trainees.birthday') }}
                                </span>
                            </th>
                            <th class="column-width17" rowspan="1"
                                colspan="1" aria-label="Tasks">
                                <span class="text-muted d-none d-sm-block">
                                    {{ trans('supervisor.list_trainees.status') }}
                                </span>
                            </th>
                            <th class="sorting_disabled column-width17"
                                rowspan="1" colspan="1" aria-label="">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $index => $user)
                            <tr class="odd" data-id="1" role="row">
                                <td class="sorting_1 column-id">
                                    <small class="text-muted">
                                        {{ $index + config('number.init') }}
                                    </small>
                                </td>
                                <td class="flex">
                                    <a href="#" class="item-title text-color ">
                                        {{ $user->fullname }}
                                    </a>
                                </td>
                                <td class="flex">
                                    <a href="#" class="item-title text-color ">
                                        {{ $user->email }}
                                    </a>
                                </td>
                                <td class="flex">
                                    <a href="#" class="item-title text-color ">
                                        {{ $user->birthday }}
                                    </a>
                                </td>
                                <td class="flex">
                                    @if ($user->status == config('number.user.active'))
                                        <strong class="text-success">
                                            {{ trans('supervisor.detail_user.active') }}
                                        </strong>
                                    @else
                                        <strong class="text-danger">
                                            {{ trans('supervisor.detail_user.inactive') }}
                                        </strong>
                                    @endif
                                </td>
                                <td>
                                    <div class="item-action dropdown">
                                        <a href="{{ route('trainee.show', ['trainee' => $user->id]) }}" data-toggle="dropdown" class="text-muted">
                                            <i class="mx-2" data-feather="more-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right bg-black"
                                             role="menu">
                                            <a class="dropdown-item"
                                                href="{{ route('trainee.show', ['trainee' => $user->id]) }}">
                                                {{ trans('supervisor.list_courses.see') }}
                                            </a>
                                            <button type="submit"
                                                class="border-0 dropdown-item trash"
                                                data-toggle="modal" data-target="#lock{{ $user->id }}">
                                                @if ($user->status == config('number.user.active'))
                                                    {{ trans('supervisor.detail_user.lock_account') }}
                                                @else
                                                    {{ trans('supervisor.detail_user.unlock_account') }}
                                                @endif
                                            </button>
                                            <div class="dropdown-divider"></div>
                                            <button type="submit"
                                                class="border-0 dropdown-item trash"
                                                data-toggle="modal" data-target="#reset{{ $user->id }}">
                                                {{ trans('supervisor.detail_user.reset_password') }}
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <div class="container">
                                    <div class="modal fade" id="lock{{ $user->id }}" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="row">
                                                <div class="col-md-1"></div>
                                                <div class="col-md-10">
                                                    <div class="modal-content box-shadow mb-4">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                {{ $user->fullname }}
                                                            </h5>
                                                            <button class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>
                                                                @if ($user->status == config('number.user.active'))
                                                                    {{ trans('supervisor.detail_user.message_lock') }}
                                                                @else
                                                                    {{ trans('supervisor.detail_user.message_unlock') }}
                                                                @endif
                                                            </p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button class="btn btn-light" data-dismiss="modal">
                                                                {{ trans('both.cancel') }}
                                                            </button>
                                                            <form id="logout-form"
                                                                action="{{ route('trainee.destroy', ['trainee' => $user->id]) }}"
                                                                method="POST">
                                                                @method('DELETE')
                                                                @csrf
                                                                <button type="submit" data-toggle="modal" data-target="#delete"
                                                                    class="btn btn-danger">
                                                                    @if ($user->status == config('number.user.active'))
                                                                        {{ trans('supervisor.detail_user.lock_account') }}
                                                                    @else
                                                                        {{ trans('supervisor.detail_user.unlock_account') }}
                                                                    @endif
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="container">
                                    <div class="modal fade" id="reset{{ $user->id }}" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="row">
                                                <div class="col-md-1"></div>
                                                <div class="col-md-10">
                                                    <div class="modal-content box-shadow mb-4">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                {{ $user->fullname }}
                                                            </h5>
                                                            <button class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>
                                                                {{ trans('supervisor.detail_user.message_reset') }}
                                                            </p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button class="btn btn-light" data-dismiss="modal">
                                                                {{ trans('both.cancel') }}
                                                            </button>
                                                            <form id="logout-form"
                                                                action="{{ route('trainee.update', ['trainee' => $user->id]) }}"
                                                                method="POST">
                                                                @method('PUT')
                                                                @csrf
                                                                <button type="submit" data-toggle="modal" data-target="#delete"
                                                                    class="btn btn-primary">
                                                                    {{ trans('supervisor.detail_user.reset_password') }}
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-sm-12 col-md-5">
                    </div>
                    <div class="col-sm-12 col-md-7">
                        <div class="dataTables_paginate paging_simple_numbers"
                            id="datatable_paginate">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('bower_components/bower_package/typeahead.js/dist/typeahead.bundle.min.js') }}"></script>
    <script src="{{ asset('bower_components/bower_package/js/plugins/typeahead.js') }}"></script>
    <script
        src="{{ asset('bower_components/bower_package/jquery-fullscreen-plugin/jquery.fullscreen-min.js') }}"></script>
    <script src="{{ asset('bower_components/bower_package/js/plugins/fullscreen.js') }}"></script>
    <script src="{{ asset('bower_components/bower_package/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script
        src="{{ asset('bower_components/bower_package/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('bower_components/bower_package/js/plugins/datatable.js') }}"></script>
@endsection
