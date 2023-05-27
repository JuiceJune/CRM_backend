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
                            <li class="breadcrumb-item active">{{ $linkedin->name }}</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="card pb-2">
                    <div class="d-flex justify-content-between mt-4 mx-4">
                        <a href="{{ url()->previous() }}" class="btn btn-primary py-1">Back</a>
                        <div class="d-flex">
                            <a href="{{route("admin.linkedin-accounts.edit", $linkedin->id)}}" class="btn btn-warning py-1">Edit</a>
                            <form class="mx-1" method="post" action="{{ route('admin.linkedin-accounts.destroy', $linkedin->id) }}">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn btn-danger py-1">Delete</button>
                            </form>
                        </div>
                    </div>
                    <hr>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <div class="card-body p-0">
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Field</th>
                                <th>Name</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="align-middle">1.</td>
                                <td class="align-middle">Avatar</td>
                                <td class="align-middle">
                                    <img src="{{ asset("/storage/{$linkedin->avatar}") }}"
                                         class="rounded-circle" style="width: 40px;"
                                         alt="Avatar" />
                                </td>
                            </tr>
                            <tr>
                                <td>2.</td>
                                <td>Name</td>
                                <td>{{ $linkedin->name }}</td>
                            </tr>
                            <tr>
                                <td>3.</td>
                                <td>Email</td>
                                <td>{{ $linkedin->mailbox->email }}</td>
                            </tr>
                            <tr>
                                <td>4.</td>
                                <td>Password</td>
                                <td>{{ $linkedin->password }}</td>
                            </tr>
                            <tr>
                                <td>5.</td>
                                <td>Link</td>
                                <td><a href="{{ $linkedin->link }}">{{ $linkedin->link }}</a></td>
                            </tr>
                            <tr>
                                <td>6.</td>
                                <td>Create Date</td>
                                <td>{{ $linkedin->create_date }}</a></td>
                            </tr>
                            <tr>
                                <td>7.</td>
                                <td>Warmup</td>
                                <td>{{ $linkedin->warmup ? "Yes" : "No"}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
