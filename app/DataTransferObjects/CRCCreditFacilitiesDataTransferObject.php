<?php

namespace App\DataTransferObjects;

use Carbon\CarbonPeriod;
use Spatie\DataTransferObject\DataTransferObject;

class CRCCreditFacilitiesDataTransferObject extends DataTransferObject
{
    public $installment_amount;
    public $structure_id;
    public $first_disbursement_date;
    public $date_last_pay_received;
    public $max_num_of_days_due;
    public $current_balance;
    public $sanctioned_amount;
    public $planned_closure_date;
    public $provider_source;
    public $hacks;

    public static function create($data): self
    {
        $month_year = self::modelMonthYear($data['MONTH_YEAR']);

        return new self([
            'structure_id'            => $data['STRUCTURE_ID'] ?? null,
            'first_disbursement_date' => isset($data['FIRST_DISBURSE_DATE']) ? now()->parse($data['FIRST_DISBURSE_DATE']) : null,
            'installment_amount'      => $data['INSTALLMENT_AMOUNT'] ?? null,
            'date_last_pay_received'  => isset($data['DATE_LATEST_PAY_RECEIVED']) ? now()->parse($data['DATE_LATEST_PAY_RECEIVED']) : null,
            'max_num_of_days_due'     => $data['MAX_NUM_DAYS_DUE'] ?? null,
            'provider_source'         => $data['PROVIDER_SOURCE'] ?? null,
            'sanctioned_amount'       => isset($data['SANCTIONED_AMOUNT']) ? str_replace(',', '', $data['SANCTIONED_AMOUNT']) : null,
            'current_balance'         => isset($data['CURRENT_BALANCE']) ? str_replace(',', '', $data['CURRENT_BALANCE']) : null,
            'planned_closure_date'    => $data['PLANNED_CLOSURE_DATE'] ?? null,
            'hacks'                   => [
                '30_days_in_arrears'                       => self::get30DaysInArrears(collect(($month_year ?? []))),
                '30_plus_in_3_months'                      => self::get30DaysIn3Months(collect(($month_year ?? []))),
                '30_plus_once_in_6_months'                 => self::get30DaysOnceIn6Months(collect(($month_year ?? []))),
                '30_plus_in_3_to_12_months_now_performing' => self::get3to12MonthsNowPerforming(collect(($month_year ?? []))),
            ]
        ]);
    }

    public static function modelMonthYear($histories)
    {
        $histories = collect($histories)->map(function ($a) {

            $position = 0;

            return array_merge($a, [
                'CONSUMER_HISTORY_SUMMARY' => collect($a['CONSUMER_HISTORY_SUMMARY'])->map(function ($b) use (&$position) {

                    $end    = now()->parse($b['from_month_year']);
                    $start  = now()->parse($b['to_month_year']);
                    $months = array();

                    foreach (CarbonPeriod::create($start, '1 month', $end) as $month) {
                        $months[] = $month->format('Y-m-d');
                    }

                    $months = collect($months)->reverse()->values();

                    $items = array_merge($b, [
                        'date' => $months[$position],
                    ]);

                    $position++;

                    return $items;
                })->where('date', '<', now()->parse($a['LAST_REPORTED_DATE']))->toArray()
            ]);
        });

        return $histories->pluck('CONSUMER_HISTORY_SUMMARY')->collapse();
    }

    public static function get30DaysInArrears($data)
    {
        $summary = $data->first();

        if (is_numeric($summary['MAXIMUM_NUMBER_OF_DAYS_OVERDUE'] ?? null)) {

            $overdue = $summary['MAXIMUM_NUMBER_OF_DAYS_OVERDUE'];

            return $overdue > 30 ? 'declined' : 'approved';
        }

        return 'approved';
    }

    public static function get30DaysIn3Months($data)
    {
        $summaries = $data->take(3)->pluck('MAXIMUM_NUMBER_OF_DAYS_OVERDUE');
        
        $status = 'approved';

        foreach ($summaries as $summary) {

            if (is_numeric($summary)) {
                
                $status = $summary > 30 ? 'declined' : 'approved';
            }
        }

        return $status;
    }

    public static function get30DaysOnceIn6Months($data)
    {
        $defaults = [];

        $summaries = $data->take(6)->pluck('MAXIMUM_NUMBER_OF_DAYS_OVERDUE');

        foreach ($summaries as $summary) {

            if (is_numeric($summary) && $summary > 30) {

                $defaults[] = $summary;
            }
        }

        return count($defaults) < 2 ? 'approved' : 'declined';
    }

    public static function get3to12MonthsNowPerforming($data)
    {
        $loanPerforming = true;

        $start = $data->first();

        $first_3_months = $data->take(3);

        $last_4_12_months = $data->where('date', '<', now()->parse($start['date'] ?? '')->sub('3', 'months'))->take(9);

        $first_3_months = $first_3_months->pluck('MAXIMUM_NUMBER_OF_DAYS_OVERDUE');

        foreach ($first_3_months as $first_3_months_item) {

            if (is_numeric($first_3_months_item) && $first_3_months_item > 30) {

                $loanPerforming = false;

                break;
            }
        }

        if (!$loanPerforming) {
            return 'declined';
        }

        if ($loanPerforming) {

            $last_4_12_months = $last_4_12_months->pluck('MAXIMUM_NUMBER_OF_DAYS_OVERDUE');

            foreach ($last_4_12_months as $last_4_12_months_item) {

                if (is_numeric($last_4_12_months_item) && $last_4_12_months_item > 30) {

                    $loanPerforming = false;

                    return 'refer';
                }

            }

        }

        return 'approved';
    }
}
