@extends('admin.layouts.main')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Clients</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Clients</li>
                        </ol>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-12">
                        @include('admin.includes.alert')
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <a href="{{ route("admin.clients.create") }}" class="btn btn-secondary">Add Client</a>
                    </div>
                    <div class="card-body" id="datatable-wrapper">
                        <table id="datatable" class="datatable1 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Logo</th>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Start Date</th>
                                <th>Location</th>
                                <th>Industry</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($clients as $client)
                                <tr>
                                    <td class="align-middle">
                                        <a href="{{route('admin.clients.show', $client->id)}}">
                                            {{ $client->id }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.clients.show', $client->id)}}">
                                            <img src="{{ asset("/storage/{$client->avatar}") }}"
                                                 class="rounded-circle" style="width: 40px;"
                                                 alt="Logo" />
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.clients.show', $client->id)}}">
                                            {{ $client->email }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.clients.show', $client->id)}}">
                                            {{ $client->name }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.clients.show', $client->id)}}">
                                            {{ $client->email }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.clients.show', $client->id)}}">
                                            {{ $client->start_date }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.clients.show', $client->id)}}">
                                            {{ $client->location }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.clients.show', $client->id)}}">
                                            {{ $client->industry }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <form class="mx-1" method="post" action="{{ route('admin.clients.edit', $client->id) }}">
                                            @method('GET')
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary">
                                                <i class="nav-icon fas fa-edit"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="align-middle">
                                        <form class="mx-1" method="post" action="{{ route('admin.clients.destroy', $client->id) }}">
                                            @method('DELETE')
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="nav-icon fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Id</th>
                                <th>Logo</th>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Start Date</th>
                                <th>Location</th>
                                <th>Industry</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
