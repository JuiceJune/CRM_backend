@extends('admin.layouts.main')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $project->name }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.main') }}">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{ route('admin.projects.index') }}">Projects</a></li>
                            <li class="breadcrumb-item active">{{ $project->name }}</li>
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
                            <a href="{{route("admin.projects.edit", $project->id)}}" class="btn btn-warning py-1">Edit</a>
                            <form class="mx-1" method="post" action="{{ route('admin.projects.destroy', $project->id) }}">
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
                                <td class="align-middle">Logo</td>
                                <td class="align-middle">
                                    <img src="{{ asset("/storage/{$project->logo}") }}"
                                         class="rounded-circle" style="width: 40px;"
                                         alt="Logo" />
                                </td>
                            </tr>
                            <tr>
                                <td>2.</td>
                                <td>Name</td>
                                <td>{{ $project->name }}</td>
                            </tr>
                            <tr>
                                <td>5.</td>
                                <td>Price</td>
                                <td>{{ $project->price }}</td>
                            </tr>
                            <tr>
                                <td>6.</td>
                                <td>Client</td>
                                <td>
                                    <a href="{{ route("admin.clients.show", $project->client->id) }}">
                                        {{ $project->client->name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>7.</td>
                                <td>CSMs</td>
                                <td>
                                    @foreach($project->usersWithPosition("CSM") as $csm)
                                        <a href="{{ route("admin.users.show", $csm->id) }}">
                                            {{$csm->name}}
                                        </a>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <td>8.</td>
                                <td>SDRs</td>
                                <td>
                                    @foreach($project->usersWithPosition("SDR") as $sdr)
                                        <a href="{{ route("admin.users.show", $sdr->id) }}">
                                            {{$sdr->name}}
                                        </a>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <td>9.</td>
                                <td>IT Specialists</td>
                                <td>
                                    @foreach($project->usersWithPosition("IT Specialist") as $it_specialist)
                                        <a href="{{ route("admin.users.show", $it_specialist->id) }}">
                                            {{$it_specialist->name}}
                                        </a>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <td>10.</td>
                                <td>Researchers</td>
                                <td>
                                    @foreach($project->usersWithPosition("Researcher") as $researcher)
                                        <a href="{{ route("admin.users.show", $researcher->id) }}">
                                            {{$researcher->name}}
                                        </a>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <td>11.</td>
                                <td>Research Managers</td>
                                <td>
                                    @foreach($project->usersWithPosition("Research Manager") as $research_manager)
                                        <a href="{{ route("admin.users.show", $research_manager->id) }}">
                                            {{$research_manager->name}}
                                        </a>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <td>12.</td>
                                <td>Mailboxes</td>
                                <td>
                                    @foreach($project->mailboxes as $mailbox)
                                        <a href="{{ route("admin.mailboxes.show", $mailbox->id) }}">
                                            {{$mailbox->email}}
                                        </a>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <td>14.</td>
                                <td>Start date</td>
                                <td>{{ $project->start_date }}</td>
                            </tr>
                            <tr>
                                <td>15.</td>
                                <td>End date</td>
                                <td>{{ $project->end_date }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
