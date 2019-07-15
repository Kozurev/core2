<?php
/**
 * Класс для REST_API на стороне сервера
 *
 * @author: BadWolf
 * @date 20.05.2019 23:58
 * Class REST
 */
require_once ROOT . '/model/rest/user.php';
Core::requireClass('Rest_Lid');

class REST
{
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';


    /**
     * @return Rest_User
     */
    public static function user() : Rest_User
    {
        return new Rest_User();
    }


    /**
     * @return Rest_Lid
     */
    public static function lid() : Rest_Lid
    {
        return new Rest_Lid();
    }


    /**
     * Преобразователь списка параметров в URL с GET параметрами
     *
     * @param string $url
     * @param string $action
     * @param array $params
     * @return string
     */
    public static function toUrl(string $url, string $action, array $params)
    {
        if (count($params) == 0) {
            return $url;
        }

        $get = '';
        foreach ($params as $paramName => $paramValue) {
            if (is_array($paramValue)) {
                foreach ($paramValue as $key => $value) {
                    if (is_string($key)) {
                        $add = '%5B'.$key.'%5D';
                    } else {
                        $add = '%5B%5D';
                    }
                    $get .= '&amp;params%5B' . $paramName . '%5D'.$add.'=' . $value;
                }
            } else {
                $get .= '&amp;params%5B' . $paramName . '%5D=' . $paramValue;
            }
        }
        return $url . '?action=' . $action . $get;
    }


    /**
     * Генератор ошибки формата JSON
     *
     * @param int $num
     * @param string $message
     * @return string
     */
    public static function error(int $num, string $message) : string
    {
        $error = new stdClass();
        $error->code = $num;
        $error->message = $message;
        return json_encode(['error' => $error]);
    }


    /**
     * Метод для формирование ответа формата JSON для API
     *
     * @param string $status
     * @param string $message
     * @return string
     */
    public static function status(string $status, string $message) : string
    {
        $output = new stdClass();
        $output->message = $message;

        if ($status === self::STATUS_SUCCESS) {
            $output->status = true;
        } elseif ($status === self::STATUS_ERROR) {
            $output->status = false;
        } else {
            $output->status = null;
        }

        return json_encode($output);
    }
}