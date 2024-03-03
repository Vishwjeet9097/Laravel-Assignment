<?php

namespace App\Services;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class UtilityService
{
    public static function createExcel($data, $headers, $title, $path, $stringFormats = [])
    {
        $spreadsheet = new Spreadsheet();
        $activeSheet = $spreadsheet->getActiveSheet();
        foreach ($stringFormats as $stringFormat) {
            $activeSheet->getStyle($stringFormat)->getNumberFormat()->setFormatCode('0');
        }
        $activeSheet->setTitle($title);
        array_unshift($data, $headers);
        $activeSheet->fromArray($data, null, 'A1', true);
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($path);
    }

    public static function getExcelCsvData($filePath)
    {
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(false);
        $worksheet = $reader->load($filePath)->getActiveSheet();
        return $worksheet->toArray(null, false);
    }

    public static function generateTransactionNumber($username)
    {
        return strtoupper(uniqid($username));
    }

    public static function generateUniqueCode()
    {
        return md5(uniqid(date('Y-m-d H:i:s'), true));
    }
    public static function generateOtp()
    {
        return str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
    }
}
