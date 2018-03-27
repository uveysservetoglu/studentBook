<?php

namespace AppBundle\Controller;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request){
        return new JsonResponse(array('result'=>'Success'));
    }

    public function installerAction(){
        $process = new Process('php bin/console doctrine:schema:update --force ApiBundle');
        try {
            $process->run(function ($type, $buffer) {
                if (Process::ERR === $type) {
                    echo 'Success:'.$buffer;
                } else {
                    echo 'Error:'.$buffer;
                }
            });
        } catch (ProcessFailedException $e) {
            echo $e->getMessage();
        }
        exit();
    }
    public function jsonToExcelAction(Request $request){
        if (!file_exists("work/{$request}.json")) {
            throw new \Exception("Geçersiz dosya.");
        }
        $fileContents  = file_get_contents("work/prod.json");
        $decodeContent = json_decode($fileContents, true);
        $spreadsheet   = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Durumu')
              ->setCellValue('B1', 'Stok Numarasi')
              ->setCellValue('C1', 'Paket Tipi')
              ->setCellValue('D1', 'Urun Adi')
              ->setCellValue('E1', 'Paket İçi Adet')
              ->setCellValue('F1', 'Koli İçerisindeki Paket Sayısı');
        $loop = 2;
        foreach($decodeContent ['RECORDS'] as $item){
            $extra_info = json_decode($item['Extra Info']);
            $packagingType = null;
            switch ($extra_info->packagingType){
                case 'p': $packagingType = 'Paket'; break;
                case 'a': $packagingType = 'Adet'; break;
                case 'k': $packagingType = 'Koli'; break;
            }
            $status = null;
            switch ($item['Durumu']){
                case 'a': $status = 'Aktif'; break;
                case 'i': $status = 'Aktif Degil'; break;
                case 'p': $status = 'On Siparis'; break;
                case 'o': $status = 'Stokta Tukendi'; break;
            }
            $sheet->setCellValue('A'.$loop , $status)
                  ->setCellValue('B'.$loop , $item['Stok Numarasi'])
                  ->setCellValue('C'.$loop , $packagingType)
                  ->setCellValue('D'.$loop , $item['Urun Adi'])
                  ->setCellValue('E'.$loop , $extra_info->itemsInPackage)
                  ->setCellValue('F'.$loop , $extra_info->packagesInBox);
            $loop++;
        }
        $xlsxWriter = new Xlsx($spreadsheet);
        $outputFile = "exported_file_".date_timestamp_get(new \DateTime());
        $xlsxWriter->save("work/{$outputFile}.xlsx");
        exit();
    }
}
