@extends('admin.layouts.main')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $campaign->name }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.main') }}">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{ route('admin.campaigns.index') }}">Campaigns</a></li>
                            <li class="breadcrumb-item active"><a href="{{ route('admin.campaigns.show', $campaign->id) }}">{{ $campaign->name }}</a></li>
                            <li class="breadcrumb-item active">Edit Campaign</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edit Campaign</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form method="post" action="{{ route('admin.campaigns.update', $campaign->id) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name"
                                       placeholder="Enter Name" name="name" value="{{ $campaign->name }}">
                                @error('name')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Project</label>
                                <select class="form-control" name="project_id" style="width: 100%;">
                                    @foreach($projects as $project)
                                        @if ($campaign->project_id == $project->id)
                                            <option value="{{ $project->id }}" selected>{{ $project->name }}</option>
                                        @else
                                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('project_id')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Mailbox</label>
                                <select class="form-control" name="mailbox_id" style="width: 100%;">
                                    @foreach($mailboxes as $mailbox)
                                        @if ($campaign->mailbox_id == $mailbox->id)
                                            <option value="{{ $mailbox->id }}" selected>{{ $mailbox->email }}</option>
                                        @else
                                            <option value="{{ $mailbox->id }}">{{ $mailbox->email }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('mailbox_id')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="campaign_id" value="{{$campaign->id}}">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ URL::previous() }}" class="btn btn-danger">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
