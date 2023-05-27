@extends('admin.layouts.main')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $mailbox->email }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.main') }}">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{ route('admin.mailboxes.index') }}">Mailboxes</a></li>
                            <li class="breadcrumb-item active">{{ $mailbox->email }}</li>
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
                            <a href="{{route("admin.mailboxes.edit", $mailbox->id)}}" class="btn btn-warning py-1">Edit</a>
                            <form class="mx-1" method="post" action="{{ route('admin.mailboxes.destroy', $mailbox->id) }}">
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
                                    <img src="{{ asset("/storage/{$mailbox->avatar}") }}"
                                         class="rounded-circle" style="width: 40px;"
                                         alt="Avatar" />
                                </td>
                            </tr>
                            <tr>
                                <td>2.</td>
                                <td>Name</td>
                                <td>{{ $mailbox->name }}</td>
                            </tr>
                            <tr>
                                <td>3.</td>
                                <td>Email</td>
                                <td>{{ $mailbox->email }}</td>
                            </tr>
                            <tr>
                                <td>4.</td>
                                <td>Password</td>
                                <td>{{ $mailbox->password }}</td>
                            </tr>
                            <tr>
                                <td>5.</td>
                                <td>App Password</td>
                                <td>{{ $mailbox->app_password }}</td>
                            </tr>
                            <tr>
                                <td>6.</td>
                                <td>Email Provider</td>
                                <td>{{ $mailbox->email_provider->title }}</td>
                            </tr>
                            <tr>
                                <td>7.</td>
                                <td>Domain</td>
                                <td>{{ $mailbox->domain }}</td>
                            </tr>
                            <tr>
                                <td>8.</td>
                                <td>Create Date</td>
                                <td>{{ $mailbox->create_date }}</td>
                            </tr>
                            <tr>
                                <td>9.</td>
                                <td>For Linkedin</td>
                                <td>{{ $mailbox->for_linkedin ? "Yes" : "No"}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
