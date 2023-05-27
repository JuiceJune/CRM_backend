@extends('admin.layouts.main')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Mailboxes</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Mailboxes</li>
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
                        <a href="{{ route("admin.mailboxes.create") }}" class="btn btn-secondary">Add Mailbox</a>
                    </div>
                    <div class="card-body" id="datatable-wrapper">
                        <table id="datatable" class="datatable1 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Avatar</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th>App Password</th>
                                <th>Phone</th>
                                <th>Domain</th>
                                <th>Email Provider</th>
                                <th>Create Date</th>
                                <th>For Linkedin</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($mailboxes as $mailbox)
                                <tr>
                                    <td class="align-middle">
                                        <a href="{{route('admin.mailboxes.show', $mailbox->id)}}">
                                            {{ $mailbox->id }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.mailboxes.show', $mailbox->id)}}">
                                            <img src="{{ asset("/storage/{$mailbox->avatar}") }}"
                                                 class="rounded-circle" style="width: 40px;"
                                                 alt="Avatar" />
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.mailboxes.show', $mailbox->id)}}">
                                            {{ $mailbox->name }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.mailboxes.show', $mailbox->id)}}">
                                            {{ $mailbox->email }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.mailboxes.show', $mailbox->id)}}">
                                            {{ $mailbox->password }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.mailboxes.show', $mailbox->id)}}">
                                            {{ $mailbox->app_password }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.mailboxes.show', $mailbox->id)}}">
                                            {{ $mailbox->phone ?: "---" }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.mailboxes.show', $mailbox->id)}}">
                                            {{ $mailbox->domain }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.mailboxes.show', $mailbox->id)}}">
                                            {{ $mailbox->email_provider->title }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.mailboxes.show', $mailbox->id)}}">
                                            {{ $mailbox->create_date }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.mailboxes.show', $mailbox->id)}}">
                                            {{ $mailbox->for_linkedin ? "Yes" : "No" }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <form class="mx-1" method="post" action="{{ route('admin.mailboxes.edit', $mailbox->id) }}">
                                            @method('GET')
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary">
                                                <i class="nav-icon fas fa-edit"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="align-middle">
                                        <form class="mx-1" method="post" action="{{ route('admin.mailboxes.destroy', $mailbox->id) }}">
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
                                <th>Email</th>
                                <th>Password</th>
                                <th>App Password</th>
                                <th>Phone</th>
                                <th>Domain</th>
                                <th>Email Provider</th>
                                <th>Create Date</th>
                                <th>For Linkedin</th>
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
