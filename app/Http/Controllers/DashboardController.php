<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\CRCReportDataTransferObject;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function welcome()
    {
        return view('welcome');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file-json' => 'required|file',
            'hack'      => 'required',
        ]);

        // try {

        if ($request->hack == 'bank-statement') {

            $json = json_decode(file_get_contents($request->file('file-json')->getRealPath()), true);

            $response = json_decode($this->defaultClient()->post('challenge1', [
                'json' => $json,
            ])->getBody(), true);

            session()->put('bank-data', [
                'salary'         => collect(($response['salary'] ?? [])),
                'salaryInterval' => collect(($response['salaryInterval'] ?? [])),
                'employerName'   => collect(($response['employerName'] ?? [])),
                'repaymentDate'  => $response['repaymentDate'] ?? null,
                'reversal'       => collect(($response['reversal'] ?? [])),
                'dudCheques'     => collect(($response['dudCheques'] ?? [])),
                'bankCharges'    => collect(($response['bankCharges'] ?? [])),
                'loanRepayments' => collect(($response['loanRepayments'] ?? [])),
            ]);

            return redirect()->route('bank-statement')->with('message', messageResponse('success', 'Bank statement hack generated successfully'));
        }

        if ($request->hack == 'credit-check') {

            if ($request->creditType == 'crc') {

                $extension = $request->file('file-json')->extension();

                if (strtolower($extension) == 'xml') {

                    $xml = file_get_contents($request->file('file-json')->getRealPath());

                    $xmlObject = simplexml_load_string($xml);

                    $json = json_encode($xmlObject);

                    $data = collect(json_decode($json, true)['BODY']);
                }

                $response = CRCReportDataTransferObject::create($data);

                session()->put('credit-data-crc', [
                    'data' => $response,
                ]);

                return redirect()->route('credit-check-crc')->with('message', messageResponse('success', 'Credit check CRC hack generated successfully'));

            }

            if ($request->creditType == 'crs') {

                $json = json_decode(file_get_contents($request->file('file-json')->getRealPath()), true);

                $response = json_decode($this->defaultClient()->post('challenge2', [
                    'json' => $json,
                ])->getBody(), true);

                session()->put('credit-data-crs', [
                    'status' => $response['message'] ?? 'undetermined',
                    'pdf'    => $json['PDFReport']['PDFContent'] ?? null,
                ]);

                return redirect()->route('credit-check-crs')->with('message', messageResponse('success', 'Credit check hack generated successfully'));
            }
        }

        // } catch (\Exception $e) {

        //     report($e);

        //     return back()->withInput()->with('message', messageResponse('danger', "An error occurred: {$e->getMessage()}"));
        // }
    }

    public function bankStatement()
    {
        $data = session()->get('bank-data') ?? [
            'salary'         => collect([]),
            'salaryInterval' => collect([]),
            'employerName'   => collect([]),
            'reversal'       => collect([]),
            'dudCheques'     => collect([]),
            'bankCharges'    => collect([]),
            'loanRepayments' => collect([]),
        ];

        return view('bank-statement', $data);
    }

    public function creditCheckCRC()
    {
        $data = session()->get('credit-data-crc')['data'];

        if (isset($data->credit_facilities) && count($data->credit_facilities) > 0) {

            $status = 'approved';

            $hacks = collect($data->credit_facilities)->where('current_balance', '>', 3000)->pluck('hacks')->map(function ($hack) {
                return collect($hack)->values();
            })->collapse();

            if ($hacks->contains('declined')) {
                $status = 'declined';
            } else if (!$hacks->contains('declined') && $hacks->contains('refer')) {
                $status = 'refer';
            } else if (!$hacks->contains('declined') && !$hacks->contains('refer') && $hacks->contains('approved')) {
                $status = 'approved';
            }
        } else {

            $status = 'undetermined';
        }

        return view('credit-check-crc', [
            'crc_data' => $data,
            'status'   => $status,
        ]);
    }

    public function creditCheckCRS()
    {
        $data = session()->get('credit-data-crs') ?? [
            'status' => null,
            'pdf'    => null,
        ];
        
        return view('credit-check-crs', $data);
    }

    public function defaultClient($http_errors = false, $headers = null)
    {
        return new Client([
            'base_uri'        => 'https://9f06f575c438.ngrok.io/',
            'http_errors'     => $http_errors,
            'headers'         => $headers,
            'timeout'         => 60,
            'connect_timeout' => 60,
        ]);
    }
}
