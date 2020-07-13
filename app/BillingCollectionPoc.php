<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingCollectionPoc extends Model
{
    //
    protected $table = 'billing_collections_poc';
    public $timestamps = false;
    protected $guarded = [];

    public function scopeD60hArea($query){
        $query->groupBy('periode','area')->selectRaw('periode,
                            area,
                                    sum(bill_amount_2) as billing, 
                                    sum(`bucket_2`) as osbalance,
                                    sum(`bill_amount_2`) - sum(`bucket_2`) as collection,
                                    "60h" as kpi,
                                    (SUM(`bill_amount_2`) - sum(`bucket_2`)) / SUM(`bill_amount_2`) as performansi
                                    ')->orderBy('periode')
            ->orderBy('area');
    }
    public function scopeD60hRegional($query){
        $query->groupBy('periode','regional')->selectRaw('periode,
                            regional,
                                    sum(bill_amount_2) as billing, 
                                    sum(`bucket_2`) as osbalance,
                                    sum(`bill_amount_2`) - sum(`bucket_2`) as collection,
                                    "60h" as kpi,
                                    (SUM(`bill_amount_2`) - sum(`bucket_2`)) / SUM(`bill_amount_2`) as performansi
                                    ')->orderBy('periode')
            ->orderBy('poc');
    }
    public function scopeBillingNonZero($query){
        $query->having('billing','>',0);
    }
    public function scopeOrderPeriodArea($query)
    {
        return $query->orderBy('periode')
            ->orderBy('area');
    }
    public function scopeD90hRegional($query){
        $query->groupBy('periode','regional')
            ->selectRaw('periode,
                                regional,
                                    sum(bill_amount_3) as billing , 
                                    sum(`bucket_3`) as osbalance,
                                    sum(`bill_amount_3`) - sum(`bucket_3`) as collection,
                                    "90h" as kpi,
                                    (SUM(`bill_amount_3`) - sum(`bucket_3`)) / SUM(`bill_amount_3`) as performansi
                                    ')->orderBy('periode')
            ->orderBy('poc');
    }
    public function scopeD90hArea($query){
        $query->groupBy('periode','area')
            ->selectRaw('periode,
                                area,
                                    sum(bill_amount_3) as billing , 
                                    sum(`bucket_3`) as osbalance,
                                    sum(`bill_amount_3`) - sum(`bucket_3`) as collection,
                                    "90h" as kpi,
                                    (SUM(`bill_amount_3`) - sum(`bucket_3`)) / SUM(`bill_amount_3`) as performansi
                                    ')->orderBy('periode')
            ->orderBy('area');
    }
    public function scopePeriode($query, $periode)
    {
        return $query->where('periode', '=', $periode);
    }
    public function scopeCustomerType($query, $customer_type)
    {
        return $query->where('customer_type', '=', $customer_type);
    }
}
