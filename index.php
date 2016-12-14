<?php
/**
 * Точка входа
 */

include 'bootstrap.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);



$file = new FileJson('data.json');
new ImportDataInDb($file->getData());



