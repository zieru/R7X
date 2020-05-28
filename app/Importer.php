<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Importer extends Model implements ToModel, WithHeadingRow
{
    protected $guarded = [];
    //
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        return new BillingCollection([
            'periode'     => $row['name'],
            'account_number'    => $row['email'],
            'msisdn' => $row['msisdn'],
            'area' => $row['area'],
            'regional' => $row['regional'],
            'poc' => $row['poc'],
            'bill_cycle' => $row['bill_cycle'],
            'customer_type' => $row['customer_type'],
            'blocking_status' => $row['blocking_status'],
            'rt' => $row['rt'],
            'bill_amount_2' => $row['bill_amount_2'],
            'bill_amount_3' => $row['bill_amount_3'],
            'bucket_2' => $row['bucket_2'],
            'bucket_3' => $row['bucket_3'],
            'rec'   => $row['rec']
        ]);
    }
}
