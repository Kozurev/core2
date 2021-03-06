<?php

namespace Model;

class Api
{
    const REQUEST_METHOD_GET = 'get';
    const REQUEST_METHOD_POST = 'post';

    /**
     * @param string $link
     * @param array $params
     * @param string $requestMethod
     * @return mixed
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
            return null;
        }
    }

    /**
     * @param string $link
     * @param string $jsonData
     * @return mixed
     */
    public static function getJsonRequest(string $link, string $jsonData)
    {
//        $context = stream_context_create([
//            'http' => [
//                'method' => 'POST',
//                'header' => [
//                    'Content-Type: application/json',
//                    'Content-Length: ' . strlen($jsonData)
//                ],
//                'content' => $jsonData
//            ]
//        ]);
//        return file_get_contents($link, false, $context);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'accept: application/json']);
        //curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}