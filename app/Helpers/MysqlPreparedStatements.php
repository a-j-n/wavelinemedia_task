<?php

namespace App\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use \Log;
use mysqli;

class MysqlPreparedStatements
{
    public array $bind_types = [
        'integer' => 'i',
        'double' => 'd',
        'string' => 's',
        'BLOB' => 'b'
    ];
    public $conection;

    public function __construct()
    {
        $servername = env('DB_HOST');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $dbname = env('DB_DATABASE');

        $this->conection = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($this->conection->connect_error) {
            die("Connection failed: " . $this->conection->connect_error);
        }
    }

    /**
     * @param $table
     * @param array $selected_columns example [name,age ,salary]
     * @param array $where_conditions example ['name' => 'ahmed']
     * @return void
     */
    public function whereStatment($table, array $selected_columns, array $where_conditions)
    {

        $table = strip_tags($table);
        $selected_columns = Arr::map($selected_columns, function ($item) {
            return strip_tags($item);
        });
        $where_conditions = Arr::map($where_conditions, function ($item) {
            return strip_tags($item);
        });

        $cache_key = $this->getCacheKey($table, $selected_columns, $where_conditions);
        $consider_cache = $this->considerCache($cache_key);
        if ($consider_cache) {
            $cache = $this->checkCache($cache_key);
            if ($cache) {
                return $cache;
            }
        }

        $selected_columns_string = implode(',', $selected_columns);
        $where_conditions_string = implode(' = ? ,', array_keys($where_conditions));
        $query_string = 'SELECT ' . $selected_columns_string . ' FROM ' . $table . ' ' . $where_conditions_string;
        $sql_statement = $this->conection->prepare($query_string);

        foreach (array_values($where_conditions) as $value) {
            $type = $this->bind_types[gettype($value)];
            $sql_statement->bind_params($type, $value);
        }

        $sql_statement->execute();
        $result = $sql_statement->get_result();
        $rows = $result->fetch_assoc();
        $result_collect = collect($rows);
        $this->setToCache($cache_key, $result_collect);
        return $result_collect;
    }


    public function getCacheKey(string $table, $selected_columns, $where_conditions): string
    {

        $cache_key = trim($table) . implode($selected_columns, '_');
        $where_conditions_string = '';
        foreach ($where_conditions as $k => $v) {
            $where_conditions_string .= (string)$k . '_' . (string)$v;
        }
        $cache_key = $cache_key . $where_conditions_string;

        return $cache_key;

    }

    public function considerCache($cache_key)
    {
        //consider max key limit
        $string_size = mb_strlen($cache_key);
        if ($string_size > env('MAX_MEMCACHED_KEY_SIZE')) {
            Log::info('cant cache : ' . $cache_key);
            return false;
        }
        return true;
    }

    public function checkCache($cache_key)
    {
        if (Cache::store('memcached')->has($cache_key)) {
            return Cache::store('memcached')->get($cache_key);
        }
        return false;
    }

    public function setToCache($cache_key, $value)
    {
        if ($this->considerCache($cache_key)) {
            return Cache::store('memcached')->put($cache_key, $value, env('DEFAULT_QUERY_CACHE_TIME'));
        }
        return false;
    }


}
