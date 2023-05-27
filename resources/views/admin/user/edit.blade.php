@extends('admin.layouts.main')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $user->name }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.main') }}">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{ route('admin.users.index') }}">Users</a></li>
                            <li class="breadcrumb-item active"><a href="{{ route('admin.users.show', $user->id) }}">{{ $user->name }}</a></li>
                            <li class="breadcrumb-item active">Edit User</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edit User</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form method="post" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="avatar">Avatar</label>
                                <div class="border my-1 p-1">
                                    <img src="{{ asset("/storage/{$user->avatar}") }}"
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
                                       placeholder="Enter Name" name="name" value="{{$user->name}}">
                                @error('name')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email"
                                       placeholder="Enter Email" name="email" value="{{$user->email}}">
                                @error('email')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password"
                                       placeholder="Enter Password" name="password" value="{{$user->password}}">
                                @error('password')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <select class="form-control" name="role_id" style="width: 100%;">
                                    <option selected="selected" value="{{ $user->role->id }}">{{ $user->role->title }}</option>
                                    @foreach($roles as $role)
                                        @if($role->id != $user->role->id)
                                            <option value="{{ $role->id }}">{{ $role->title }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('role')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Position</label>
                                <select class="form-control" name="position_id" style="width: 100%;">
                                    <option selected="selected" value="{{ $user->position->id }}">{{ $user->position->title }}</option>
                                    @foreach($positions as $position)
                                        @if($position->id != $user->position->id)
                                            <option value="{{ $position->id }}">{{ $position->title }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('position_id')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <div class="input-group date" id="start_date" data-target-input="nearest">
                                    <input type="text" name="start_date" class="form-control datetimepicker-input"
                                           data-target="#start_date" placeholder="Enter Start Date" value="{{ $user->start_date }}">
                                    <div class="input-group-append" data-target="#start_date" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                @error('start_date')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="birthday">Birthday</label>
                                <div class="input-group date" id="birthday" data-target-input="nearest">
                                    <input type="text" name="birthday" class="form-control datetimepicker-input"
                                           data-target="#birthday" placeholder="Enter Birthday" value="{{ $user->birthday }}">
                                    <div class="input-group-append" data-target="#birthday" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                @error('birthday')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="location">Location</label>
                                <input type="text" class="form-control" id="location"
                                       placeholder="Enter Location" name="location" value="{{ $user->location }}">
                                @error('location')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <input type="hidden" name="user_id" value="{{$user->id}}">
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
