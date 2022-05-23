<?php

namespace App\Helpers;

use Illuminate\Support\Arr;
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
        $selected_columns_string = implode(',', $selected_columns);
        $where_conditions_string = implode(' = ? ,', array_keys($where_conditions));
        $sql_statement = 'SELECT ' . $selected_columns_string . ' FROM ' . $table .' '. $where_conditions_string;
        $sql_statement = $this->conection->prepare($sql_statement);
        foreach (array_values($where_conditions) as $value) {
            $type = $this->bind_types[gettype($value)];
            $sql_statement->bind_params($type, $value);
        }

        $sql_statement->execute();
        $result = $sql_statement->get_result();
        $rows = $result->fetch_assoc();
        return collect($rows);
    }
}
