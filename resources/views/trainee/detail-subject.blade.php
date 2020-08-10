@extends('trainee.layouts.app')
@section('css')
    <script src="{{ asset('bower_components/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('bower_components/ckeditor/samples/js/sample.js') }}"></script>
    <link rel="stylesheet" type="text/css"
        href="{{ asset('bower_components/bower_package/summernote/dist/summernote-bs4.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('bower_components/bower_package/summernote/dist/summernote.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/supervisor_detail_course.css') }}">
    <script src="{{ asset('bower_components/bower_package/jquery/dist/jquery.min.js') }}"></script>
    <link type="text/css" rel="stylesheet" href="{{ asset('css/trainee_detail_subject.css') }}">
@endsection
@section('content')
    <div id="main" class="layout-column flex">
        <div id="content" class="flex ">
            <div>
                <div class="page-hero page-container " id="page-hero">
                    <div class="d-flex justify-content-center">
                        <h1>
                            {{ $subject->title }}&ndash;
                            @if ($subjectUser->status == config('number.inactive'))
                                <span class="text-warning">
                                    {{ trans('trainee.app.inactive') }}
                                </span>
                            @elseif ($subjectUser->status == config('number.active'))
                                <span class="text-info">
                                    {{ trans('trainee.app.active') }}
                                </span>
                            @else
                                <span class="text-success">
                                    {{ trans('trainee.app.passed') }}
                                </span>
                            @endif
                        </h1>
                    </div>
                    @if (isset($messenger))
                        <div class="mr-5 ml-5 mt-5 alert alert-success alert-dismissible fade show" role="alert">
                            <div class="d-flex justify-content-center">
                                <div class="mx-3">
                                    {{ $messenger }}
                                </div>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="padding">
                        <div class="d-flex justify-content-center">
                            <img class="w-50" src="{{ asset(config('image.folder') . $subject->image) }}"
                                alt="{{ trans('trainee.detail_subject.alt_image') }}">
                        </div>
                        <br>
                        <div>
                            {{ $subject->description }}
                        </div>
                        @if ($subjectUser->status == config('number.active'))
                            <div class="d-flex justify-content-center padding">
                                <button type="button" class="btn w-sm mb-1 btn-outline-info"
                                    data-toggle="modal" data-target="#myModal">
                                    {{ trans('trainee.detail_subject.create_task') }}
                                </button>
                            </div>
                        @endif
                        <div id="accordion" class="mb-4">
                            @if ($subjectUser->status != config('number.inactive'))
                                <div class="card mb-1">
                                    <div class="card-header no-border" id="headingOne">
                                        <a href="#" data-toggle="collapse" data-target="#collapseOne"
                                            aria-expanded="false" aria-controls="collapseOne">
                                            {{ trans('trainee.detail_subject.list_tasks') }}
                                        </a>
                                    </div>
                                    <div id="collapseOne" class="collapse" aria-labelledby="headingOne"
                                        data-parent="#accordion">
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush">
                                                @foreach ($tasks as $task)
                                                    <li class="list-group-item list-group-item-action">
                                                        <a href="#" data-toggle="modal" data-target="#task{{ $task->id }}">
                                                            {{ trans('trainee.app.task') }} {{ $task->id }}
                                                        </a>
                                                    </li>
                                                    <div class="container">
                                                        <div class="modal fade" id="task{{ $task->id }}" role="dialog">
                                                            <div class="modal-dialog" role="document"x>
                                                                <div class="modal-content">
                                                                    <div class="modal-header text-center border bg-info">
                                                                        <h4 class="modal-title w-100 color_light">
                                                                            {{ trans('trainee.detail_subject.task') }}
                                                                            {{ $task->id }}
                                                                        </h4>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <form action="{{ route('task.update', ['task' => $task->id]) }}"
                                                                        method="POST">
                                                                        @method('PUT')
                                                                        @csrf
                                                                        <div class="modal-body mx-3">
                                                                            <label class="col-form-label">
                                                                                {{ trans('trainee.detail_subject.created_at') }}:
                                                                                {{ $task->created_at }}-
                                                                                @if ($task->status == config('number.task.new'))
                                                                                    <span class="text-warning">
                                                                                        {{ trans('trainee.detail_subject.new') }}
                                                                                    </span>
                                                                                @elseif ($task->status == config('number.task.passed'))
                                                                                    <span class="text-success">
                                                                                        {{ trans('trainee.detail_subject.passed') }}
                                                                                    </span>
                                                                                @else
                                                                                    <span class="text-danger">
                                                                                        {{ trans('trainee.detail_subject.failed') }}
                                                                                    </span>
                                                                                @endif
                                                                            </label>
                                                                            @if ($task->status != config('number.task.new'))
                                                                                <div class="form-group row">
                                                                                    <label class="col-sm-12 col-form-label">
                                                                                        {{ trans('trainee.detail_subject.review') }}
                                                                                    </label>
                                                                                    <div class="col-sm-12">
                                                                                        <textarea class="form-control"
                                                                                            rows="2" name="review">{{ $task->review }}
                                                                                        </textarea>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                            <div class="form-group row">
                                                                                <label class="col-sm-12 col-form-label">
                                                                                    {{ trans('trainee.detail_subject.plan') }}
                                                                                </label>
                                                                                <div class="col-sm-12">
                                                                                    <textarea class="form-control"
                                                                                        rows="3" name="plan">{{ $task->plan }}
                                                                                    </textarea>
                                                                                    @error ('plan')
                                                                                        <div class="alert alert-danger">
                                                                                            {{ $message }}
                                                                                        </div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <label class="col-sm-12 col-form-label">
                                                                                    {{ trans('trainee.detail_subject.actual') }}
                                                                                </label>
                                                                                <div class="col-sm-12">
                                                                                    <textarea class="form-control"
                                                                                        rows="3" name="actual">{{ $task->actual }}
                                                                                    </textarea>
                                                                                    @error ('actual')
                                                                                        <div class="alert alert-danger">
                                                                                            {{ $message }}
                                                                                        </div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <label class="col-sm-12 col-form-label">
                                                                                    {{ trans('trainee.detail_subject.next_plan') }}
                                                                                </label>
                                                                                <div class="col-sm-12">
                                                                                    <textarea class="form-control"
                                                                                        rows="3" name="next_plan">{{ $task->next_plan }}
                                                                                    </textarea>
                                                                                    @error ('next_plan')
                                                                                        <div class="alert alert-danger">
                                                                                            {{ $message }}
                                                                                        </div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <label class="col-sm-12 col-form-label">
                                                                                    {{ trans('trainee.detail_subject.comment') }}
                                                                                </label>
                                                                                <div class="col-sm-12">
                                                                                    <textarea class="form-control"
                                                                                        rows="2" name="comment">{{ $task->comment }}
                                                                                    </textarea>
                                                                                    @error ('comment')
                                                                                        <div class="alert alert-danger">
                                                                                            {{ $message }}
                                                                                        </div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        @if ($task->status == config('number.task.new'))
                                                                            <div class="modal-footer d-flex justify-content-center">
                                                                                <button class="btn btn-info" type="submit">
                                                                                    {{ trans('trainee.detail_subject.update') }}
                                                                                </button>
                                                                            </div>
                                                                        @endif
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="card mb-1">
                                <div class="card-header no-border" id="headingTwo">
                                    <a href="#" data-toggle="collapse" data-target="#collapseTwo"
                                        aria-expanded="false" aria-controls="collapseTwo">
                                        {{ trans('trainee.detail_course.list_trainees') }}
                                    </a>
                                </div>
                                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                    data-parent="#accordion">
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush">
                                            @foreach ($subject->users as $user)
                                                <li class="list-group-item list-group-item-action">
                                                    <a href="{{ route('trainee.show', ['trainee' => $user->id]) }}">
                                                        {{ $user->fullname }}
                                                    </a>
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
    </div>
    <div class="container">
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header text-center border bg-info">
                        <h4 class="modal-title w-100 color_light">
                            {{ trans('trainee.detail_subject.task') }}
                        </h4>
                        <button type="button" class="close"
                            data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('task.store') }}" method="POST">
                        @csrf
                        <div class="modal-body mx-3">
                            <div class="form-group row">
                                <label class="col-sm-12 col-form-label">
                                    {{ trans('trainee.detail_subject.plan') }}
                                </label>
                                <div class="col-sm-12">
                                    <textarea class="form-control" rows="3"
                                        placeholder="{{ trans('trainee.detail_subject.pla_plan') }}"
                                        name="plan" required>{{ old('plan') }}</textarea>
                                    @error ('plan')
                                        <div class="alert alert-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-12 col-form-label">
                                    {{ trans('trainee.detail_subject.actual') }}
                                </label>
                                <div class="col-sm-12">
                                    <textarea class="form-control" rows="3"
                                        placeholder="{{ trans('trainee.detail_subject.pla_actual') }}"
                                        name="actual" required>{{ old('actual') }}</textarea>
                                    @error ('actual')
                                        <div class="alert alert-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-12 col-form-label">
                                    {{ trans('trainee.detail_subject.next_plan') }}
                                </label>
                                <div class="col-sm-12">
                                    <textarea class="form-control" rows="3"
                                        placeholder="{{ trans('trainee.detail_subject.pla_next_plan') }}"
                                        name="next_plan" required>{{ old('next_plan') }}</textarea>
                                    @error ('next_plan')
                                        <div class="alert alert-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-12 col-form-label">
                                    {{ trans('trainee.detail_subject.comment') }}
                                </label>
                                <div class="col-sm-12">
                                    <textarea class="form-control" rows="2"
                                        placeholder="{{ trans('trainee.detail_subject.pla_comment') }}"
                                        name="comment" required>{{ old('comment') }}</textarea>
                                    @error ('comment')
                                        <div class="alert alert-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <input type="hidden" value="{{ $subject->id }}" name="subject_id">
                        <div class="modal-footer d-flex justify-content-center">
                            <button class="btn btn-info" type="submit">
                                {{ trans('trainee.detail_subject.submit') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('bower_components/bower_package/typeahead.js/dist/typeahead.bundle.min.js') }}">
    </script>
    <script src="{{ asset('bower_components/bower_package/js/plugins/typeahead.js') }}"></script>
    <script src="{{ asset('bower_components/bower_package/jquery-fullscreen-plugin/jquery.fullscreen-min.js') }}">
    </script>
    <script src="{{ asset('bower_components/bower_package/js/plugins/fullscreen.js') }}"></script>
    <script src="{{ asset('bower_components/bower_package/summernote/dist/summernote.min.js') }}"></script>
    <script src="{{ asset('bower_components/bower_package/js/summernote/dist/summernote-bs4.min.js') }}">
    </script>
@endsection
