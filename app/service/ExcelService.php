<?php

namespace app\libs;

use mon\util\Instance;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * Excel导出服务
 * 
 * @require phpoffice/phpspreadsheet
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class ExcelService
{
    use Instance;

    /**
     * 列
     *
     * @var array
     */
    protected $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

    /**
     * 导出Excel
     *
     * @param array $list       数据列表 
     * @param array $header     表格头部
     * @param string $filename  文件名
     * @param string $title     标题
     * @return boolean
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * 
     * @see 参数格式
     * $header = [
     *     ['标题名称', '数据中对应的字段名', '[数据类型(默认txet类型)]'],
     * ];
     * $list = [
     *     ['title' => '标题名称对应的字段名', 'id' => 1]
     * ];
     *
     */
    public function exportData(array $list = [], array $header = [], $filename = '', $title = '', $suffix = 'xlsx')
    {
        if (!is_array($list) || !is_array($header)) {
            return false;
        }

        // 清除之前的错误输出
        ob_end_clean();
        ob_start();
        // 文件名
        !$filename && $filename = time();
        // xls实例
        $spreadsheet = $this->parse($list, $header, $title);

        // 直接输出下载
        switch ($suffix) {
            case 'xlsx':
                $writer = new Xlsx($spreadsheet);
                header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8;");
                header("Content-Disposition: inline;filename=\"{$filename}.xlsx\"");
                header('Cache-Control: max-age=0');
                $writer->save('php://output');
                exit();

                break;
            case 'xls':
                $writer = new Xls($spreadsheet);
                header("Content-Type:application/vnd.ms-excel;charset=utf-8;");
                header("Content-Disposition:inline;filename=\"{$filename}.xls\"");
                header('Cache-Control: max-age=0');
                $writer->save('php://output');
                exit();

                break;
            case 'csv':
                $writer = new Csv($spreadsheet);
                header("Content-type:text/csv;charset=utf-8;");
                header("Content-Disposition:attachment; filename={$filename}.csv");
                header('Cache-Control: max-age=0');
                $writer->save('php://output');
                exit();

                break;
            case 'html':
                $writer = new Html($spreadsheet);
                header("Content-Type:text/html;charset=utf-8;");
                header("Content-Disposition:attachment;filename=\"{$filename}.{$suffix}\"");
                header('Cache-Control: max-age=0');
                $writer->save('php://output');
                exit();

                break;
        }

        return true;
    }

    /**
     * 分析xls
     *
     * @see exportData
     * @param string $title     标题
     * @param array  $list      数据列表
     * @param array  $header    表格头部列表
     * @return \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    public function parse(array $list, array $header, $title = '')
    {
        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $centerStyle = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        // 初始化
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // 写入标题
        if (!empty($title)) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex(1) . '1', $title)->getStyle(Coordinate::stringFromColumnIndex(1) . '1')->applyFromArray($centerStyle);
            // 处理标题样式
            $sheet->mergeCells('A1:' . $this->cols[1 + count($header)] . '1');
            $sheet->setCellValue('A1', $title)->getStyle('A1')->applyFromArray($centerStyle);
        }

        // 写入头部
        $hk = 1;
        foreach ($header as $k => $v) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($hk) . '1', $v[0])->getStyle(Coordinate::stringFromColumnIndex($hk) . '1', $v[0])->applyFromArray($styleArray);

            $col = Coordinate::stringFromColumnIndex($hk, $v[0]);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $hk += 1;
        }

        // 开始写入内容
        $column = 2;
        $size = ceil(count($list) / 500);
        for ($i = 0; $i < $size; $i++) {
            $buffer = array_slice($list, $i * 500, 500);

            foreach ($buffer as $k => $row) {
                $span = 1;
                foreach ($header as $key => $value) {
                    // 解析字段
                    $realData = $this->formatting($header[$key], trim($this->formattingField($row, $value[1])), $row);
                    // 写入excel
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($span) . $column, $realData)->getStyle(Coordinate::stringFromColumnIndex($span) . $column)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);
                    $hk += 1;
                    $span++;
                }

                $column++;
                unset($buffer[$k]);
            }
        }

        return $spreadsheet;
    }

    /**
     * 格式化内容
     *
     * @param array $array  规则
     * @param mixed $value  值
     * @param array $row    行值
     * @return false|mixed|null|string 内容值
     */
    protected function formatting(array $array, $value, $row)
    {
        !isset($array[2]) && $array[2] = 'text';
        switch ($array[2]) {
                // 文本
            case 'text':
                return $value;
                break;
                // 日期
            case 'date':
                return !empty($value) ? date($array[3], $value) : null;
                break;
                // 选择框
            case 'selectd':
                return  isset($array[3][$value]) ? $array[3][$value] : null;
                break;
                // 匿名函数
            case 'function':
                return isset($array[3]) ? call_user_func($array[3], $row) : null;
                break;
                // 默认
            case 'num':
                if ($value) {
                    return number_format($value, 2);
                } else {
                    return $value;
                }
                break;
            default:
                break;
        }

        return null;
    }

    /**
     * 解析字段
     *
     * @param $row   array   数据集
     * @param $field string  字段
     * @return mixed
     */
    protected function formattingField($row, $field)
    {
        $newField = explode('.', $field);
        if (count($newField) == 1) {
            return $row[$field];
        }

        foreach ($newField as $item) {
            if (isset($row[$item])) {
                $row = $row[$item];
            } else {
                break;
            }
        }

        return is_array($row) ? false : $row;
    }
}
