@extends('admin.layouts.main')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Positions</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Positions</li>
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
                        <a href="{{ route("admin.positions.create") }}" class="btn btn-secondary">Add Position</a>
                    </div>
                    <div class="card-body" id="datatable-wrapper">
                        <table id="datatable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Position</th>
                                <th>Created</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($positions as $position)
                                <tr>
                                    <td class="align-middle">
                                        <a href="{{route('admin.positions.show', $position->id)}}">
                                            {{ $position->id }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.positions.show', $position->id)}}">
                                            {{ $position->title }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.positions.show', $position->id)}}">
                                            {{ $position->created_at }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <form class="mx-1" method="post" action="{{ route('admin.positions.edit', $position->id) }}">
                                            @method('GET')
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary">
                                                <i class="nav-icon fas fa-edit"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="align-middle">
                                        <form class="mx-1" method="post" action="{{ route('admin.positions.destroy', $position->id) }}">
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
                                <th>Position</th>
                                <th>Created</th>
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
