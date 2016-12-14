<?php

/**
 * Функция обработки литеральных констант для SQL
 */
function escapeString($data)
{
    if(!isset(DB::$link))
        DB::conhect();

    if(is_array($data))
        $data = array_map("escapeString", $data);
    else
        $data = mysqli_real_escape_string(DB::$link, $data);

    return $data;
}


/**
 * Функция для запроса к БД MySQL.
 */
function mysqlQuery($sql, $print = false)
{
    if(!isset(DB::$link))
        DB::connect();

    DB::$count++;

    $result = mysqli_query(DB::$link, $sql);

    if($result === false || $print)
    {
        $error =  mysqli_error(DB::$link);
        $trace =  debug_backtrace();

        $head = !empty($error) ?'<b style="color:red">MySQL error: </b><br> 
            <b style="color:green">'. $error .'</b><br><br>':NULL;

        $error_log = date("Y-m-d h:i:s") .' '. $head .' 
            <b>Query: </b><br> 
            <pre><span style="color:#CC0000">'. $trace[0]['args'][0] .'</pre></span><br><br>
            <b>File: </b><b style="color:#660099">'. $trace[0]['file'] .'</b><br> 
            <b>Line: </b><b style="color:#660099">'. $trace[0]['line'] .'</b>';

        /**
         * @TODO To clean in release
         */
//-----------------------------
        die($error_log);
//-----------------------------
    }
    else
        return $result;
}
