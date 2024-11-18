<?php

if (!function_exists('getInfoCountry')) {
    function getInfoCountry($ip = null)
    {
        if (!$ip) {
            $ip = getRealIP();  // Get the real IP if not provided
        }

        // Fetch the XML response from the geoplugin API
        $xml = @simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=" . $ip);

        // Check if the XML was loaded successfully
        if ($xml === false) {
            return 'Unknown'; // Return 'Unknown' if there was an issue with the request
        }

        // Extract country information from the XML response
        $country = (string) @$xml->geoplugin_countryName;

        // Return the country name, defaulting to 'Unknown' if not found
        return $country ?: 'Unknown';
    }
}
