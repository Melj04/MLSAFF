<?php

namespace App\Exports;

use App\Livewire\Weight;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class WeightDataExport
{
    protected $data;
    protected $Weight;
    protected $aveFed;
    public function __construct($data,$totalWeight,$averageFeedingTime)
    {
        $this->data = $data;
        $this->Weight=$totalWeight;
        $this->aveFed=$averageFeedingTime;
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Weight');
        $sheet->setCellValue('B1', 'Feed Time');

        // Add data
        $row = 2;
        foreach ($this->data as $item) {
            // Handle both object and array cases
            $weight = is_array($item) ? $item['value'] : $item->value;
            $time = is_array($item) ? $item['time'] : $item->time;

            // Add the data to the cells
            $sheet->setCellValue("A$row", $weight);                        // Add weight data
            $sheet->setCellValue("B$row", $time);  // Format and add time data
            $row++;
        }
        $row++;
        $sheet->setCellValue("A$row", "Total Weight");
        $sheet->setCellValue("B$row", $this->Weight.'g');
        $row++;
        $sheet->setCellValue("A$row", "Average Time Feeding");
        $sheet->setCellValue("B$row", $this->aveFed);

        // Save Excel file to a temporary file
        $writer = new Xlsx($spreadsheet);
        $filePath = sys_get_temp_dir() . '/monthly_report.xlsx';
        $writer->save($filePath);

        return $filePath;
    }
}
