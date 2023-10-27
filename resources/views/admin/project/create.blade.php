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
                            <li class="breadcrumb-item"><a href="{{ route('admin.main') }}">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{ route('admin.projects.index') }}">Projects</a></li>
                            <li class="breadcrumb-item active">Add Project</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Add Project</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form method="post" action="{{ route('admin.projects.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="logo">Logo</label>
                                <input class="form-control" type="file" id="logo" name="logo" value="{{ old('logo') }}">
                                @error('logo')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name"
                                       placeholder="Enter Name" name="name" value="{{ old('name') }}">
                                @error('name')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="price">Price</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" class="form-control" id="price"
                                           placeholder="Enter Price" name="price" value="{{ old('price') }}">
                                </div>
                                @error('price')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Client</label>
                                <select class="form-control" name="client_id" style="width: 100%;">
                                    @foreach($clients as $client)
                                        @if (old('client_id') == $client->id)
                                            <option value="{{ $client->id }}" selected>{{ $client->name }}</option>
                                        @else
                                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('client_id')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>IT Specialists</label>
                                <select id="it_specialists" class="select2 select2-hidden-accessible" name="users[]" multiple="" data-placeholder="Select IT Specialists" style="width: 100%;" data-select2-id="1" tabindex="-1" aria-hidden="true">
                                    @foreach($users->getUsersByPosition("IT Specialist") as $ITSpecialist)
                                        <option value="{{$ITSpecialist->id}}">{{ $ITSpecialist->name }}</option>
                                    @endforeach
                                </select>
                                @error('users[]')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>CSM</label>
                                <select id="csms" class="select2 select2-hidden-accessible" name="users[]" multiple="" data-placeholder="Select CSM" style="width: 100%;" data-select2-id="2" tabindex="-1" aria-hidden="true">
                                    @foreach($users->getUsersByPosition("CSM") as $csm)
                                        <option value="{{$csm->id}}">{{ $csm->name }}</option>
                                    @endforeach
                                </select>
                                @error('users[]')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>SDRs</label>
                                <select id="sdrs" class="select2 select2-hidden-accessible" name="users[]" multiple="" data-placeholder="Select SDRs" style="width: 100%;" data-select2-id="3" tabindex="-1" aria-hidden="true">
                                    @foreach($users->getUsersByPosition("SDR") as $sdr)
                                        <option value="{{$sdr->id}}">{{ $sdr->name }}</option>
                                    @endforeach
                                </select>
                                @error('users[]')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Researchers</label>
                                <select id="researchers" class="select2 select2-hidden-accessible" name="users[]" multiple="" data-placeholder="Select researchers" style="width: 100%;" data-select2-id="4" tabindex="-1" aria-hidden="true">
                                    @foreach($users->getUsersByPosition("Researcher") as $researcher)
                                        <option value="{{$researcher->id}}">{{ $researcher->name }}</option>
                                    @endforeach
                                </select>
                                @error('users[]')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Research Manager</label>
                                <select id="research_managers" class="select2 select2-hidden-accessible" name="users[]" multiple="" data-placeholder="Select research managers" style="width: 100%;" data-select2-id="5" tabindex="-1" aria-hidden="true">
                                    @foreach($users->getUsersByPosition("Research Manager") as $researchManager)
                                        <option value="{{$researchManager->id}}">{{ $researchManager->name }}</option>
                                    @endforeach
                                </select>
                                @error('users[]')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Mailboxes</label>
                                <select id="mailboxes"  class="select2 select2-hidden-accessible" name="mailboxes[]" multiple="" data-placeholder="Select mailboxes" style="width: 100%;" data-select2-id="6" tabindex="-1" aria-hidden="true">
                                    @foreach($mailboxes as $mailbox)
                                        <option value="{{$mailbox->id}}">{{ $mailbox->email }}</option>
                                    @endforeach
                                </select>
                                @error('mailboxes[]')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <div class="input-group date" id="start_date" data-target-input="nearest">
                                    <input type="text" id="start_date" name="start_date" class="form-control datetimepicker-input"
                                           data-target="#start_date" placeholder="Enter Start Date" value="{{ old('start_date', date('Y-m-d')) }}">
                                    <div class="input-group-append" data-target="#start_date" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                @error('start_date')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <div class="input-group date" id="end_date" data-target-input="nearest">
                                    <input type="text" id="end_date" name="end_date" class="form-control datetimepicker-input"
                                           data-target="#end_date" placeholder="Enter Start Date" value="{{ old('end_date', date('Y-m-d')) }}">
                                    <div class="input-group-append" data-target="#end_date" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                @error('end_date')
                                <div class="alert alert-danger mt-2 py-1" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Create</button>
                            <a href="{{ URL::previous() }}" class="btn btn-danger">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
