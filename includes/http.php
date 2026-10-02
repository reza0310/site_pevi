<?php

class HTTP {
    /**
     * @description Make HTTP-GET call
     * @param   $url
     * @param   array $params
     * @param   array $header
     * @return  HTTP-Response body or an empty string if the request fails or is empty
     */
    private static function request(string $method, string $url, array $params, array $header, string $body) {
        if (!empty($params)) {
            $query = http_build_query($params);
            $url = $url.'?'.$query;
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
        }
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        if ($method !== 'POST' && $method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    /**
     * @description Make HTTP-GET call
     * @param   $url
     * @param   array $params
     * @param   array $header
     * @return  HTTP-Response body or an empty string if the request fails or is empty
     */
    public static function get(string $url, array $params = [], array $header = []) {
        return request('GET', $url, $params, $header, null);
    }

    /**
     * @description Make HTTP-POST call
     * @param   $url
     * @param   $body
     * @param   array $header
     * @return  HTTP-Response body or an empty string if the request fails or is empty
     */
    private static function post(string $url, string $body, array $header = []) {
        return request('POST', $url, [], $header, $body);
    }

    /**
     * @description Make HTTP-PUT call
     * @param   $url
     * @param   $body
     * @param   array $header
     * @return  HTTP-Response body or an empty string if the request fails or is empty
     */
    public static function put(string $url, string $body, array $header = []) {
        return request('PUT', $url, [], $header, $body);
    }

    /**
     * @category Make HTTP-DELETE call
     * @param   $url
     * @param   $body
     * @param   array $header
     * @return  HTTP-Response body or an empty string if the request fails or is empty
     */
    public static function delete(string $url, string $body, array $header = []) {
        return request('DELETE', $url, [], $header, $body);
    }
}

?>
