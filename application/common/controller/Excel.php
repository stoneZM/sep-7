<?php
/**
 * Created by PhpStorm.
 * User: stone
 * Date: 2017/9/14
 * Time: 21:06
 */

namespace app\common\controller;
use think\Controller;

class Excel extends Controller
{
    static public function export_excel($sheet_title,$col_title,$col_width,$data)
     {
        vendor('PHPExcel.Classes.PHPExcel');
        vendor('PHPExcel.Classes.PHPExcel.Writer.Excel2007.php');
        $PHPExcel = new \PHPExcel();
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle($sheet_title); //给当前活动sheet设置名称

         foreach ($col_title as $key=>$value){
             $PHPSheet->setCellValue($key,$value);
         }
         foreach ($col_width as $key=>$value){
             $PHPSheet->getColumnDimension($key)->setWidth($value);
         }

         $row = 2;
         foreach ($data as $key=>$value){

             $PHPSheet->setCellValue("A".$row,$value['id']);
             $PHPSheet->setCellValue("B".$row,$value['phone_num']);
             $PHPSheet->setCellValue("C".$row,$value['is_vip']);
             $PHPSheet->setCellValue("D".$row,$value['vip_expire_time']);
             $PHPSheet->setCellValue("E".$row,$value['reg_time']);
             $row++;
         }


        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$sheet_title.'(' . date('Ymd-His') . ').xlsx"');
        header('Cache-Control: max-age=0');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //导出交易表格
    static public function export_trans_excel($sheet_title,$col_title,$col_width,$data)
    {
        vendor('PHPExcel.Classes.PHPExcel');
        vendor('PHPExcel.Classes.PHPExcel.Writer.Excel2007.php');
        $PHPExcel = new \PHPExcel();
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle($sheet_title); //给当前活动sheet设置名称

        foreach ($col_title as $key=>$value){
            $PHPSheet->setCellValue($key,$value);
        }
        foreach ($col_width as $key=>$value){
            $PHPSheet->getColumnDimension($key)->setWidth($value);
        }
        $PHPSheet->getDefaultRowDimension()->setRowHeight(30);
        $row = 2;

        foreach ($data as $key=>$value){

            $PHPSheet->setCellValue("A".$row,$value['id']);
            $PHPSheet->setCellValue("B".$row,$value['telephone']);
            $PHPSheet->setCellValue("C".$row,$value['trade_sn']);
            $PHPSheet->setCellValue("D".$row,$value['pay_ment']);
            $PHPSheet->setCellValue("E".$row,$value['totalMoney']);
            $PHPSheet->setCellValue("F".$row,$value['contactname']);
            $PHPSheet->setCellValue("G".$row,$value['status']==0?'未完成':"已完成");
            $PHPSheet->setCellValue("H".$row,date("Y-m-d",$value['addtime']));
            $PHPSheet->setCellValue("I".$row,$value['edittime']?date("Y-m-d",$value['edittime']):"--:--:--");
            $row++;
        }


        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$sheet_title.'(' . date('Ymd-His') . ').xlsx"');
        header('Cache-Control: max-age=0');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }
}