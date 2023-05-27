@extends('admin.layouts.main')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $email_provider->title }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.main') }}">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{ route('admin.email-providers.index') }}">Email Providers</a></li>
                            <li class="breadcrumb-item active"><a href="{{ route('admin.email-providers.show', $email_provider->id) }}">{{ $email_provider->title }}</a></li>
                            <li class="breadcrumb-item active">Edit Email Providers</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edit Email Providers</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form method="post" action="{{ route('admin.email-providers.update', $email_provider->id) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="logo">Logo</label>
                                <div class="border my-1 p-1">
                                    <img src="{{ asset("/storage/{$email_provider->logo}") }}"
                                         class="rounded-circle" style="width: 100px;"
                                         alt="Logo" />
                                </div>
                                <input class="form-control" type="file" id="logo" name="logo">
                                @error('logo')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" id="title"
                                       placeholder="Enter Role" name="title" value="{{$email_provider->title}}">
                                @error('title')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="email_provider_id" value="{{$email_provider->id}}">
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ URL::previous() }}" class="btn btn-danger">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
