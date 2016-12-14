<?php

class DB
{
    static $link;
    static $count = 0;

    public static function connect()
    {
        self::$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME)
        or die('No connect');

        mysqli_set_charset(self::$link, 'utf8');
    }
}
