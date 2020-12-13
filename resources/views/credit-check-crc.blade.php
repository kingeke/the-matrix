@extends('layouts.app')


@section('content')

<div class="my-5">
    <div class="col-sm-8 col-md-6 mx-auto">
        <div class="card shadow">
            <div class="card-body">
                <form action="{{ route('upload') }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    <input type="hidden" name="hack" value="credit-check">
                    <input type="hidden" name="creditType" value="crc">
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
    @isset($crc_data)
    <div class="my-5">
        <div class="col-lg-5 mx-auto">
            <div class="card shadow">
                <div class="card-header">
                    <div class="card-title m-0">
                        Client Info
                    </div>
                </div>
                <div class="card-body">
                    <ul>
                        <li>Client Name: {{ $crc_data->client_name ?? null }}</li>
                        <li>Client DOB: {{ $crc_data->data_of_birth ?? null }}</li>
                        <li>Client Gender: {{ $crc_data->gender ?? null }}</li>
                    </ul>
                </div>
            </div>
        </div>
        @isset($status)
        <div class="col-4 mx-auto my-5">
            <div class="card shadow border-{{ $status == 'approved' ? 'success' : ($status == 'refer' ? 'info' : 'danger') }}">
                <div class="card-header">
                    <div class="card-title m-0">
                        Credit Status
                    </div>
                </div>
                <div class="card-body text-center">
                    <code>
                        <h4 class="text-capitalize">{{ $status }}</h4>
                    </code>
                </div>
            </div>
        </div>
        @endisset
        <div class="my-5">
            <ul class="nav nav-tabs">
                @foreach($crc_data->credit_facilities as $index => $nav_item_credit_facilities)
                <li class="nav-item">
                    <a class="nav-link {{ ($loop->first ? 'active' : '') }}" data-toggle="tab"
                        href="{{ "#item-$index" }}">
                        {{ $nav_item_credit_facilities->provider_source }}</span>
                    </a>
                </li>
                @endforeach
            </ul>
            <div class="tab-content">
                @foreach($crc_data->credit_facilities as $index => $credit_facilities)
                <div class="tab-pane fade {{ ($loop->first ? 'show active' : '') }}" id="{{ "item-$index" }}">
                    <div class="row my-4">
                        <div class="col-lg-6">
                            <div class="card shadow">
                                <div class="card-body p-5">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" data-no-table="true">
                                            <thead>
                                                <tr>
                                                    <th>Provider</th>
                                                    <th>{{ $credit_facilities->provider_source }}</th>
                                                </tr>
                                                <tr>
                                                    <th>Disbursement Date</th>
                                                    <th>{{ dateFormat($credit_facilities->first_disbursement_date, false) }}
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>Sanctioned Amount</th>
                                                    <th>{{ number_format(($credit_facilities->sanctioned_amount ?? 0), 2) }}
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>Current Balance</th>
                                                    <th>{{ number_format(($credit_facilities->current_balance ?? 0), 2) }}
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>Planned Closure Date</th>
                                                    <th>{{ dateFormat($credit_facilities->planned_closure_date, false) }}
                                                    </th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card shadow">
                                <div class="card-body p-5">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" data-no-table="true">
                                            <thead>
                                                <tr>
                                                    <th>S/N</th>
                                                    <th>Conditions</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($credit_facilities->hacks as $hack => $status)
                                                <tr>
                                                    <th>{{ $loop->iteration }}</th>
                                                    <td>{{ formatHack($hack) }}
                                                    <td class="text-capitalize">{{ $status }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endisset
</div>

@endsection
