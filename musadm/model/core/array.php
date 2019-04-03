<?php
/**
 * Класс реализующий набор статических методов для работы с массивами
 *
 * @author Bad Wolf
 * @date 20.03.2018 16:03
 * @version 20190401
 * @version 20190403
 */
class Core_Array
{
    /**
     * Список возможных типов возвращаемых значений
     * Ключ элемента массива является названием константы, передаваемой в качестве параметра $type
     *
     * @var array
     */
    private static $types = [
        'PARAM_INT'     => 'int',
        'PARAM_FLOAT'   => 'float',
        'PARAM_STRING'  => 'string',
        'PARAM_BOOL'    => 'bool',
        'PARAM_ARRAY'   => 'array',
        'PARAM_DATE'    => 'date',
        'PARAM_TIME'    => 'time',
        'PARAM_DATETIME'=> 'datetime'
    ];


    /**
     * Получение значения элемента из массива по ключу
     *
     * @param array $arr - исходный массив
     * @param string $key - ключь
     * @param null $default - значение по умолчанию
     * @param string|null $type - тип возвращаемого значения
     * @return mixed
     */
    public static function getValue(array $arr, string $key, $default = null, string $type = null)
    {
        /**
         * Поиск значения во вложенных массивах
         * к примеру чтобы получить значнеие $_SESSION['core']['user_backup']
         * необходимо: getValue($_SESSION, 'core/user_backup', null)
         */
        $array = $arr;
        $nesting = explode('/', $key);
        $resValKey = array_pop($nesting);

        foreach ($nesting as $arrKey) {
            $array = self::getValue($array, $arrKey, $default, PARAM_ARRAY);
            if (!is_array($array)) {
                return $default;
            }
        }

        if (isset($array[$resValKey])) {
            $value = $array[$resValKey];
        } else {
            $value = $default;
        }

        //Контроль возвращаемого типа данных
        if (!is_null($type) && in_array($type, self::$types)) {
            switch ($type)
            {
                case PARAM_INT:
                    is_numeric($value) && ($value == intval($value))
                        ?   $value = intval($value)
                        :   $value = $default;
                    break;

                case PARAM_FLOAT:
                    is_numeric($value) && ($value == floatval($value))
                        ?   $value = floatval($value)
                        :   $value = $default;
                    break;

                case PARAM_STRING:
                    is_string($value)
                        ?   $value = strval($value)
                        :   $value = $default;
                    break;

                case PARAM_BOOL:
                    if (is_bool($value) || is_numeric($value)) {
                        $value = boolval($value);
                        break;
                    }
                    if ($value === 'true') {
                        $value = true;
                        break;
                    }
                    if ($value === 'false') {
                        $value = false;
                        break;
                    }
                    if (is_bool($value) || is_numeric($value)) {
                        $value = boolval($value);
                    } else {
                        $value = $default;
                    }
                    break;

                case PARAM_ARRAY:
                    !is_array($value)
                        ?   $value = $default
                        :   $value = (array)$value;
                    break;

                case PARAM_DATE:
                    if (!(is_string($value) && preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $value))) {
                        $value = $default;
                    }
                    break;

                case PARAM_TIME:
                    if (!(is_string($value) && preg_match('/^(00|[0-9]|1[0-9]|2[0-3]):([0-9]|[0-5][0-9]):([0-9]|[0-5][0-9])$/', $value))) {
                        $value = $default;
                    }
                    break;

                case PARAM_DATETIME:
                    if (!(is_string($value) && preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) (00|[0-9]|1[0-9]|2[0-3]):([0-9]|[0-5][0-9]):([0-9]|[0-5][0-9])$/', $value))) {
                        $value = $default;
                    }
                    break;
            }
        }

        return $value;
    }


    /**
     * Метод для получения значения из массива $_GET
     *
     * @param string $key - ключь
     * @param null $default - значение по умолчанию
     * @param string|null $type - тип возвращаемого значнеия
     * @return mixed
     */
    public static function Get(string $key, $default = null, string $type = null )
    {
        return self::getValue($_GET, $key, $default, $type);
    }


    /**
     * Метод для получения значения из массива $_POST
     *
     * @param string $key - ключь
     * @param null $default - значение по умолчанию
     * @param string|null $type - тип возвращаемого значнеия
     * @return mixed
     */
    public static function Post(string $key, $default = null, string $type = null)
    {
        return self::getValue($_POST, $key, $default, $type);
    }


    /**
     * Метод для получения значения из массива $_REQUEST
     *
     * @param string $key - ключь
     * @param null $default - значение по умолчанию
     * @param string|null $type - тип возвращаемого значнеия
     * @return mixed
     */
    public static function Request(string $key, $default = null, string $type = null)
    {
        return self::getValue($_REQUEST, $key, $default, $type);
    }


    /**
     * Метод для получения значения из массива $_FILE
     *
     * @param string $key - ключь
     * @param null $default - значение по умолчанию
     * @return mixed
     */
    public static function File(string $key, $default = null)
    {
        return self::getValue($_FILES, $key, $default);
    }


    /**
     * Метод для получения значения из массива $_SESSION
     *
     * @param string $key - ключь
     * @param null $default - значение по умолчанию
     * @param string|null $type - тип возвращаемого значнеия
     * @return mixed
     */
    public static function Session(string $key, $default = null, string $type = null)
    {
        return self::getValue($_SESSION, $key, $default, $type);
    }


    /**
     * Метод для получения значения из массива $_SERVER
     *
     * @param string $key - ключь
     * @param null $default - значение по умолчанию
     * @param string|null $type - тип возвращаемого значнеия
     * @return mixed
     */
    public static function Server(string $key, $default = null, string $type = null )
    {
        return self::getValue($_SERVER, $key, $default, $type);
    }


    /**
     * Метод для получения значения из массива $_COOKIE
     *
     * @param string $key - ключь
     * @param null $default - значение по умолчанию
     * @param string|null $type - тип возвращаемого значнеия
     * @return mixed
     */
    public static function Cookie(string $key, $default = null, string $type = null)
    {
        return self::getValue($_COOKIE, $key, $default, $type);
    }
}