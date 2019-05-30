<?php
/**
 * 数据层基类
 *
 * @package    EntBoss
 * @copyright  Copyright (c) 2019 EntBoss (http://www.entboss.com)
 * @license    http://www.entboss.com/license
 * @author     EntBoss Team
 * @version    3.0
 *
 */

namespace Modules\Core\Base;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public $params;
    public $err;

    //不自动更新时间
    public $timestamps = false;
    //自定义数据表的主键
    //public $primaryKey = 'id';
    //主键不自动增加
    //public $incrementing = false;
    //字段黑名单
    protected $guarded = [];

    /**
     * 设置错误
     *
     * @param $error_msg  string          错误的内容
     * @param $code
     *
     * @return array
     */
    public function setError($error_msg = '参数不完整', $code = -1000)
    {
        if (is_array($error_msg)) {
            $this->err = $error_msg;

            return $this->err;
        }
        $this->err = [
            'msg'  => $error_msg,
            'code' => $code
        ];

        return $this->err;
    }

    /**
     * 获取错误消息
     *
     * @return string
     */
    public function getErrorMsg()
    {
        return $this->err['msg'];
    }

    /**
     * 获取错误代码
     *
     * @return string
     */
    public function getErrorCode()
    {
        return $this->err['code'];
    }


    /**
     * 获取错误消息并清除错误
     *
     * @return string
     */
    public function getErrorMsgAndFlushError()
    {
        $err_msg = $this->err['msg'];
        $this->flushError();

        return $err_msg;
    }


    /**
     * 判断是否有错误
     *
     * @return bool
     */
    public function hasError()
    {
        return !empty($this->err);
    }

    /**
     * 清空错误信息
     */
    public function flushError()
    {
        $this->err = null;
    }

    /**
     * 根据主键id获取数据对象
     *
     * @access public
     *
     * @param mixed $id 主键id
     *
     * @param array $fields
     *
     * @return object 数据对象
     */
    public static function getItemById($id, $fields = ['*'])
    {
        $wheres = [
            [
                'id',
                '=',
                $id
            ],
        ];

        return self::getItemByWhereBase($fields, $wheres);
    }

    /**
     * 根据查询条件获取信息
     *
     * @param $wheres
     * @param $fields
     *
     * @return object
     */
    public static function getInfo($fields = ['*'], $wheres = [])
    {
        return self::getItemByWhereBase($fields, $wheres);
    }

    /**
     * 根据字段获取数据对象
     *
     * @access public
     *
     * @param mixed $field 字段
     * @param mixed $value 值
     *
     * @return object 数据对象
     */
    public static function getItemByField($field, $value)
    {
        $fields = ['*'];
        $wheres = [
            [
                $field,
                '=',
                $value
            ],
        ];

        return self::getItemByWhereBase($fields, $wheres);
    }

    /**
     * 根据查询条件获取数据对象
     *
     * @access protected
     *
     * @param mixed  $fields 字段列表
     * @param mixed  $wheres where语句, $wheres = [ ['name', '=', 'my name', 'or'] ];
     * @param mixed  $sorts  orderBy语句, $sorts = [ ['id', 'desc'], ['name', 'asc'] ];
     * @param mixed  $joins  leftJoin语句, $joins = [ ['product_desc', 'product_desc.product_id', '=', 'product.id'] ];
     * @param string $alias  数据表别称
     * @param mixed  $withs  with 预加载, $withs = ['product_desc', 'parent'];
     * @param string $group  groupBy语句， $group = ['store_id'];
     * @param mixed  $having having语句, $having = [['account_id', '>', 100]]
     *
     * @return object 数据对象
     */
    protected static function getItemByWhereBase($fields = ['*'], $wheres = [], $sorts = [], $joins = [], $alias = '', $withs = [], $group = [], $having = [])
    {
        $type = 'item';

        return self::getWhereBase($type, $fields, $wheres, $sorts, $joins, $alias, $withs, 0, $group, $having);
    }

    /**
     * 根据查询条件获取信息
     *
     * @param $condition
     * @param $field
     *
     * @return collection 数据集合
     */
    public static function getModelList($fields = ['*'], $wheres = [])
    {
        return self::getListBase($fields, 0, $wheres);
    }

    /**
     * 根据查询条件获取信息
     *
     * @param $condition
     * @param $field
     *
     * @return collection 数据集合
     */
    public static function getHandleMultiList($fields, $wheres = [])
    {
        $list   = objectToArray(self::getListBase($fields, 0, $wheres));

        if (count($fields) == 2) {
            $list = value_to_key($list, $fields[0], $fields[1]);
        } else {
            $list = value_to_key($list, $fields[0]);
        }

        return $list;
    }

    /**
     * 根据条件获取指定数量和排序的数据集合
     *
     * @access protected
     *
     * @param mixed   $fields 字段列表
     * @param integer $limit  指定数量
     * @param mixed   $wheres where语句, $wheres = [ ['name', '=', 'my name', 'or'] ];
     * @param mixed   $sorts  orderBy语句, $sorts = [ ['id', 'desc'], ['name', 'asc'] ];
     * @param mixed   $joins  leftJoin语句, $joins = [ ['product_desc', 'product_desc.product_id', '=', 'product.id'] ];
     * @param string  $alias  数据表别称
     * @param mixed   $withs  with 预加载, $withs = ['product_desc', 'parent'];
     * @param string  $group  groupBy语句， $group = ['store_id'];
     * @param mixed   $having having语句, $having = [['account_id', '>', 100]]
     *
     * @return collection 数据集合
     */
    protected static function getListBase($fields = ['*'], $limit = 0, $wheres = [], $sorts = [], $joins = [], $alias = '', $withs = [], $group = [], $having = [])
    {
        $type = 'top';

        return self::getWhereBase($type, $fields, $wheres, $sorts, $joins, $alias, $withs, $limit, $group, $having);
    }

    /**
     * 根据条件获取指定分页和排序的数据集合
     *
     * @access protected
     *
     * @param mixed   $fields    字段列表
     * @param integer $page_size 分页数量
     * @param mixed   $wheres    where语句, $wheres = [ ['name', '=', 'my name', 'or'] ];
     * @param mixed   $sorts     orderBy语句, $sorts = [ ['id', 'desc'], ['name', 'asc'] ];
     *
     * @return collection 数据集合
     */
    public static function getList($fields = ['*'], $page_size = 0, $wheres = [], $sorts = [])
    {
        return self::getListByPageBase($fields, $page_size, $wheres, $sorts);
    }

    /**
     * 根据条件获取指定分页和排序的数据集合
     *
     * @access protected
     *
     * @param mixed   $fields    字段列表
     * @param integer $page_size 分页数量
     * @param mixed   $wheres    where语句, $wheres = [ ['name', '=', 'my name', 'or'] ];
     * @param mixed   $sorts     orderBy语句, $sorts = [ ['id', 'desc'], ['name', 'asc'] ];
     * @param mixed   $joins     leftJoin语句, $joins = [ ['product_desc', 'product_desc.product_id', '=', 'product.id'] ];
     * @param string  $alias     数据表别称
     * @param mixed   $withs     with 预加载, $withs = ['product_desc', 'parent'];
     * @param string  $group     groupBy语句， $group = ['store_id'];
     * @param mixed   $having    having语句, $having = [['account_id', '>', 100]]
     *
     * @return collection 数据集合
     */
    protected static function getListByPageBase($fields = ['*'], $page_size = 0, $wheres = [], $sorts = [], $joins = [], $alias = '', $withs = [], $group = [], $having = [])
    {
        $type = 'pager';

        return self::getWhereBase($type, $fields, $wheres, $sorts, $joins, $alias, $withs, $page_size, $group, $having);
    }

    public static function getModelCount($wheres)
    {
        return self::getCountBase($wheres);
    }

    /**
     * 根据条件获取指定分页和排序的数据集合
     *
     * @access protected
     *
     * @param mixed  $wheres where语句, $wheres = [ ['name', '=', 'my name', 'or'] ];
     * @param mixed  $joins  leftJoin语句, $joins = [ ['product_desc', 'product_desc.product_id', '=', 'product.id'] ];
     * @param string $alias  数据表别称
     *
     * @return int 数量
     */
    protected static function getCountBase($wheres = [], $joins = [], $alias = '')
    {
        $type = 'count';

        return self::getWhereBase($type, ['*'], $wheres, [], $joins, $alias);
    }

    /**
     * 根据条件获取指定数量或分页的数据集合
     *
     * @access private
     *
     * @param mixed  $type   item/top/pager
     * @param mixed  $fields 字段列表 ['product.id', 'product_desc.name']
     * @param mixed  $wheres where语句, $wheres = [ ['name', '=', 'my name', 'or'] ];
     * @param mixed  $sorts  orderBy语句, $sorts = [ ['id', 'desc'], ['name', 'asc'] ];
     * @param mixed  $joins  leftJoin语句, $joins = [ ['product_desc', 'product_desc.product_id', '=', 'product.id'] ];
     * @param string $alias  表名的别称
     * @param mixed  $withs  with 预加载, $withs = ['product_desc', 'parent'];
     * @param mixed  $num    指定数量
     * @param mixed  $group  groupBy语句, $group = ['store_id']
     * @param mixed  $having having语句, $having = [['account_id', '>', 100]]
     *
     * @return mixed|collection 数据集合
     */
    private static function getWhereBase($type, $fields = ['*'], $wheres = [], $sorts = [], $joins = [], $alias = '', $withs = [], $num = 0, $group = [], $having = [])
    {
        $list = self::buildQuery($fields, $wheres, $sorts, $joins, $alias, $withs, $group, $having);

        switch ($type) {
            case 'item':
                $list = $list->first();
                break;
            case 'pager':
                $page_size = $num > 0 ? $num : config('global.pagesize');
                $page      = config('global.page');
                $offset    = ($page - 1) * $page_size;

                $list = $list->limit($page_size)->offset($offset)->get();
                break;
            case 'count':
                $list = $list->count();
                break;
            default:
                if ($num > 0) {
                    $list = $list->limit($num);
                }
                $list = $list->get();
                break;
        }

        return $list;
    }

    /**
     * 新增单条记录
     *
     * @access public
     *
     * @param mixed $item  对象或数组
     * @param bool  $retid 是否返回id
     *
     * @return integer 是否成功：成功返回id或true, 失败返回false
     */
    public static function addItem($item, $retid = true)
    {
        if ($retid) {
            return self::insertGetId($item);
        } else {
            return self::insert($item);
        }
    }

    /**
     * 更新指定id的记录
     *
     * @access public
     *
     * @param integer $id   指定id
     * @param mixed   $item 对象或数组
     * @param bool    $obj  是否对象
     *
     * @return bool 是否成功
     */
    public static function editItem($id, $item, $obj = false)
    {
        if ($obj) {
            $arr = objectToArray($item);
        } else {
            $arr = $item;
        }

        return self::where('id', $id)->update($arr);
    }

    /**
     * 更新指定条件的所有记录
     *
     * @access protected
     *
     * @param mixed $wheres where语句, $wheres = [ ['name', '=', 'my name', 'or'] ];
     * @param mixed $item   对象或数组
     * @param bool  $obj    是否对象
     *
     * @return bool 是否成功
     */
    protected static function editByWhereBase($wheres, $item, $obj = false)
    {
        if ($obj) {
            $arr = objectToArray($item);
        } else {
            $arr = $item;
        }
        if ($wheres) {
            $list = self::select();
            $list = self::buildWhere($list, $wheres);

            return $list->update($arr);
        } else {
            return false;
        }
    }

    /**
     * 删除指定ids的记录
     *
     * @access public
     *
     * @param mixed $ids id数组或字符串
     *
     * @return bool 是否成功
     */
    public static function delByIds($ids)
    {
        return self::destroy($ids);
    }

    /**
     * 删除指定条件的所有记录
     *
     * @access protected
     *
     * @param mixed $wheres where语句, $wheres = [ ['name', '=', 'my name', 'or'] ];
     *
     * @return bool 删除成功与否
     */
    protected static function delByWhereBase($wheres)
    {
        if ($wheres) {
            $list = self::select();
            $list = self::buildWhere($list, $wheres);

            return $list->delete();
        } else {
            return false;
        }
    }

    /**
     * 生成Query语句
     *
     * @access private
     *
     * @param mixed  $fields 字段列表 ['product.id', 'product_desc.name']
     * @param mixed  $wheres where语句, $wheres = [ ['name', '=', 'my name', 'or'] ];
     * @param mixed  $sorts  orderBy语句, $sorts = [ ['id', 'desc'], ['name', 'asc'] ];
     * @param mixed  $joins  leftJoin语句, $joins = [ ['product_desc', 'product_desc.product_id', '=', 'product.id'] ];
     * @param string $alias  数据表别名
     * @param mixed  $withs  with 预加载, $withs = ['product_desc', 'parent'];
     * @param mixed  $group  groupBy语句, $group = []
     * @param mixed  $having having语句, $having = [['account_id', '>', 100]]
     *
     * @return object 查询对象
     */
    private static function buildQuery($fields = '*', $wheres = [], $sorts = [], $joins = [], $alias = '', $withs = [], $group = [], $having = [])
    {
        if (isQueryMaster()) {
            $list = self::onWriteConnection()->select($fields);
        } else {
            $list = self::select($fields);
        }
        //别称
        if (!empty($alias)) {
            $list = $list->from((new static())->getTable() . ' as ' . $alias);
        }
        //leftJoin 语句
        for ($i = 0; $i < count($joins); $i++) {
            $list = $list->leftJoin($joins[$i][0], $joins[$i][1], $joins[$i][2], $joins[$i][3]);
        }
        //where 语句
        $list = self::buildWhere($list, $wheres);
        //orederBy 语句
        for ($i = 0; $i < count($sorts); $i++) {
            $list = $list->orderBy($sorts[$i][0], $sorts[$i][1]);
        }
        //with 预加载
        if ($withs) {
            $list = $list->with($withs);
        }

        if ($group) {
            $list = $list->groupBy($group);
        }

        if ($having) {
            foreach ($having as $value) {
                $list = $list->having($value[0], $value[1], $value[2]);
            }
        }

        return $list;
    }

    /**
     * 生成Where语句
     *
     * @access private
     *
     * @param mixed $list   查询对象
     * @param mixed $wheres where语句, $wheres = [ ['name', '=', 'my name', 'or'] ];
     *                      ['function', $para = [], [['name', '=', 'my name', 'or'], ['name', '=', 'my name', 'or']], 'or']
     *
     * @return object 查询对象
     */
    private static function buildWhere($list, $wheres = [])
    {
        if ($wheres) {
            $where_count = count($wheres);
        } else {
            $where_count = 0;
        }
        $wheres = array_values($wheres);
        for ($i = 0; $i < $where_count; $i++) {
            $field   = $wheres[$i][0];
            $operate = $wheres[$i][1];
            $value   = isset($wheres[$i][2]) ? $wheres[$i][2] : '';
            $or      = false;
            if (isset($wheres[$i][3]) && $wheres[$i][3] != '') {
                $tmp = strtolower($wheres[$i][3]);
                if ($tmp == 'or') {
                    $or = true;
                }
            }
            if ($field != '' && $field == 'function') {
                if ($or) {
                    $list = $list->orWhere(function ($query) use ($value) {
                        $query = self::buildWhere($query, $value);
                    });
                }
                else {
                    $list = $list->where(function ($query) use ($value) {
                        $query = self::buildWhere($query, $value);
                    });
                }
            } elseif ($field != '' && $value != '') {
                switch ($operate) {
                    case '':
                        if ($or) {
                            $list = $list->orWhere($field, $value);
                        } else {
                            $list = $list->where($field, $value);
                        }
                        break;
                    case 'like':
                        if ($or) {
                            $list = $list->orWhere($field, 'LIKE', "%" . trim($value) . "%");
                        } else {
                            $list = $list->where($field, 'LIKE', "%" . trim($value) . "%");
                        }
                        break;
                    case 'in':
                        if ($or) {
                            $list = $list->orWhereIn($field, $value);
                        } else {
                            $list = $list->whereIn($field, $value);
                        }
                        break;
                    case 'notin':
                        if ($or) {
                            $list = $list->orWhereNotIn($field, $value);
                        } else {
                            $list = $list->whereNotIn($field, $value);
                        }
                        break;
                    case 'find':
                        if ($or) {
                            $list = $list->orWhereRaw("FIND_IN_SET(?, $field)", [$value]);
                        } else {
                            $list = $list->whereRaw("FIND_IN_SET(?, $field)", [$value]);
                        }
                        break;
                    case 'between':
                        if ($or) {
                            $list = $list->orWhereBetween($field, $value);
                        } else {
                            $list = $list->whereBetween($field, $value);
                        }
                        break;
                    case 'raw':
                        if ($or) {
                            if ($value == '') {
                                $list = $list->orWhereRaw($field);
                            } else {
                                $list = $list->orWhereRaw($field, $value);
                            }
                        } else {
                            if ($value == '') {
                                $list = $list->whereRaw($field);
                                //whereRaw('option_value_id = size_option_value_id')
                            } else {
                                $list = $list->whereRaw($field, $value);
                                //whereRaw('vip_ID > ? and vip_fenshu >= ?',[2,300])
                            }
                        }
                        break;
                    default:
                        if ($or) {
                            $list = $list->orWhere($field, $operate, $value);
                        } else {
                            $list = $list->where($field, $operate, $value);
                        }
                        break;
                }
            }
        }

        return $list;
    }
}
