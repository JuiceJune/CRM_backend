@extends('admin.layouts.main')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Linkedin Accounts</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Linkedin Accounts</li>
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
                        <a href="{{ route("admin.linkedin-accounts.create") }}" class="btn btn-secondary">Add Linkedin</a>
                    </div>
                    <div class="card-body" id="datatable-wrapper">
                        <table id="datatable" class="datatable1 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Avatar</th>
                                <th>Name</th>
                                <th>Link</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th>Create Date</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($linkedin_accounts as $linkedin)
                                <tr>
                                    <td class="align-middle">
                                        <a href="{{route('admin.linkedin-accounts.show', $linkedin->id)}}">
                                            {{ $linkedin->id }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.linkedin-accounts.show', $linkedin->id)}}">
                                            <img src="{{ asset("/storage/{$linkedin->avatar}") }}"
                                                 class="rounded-circle" style="width: 40px;"
                                                 alt="Avatar" />
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.linkedin-accounts.show', $linkedin->id)}}">
                                            {{ $linkedin->name }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{ $linkedin->link }}">
                                            {{ $linkedin->link }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.mailboxes.show', $linkedin->mailbox->id)}}">
                                            {{ $linkedin->mailbox->email }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.linkedin-accounts.show', $linkedin->id)}}">
                                            {{ $linkedin->password }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.linkedin-accounts.show', $linkedin->id)}}">
                                            {{ $linkedin->create_date }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <form class="mx-1" method="post" action="{{ route('admin.linkedin-accounts.edit', $linkedin->id) }}">
                                            @method('GET')
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary">
                                                <i class="nav-icon fas fa-edit"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="align-middle">
                                        <form class="mx-1" method="post" action="{{ route('admin.linkedin-accounts.destroy', $linkedin->id) }}">
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
                                <th>Avatar</th>
                                <th>Name</th>
                                <th>Link</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th>Create Date</th>
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
