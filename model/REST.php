<?php
/**
 * Класс для REST_API на стороне сервера
 *
 * @author: BadWolf
 * @date 20.05.2019 23:58
 * Class REST
 */

class REST
{
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';

    const ERROR_UNAUTHORIZED = 'unauthorized';

    const ERROR_CODE_EMPTY = 0;         //Ошибки отсутствуют
    const ERROR_CODE_AUTH = 1;          //Пользователь не авторизован
    const ERROR_CODE_ACCESS = 2;        //Недостаточно прав
    const ERROR_CODE_NOT_FOUND = 3;     //Объект не найден
    const ERROR_CODE_TIME = 4;          //Неподходящее время
    const ERROR_CODE_REQUIRED_PARAM = 5;//Отсутствует обязательный параметр
    const ERROR_CODE_CUSTOM = 999;      //Кастомная ошибка

    /**
     * @var string[]
     */
    private static array $messages = [
        self::ERROR_CODE_EMPTY => 'Ок',
        self::ERROR_CODE_AUTH => 'Пользователь не авторизован',
        self::ERROR_CODE_ACCESS => 'Недостаточно прав',
        self::ERROR_CODE_NOT_FOUND => 'Искомый объект не найден',
        self::ERROR_CODE_TIME => 'В данный момент действие недоступно',
        self::ERROR_CODE_REQUIRED_PARAM => 'Отсутствует один или несколько обязательных параметров'
    ];

    /**
     * @param int $errorCode
     * @return string
     */
    public static function getErrorMessage(int $errorCode): string
    {
        return self::$messages[$errorCode] ?? '';
    }

    /**
     * @return Rest_User
     */
    public static function user(): Rest_User
    {
        return new Rest_User();
    }

    /**
     * @return Rest_Lid
     */
    public static function lid(): Rest_Lid
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
    public static function toUrl(string $url, string $action, array $params): string
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
    public static function error(int $num, string $message): string
    {
        $error = new stdClass();
        $error->code = $num;
        $error->error = $num;
        $error->message = $message;
        return json_encode(['error' => $error]);
    }

    /**
     * Метод для формирование ответа формата JSON для API
     *
     * @param string $status
     * @param string $message
     * @param int|null $errorCode
     * @return string
     */
    public static function status(string $status, string $message, int $errorCode = null): string
    {
        $output = new stdClass();
        $output->error = $errorCode;
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

    /**
     * @param int $errorCode
     * @param string $message
     * @return string
     */
    public static function responseError(int $errorCode, string $message = ''): string
    {
        $response = new stdClass();
        $response->error = $errorCode;
        $response->message = !empty($message) ? $message : self::getErrorMessage($errorCode);
        $response->status = false;
        return json_encode($response);
    }
}