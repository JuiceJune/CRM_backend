@extends('admin.layouts.main')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Projects</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Projects</li>
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
                        <a href="{{ route("admin.projects.create") }}" class="btn btn-secondary">Add Project</a>
                    </div>
                    <div class="card-body" id="datatable-wrapper">
                        <table id="datatable" class="datatable1 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Logo</th>
                                <th>Name</th>
                                <th>Client</th>
                                <th>CSM</th>
                                <th>Research Manager</th>
                                <th>SDRs</th>
                                <th>IT Specialist</th>
                                <th>Mailboxes</th>
                                <th>Linkedin Accounts</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($projects as $project)
                                <tr>
                                    <td class="align-middle">
                                        <a href="{{route('admin.projects.show', $project->id)}}">
                                            <img src="{{ asset("/storage/{$project->logo}") }}"
                                                 class="rounded-circle" style="width: 40px;"
                                                 alt="Logo"/>
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.projects.show', $project->id)}}">
                                            {{ $project->name }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('admin.clients.show', $project->client->id)}}">
                                            {{ $project->client->name }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        @if(count($project->usersWithPosition("CSM")))
                                            @foreach($project->usersWithPosition("CSM") as $csm)
                                                <a href="{{route('admin.users.show', $csm->id)}}">
                                                    | {{ $csm->name }}
                                                </a>
                                            @endforeach
                                        @else
                                            not specified
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if(count($project->usersWithPosition("Research Manager")))
                                            @foreach($project->usersWithPosition("Research Manager") as $research_manager)
                                                <a href="{{route('admin.users.show', $research_manager->id)}}">
                                                    {{ $research_manager->name }}
                                                </a>
                                            @endforeach
                                        @else
                                            not specified
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if(count($project->usersWithPosition("SDR")))
                                            @foreach($project->usersWithPosition("SDR") as $sdr)
                                                <a href="{{route('admin.users.show', $sdr->id)}}">
                                                    {{ $sdr->name }}
                                                </a>
                                            @endforeach
                                        @else
                                            not specified
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if(count($project->usersWithPosition("IT Specialist")))
                                            @foreach($project->usersWithPosition("IT Specialist") as $sdr)
                                                <a href="{{route('admin.users.show', $sdr->id)}}">
                                                    {{ $sdr->name }}
                                                </a>
                                            @endforeach
                                        @else
                                            not specified
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if($project->mailboxes->toArray())
                                            @foreach($project->mailboxes as $mailbox)
                                                <a href="{{route('admin.mailboxes.show', $mailbox->id)}}">
                                                    {{ $mailbox->email }}
                                                </a>
                                            @endforeach
                                        @else
                                            not specified
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if($project->linkedin_accounts->toArray())
                                            @foreach($project->linkedin_accounts as $linkedin)
                                                <a href="{{route('admin.linkedin-accounts.show', $linkedin->id)}}">
                                                    {{ $linkedin->name }}
                                                </a>
                                            @endforeach
                                        @else
                                            not specified
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <form class="mx-1" method="post"
                                              action="{{ route('admin.projects.edit', $project->id) }}">
                                            @method('GET')
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary">
                                                <i class="nav-icon fas fa-edit"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="align-middle">
                                        <form class="mx-1" method="post"
                                              action="{{ route('admin.projects.destroy', $project->id) }}">
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
                                <th>Logo</th>
                                <th>Name</th>
                                <th>Client</th>
                                <th>CSM</th>
                                <th>Research Manager</th>
                                <th>SDRs</th>
                                <th>IT Specialist</th>
                                <th>Mailboxes</th>
                                <th>Linkedin Accounts</th>
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
