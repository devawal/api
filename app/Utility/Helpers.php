<?php

namespace App\Utility;

/**
 * Global helper methods
 *
 * @author Abdul awal <awal.ashu@gmail.com>
 */
class Helpers
{
    /**
     * Get CURL response
     *
     * @param string $url
     * @return object
     */
    public static function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
