@extends('admin.layouts.main')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $client->name }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.main') }}">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
                            <li class="breadcrumb-item active">{{ $client->name }}</li>
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
                            <a href="{{route("admin.clients.edit", $client->id)}}" class="btn btn-warning py-1">Edit</a>
                            <form class="mx-1" method="post" action="{{ route('admin.clients.destroy', $client->id) }}">
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
                                    <img src="{{ asset("/storage/{$client->logo}") }}"
                                         class="rounded-circle" style="width: 40px;"
                                         alt="Logo" />
                                </td>
                            </tr>
                            <tr>
                                <td>2.</td>
                                <td>Company</td>
                                <td>{{ $client->company }}</td>
                            </tr>
                            <tr>
                                <td>3.</td>
                                <td>Name</td>
                                <td>{{ $client->name }}</td>
                            </tr>
                            <tr>
                                <td>4.</td>
                                <td>Email</td>
                                <td>{{ $client->email }}</td>
                            </tr>
                            <tr>
                                <td>5.</td>
                                <td>Start date</td>
                                <td>{{ $client->start_date }}</td>
                            </tr>
                            <tr>
                                <td>6.</td>
                                <td>Location</td>
                                <td>{{ $client->location }}</td>
                            </tr>
                            <tr>
                                <td>7.</td>
                                <td>Industry</td>
                                <td>{{ $client->industry }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
