@extends('supervisor.layouts.app')
@section('css')
    <script src="{{ asset('bower_components/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('bower_components/ckeditor/samples/js/sample.js') }}">
    </script>
@endsection
@section('content')
    <div class="container">
        <div class="mt-5 row d-flex justify-content-center">
            <div class="card col-md-8">
                <div class="card-header d-flex justify-content-center">
                    <h3>
                        {{ trans('both.change_password') }}
                    </h3>
                </div>
                <div class="card-body">
                    @if (session('messenger'))
                        <div class="alert alert-danger">
                            {{ session('messenger') }}
                        </div>
                    @endif
                    <form action="{{ route('user.update.password') }}" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="form-group">
                            <label class="text-muted">
                                {{ trans('both.password') }}
                            </label>
                            <input type="password" class="form-control"
                                name="password" required>
                        </div>
                        <div class="form-group">
                            <label class="text-muted">
                                {{ trans('both.new_password') }}
                            </label>
                            <input type="password" class="form-control"
                                name="newPassword" required>
                        </div>
                        <div class="form-group">
                            <label class="text-muted">
                                {{ trans('both.re_password') }}
                            </label>
                            <input type="password" class="form-control"
                                name="rePassword" required>
                        </div>
                        <div class="d-flex justify-content-center mt-5 row">
                            <div class="col-3"></div>
                            <div class="col-3 d-flex justify-content-center">
                                <a href="{{ url()->previous() }}"
                                    class="btn red">
                                    {{ trans('both.cancel') }}
                                </a>
                            </div>
                            <div class="col-3 d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary">
                                    {{ trans('both.submit') }}
                                </button>
                            </div>
                            <div class="col-3"></div>
                        </div>
                    </form>
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
    <script src="{{ asset('js/createSubject.js') }}"></script>
@endsection
