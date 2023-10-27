@extends('admin.layouts.main')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Campaigns</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Campaigns</li>
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
                        <a href="{{ route("admin.campaigns.create") }}" class="btn btn-secondary">Add Campaign</a>
                    </div>
                    <div class="card-body" id="datatable-wrapper">
                        <table id="datatable" class="datatable1 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Project</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($campaigns as $campaign)
                                <tr>
                                    <td class="align-middle">
                                        <a href="{{route('admin.campaigns.show', $campaign->id)}}">
                                            {{ $campaign->name }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.mailboxes.show', $campaign->mailbox->id)}}">
                                            {{ $campaign->mailbox->email }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.projects.show', $campaign->project->id)}}">
                                            {{ $campaign->project->name }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <form class="mx-1" method="post"
                                              action="{{ route('admin.campaigns.edit', $campaign->id) }}">
                                            @method('GET')
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary">
                                                <i class="nav-icon fas fa-edit"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="align-middle">
                                        <form class="mx-1" method="post"
                                              action="{{ route('admin.campaigns.destroy', $campaign->id) }}">
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
                                <th>Name</th>
                                <th>Email</th>
                                <th>Project</th>
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
