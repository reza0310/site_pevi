<?php

class HTTP {
    /**
     * @description Make HTTP-GET call
     * @param   $url
     * @param   array $params
     * @param   array $header
     * @return  HTTP-Response body or an empty string if the request fails or is empty
     */
    public static function get($url, array $params = [], $header = []) {
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
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    /**
     * @description Make HTTP-POST call
     * @param   $url
     * @param   $body
     * @param   array $header
     * @return  HTTP-Response body or an empty string if the request fails or is empty
     */
    public static function post($url, $body, $header = []) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    /**
     * @description Make HTTP-PUT call
     * @param   $url
     * @param   $body
     * @param   array $header
     * @return  HTTP-Response body or an empty string if the request fails or is empty
     */
    public static function put($url, $body, $header = []) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    /**
     * @category Make HTTP-DELETE call
     * @param   $url
     * @param   $body
     * @param   array $header
     * @return  HTTP-Response body or an empty string if the request fails or is empty
     */
    public static function delete($url, $body, $header = []) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}

?>
