@extends('admin.layouts.main')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $mailbox->name }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.main') }}">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{ route('admin.mailboxes.index') }}">Users</a></li>
                            <li class="breadcrumb-item active"><a href="{{ route('admin.mailboxes.show', $mailbox->id) }}">{{ $mailbox->email }}</a></li>
                            <li class="breadcrumb-item active">Edit Mailbox</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edit Mailbox</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form method="post" action="{{ route('admin.mailboxes.update', $mailbox->id) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="avatar">Avatar</label>
                                <div class="border my-1 p-1">
                                    <img src="{{ asset("/storage/{$mailbox->avatar}") }}"
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
                                       placeholder="Enter Name" name="name" value="{{$mailbox->name}}">
                                @error('name')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email"
                                       placeholder="Enter Email" name="email" value="{{$mailbox->email}}">
                                @error('email')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="text" class="form-control" id="password"
                                       placeholder="Enter Password" name="password" value="{{$mailbox->password}}">
                                @error('password')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="app_password">App Password</label>
                                <input type="text" class="form-control" id="app_password"
                                       placeholder="Enter Password" name="app_password" value="{{$mailbox->app_password}}">
                                @error('app_password')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" id="phone" placeholder="Enter Phone"
                                       name="phone" value="{{$mailbox->phone}}">
                                @error('phone')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Email Provider</label>
                                <select class="form-control" name="email_provider_id" style="width: 100%;">
                                    <option selected="selected" value="{{ $mailbox->email_provider->id }}">{{ $mailbox->email_provider->title }}</option>
                                    @foreach($email_providers as $email_provider)
                                        @if($email_provider->id != $mailbox->email_provider->id)
                                            <option value="{{ $email_provider->id }}">{{ $email_provider->title }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('role')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="domain">Domain</label>
                                <input type="text" class="form-control" id="domain"
                                       placeholder="Enter Domain" name="domain" value="{{ $mailbox->domain }}">
                                @error('domain')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="create_date">Create Date</label>
                                <div class="input-group date" id="create_date" data-target-input="nearest">
                                    <input type="text" name="create_date" class="form-control datetimepicker-input"
                                           data-target="#create_date" placeholder="Enter Start Date" value="{{ $mailbox->create_date }}">
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
                                    <input type="checkbox" class="custom-control-input" name="for_linkedin" id="for_linkedin"
                                           {{ $mailbox->for_linkedin ? "checked" : "" }}>
                                    <label class="custom-control-label" for="for_linkedin">For linkedin</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="mailbox_id" value="{{$mailbox->id}}">
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
