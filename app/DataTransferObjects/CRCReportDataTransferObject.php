<?php

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class CRCReportDataTransferObject extends DataTransferObject
{
    public $client_name;
    public $data_of_birth;
    public $gender;
    public $credit_facilities;

    public static function create($data): self
    {
        return new self([
            'client_name'       => $data['CONSUMER_PROFILE']['CONSUMER_DETAILS']['NAME'] ?? null,
            'data_of_birth'     => $data['CONSUMER_PROFILE']['CONSUMER_DETAILS']['DATE_OF_BIRTH'] ?? null,
            'gender'            => isset($data['CONSUMER_PROFILE']['CONSUMER_DETAILS']['GENDER']) ? $data['CONSUMER_PROFILE']['CONSUMER_DETAILS']['GENDER'] == '001' ? 'Male' : 'Female' : null,
            'credit_facilities' => self::getCreditFacilities($data),
        ]);
    }

    public static function getCreditFacilities($data)
    {
        // dd($data);
        $result = [];

        $facilities = self::getCreditSummary($data);

        $facilities = $facilities->where('ACCOUNT_STATUS', '001')->values();

        $facilities->each(function ($item) use (&$result) {

            $result[] = CRCCreditFacilitiesDataTransferObject::create($item);

        });

        return $result;
    }

    public static function getCreditSummary($data)
    {
        $data = collect([])->merge(($data['MFCONSUMER_CREDIT_FACILITY_SI']['CREDIT_DETAILS'] ?? []))
        ->merge(($data['MGCONSUMER_CREDIT_FACILITY_SI']['CREDIT_DETAILS'] ?? []))
        ->merge(($data['CONSUMER_CREDIT_FACILITY_SI']['CREDIT_DETAILS'] ?? []));

        return $data;
    }
}
