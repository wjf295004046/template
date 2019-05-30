<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExcelExport implements FromArray, WithHeadings, WithMapping
{
    protected $field_arr = [];
    protected $data = [];

    /**
     * 设置内容
     *
     * @return array
     */
    public function array(): array
    {
        return $this->getData();
    }

    /**
     * 设置标题行
     *
     * @return array
     */
    public function headings(): array
    {
        return $this->getFieldArr();
    }

    /**
     * 字段过滤
     *
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        $field_arr = $this->getFieldArr();
        $new_row   = [];
        foreach ($field_arr as $key) {
            $new_row[$key] = $row[$key] ?? '';
        }

        return $new_row;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setFieldArr($field_arr)
    {
        $this->field_arr = $field_arr;
    }

    public function getFieldArr()
    {
        return $this->field_arr;
    }

    /**
     * 清空数据
     */
    public function flush()
    {
        unset($this->data, $this->field_arr);
        $this->data      = [];
        $this->field_arr = [];
    }
}
