<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ExcelImport implements ToArray, WithMapping, WithStartRow, WithCalculatedFormulas,WithCustomCsvSettings
{
    protected $field_arr = [];
    protected $start_row = 1;

    /**
     * 返回结果
     *
     * @param array $array
     *
     * @return array
     */
    public function array(array $array)
    {
        return $array;
    }

    /**
     * 键值对匹配
     *
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        $field_arr = $this->getFieldArr();

        if (empty($field_arr)) {
            return $row;
        }

        $count = count($field_arr);
        $row   = array_slice($row, 0, $count);

        return array_combine($field_arr, $row);
    }

    /**
     * 开始行数从1开始
     *
     * @return int
     */
    public function startRow(): int
    {
        $start_row = $this->start_row;

        return $start_row;
    }

    /**
     * 设置csv属性
     * delimiter,enclosure,line_ending,use_bom,include_separator_line,excel_compatibility,escape_character,contiguous,input_encoding
     *
     * @return array
     */
    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'GBK'
        ];
    }

    public function setFieldArr($field_arr)
    {
        $this->field_arr = $field_arr;
    }

    public function getFieldArr()
    {
        return $this->field_arr;
    }

    public function setStartRow($start_row)
    {
        $this->start_row = (int) $start_row;
    }

    public function getStartRow()
    {
        return $this->start_row;
    }
}
