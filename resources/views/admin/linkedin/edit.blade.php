@extends('admin.layouts.main')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $linkedin->name }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.main') }}">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{ route('admin.linkedin-accounts.index') }}">Linkedin Accounts</a></li>
                            <li class="breadcrumb-item active"><a href="{{ route('admin.linkedin-accounts.show', $linkedin->id) }}">{{ $linkedin->email }}</a></li>
                            <li class="breadcrumb-item active">Edit Linkedin</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edit Linkedin</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form method="post" action="{{ route('admin.linkedin-accounts.update', $linkedin->id) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="avatar">Avatar</label>
                                <div class="border my-1 p-1">
                                    <img src="{{ asset("/storage/{$linkedin->avatar}") }}"
                                         class="rounded-circle" style="width: 100px;"
                                         alt="Avatar" />
                                </div>
                                <input class="form-control" type="file" id="avatar" name="avatar">
                                @error('avatar')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name"
                                       placeholder="Enter Name" name="name" value="{{$linkedin->name}}">
                                @error('name')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="text" class="form-control" id="password"
                                       placeholder="Enter Password" name="password" value="{{$linkedin->password}}">
                                @error('password')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="link">Link</label>
                                <input type="text" class="form-control" id="link"
                                       placeholder="Enter Link" name="link" value="{{$linkedin->link}}">
                                @error('link')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Mailbox</label>
                                <select class="form-control" name="mailbox_id" style="width: 100%;">
                                    <option selected="selected" value="{{ $linkedin->mailbox->id }}">{{ $linkedin->mailbox->email }}</option>
                                    @foreach($mailboxes as $mailbox)
                                        @if($mailbox->id != $linkedin->mailbox->id)
                                            <option value="{{ $mailbox->id }}">{{ $mailbox->email }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('mailbox_id')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="create_date">Create Date</label>
                                <div class="input-group date" id="create_date" data-target-input="nearest">
                                    <input type="text" name="create_date" class="form-control datetimepicker-input"
                                           data-target="#create_date" placeholder="Enter Start Date" value="{{ $linkedin->create_date }}">
                                    <div class="input-group-append" data-target="#create_date" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                @error('create_date')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="warmup" id="warmup"
                                           {{ $linkedin->warmup ? "checked" : "" }}>
                                    <label class="custom-control-label" for="warmup">Warmup</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="linkedin_id" value="{{$linkedin->id}}">
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
