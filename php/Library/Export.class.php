<?php

namespace Library;

class Export {

    public static function syncCSV($title, $filename, $data, $listField){
        $sourceData = [];
        foreach ($data as $key => $value){
            $sourceData[] = Tool::sortBykeys($value,$listField);
        }

        $fileData[] = iconv("UTF-8", "GB2312//IGNORE",implode(',',array_keys($listField)));
        foreach ($sourceData as $item) {
            $fileData[] = iconv("UTF-8", "GB2312//IGNORE",'"'.implode('","',$item) .'"');
        }
        $fileData = implode("\n",$fileData);

        // 头信息设置
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=" . $filename.'.csv');
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $fileData;
        exit;
    }

    /**
     * 同步导出excel
     * @param $title string excel标题
     * @param $filename string excel文件名
     * @param $data  数据
     * @param $listField 字段与标题对应关系
     * @return int
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public static function syncExcel($title, $filename, $data, $listField)
    {
        $phpExcelPath = APP_PATH.'/Vendor/PHPExcel/Classes/PHPExcel.php';
        $sourceData = [];
        foreach ($data as $key => $value){
            $sourceData[] = Tool::sortBykeys($value,$listField);
        }

        // 初始化 PHPExcel 类库
        if (false == is_file($phpExcelPath)) {
            return 1;
        }
        include_once $phpExcelPath;

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        // sheet名称
        $objPHPExcel->getActiveSheet()->setTitle($title);
        //设置列的宽度
        for ($i = 65; $i <= 81; $i++) {
            $objPHPExcel->getActiveSheet()->getColumnDimension(chr($i))->setWidth(20);
        }

        $start = 65;
        foreach ($listField as $k => $v) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($start) . '1', $k);
            $start++;
        }

        //处理excel格式
        if(!empty($sourceData)) {
            $row = 2;
            foreach ($sourceData as $v) {
                $line = 65;
                foreach ($listField as $name => $field) {
                    $objPHPExcel->getActiveSheet()->getStyle(chr($line) . $row)->getNumberFormat()
                        ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($line) . $row, strval($v[$field] . ' '));
                    $line++;
                }
                $row++;
            }
        }

        $filename = iconv("UTF-8", "gb2312", $filename);
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        Header('content-Type:application/vnd.ms-excel;charset=utf-8');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header("Pragma: no-cache"); // HTTP/1.0

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }


    /*
     * 例子：
     * $listField = [
            '账单日期' => 'bill_time',
            '购买订单数' => 'order_count',
            '购买订单金额' => 'order_total',
            '取消单数' => 'cancel_order_count',
            '取消退款' => 'cancel_order_total',
            '核销单数' => 'closure_order_count',
            '核销单金额' => 'closure_order_total',
            '佣金' => 'commission',
            '商家余额' => 'business_balance',
            '商家提现' => 'withdraw',
            '购买单中优惠券金额' => 'promotion_total',
            '取消单中优惠券金额' => 'cancel_promotion_total',
            '核销单中优惠券金额' => 'closure_promotion_total',
            '待核销总额' => 'pre_closure_total',
            '商家总余额' => 'business_balance_total',
            '资产总净额' => 'net_assets_total'
        ];
        Export::syncExcel("结算账单","结算账单",$data,$listField);

     * */

}