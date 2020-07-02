<?php

namespace App\Imports;

use App\FormAdjustment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;
//use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;

class FormAdjustmentImport implements ToModel, WithHeadingRow, WithValidation, SkipsUnknownSheets
{
  use Importable;

  private $rows = 0;

  public function rules(): array
  {
    return [
      '0' => 'required',
      '1' => 'required',
      '2' => 'required',
      '3' => 'required',
      '4' => 'required',
      '5' => 'required',
      '6' => 'required',
      '7' => 'required',
      '8' => 'required',
      '9' => 'required',
      '10' => 'required',
      '11' => 'required',
      '12' => 'required',
      // so on
    ];
  }
  /*public function onUnknownSheet($sheetName)
  {

  }*/
  /*public function collection(Collection $rows)
  {
    foreach ($rows as $row)
    {

      ++$this->rows;
      if($row->filter()->isNotEmpty()){
        FormAdjustment::create([
          'author' => 55,
          'import_batch' => 7,
          'shop' => $row[0],
          'account' => $row[1],
          'msisdn' => $row[2],
          'bill_cycles' => $row[3],
          'status_msisdn' => $row[4],
          'los' => $row[5],
          'arpu' => $row[6],
          'bulantagihan' => $row[7],
          'nominal' => $row[8],
          'reason' => $row[9],
          'notes_dsc' => $row[10],
          'nodin_ba' => $row[11],
          'tgl_adj' => $row[12]
        ]);
      }

    }
  }*/
  public function onUnknownSheet($sheetName)
  {
    // E.g. you can log that a sheet was not found.
    info("Sheet {$sheetName} was skipped");
  }
  public function model(array $row)
  {
    header("Access-Control-Allow-Origin: *");
    ++$this->rows;
    if(array_filter($row)) {
      return new FormAdjustment([
        'author' => 1,
        'import_batch' => 1,
        'shop' => $row[0],
        'account' => $row[1],
        'msisdn' => $row[2],
        'bill_cycle' => $row[3],
        'status_msisdn' => $row[4],
        'los' => $row[5],
        'arpu' => $row[6],
        'bulantagihan' => $row[7],
        'nominal' => $row[8],
        'reason' => $row[9],
        'notes_dsc' => $row[10],
        'nodin_ba' => $row[11],
        'tgl_adj' => $row[12]
      ]);
    }
  }
}
