@extends('supervisor.layouts.app')
@section('css')
    <script src="{{ asset('bower_components/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('bower_components/ckeditor/samples/js/sample.js') }}"></script>
    <script src="{{ asset('js/message.js') }}"></script>
    <link rel="stylesheet" type="text/css"
        href="{{ asset('bower_components/bower_package/summernote/dist/summernote-bs4.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('bower_components/bower_package/summernote/dist/summernote.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/supervisor_detail_course.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/message.css') }}">
@endsection
@section('content')
    @if (session('messenger'))
        <div id="messenger" class="alert alert-success" role="alert">
            <i data-feather="check"></i>
            <span class="mx-2">{{ session('messenger') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div id="messenger" class="alert alert-danger" role="alert">
            <i data-feather="x"></i>
            <span class="mx-2">{{ session('error') }}</span>
        </div>
    @endif
    <div id="main" class="layout-column flex">
        <div id="content" class="flex ">
            <div>
                <div class="page-hero page-container " id="page-hero">
                    <div class="padding">
                        <div class="page-title">
                            <div class="float-left">
                                <p>
                                    <a href="{{ route('course.show', ['course' => $subject->course_id]) }}">
                                        {{ trans('trainee.app.course') . ' '
                                            . $subject->course_id . ': ' .  $subject->course->title }}
                                    </a>
                                </p>
                            </div>
                            <div class="float-right">
                                <h3 class="text-success">
                                    {{ trans('supervisor.list_subjects.time')
                                        . ' : ' . $subject->time . ' ' . trans('supervisor.app.days') }}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-5">
                        <h1>
                            {{ $subject->title }}
                        </h1>
                    </div>
                    <div class="padding">
                        <div>
                            {!! $subject->description !!}
                        </div>
                    </div>
                    <div id="accordion" class="mb-4 padding">
                        <div class="card mb-1">
                            <div class="card-header no-border" id="headingOne">
                                <h4>
                                    <span>
                                        <a href="#" data-toggle="collapse" data-target="#collapseOne"
                                            aria-expanded="false" aria-controls="collapseOne">
                                            {{ trans('supervisor.detail_subject.list_trainees') }}
                                        </a>
                                    </span>
                                    <span class="badge badge-success float-right">
                                        {{ count($subject->usersActive) }}
                                    </span>
                                </h4>
                            </div>
                            <div id="collapseOne" class="collapse" aria-labelledby="headingOne"
                                data-parent="#accordion">
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        @foreach ($subject->usersActive as $user)
                                            <li class="list-group-item">
                                                <a href="{{ route('trainee.show', ['trainee' => $user->id]) }}" class="link">
                                                    <span class="nav-text">
                                                        {{ $user->fullname }}
                                                    </span>
                                                    @if ($user->time > $subject->time)
                                                        <span class="text-danger">
                                                            ( {{ trans('supervisor.detail_subject.workdays') . ' : ' . $user->time }} )
                                                        </span>
                                                    @else
                                                        <span class="text-warning">
                                                            ( {{ trans('supervisor.detail_subject.workdays') . ' : ' . $user->time }} )
                                                        </span>
                                                    @endif
                                                </a>
                                                <span class="float-right">
                                                    <button type="submit" class="btn btn-primary w-sm"
                                                        data-toggle="modal" data-target="#passUser{{ $user->id }}">
                                                        {{ trans('supervisor.detail_task.pass') }}
                                                    </button>
                                                </span>
                                                <div class="container">
                                                    <div class="modal fade" id="passUser{{ $user->id }}" role="dialog">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="row">
                                                                <div class="col-md-3"></div>
                                                                <div class="col-md-6">
                                                                    <div class="modal-content box-shadow mb-4">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">
                                                                                {{ trans('supervisor.app.message') }} :
                                                                                {{ $user->fullname }}
                                                                            </h5>
                                                                            <button class="close" data-dismiss="modal">&times;</button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <p>
                                                                                {{ trans('supervisor.detail_subject.message_pass') }}
                                                                            </p>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button class="btn btn-light" data-dismiss="modal">
                                                                                {{ trans('both.cancel') }}
                                                                            </button>
                                                                            <form action="{{ route('trainee.subject.pass',
                                                                                ['trainee' => $user->id, 'subject' => $subject->id]) }}"
                                                                                method="post">
                                                                                @method('PUT')
                                                                                @csrf
                                                                                <button type="submit" class="btn btn-primary w-sm">
                                                                                    {{ trans('supervisor.detail_task.pass') }}
                                                                                </button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-header no-border" id="headingTwo">
                                <h4>
                                    <span>
                                        <a href="#" data-toggle="collapse" data-target="#collapseTwo"
                                            aria-expanded="false" aria-controls="collapseTwo">
                                            {{ trans('supervisor.detail_subject.list_tasks') }}
                                        </a>
                                    </span>
                                    <span class="badge badge-success float-right">
                                        {{ count($tasks) }}
                                    </span>
                                </h4>
                            </div>
                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                data-parent="#accordion">
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        @foreach ($tasks as $task)
                                            <li class="list-group-item">
                                                <a href="{{ route('task.show', ['task' => $task->id]) }}" class="link">
                                                    <span class="nav-text">
                                                        {{ $task->created_at }} -
                                                        {{ $task->user->fullname }}
                                                    </span>
                                                </a>
                                                @if ($task->status == config('number.task.new'))
                                                    <span class="badge badge-warning float-right">
                                                        {{ trans('supervisor.detail_task.new') }}
                                                    </span>
                                                @elseif ($task->status == config('number.task.passed'))
                                                    <span class="badge badge-success float-right">
                                                        {{ trans('supervisor.detail_task.passed') }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger float-right">
                                                        {{ trans('supervisor.detail_task.failed') }}
                                                    </span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-3"></div>
            <div class="col-2 d-flex justify-content-center">
                <a href="{{ url()->previous() }}"
                    class="btn w-sm mb-1 btn-info">
                    {{ trans('both.back') }}
                </a>
            </div>
            <div class="col-2 d-flex justify-content-center">
                <button type="submit" data-toggle="modal" data-target="#delete"
                    class="btn w-sm mb-1 red">
                    {{ trans('both.delete') }}
                </button>
            </div>
            <div class="col-2 d-flex justify-content-center">
                <a href="{{ route('subject.edit', ['subject' => $subject->id]) }}"
                    class="btn w-sm mb-1 btn-primary">
                    {{ trans('both.update') }}
                </a>
            </div>
            <div class="col-3"></div>
        </div>
        <div class="container">
            <div class="modal fade" id="delete" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-6">
                            <div class="modal-content box-shadow mb-4">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        {{ trans('supervisor.app.message') }}
                                    </h5>
                                    <button class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p>
                                        {{ trans('supervisor.detail_subject.message_delete') }}
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-light" data-dismiss="modal">
                                        {{ trans('both.cancel') }}
                                    </button>
                                    <form id="logout-form"
                                        action="{{ route('subject.destroy', ['subject' => $subject->id]) }}"
                                        method="POST">
                                        @method('DELETE')
                                        @csrf
                                        <button type="submit" data-toggle="modal" data-target="#delete"
                                            class="btn w-sm mb-1 red">
                                            {{ trans('both.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('bower_components/bower_package/typeahead.js/dist/typeahead.bundle.min.js') }}"></script>
    <script src="{{ asset('bower_components/bower_package/js/plugins/typeahead.js') }}"></script>
    <script src="{{ asset('bower_components/bower_package/jquery-fullscreen-plugin/jquery.fullscreen-min.js') }}"></script>
    <script src="{{ asset('bower_components/bower_package/js/plugins/fullscreen.js') }}"></script>
    <script src="{{ asset('bower_components/bower_package/summernote/dist/summernote.min.js') }}"></script>
    <script src="{{ asset('bower_components/bower_package/js/summernote/dist/summernote-bs4.min.js') }}"></script>
@endsection
