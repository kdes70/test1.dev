<?php


/**
 * Класс получает путь к файлу, проверяет на расширение, распарсивает содержимое
 * ! ДА можно еще добавить кучу проверок на MINI, проверинь на вход в блек лист
 * Class FileParser
 */
class FileJson
{

    private $file;
    private $file_type = 'json';

    /**
     * FileParser constructor.
     * @param $file
     */
    function __construct($file)
    {
        $this->file = $file;
        if (!$this->validationExtensionFile($this->file, $this->file_type)) {
            die('Фаил <strong>' . $this->file . '</strong> не соответствует требование');
        }
    }

    /**
     * Фаил
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }


    /**
     * Содержимое файла
     * @return string
     */
    public function getContent()
    {
        return file_get_contents($this->file);
    }

    /**
     * Получаем содержимое файла в массив
     * @return array
     */
    public function getData()
    {
        return $data = json_decode($this->getContent(), true);
    }


    /**
     * Фалидируем фаил по расширению
     *
     * @param $filename string имя валидируемого файла
     * @param $extension string расширение с которим нужно сравнить
     * @return bool
     */
    private function validationExtensionFile($filename, $extension)
    {
        return ($this->getExtension($filename) === $extension) ? true : false;
    }

    /**
     * Получаем расширение файла
     * @param $filename string имя фаила
     * @return mixed
     */
    private function getExtension($filename)
    {
        $path_info = pathinfo($filename);
        return $path_info['extension'];
    }

}