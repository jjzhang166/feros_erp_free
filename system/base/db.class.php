<?php

/**
 * FerOS PHP Framework
 * @author feros<admin@feros.com.cn>
 * @copyright ©2014 feros.com.cn
 * @link http://www.feros.com.cn
 * @version 2.0
 */

namespace base;

/**
 * 装载机
 * @author sanliang
 */
class db extends common {

    protected $query_string, $_query = array();
    protected $primary_key; //主键
    static $pdo = array();
    public $dns = NULL,
            $username = NULL,
            $password = NULL,
            $prefix = NULL,
            $option = array(),
            $cachetime = '',
            $table_name='';

    /**
     * 查询SQL
     * @param string $query
     * @return \PDO\query
     */
    public function query($query) {
        $time = microtime(TRUE);
        $run = $this->connect()->query($query);
        $this->to_sql($query, $time);
        return $run === FALSE ? $this->error() : $run;
    }

    private function error() {
        if ($this->connect()) {
            $error = $this->connect()->errorInfo();
            throw new \Exception($error[1] . ':' . $error[2]);
        }
    }

    /**
     * 执行SQL
     * @param string $query
     * @return \PDO\exec
     */
    public function exec($query) {
        $time = microtime(TRUE);
        $run = $this->connect()->exec($query);
        $this->to_sql($query, $time);
        return $run === FALSE ? $this->error() : $run;
    }

    private function to_sql($query, $time, $type = NULL) {
        $this->query_string = $query;
        $this->query_cleared();
        loader::init('log', 'sql', '驱动:' . strtolower($type? : $this->get_driver()) . ' 时间:' . \feros::run_time($time) . "s\nSQL:\n" . $query);
    }

    private function get_sql($query) {
        $this->query_string = $query;
        $this->query_cleared();
        return $query;
    }
    /**
     * 返回最后一条SQL
     */
    public function get_last_sql(){
        return $this->query_string;
    }

    /**
     * SQL中的字符串添加引号
     * @param string $string
     * @return string
     */
    public function quote($string) {
        return $this->connect()->quote($string);
    }

    /**
     * 查找多条记录
     * @param bool|int $cache 是否缓存或缓存时间
     * @return array|NULLs
     */
    public function fetchs($return = FALSE) {
        $sql = $this->build_query();
        if ($return)
            return $this->get_sql($sql);
        if (!($list = $this->get_cache($sql))) {
            $list = $this->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            if ($this->cachetime)
                $this->set_cache($sql, $list);
        }
        $this->cachetime = '';
        return !empty($list) ? $list : NULL;
    }

    /**
     * 设计缓存
     * @param bool|int $time 是否缓存或缓存时间
     * @return \base\db
     */
    public function cache($time = '') {
        $this->cachetime = $time;
        return $this;
    }

    /**
     * 查找一条记录
     * @return array|NULLs
     */
    public function fetch($pk = NULL, $return = FALSE) {
        if (!empty($pk) && is_string($this->primary_key))
            $this->where(array($this->primary_key => $pk));
        $this->limit(1);
        $sql = $this->build_query();
        if ($return)
            return $this->get_sql($sql);
        if (!($list = $this->get_cache($sql))) {
            $list = $this->query($sql)->fetch(\PDO::FETCH_ASSOC);
            if ($this->cachetime)
                $this->set_cache($sql, $list);
        }
        $this->cachetime = '';
        return !empty($list) ? $list : NULL;
    }

    public function findcolumn() {
        $this->limit(1);
        $sql = $this->build_query();
        if (!($list = $this->get_cache($sql))) {
            $list = $this->query($sql)->fetchColumn();
            if ($this->cachetime)
                $this->set_cache($sql, $list);
        }
        
        $this->cachetime = '';
        return !empty($list) ? $list : NULL;
    }

    private function get_cache($sql) {
        if ($this->cachetime && !is_null($this->cachetime)) {
            $driver = $this->config->database->cache_driver;
            $time = microtime(TRUE);
            $run = $this->cache->driver($driver)->get($sql);
            $this->to_sql($sql, $time, $driver? : 'cache_auto');
            return $run;
        }
        return FALSE;
    }

    private function set_cache($sql, $list) {
        $driver = $this->config->database->cache_driver;
        is_null($this->cachetime) ? $this->cache->driver($driver)->del($sql) : ($this->cache->driver($driver)->set($sql, $list, is_numeric($this->cachetime) ? $this->cachetime : $this->config->database->cache_expire));
    }

    /**
     * 查找一条记录
     * @return array|NULLs
     */
    public function find($pk = NULL, $return = FALSE) {
        return $this->fetch($pk, $return);
    }

    /**
     * 查找多条记录
     * @return array|NULLs
     */
    public function finds($return = FALSE) {
        return $this->fetchs($return);
    }

    /**
     * 新增数据
     * @param array $data 新增的数据
     * @param bool $replace 是否REPLACE
     * @return bool
     * @throws \Exception
     */
    public function insert($data, $replace = FALSE, $return = FALSE) {
        $sql = ($replace ? 'REPLACE' : 'INSERT') . ' INTO ';
        foreach ((array) $data as $key => $value) {
            $fields[] = $this->quote_column_name($key);
            $placeholders[] = $this->quote($value);
        }
        if (!empty($this->_query['from']))
            $sql.=$this->_query['from'];
        else
            throw new \Exception($this->language->database_query_from);
        $sql.=' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $placeholders) . ')';

        return !$return ? ($this->exec($sql) ? $this->insert_id() : FALSE) : $this->get_sql($sql);
    }

    public function insert_batch($data, $return = FALSE) {
        $sql = 'INSERT INTO ';
        foreach ((array) $data as $vars) {
            foreach ((array) $vars as $key => $value) {
                $fields[] = $this->quote_column_name($key);
                $placeholders[] = $this->quote($value);
            }
            $field = ' (' . implode(', ', $fields) . ')';
            $var[] = '(' . implode(', ', $placeholders) . ')';
            unset($fields, $placeholders);
        }
        if (!empty($this->_query['from']))
            $sql.=$this->_query['from'];
        else
            throw new \Exception($this->language->database_query_from);
        $sql.=$field . ' VALUES ' . implode(',', $var);
        return !$return ? $this->exec($sql) : $this->get_sql($sql);
    }

    /**
     * 返回新增主键
     * @return string
     */
    public function insert_id() {
        switch (strtoupper($this->get_driver())) {
            case 'PGSQL':
            case 'SQLITE':
            case 'MSSQL':
            case 'SQLSRV':
            case 'IBASE':
            case 'MYSQL':
                return $this->connect()->lastInsertId();
            case 'ORACLE':
            case 'OCI':
                $vo = $this->query("SELECT {$this->_query['from']}.currval currval FROM dual")->fetch(\PDO::FETCH_ASSOC);
                return $vo ? $vo[0]["currval"] : 0;
        }
    }

    /**
     * 更新数据
     * @param array $data 更新的数据
     * @return bool|int
     * @throws \Exception
     */
    public function update($data, $pk = NULL, $return = FALSE) {
        if (!empty($pk) && is_string($this->primary_key))
            $this->where(array($this->primary_key => $pk));
        foreach ((array) $data as $key => $value) {
            preg_match('/([\w]+)(\[(\+|\-|\*|\/)\])?/i', $key, $match);
            if (isset($match[3])) {
                if (is_numeric($value)) {
                    $fields[] = $this->quote_column_name($match[1]) . ' = ' . $this->quote_column_name($match[1]) . ' ' . $match[3] . ' ' . $value;
                }
            } else {
                $fields[] = $this->quote_column_name($key) . '=' . $this->quote($value);
            }
        }
        if (!empty($this->_query['from']))
            $sql.=$this->_query['from'];
        else
            throw new \Exception($this->language->database_query_from);
        $sql = "UPDATE  {$this->_query['from']} SET " . implode(', ', $fields);
        $this->apply_join($sql);
        $this->apply_condition($sql);
        $this->apply_order($sql);
        $this->apply_limit($sql);
        return !$return ? $this->exec($sql) : $this->get_sql($sql);
    }

    /**
     * 删除数据
     * @return bool|int
     */
    public function delete($pk = NULL, $return = FALSE) {
        if (!empty($pk) && is_string($this->primary_key))
            $this->where(array($this->primary_key => $pk));
        $sql = 'DELETE';
        $this->apply_from($sql);
        $this->apply_join($sql);
        $this->apply_condition($sql);
        $this->apply_group($sql);
        $this->apply_having($sql);
        $this->apply_order($sql);
        $this->apply_limit($sql);
        return !$return ? $this->exec($sql) : $this->get_sql($sql);
    }

    public function count($column = '*') {
        return (int) $this->select("COUNT($column)")->findcolumn();
    }

    public function max($column) {
        return $this->select("MAX($column)")->findcolumn();
    }

    public function min($column) {
        return $this->select("MIN($column)")->findcolumn();
    }

    public function avg($column) {
        return $this->select("AVG($column)")->findcolumn();
    }

    public function sum($column) {
        return $this->select("SUM($column)")->findcolumn();
    }

    public function query_cleared($from=NULL) {
        $from=$from?:$this->_query['from'];
        $this->_query = array();
        $this->_query['from'] = $this->table_name ? $this->quote_table_name(($this->prefix? : $this->config->database->prefix) . $this->table_name) :$from;
    }

    /**
     * 设置查询表
     * @param string $tables
     * @return \base\db
     */
    public function from($tables) {
        if (is_string($tables) && strpos($tables, '(') !== FALSE)
            $this->_query['from'] = $tables;
        else {
            if (!is_array($tables))
                $tables = preg_split('/\s*,\s*/', trim($tables), -1, \PREG_SPLIT_NO_EMPTY);
            foreach ($tables as $i => $table) {
                if (strpos($table, '(') === FALSE) {
                    if (preg_match('/^(.*?)(?i:\s+as\s+|\s+)(.*)$/', $table, $matches))
                        $tables[$i] = $this->quote_table_name(($this->prefix? : $this->config->database->prefix) . $matches[1]) . ' ' . $this->quote_table_name($matches[2]);
                    else
                        $tables[$i] = $this->quote_table_name(($this->prefix? : $this->config->database->prefix) . $table);
                }
            }
            $this->_query['from'] = implode(', ', $tables);
        }
        return $this;
    }

    public function hash_db($u, $s = 10) {
        $h = sprintf("%u", crc32($u));
        $h1 = intval(fmod($h, $s));
        return $h1;
    }

    /**
     * 设置返回字段
     * @param string $columns 字段
     * @param string $option
     * @return \base\db
     */
    public function select($columns = '*', $option = '') {
        if (is_string($columns) && strpos($columns, '(') !== FALSE)
            $this->_query['select'] = $columns;
        else {
            if (!is_array($columns))
                $columns = preg_split('/\s*,\s*/', trim($columns), -1, \PREG_SPLIT_NO_EMPTY);

            foreach ($columns as $i => $column) {
                if (is_object($column))
                    $columns[$i] = (string) $column;
                elseif (strpos($column, '(') === FALSE) {
                    if (preg_match('/^(.*?)(?i:\s+as\s+|\s+)(.*)$/', $column, $matches))
                        $columns[$i] = $this->quote_column_name($matches[1]) . ' AS ' . $this->quote_column_name($matches[2]);
                    else
                        $columns[$i] = $this->quote_column_name($column);
                }
            }
            $this->_query['select'] = implode(', ', $columns);
        }
        if ($option != '')
            $this->_query['select'] = $option . ' ' . $this->_query['select'];
        return $this;
    }

    public function set_join($value) {
        $this->_query['join'] = $value;
    }

    public function join($table, $where) {
        return $this->inner_join('join', $table, $where);
    }

    public function inner_join($table, $where) {
        return $this->join_internal('inner join', $table, $where);
    }

    public function left_join($table, $where) {
        return $this->join_internal('left join', $table, $where);
    }

    public function right_join($table, $where) {
        return $this->join_internal('right join', $table, $where);
    }

    public function full_join($table, $where) {
        return $this->join_internal('full join', $table, $where);
    }

    /**
     * 新增查询条件 AND
     * @param array|string $where
     * @return \base\db
     */
    public function where($where) {
        $this->where_and($where);
        return $this;
    }

    /**
     * 新增查询条件 OR
     * @param array|string $where
     * @return \base\db
     */
    public function where_or($where) {
        $this->_query['where']['or'][] = $this->condition($where, 'or');
        return $this;
    }

    /**
     * 新增查询条件 AND
     * @param array|string $where
     * @return \base\db
     */
    public function where_and($where) {
        $this->_query['where']['and'][] = $this->condition($where, 'and');
        return $this;
    }

    public function group($columns) {
        if (is_string($columns) && strpos($columns, '(') !== FALSE)
            $this->_query['group'] = $columns;
        else {
            if (!is_array($columns))
                $columns = preg_split('/\s*,\s*/', trim($columns), -1, \PREG_SPLIT_NO_EMPTY);
            foreach ($columns as $i => $column) {
                if (is_object($column))
                    $columns[$i] = (string) $column;
                elseif (strpos($column, '(') === FALSE)
                    $columns[$i] = $this->quote_column_name($column);
            }
            $this->_query['group'] = implode(', ', $columns);
        }
        return $this;
    }

    public function order($columns) {
        if (is_string($columns) && strpos($columns, '(') !== FALSE)
            $this->_query['order'] = $columns;
        else {
            if (!is_array($columns))
                $columns = preg_split('/\s*,\s*/', trim($columns), -1, \PREG_SPLIT_NO_EMPTY);
            foreach ($columns as $i => $column) {
                if (is_object($column))
                    $columns[$i] = (string) $column;
                elseif (strpos($column, '(') === FALSE) {
                    if (preg_match('/^(.*?)\s+(asc|desc)$/i', $column, $matches))
                        $columns[$i] = $this->quote_column_name($matches[1]) . ' ' . strtoupper($matches[2]);
                    else
                        $columns[$i] = $this->quote_column_name($column);
                }
            }
            $this->_query['order'] = implode(', ', $columns);
        }
        return $this;
    }

    public function limit($limit, $offset = NULL) {
        if ($limit > 0)
            $this->_query['limit'] = (int) $limit;
        if ($offset !== NULL)
            $this->offset($offset);
        return $this;
    }

    public function offset($offset) {
        if ($offset > 0)
            $this->_query['offset'] = (int) $offset;
        return $this;
    }

    public function union($sql) {
        if (isset($this->_query['union']) && is_string($this->_query['union']))
            $this->_query['union'] = array($this->_query['union']);
        $this->_query['union'][] = $sql;

        return $this;
    }

    public function distinct($columns = '*') {
        $this->_query['distinct'] = true;
        return $this->select($columns);
    }

    public function build_query() {
        $sql = !empty($this->_query['distinct']) ? 'SELECT DISTINCT' : 'SELECT';
        $this->apply_select($sql);
        $this->apply_from($sql);
        $this->apply_join($sql);
        $this->apply_condition($sql);
        $this->apply_group($sql);
        $this->apply_having($sql);
        $this->apply_order($sql);
        $this->apply_limit($sql);
        $this->apply_union($sql);
        //echo $sql;
        return $sql;
    }

    public function apply_from(&$sql) {
        if (!empty($this->_query['from']))
            $sql.=" FROM " . $this->_query['from'];
        else
            throw new \Exception($this->language->database_query_from);
        return $sql;
    }

    public function apply_select(&$sql) {
        $sql.=" " . (!empty($this->_query['select']) ? $this->_query['select'] : '*');
        return $sql;
    }

    public function apply_join(&$sql) {
        if (!empty($this->_query['join']))
            $sql.=" " . (is_array($this->_query['join']) ? implode(" ", $this->_query['join']) : $this->_query['join']);
        return $sql;
    }

    public function apply_condition(&$sql) {
        if (!empty($this->_query['where'])) {
            $where = array();
            foreach ($this->_query['where'] as $key => $value) {
                $type = gettype($value);
                if (preg_match("/^(AND|OR)\s*#?/i", $key, $match) && $type == 'array') {
                    $where[] = '(' . implode(' ' . strtoupper($match[1]) . ' ', $value) . ')';
                }
            }
            if (!empty($where))
                $sql.=" WHERE " . implode(' AND ', $where);
        }

        return $sql;
    }

    public function apply_order(&$sql) {
        if (!empty($this->_query['order']))
            $sql.=" ORDER BY {$this->_query['order']}";
        return $sql;
    }

    public function apply_limit(&$sql) {
        if (!empty($this->_query['limit']))
            $sql.= " LIMIT {$this->_query['limit']}";
        if (!empty($this->_query['offset']))
            $sql.=" OFFSET {$this->_query['offset']}";
        return $sql;
    }

    public function apply_group(&$sql) {
        if (!empty($this->_query['group']))
            $sql.=" GROUP BY {$this->_query['group']}";
        return $sql;
    }

    public function apply_having(&$sql) {
        if (!empty($this->_query['having']))
            $sql.=" HAVING {$this->_query['having']}";
        return $sql;
    }

    public function apply_union(&$sql) {
        if (!empty($this->_query['union']))
            $sql.=" UNION ( " . (is_array($this->_query['union']) ? implode(" ) UNION ( ", $this->_query['union']) : $this->_query['union']) . ')';
        return $sql;
    }

    public function info() {
        $output = array('server' => 'SERVER_INFO', 'driver' => 'DRIVER_NAME', 'client' => 'CLIENT_VERSION', 'version' => 'SERVER_VERSION', 'connection' => 'CONNECTION_STATUS');
        foreach ($output as $key => $value) {
            $output[$key] = $this->connect()->getAttribute(constant('\PDO::ATTR_' . $value));
        }return $output;
    }

    public function array_quote($array) {
        $temp = array();
        foreach ($array as $value)
            $temp[] = is_int($value) ? $value : $this->quote($value);
        return implode($temp, ',');
    }

    public function inner_conjunct($data, $conjunctor, $outer_conjunctor) {
        $haystack = array();
        foreach ($data as $value) {
            $haystack[] = '(' . $this->data_implode($value, $conjunctor) . ')';
        }return implode($outer_conjunctor . ' ', $haystack);
    }

    public function column_quote($string) {
        return '"' . str_replace('.', '"."', preg_replace('/(^#|\(JSON\))/', '', $string)) . '"';
    }

    public function condition($data, $conjunctor = 'and') {
        if (is_string($data))
            return $data;

        foreach ((array) $data as $key => $value) {
            $type = gettype($value);
            if (preg_match("/^(AND|OR)\s*#?/i", $key, $relation_match) && $type == 'array') {
                $wheres[] = 0 !== count(array_diff_key($value, array_keys(array_keys($value)))) ? '(' . $this->condition($value, ' ' . $relation_match[1]) . ')' : '(' . $this->inner_conjunct($value, ' ' . $relation_match[1], $conjunctor) . ')';
            } else {
                preg_match('/(%?)([a-zA-Z0-9_\-\.]*)(%?)((\[!\])?)/', $key, $match);
                if (!empty($match[1]) || !empty($match[3])) {
                    $wheres[] = $this->quote_column_name($match[2]) . ($match[4] != '' ? ' NOT' : '') . ' LIKE ' . $this->quote($match[1] . $value . $match[3]);
                } else {
                    preg_match('/(#?)([\w\.]+)(\[(\>|\>\=|\<|\<\=|\!|\<\>|\>\<)\])?/i', $key, $match);
                    $column = $this->quote_column_name($match[2]);
                    if (isset($match[4])) {
                        if ($match[4] == '') {
                            $wheres[] = $column . ' ' . $match[4] . '= ' . $this->quote($value);
                        } elseif ($match[4] == '!') {
                            switch ($type) {
                                case 'NULL':
                                    $wheres[] = $column . ' IS NOT NULL';
                                    break;
                                case 'array':
                                    $wheres[] = $column . ' NOT IN (' . $this->array_quote($value) . ')';
                                    break;
                                case 'integer':
                                case 'double':
                                    $wheres[] = $column . ' != ' . $value;
                                    break;
                                case 'boolean':
                                    $wheres[] = $column . ' != ' . ($value ? '1' : '0');
                                    break;
                                case 'string':
                                    $wheres[] = $column . ' != ' . $this->quote($value);
                                    break;
                            }
                        } else {
                            if ($match[4] == '<>' || $match[4] == '><') {
                                if ($type == 'array') {
                                    if ($match[4] == '><') {
                                        $column.=' NOT';
                                    }if (is_numeric($value[0]) && is_numeric($value[1])) {
                                        $wheres[] = '(' . $column . ' BETWEEN ' . $value[0] . ' AND ' . $value[1] . ')';
                                    } else {
                                        $wheres[] = '(' . $column . ' BETWEEN ' . $this->quote($value[0]) . ' AND ' . $this->quote($value[1]) . ')';
                                    }
                                }
                            } else {
                                if (is_numeric($value)) {
                                    $wheres[] = $column . ' ' . $match[4] . ' ' . $value;
                                } else {
                                    $datetime = strtotime($value);
                                    if ($datetime) {
                                        $wheres[] = $column . ' ' . $match[4] . ' ' . $this->quote(date('Y-m-d H:i:s', $datetime));
                                    }
                                }
                            }
                        }
                    } else {
                        if (is_int($key)) {
                            $wheres[] = $this->quote($value);
                        } else {
                            switch ($type) {
                                case 'NULL':
                                    $wheres[] = $column . ' IS NULL';
                                    break;
                                case 'array':
                                    $wheres[] = $column . ' IN (' . $this->array_quote($value) . ')';
                                    break;
                                case 'integer':
                                case 'double':
                                    $wheres[] = $column . ' = ' . $value;
                                    break;
                                case 'boolean':
                                    $wheres[] = $column . ' = ' . ($value ? '1' : '0');
                                    break;
                                case 'string':
                                    $wheres[] = $column . ' = ' . $this->quote($value);
                                    break;
                            }
                        }
                    }
                }
            }
        }

        return implode(' ' . strtoupper($conjunctor) . ' ', $wheres);
    }

    private function join_internal($type, $table, $where) {
        if (strpos($table, '(') === FALSE) {
            if (preg_match('/^(.*?)(?i:\s+as\s+|\s+)(.*)$/', $table, $matches))  // with alias
                $table = $this->quote_table_name(($this->prefix? : $this->config->database->prefix) . $matches[1]) . ' ' . $this->quote_table_name($matches[2]);
            else
                $table = $this->quote_table_name(($this->prefix? : $this->config->database->prefix) . $table);
        }

        $conditions = $this->condition($where);

        if ($conditions != '')
            $conditions = ' ON ' . $conditions;

        if (isset($this->_query['join']) && is_string($this->_query['join']))
            $this->_query['join'] = array($this->_query['join']);

        $this->_query['join'][] = strtoupper($type) . ' ' . $table . $conditions;
        return $this;
    }

    public function quote_column_name($key) {
        $k = '';
        foreach (explode('.', $key) as $value) {
            if (is_numeric($value) || preg_match('/[,\'\"\*\(\)`.\s]/', $value))
                $k = !empty($k) ? $k . ".{$value}" : "{$value}";
            else {
                switch (strtolower($this->get_driver())) {
                    case 'mysql':
                    case 'sqlite':
                        $k = !empty($k) ? $k . ".`{$value}`" : "`{$value}`";
                        break;
                    case 'mssql':
                        $k = !empty($k) ? $k . ".[{$value}]" : "[{$value}]";
                        break;
                    default:
                        break;
                }
            }
        }

        return $k;
    }

    public function quote_table_name($table) {
        switch (strtolower($this->get_driver())) {
            case 'mysql':
            case 'sqlite':
                $table = "`{$table}`";
                break;
            case 'mssql':
                $table = "[{$table}]";
                break;
            default:
                break;
        }
        return $table;
    }

    public function get_driver() {
        return $this->connect()->getAttribute(\PDO::ATTR_DRIVER_NAME);
    }

    public function connect() {
        try {
            $dns = $this->dns? : $this->config->database->dns;
            if (empty(self::$pdo[$dns]))
                self::$pdo[$dns] = new \PDO($dns, $this->username? : $this->config->database->username, $this->password? : $this->config->database->password, $this->option? : $this->config->database->option);
            return self::$pdo[$dns];
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
