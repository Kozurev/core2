<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 28.06.2020
 * Time: 11:48
 */

/**
 * Фассад для хранения временной информации в файлах
 *
 * Class Temp
 */
class Temp
{
    /**
     * Абсолютный путь к дирректории для временных файлов
     *
     * @var string
     */
    const TMP_DIR = ROOT . '/tmp';

    /**
     * Получение данных из временного файла
     *
     * @param string $fileName
     * @return mixed|null
     */
    public static function get(string $fileName)
    {
        $file = self::getAbsolutePath($fileName);
        if (!file_exists($file)) {
            return null;
        } else {
            return json_decode(file_get_contents($file));
        }
    }

    /**
     * Получение данных из файла с последующим его удалением
     *
     * @param string $fileName
     * @return mixed|null
     */
    public static function getAndRemove(string $fileName)
    {
        $data = self::get($fileName);
        self::remove($fileName);
        return $data;
    }

    /**
     * Создание файла с json строкой данных
     *
     * @param string $fileName
     * @param $content
     * @param string $mode
     */
    public static function put(string $fileName, $content, $mode = 'w') : void
    {
        self::makeTmpDirOfNotExists();

        $file = self::getAbsolutePath($fileName);
        $fp = fopen($file, $mode);
        fwrite($fp, json_encode($content));
        fclose($fp);
    }

    /**
     * Удаление файла, если такой существует
     *
     * @param string $fileName
     */
    public static function remove(string $fileName) : void
    {
        $file = self::getAbsolutePath($fileName);
        if (file_exists($file)) {
            unset($file);
        }
    }

    /**
     * Создание директории для временных файлов если она не существует
     */
    protected static function makeTmpDirOfNotExists() : void
    {
        if (!is_dir(self::TMP_DIR)) {
            mkdir(self::TMP_DIR, 0777);
        }
    }

    /**
     * Генерация абсолютного пути к файлу
     *
     * @param string $fileName
     * @return string
     */
    protected static function getAbsolutePath(string $fileName) : string
    {
        return self::TMP_DIR . '/' . $fileName;
    }

}