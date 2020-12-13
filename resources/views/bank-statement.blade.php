@extends('layouts.app')


@section('content')

<div class="my-5">
    <div class="col-sm-8 col-md-6 mx-auto">
        <div class="card shadow">
            <div class="card-body">
                <form action="{{ route('upload') }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    <input type="hidden" name="hack" value="bank-statement">
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
    <div class="my-4">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#salary-information">
                    Salary Information <span
                    class="badge badge-info">{{ collect($salary)->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#bank-charges">
                    Bank Charges <span
                    class="badge badge-info">{{ collect($bankCharges)->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#dud-cheques">
                    Dud Cheques <span
                    class="badge badge-info">{{ collect($dudCheques)->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#loan-repayments">
                    Loan Repayments <span
                    class="badge badge-info">{{ collect($loanRepayments)->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#reversals">
                    Reversals <span
                    class="badge badge-info">{{ collect($reversal)->count() }}</span>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="salary-information">
                <div class="card shadow my-5">
                    <div class="card-header">
                        <div class="card-title m-0">
                            Salary Information
                        </div>
                    </div>
                    <div class="card-body">
                        @isset($repaymentDate)
                        <div class="my-3 text-center">
                            <h5>Repayment Date: {{ $repaymentDate }}</h5>
                        </div>
                        @endisset
                        @if(collect(collect(($salary->first()['keywords'])))->count() > 0)
                        <div class="my-4 text-center col-sm-8 mx-auto">
                            <h5>Key words:</h5>
                            <div class="bg-danger rounded shadow p-3">
                                <code>
                                    <h6 class="m-0  text-white">
                                        {{ collect(($salary->first()['keywords'] ?? []))->join(', ') }}
                                    </h6>
                                </code>
                            </div>
                        </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Salary Date</th>
                                        <th>Salary Amount</th>
                                        <th>Employer Name</th>
                                        <th>Days Between Salary</th>
                                        <th>Narration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salary as $salaryItem)
                                    <tr>
                                        <td data-sort="{{ strtotime($salaryItem['date']) }}">{{ isset($salaryItem['date']) ? dateFormat($salaryItem['date'], false): 'N/A' }}
                                        </td>
                                        <td>{{ isset($salaryItem['amount']) ? number_format($salaryItem['amount'], 2): 'N/A' }}
                                        </td>
                                        <td class="text-capitalize">
                                            {{ empty($employerName[$loop->index]) ? 'N/A' : $employerName[$loop->index] }}
                                        </td>
                                        <td>{{ $salaryInterval[$loop->index] ?? 'N/A' }}</td>
                                        <td>{{ $salaryItem['narration'] ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="bank-charges">
                <div class="card shadow my-5">
                    <div class="card-header">
                        <div class="card-title m-0">
                            Bank Charges
                        </div>
                    </div>
                    <div class="card-body">
                        @if(collect(collect(($bankCharges->first()['keywords'])))->count() > 0)
                        <div class="my-4 text-center col-sm-8 mx-auto">
                            <h5>Key words:</h5>
                            <div class="bg-danger rounded shadow p-3">
                                <code>
                                    <h6 class="m-0  text-white">
                                        {{ collect(($bankCharges->first()['keywords'] ?? []))->join(', ') }}
                                    </h6>
                                </code>
                            </div>
                        </div>
                        @endif
                        <div class="my-3 text-center">
                            <h5>Total Number of Charges -> <span
                                    class="badge badge-info">{{ collect($bankCharges)->count() }}</span></h5>
                            <h5>Total Amount -> <span
                                    class="badge badge-success">{{ number_format(collect($bankCharges)->sum('amount'), 2) }}</span>
                            </h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Narration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bankCharges as $bankCharge)
                                    <tr>
                                        <th>{{ $loop->iteration }}</th>
                                        <td>{{ isset($bankCharge['date']) ? dateFormat($bankCharge['date'], false): 'N/A' }}
                                        </td>
                                        <td>{{ isset($bankCharge['amount']) ? number_format($bankCharge['amount'], 2): 'N/A' }}
                                        </td>
                                        <td>{{ $bankCharge['narration'] ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="dud-cheques">
                <div class="card shadow my-5">
                    <div class="card-header">
                        <div class="card-title m-0">
                            Dud Cheques
                        </div>
                    </div>
                    <div class="card-body">
                        @if(collect(collect(($dudCheques->first()['keywords'])))->count() > 0)
                        <div class="my-4 text-center col-sm-8 mx-auto">
                            <h5>Key words:</h5>
                            <div class="bg-danger rounded shadow p-3">
                                <code>
                                    <h6 class="m-0  text-white">
                                        {{ collect(($dudCheques->first()['keywords'] ?? ['None']))->join(', ') }}
                                    </h6>
                                </code>
                            </div>
                        </div>
                        @endif
                        <div class="my-3 text-center">
                            <h5>Total Number of DUDs -> <span
                                    class="badge badge-info">{{ collect($dudCheques)->count() }}</span>
                            </h5>
                            <h5>Total Amount -> <span
                                    class="badge badge-success">{{ number_format(collect($dudCheques)->sum('amount'), 2) }}</span>
                            </h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Narration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dudCheques as $dudChequesItem)
                                    <tr>
                                        <th>{{ $loop->iteration }}</th>
                                        <td>{{ isset($dudChequesItem['date']) ? dateFormat($dudChequesItem['date'], false): 'N/A' }}
                                        </td>
                                        <td>{{ isset($dudChequesItem['amount']) ? number_format($dudChequesItem['amount'], 2): 'N/A' }}
                                        </td>
                                        <td>{{ $dudChequesItem['narration'] ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="loan-repayments">
                <div class="card shadow my-5">
                    <div class="card-header">
                        <div class="card-title m-0">
                            Loan Repayments
                        </div>
                    </div>
                    <div class="card-body">
                        @if(collect(collect(($loanRepayments->first()['keywords'])))->count() > 0)
                        <div class="my-4 text-center col-sm-8 mx-auto">
                            <h5>Key words:</h5>
                            <div class="bg-danger rounded shadow p-3">
                                <code>
                                    <h6 class="m-0  text-white">
                                        {{ collect(($loanRepayments->first()['keywords'] ?? ['None']))->join(', ') }}
                                    </h6>
                                </code>
                            </div>
                        </div>
                        @endif
                        <div class="my-3 text-center">
                            <h5>Total Number of Repayments -> <span
                                    class="badge badge-info">{{ collect($loanRepayments)->count() }}</span></h5>
                            <h5>Total Amount -> <span
                                    class="badge badge-success">{{ number_format(collect($loanRepayments)->sum('amount'), 2) }}</span>
                            </h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Repayment Date</th>
                                        <th>Amount</th>
                                        <th>Narration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($loanRepayments as $loanRepaymentsItem)
                                    <tr>
                                        <th>{{ $loop->iteration }}</th>
                                        <td>{{ isset($loanRepaymentsItem['date']) ? dateFormat($loanRepaymentsItem['date'], false): 'N/A' }}
                                        </td>
                                        <td>{{ isset($loanRepaymentsItem['amount']) ? number_format($loanRepaymentsItem['amount'], 2): 'N/A' }}
                                        </td>
                                        <td>{{ $loanRepaymentsItem['narration'] ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="reversals">
                <div class="card shadow my-5">
                    <div class="card-header">
                        <div class="card-title m-0">
                            Reversals
                        </div>
                    </div>
                    <div class="card-body">
                        @if(collect(collect(($reversal->first()['keywords'])))->count() > 0)
                        <div class="my-4 text-center col-sm-8 mx-auto">
                            <h5>Key words:</h5>
                            <div class="bg-danger rounded shadow p-3">
                                <code>
                                    <h6 class="m-0  text-white">
                                        {{ collect(($reversal->first()['keywords'] ?? ['None']))->join(', ') }}
                                    </h6>
                                </code>
                            </div>
                        </div>
                        @endif
                        <div class="my-3 text-center">
                            <h5>Total Number of Reversals -> <span
                                    class="badge badge-info">{{ collect($reversal)->count() }}</span>
                            </h5>
                            <h5>Total Amount -> <span
                                    class="badge badge-success">{{ number_format(collect($reversal)->sum('amount'), 2) }}</span>
                            </h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Narration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reversal as $reversalItem)
                                    <tr>
                                        <th>{{ $loop->iteration }}</th>
                                        <td>{{ isset($reversalItem['date']) ? dateFormat($reversalItem['date'], false): 'N/A' }}
                                        </td>
                                        <td>{{ isset($reversalItem['amount']) ? number_format($reversalItem['amount'], 2): 'N/A' }}
                                        </td>
                                        <td>{{ $reversalItem['narration'] ?? 'N/A' }}</td>
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





</div>

@endsection
