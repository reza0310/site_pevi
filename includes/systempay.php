<?php

class SystemPay {
    public static function sign(array $parameters, string $key) {
        ksort($parameters);
        $body = '';
        foreach ($parameters as $name => $value) {
            if (str_starts_with($name, 'vads_')) {
                $body .= $value . '+';
            }
        }
        $body .= $key;
        return sha1($body);
    }
}

?>
