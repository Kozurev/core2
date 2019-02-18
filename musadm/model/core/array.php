<?php
/**
 * Класс реализующий набор статических методов для работы с массивами
 *
 * @author Bad Wolf
 * @date 20.03.2018 16:03
 * @version 20190218
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
        'PARAM_BOOL'    => 'bool'
    ];



    /**
     * Получение значения элемента из массива по ключу
     *
     * @param $arr - исходный массив
     * @param $key - ключь
     * @param null $default - значение по умолчанию
     * @param null $type - тип возвращаемого значения
     * @return mixed
     */
    public static function getValue( $arr, $key, $default = null, $type = null )
    {
        if ( !is_array( $arr ) )
        {
            exit ( 'Core_Array:: Параметр $arr метода getValue должен быть массивом' );
        }

        if ( !is_string( $key ) && !is_numeric( $key ) && $key != '' )
        {
            exit ( 'Core_Array:: Параметр $key должен иметь строковый или численный тип' );
        }


        /**
         * Поиск значения во вложенных массивах
         * к примеру чтобы получить значнеие $_SESSION['core']['user_backup']
         * необходимо: getValue($_SESSION, 'core/user_backup', null)
         */
        $array = $arr;
        $nesting = explode( '/', $key );
        $resValKey = array_pop( $nesting );

        foreach ( $nesting as $arrKey )
        {
            $array = self::getValue( $array, $arrKey, $default, PARAM_ARRAY );

            if ( !is_array( $array ) )
            {
                return $default;
            }
        }


        if ( isset( $array[$resValKey] ) && $array[$resValKey] != '' )
        {
            $value = $array[$resValKey];
        }
        else
        {
            $value = $default;
        }


        /**
         * Контроль возвращаемого типа данных
         */
        if ( !is_null( $type ) && in_array( $type, self::$types ) )
        {
            switch ( $type )
            {
                case PARAM_INT:
                    {
                        is_numeric( $value )
                            ?   $value = intval( $value )
                            :   $value = $default;

                        break;
                    }

                case PARAM_FLOAT:
                    {
                        is_numeric( $value )
                            ?   $value = floatval( $value )
                            :   $value = $default;

                        break;
                    }

                case PARAM_STRING:
                    {
                        is_string( $value )
                            ?   $value = strval( $value )
                            :   $value = $default;

                        break;
                    }

                case PARAM_BOOL:
                    {
                        if ( is_bool( $value ) || is_numeric( $value ) )
                        {
                            $value = boolval( $value );
                            break;
                        }

                        if ( $value === 'true' )
                        {
                            $value = true;
                            break;
                        }

                        if ( $value === 'false' )
                        {
                            $value = false;
                            break;
                        }

                        $value = boolval( $value );
                        break;
                    }

                case PARAM_ARRAY:
                    {
                        !is_array( $value )
                            ?   $value = $default
                            :   $value = (array)$value;

                        break;
                    }
            }
        }

        return $value;
    }


    /**
     * Метод для получения значения из массива $_GET
     *
     * @param $key - ключь
     * @param null $default - значение по умолчанию
     * @param null $type - тип возвращаемого значнеия
     * @return mixed
     */
    public static function Get( $key, $default = null, $type = null )
    {
        return self::getValue( $_GET, $key, $default, $type );
    }


    /**
     * Метод для получения значения из массива $_POST
     *
     * @param $key - ключь
     * @param null $default - значение по умолчанию
     * @param null $type - тип возвращаемого значнеия
     * @return mixed
     */
    public static function Post( $key, $default = null, $type = null )
    {
        return self::getValue( $_POST, $key, $default, $type );
    }


    /**
     * Метод для получения значения из массива $_REQUEST
     *
     * @param $key - ключь
     * @param null $default - значение по умолчанию
     * @param null $type - тип возвращаемого значнеия
     * @return mixed
     */
    public static function Request( $key, $default = null, $type = null )
    {
        return self::getValue( $_REQUEST, $key, $default, $type );
    }


    /**
     * Метод для получения значения из массива $_FILE
     *
     * @param $key - ключь
     * @param null $default - значение по умолчанию
     * @return mixed
     */
    public static function File( $key, $default = null )
    {
        return self::getValue( $_FILES, $key, $default );
    }


    /**
     * Метод для получения значения из массива $_SESSION
     *
     * @param $key - ключь
     * @param null $default - значение по умолчанию
     * @param null $type - тип возвращаемого значнеия
     * @return mixed
     */
    public static function Session( $key, $default = null, $type = null )
    {
        return self::getValue( $_SESSION, $key, $default, $type );
    }


    /**
     * Метод для получения значения из массива $_SERVER
     *
     * @param $key - ключь
     * @param null $default - значение по умолчанию
     * @param null $type - тип возвращаемого значнеия
     * @return mixed
     */
    public static function Server( $key, $default = null, $type = null )
    {
        return self::getValue( $_SERVER, $key, $default, $type );
    }

}