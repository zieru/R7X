<?php
namespace App\Exports;

use App\BillingCollectionPoc;
use Maatwebsite\Excel\Concerns\FromCollection;

class BillingCollectionExport implements FromCollection{
    public function collection()
    {
        return BillingCollectionPOC::groupBy('periode','area')->billingNonZero();
    }
}
