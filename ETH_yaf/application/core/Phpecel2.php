<?php
namespace app\core;
include "PHPExcel.php";

class Phpecel2{
     public function __construct(){

     }
    /**
     * 文件路径读取Excel模板
     * @param string $path
     * @return object
     */
     public function  getExcelObjectByPath($path){
         if (empty($path) or !file_exists($path)) {
             die('file not exists');
         }
         $PHPReader = new PHPExcel_Reader_Excel2007();
         if (!$PHPReader->canRead($path)) {
            $PHPReader = new PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($path)) {
                echo 'no Excel';
                return;
            }
         }
         $PHPExcel = $PHPReader->load($path);
         return $PHPExcel;
    }
    /**
     * PHPExcel转换为数组
     * @param object $PHPExcel
     * @param int $startRow
     * @param int $endRow
     * @param int $sheet
     * @return array
     */
    public function excelToData($PHPExcel, $startRow = 1,  $endRow = 0, $sheet = 0)
    {
        /** 读取excel文件中的指定工作表 */
        $currentSheet = $PHPExcel->getSheet($sheet);
        /** 取得最大的列号 */
        $maxColumn = $currentSheet->getHighestColumn();
        /** 取得一共有多少行 */
        $maxRow = $currentSheet->getHighestRow() - $endRow;
        $data = array();
        $rowIndex = $startRow;
        /** 循环读取每个单元格的内容。 列从A开始 */
        for ($rowIndex; $rowIndex <= $maxRow; $rowIndex++)
        {
            $data_row = array();
            for($colIndex ='A'; $colIndex <= $maxColumn; $colIndex++)
            {
                $addr = $colIndex.$rowIndex;
                $cell = $currentSheet->getCell($addr)->getValue();
                if($cell instanceof PHPExcel_RichText)
                {
                    /** 富文本转换字符串 */
                    $cell = $cell->__toString();
                }
                /** 判断单元格内容是否为公式 */
                $cell_one = substr($cell, 0, 1);
                if($cell_one == '=')
                {
                    /** 取公式计算后的结果 */
                    $value = $currentSheet->getCell($addr)->getFormattedValue();
                    $cell = $value;
                }
                $data_row[] = $cell;
            }
            $data[] = $data_row;
        }
        return $data;
    }
    /**
     * PHPExcel生成并添加内容
     * @param array $content
     * @param array $title
     * @param int $startRow
     * @param int $sheet
     * @param int $type
     * @return object
     */
    public function importDataForObj($content = array(), $title = array(), $startRow = 1, $sheet = 0, $type = 2007){
        $PHPExcel = new \PHPExcel();
        /** 07版参数设置 */
        if ($type == 2007)
        {
            $PHPExcel->getProperties()->setCreator("ctos")
                ->setLastModifiedBy("ctos")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");
        }
        /** 读取excel文件中的指定工作表 */
        $currentSheet = $PHPExcel->getSheet($sheet);
        /** 设置工作薄名称 */
        $currentSheet->setTitle(iconv('gbk', 'utf-8', 'test'));
        /** 设置插入起始行数, 这里用获取的行数计算。可以自行根据模板进行设置固定数值 */
        $startColumn = "A";
        if (!empty($title))
        {
            foreach ($title as $item)
            {
                /** 设置宽cell宽 */
                $currentSheet->getColumnDimension($startColumn)->setWidth(18);
                /** 对齐方式 */
                $currentSheet->getStyle($startColumn)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
                /** 为标题赋值 */
                $currentSheet->setCellValue($startColumn . "1", $item);
                $startColumn++;
            }
            $startRow++;
        }
        $i = $startRow;
        /** 写入数据 */
        foreach($content as $data)
        {
            $startColumn = 'A';
            foreach($data as $value)
            {
                /** excel科学计数很烦 这里举例简单实用解决方式 拼接空字符串 */
                if($startColumn == 'A')
                {
                    $currentSheet->setCellValue($startColumn . $i, " ".$value);
                } else
                {
                    $currentSheet->setCellValue($startColumn . $i, $value);
                }
                $startColumn++;
            }
            $i++;
        }
        return $PHPExcel;
    }


    /**
     * PHPExcel转换为数组
     * @param object $PHPExcel
     * @param string $filename
     * @param string $ex
     */
    public function download($PHPExcel,  $filename = 'HelloWord', $ex = '2007')
    {
        ob_end_clean();
        /** 导出excel2007文档*/
        if($ex == '2007') {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
            /** 导出excel2003文档 */
        } else if ($ex == '2005'){

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        } else if ($ex == 'pdf')
        {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment;filename="'.$filename.'.pdf"');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'PDF');
            $objWriter->setFont('arialunicid0-chinese-simplified');
            $objWriter->save('php://output');
            exit;
        } else if ($ex == 'csv')
        {
            header("Content-type:text/csv");
            header("Content-Disposition:attachment;filename=".$filename.'.csv');
            header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
            header('Expires:0');
            header('Pragma:public');
            $objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'CSV');
            $objWriter->setFont('arialunicid0-chinese-simplified');
            $objWriter->save('php://output');
            exit;
        }
    }
}