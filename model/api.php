<?php

class Api
{

    /**
     * Тип запроса к API вконтакте
     */
    const REQUEST_METHOD_GET = 'get';

    /**
     * Тип запроса к API вконтакте
     */
    const REQUEST_METHOD_POST = 'post';


    /**
     * @param string $link
     * @param array $params
     * @param string $requestMethod
     * @return mixed
     * @throws Exception
     */
    public static function getRequest(string $link, array $params = [], string $requestMethod = self::REQUEST_METHOD_GET)
    {
        $queryParams = http_build_query($params);
        if ($requestMethod === self::REQUEST_METHOD_GET) {
            return json_decode(file_get_contents($link .'?' . $queryParams));
        } elseif ($requestMethod === self::REQUEST_METHOD_POST) {
            return json_decode(file_get_contents($link, false, stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-Type: application/x-www-form-urlencoded',
                    'content' => $queryParams
                ]
            ])));
        } else {
            throw new Exception('Api->getRequest - неопознанный тип запроса: ' . $requestMethod);
        }
    }
}