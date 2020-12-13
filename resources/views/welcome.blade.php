@extends('layouts.app')


@section('content')

<div class="my-5">
    <div class="col-sm-8 col-md-6 mx-auto">
        <div class="card shadow">
            <div class="card-body p-5">
                <form action="{{ route('upload') }}" enctype="multipart/form-data" method="POST">
                    
                    @csrf

                    <div class="form-group">
                        <label>Choose your hack</label>
                        <select class="form-control" name="hack" required id="hack-select-dropdown">
                            <option value="">Select hack</option>
                            <option value="bank-statement" {{ old('hack') == 'bank-statement' ? 'selected' : '' }}>Bank Statement</option>
                            <option value="credit-check" {{ old('hack') == 'credit-check' ? 'selected' : '' }}>Credit Check</option>
                        </select>
                    </div>
                    
                    <div class="form-group hidden">
                        <label>Choose your credit type</label>
                        <select class="form-control" name="creditType" required id="credit-type-dropdown">
                            <option value="">Select credit type</option>
                            <option value="crc" {{ old('creditType') == 'crc' ? 'selected' : '' }}>CRC Statement</option>
                            <option value="crs" {{ old('creditType') == 'crs' ? 'selected' : '' }}>CRS Statement</option>
                        </select>
                    </div>

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
                        <button type="submit" class="btn btn-success btn-block">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
