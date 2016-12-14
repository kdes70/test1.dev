<?php

/**
 * @author: kdes70@mail.ru
 * Class ImportDataInDb
 */
class ImportDataInDb
{

    public $file;
    private $category;

    /**
     * ImportDataInDb constructor.
     * @param $fileParser
     */
    public function __construct($fileParser)
    {
        if (!isset($fileParser)) {
            die('Нечего импортировать!');
        }

        $this->file = $fileParser;

        $this->initImport($this->file);

    }


    /**
     * @param $file
     */
    public function initImport($file)
    {
        $this->log('Начинаем импорт');

        $this->_truncateTable(['category', 'news']);

        $this->log('Очистили таблицы');

        $this->log('Начинаем рекурсивный импорт данных');

        $this->_iteratorImport($file);
    }


    /**
     * Рекуривный обход массива для записи данных в баззу
     * @param $array
     * @param array $holder
     * @param null $parent
     * @return array|bool
     */
    private function _iteratorImport($array, $parent = null)
    {
        if (empty($array)) {
            return false;
        }

        foreach ($array as $key => $value) {

            if (isset($value['id'])) {

                $this->_itemCategory($value, $parent);

                // Назначаем id родительской категории
                $this->category = $value['id'];

                if (isset($value['news']) && is_array($value['news'])) {
                    $this->_itemNews($value['news'], $this->category);
                }

                // Если есть подкатегории идем на рекурсию
                if (isset($value['subcategories']) && is_array($value['subcategories'])) {

                    $this->_iteratorImport($value['subcategories'], $this->category);
                }
            }
        }

        return true;
    }

    /**
     * @param $data
     * @param $parent
     * @return mixed|string
     */
    private function _itemCategory($data, $parent)
    {
        return $this->log($this->_importCategory(
            [
                'id'            => $data['id'],
                'name'          => $data['name'],
                'active'        => $data['active'],
                'subcategories' => $parent
            ]
        ));
    }

    /**
     * @param $data
     * @return bool|string
     */
    private function _importCategory($data)
    {
        if (!is_array($data)) {
            return false;
        }

        $sql = "INSERT INTO `category`(`id`, `name`, `active`, `subcategories`) 
          VALUES (" . $this->_formatImportCategoryData($data) . ")";

        $query = mysqlQuery($sql);

        return ($query == true) ? 'Категория №' . $data['id'] . " -  Импорирован" : 'Не удалось!';

    }

    /**
     * @param $param
     * @return string
     */
    private function _formatImportCategoryData($param)
    {
        $data = (int)$param['id'] . ",";
        $data .= "'" . (string)$param['name'] . "',";
        $data .= $this->_isActivate($param['active']) . ",";
        $data .= (int)$param['subcategories'];

        return $data;
    }


    /**
     * @param $item
     * @return bool
     */
    private function _itemNews($item, $category_id)
    {
        if (!is_array($item)) {
            return false;
        }

        foreach ($item as $news) {
            if (is_array($news)) {

                $this->log($this->_importNews($news, $category_id));
            }
        }
    }

    /**
     * @param $message
     * @return mixed|string
     */
    public function log($message)
    {
        return dump($message);
    }


    /**
     * Очищаем таблицы
     * @param $table
     * @return bool|mysqli_result
     */
    private function _truncateTable($table)
    {
        if (is_array($table)) {
            foreach ($table as $item) {
                mysqlQuery('TRUNCATE TABLE ' . $item);
            }
            return true;
        }
        return mysqlQuery('TRUNCATE TABLE ' . $table);
    }

    /**
     * Запись новасти в базу
     * @param $param
     * @return bool|string
     */
    private function _importNews($param, $category_id)
    {
        if (!is_array($param)) {
            return false;
        }

        // dump($category_id);

        $sql = "INSERT INTO `news`(`id`, `category_id`, `active`, `title`, `image`, `description`, `text`, `date`) 
          VALUES (" . $this->_formatImportNewsData($param, $category_id) . ")";

        $query = mysqlQuery($sql);

        return ($query == true) ? 'Новость №' . $param['id'] . " -  Импорирована" : 'Не удалось!';
    }

    /**
     * Форматируем данные новости для вставки
     * @param $param
     * @return string
     */
    private function _formatImportNewsData($param, $category_id)
    {
        $data = (int)$param['id'] . ",";
        $data .= (int)$category_id . ",";
        $data .= $this->_isActivate($param['active']) . ",";
        $data .= "'" . (string)$param['title'] . "',";
        $data .= "'" . (string)$param['image'] . "',";
        $data .= "'" . (string)$param['description'] . "',";
        $data .= "'" . (string)$param['text'] . "',";
        $data .= "STR_TO_DATE('" . $param['date'] . "', '%Y-%m-%d')";

        return $data;
    }

    /**
     * @param bool $param
     * @return int
     */
    private function _isActivate($param)
    {
        return (boolean)($param === true) ? 1 : 0;
    }


}