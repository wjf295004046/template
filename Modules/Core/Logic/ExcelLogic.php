<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/7
 * Time: 9:57
 */

namespace Modules\Core\Logic;

use App\Exports\ExcelExport;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Base\BaseLogic;

class ExcelLogic extends BaseLogic
{
    public function __construct()
    {
        ini_set('memory_limit', '2048M');

        parent::__construct();
    }

    /**
     * 读取excel、csv文件
     *
     * @param           $filename
     * @param           $field_arr
     * @param int       $sheet_index
     * @param int       $start_row 开始行数 默认第二行开始
     * @param string    $disk
     *
     * @return array
     */
    public function read($filename, $field_arr, $sheet_index = 0, $start_row = 2, $disk = 'file')
    {
        try {
            $Import = new ExcelImport();
            $Import->setFieldArr($field_arr);
            //设置开始行数
            $Import->setStartRow($start_row);

            $result = Excel::toArray($Import, $filename, $disk);

            $sheet_num = count($result);

            if (is_array($sheet_index)) {
                $new_result = [];
                foreach ($sheet_index as $index) {
                    if ($index >= $sheet_num) {
                        continue;
                    }

                    $new_result[$index] = (array) $result[$index];
                }

                return $new_result;
            } else {
                if ($sheet_index >= $sheet_num) {
                    throw new \Exception('选择的sheet页不存在');
                }

                return $result[$sheet_index];
            }
        } catch (\Exception $e) {
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 直接导出文件 多格式
     *
     * @param        $data
     * @param        $field_arr
     * @param        $filename
     * @param string $suffix
     *
     * @return array
     */
    public function export($data, $field_arr, $filename, $suffix = 'xlsx')
    {
        try {
            $Export = new ExcelExport();
            $Export->setFieldArr($field_arr);
            $Export->setData($data);

            $writer_type = $this->getWriteType($suffix);
            $filename    .= date('YmdHis') . '.' . $suffix;

            header("Content-type:application/octet-stream");
            header("Content-Disposition:filename=" . $filename);
            echo Excel::raw($Export, $writer_type);
            exit;
        } catch (\Exception $e) {
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 导出并保存文件  用于异步
     *
     * @param        $data
     * @param        $field_arr
     * @param        $filename
     * @param string $suffix
     * @param string $disk
     *
     * @return array|string
     */
    public function storeExport($data, $field_arr, $filename, $suffix = 'xlsx', $disk = 'file')
    {
        try {
            $Export = new ExcelExport();
            $Export->setFieldArr($field_arr);
            $Export->setData($data);

            $writer_type = $this->getWriteType($suffix);
            $filename    .= date('YmdHis');
            $filename    = getUploadFilePath('Excel') . $filename . '.' . $suffix;

            Excel::store($Export, $filename, 'file', $writer_type);

            return $filename;
        } catch (\Exception $e) {
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 获取writeType
     *
     * @param $suffix
     *
     * @return string
     */
    public function getWriteType($suffix)
    {
        return config('excel.extension_detector')[$suffix] ?? 'xlsx';
    }
}