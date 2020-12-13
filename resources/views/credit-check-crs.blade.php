@extends('layouts.app')


@section('content')

<div class="my-5">
    <div class="col-sm-8 col-md-6 mx-auto">
        <div class="card shadow">
            <div class="card-body">
                <form action="{{ route('upload') }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    <input type="hidden" name="hack" value="credit-check">
                    <input type="hidden" name="creditType" value="crs">
                    <div class="form-group">
                        <label>Select your hacked file</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupFileAddon01">Upload</span>
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file-upload" name="file-json" required>
                                <label class="custom-file-label">Choose file</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success btn-block">Try another hack?</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="my-5">
        @if($status)
        <div class="col-4 mx-auto">
            <div class="card shadow border-{{ $status == 'approved' ? 'success' : ($status == 'refer' ? 'info' : 'danger') }}">
                <div class="card-header">
                    <div class="card-title m-0">
                        Credit Status
                    </div>
                </div>
                <div class="card-body text-center p-5">
                    <code>
                        <h4 class="text-capitalize">{{ $status }}</h4>
                    </code>
                </div>
            </div>
        </div>
        @endif
        @isset($pdf)
        <div class="my-4">
            <iframe src="data:application/pdf;base64,{{ $pdf }}" width="100%" height="1000px"></iframe>
        </div>
        @endisset
    </div>
</div>

@endsection
