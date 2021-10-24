<?php
/*
 * Created on Sun Oct 24 2021 9:39:39 PM
 *
 * Author     : Mile S.
 * Contact    : info@ccwebspot.com
 * Website    : https://ccwebspot.com/
 * Copyright (c) 2021 CC.Webspot
 *
*/

  // Get visitors Browser info 
  function getBrowser() {
     $BROWSER = $_SERVER['HTTP_USER_AGENT'];
     $BROWSER_NAME = 'NA';

     if (preg_match('/MSIE/i', $BROWSER) && !preg_match('/Opera/i', $BROWSER)) {
         $BROWSER_NAME = 'Internet Explorer';
     } elseif (preg_match('/Firefox/i', $BROWSER)) {
         $BROWSER_NAME = 'Mozilla Firefox';
     } elseif (preg_match('/Chrome/i', $BROWSER)) {
         $BROWSER_NAME = 'Google Chrome';
     } elseif (preg_match('/Safari/i', $BROWSER)) {
         $BROWSER_NAME = 'Apple Safari';
     } elseif (preg_match('/Opera/i', $BROWSER)) {
         $BROWSER_NAME = 'Opera';
     } elseif (preg_match('/Netscape/i', $BROWSER)) {
         $BROWSER_NAME = 'Netscape';
     }
     return $BROWSER_NAME;
  }
  // Get visitors info `Country, City, Postal Code, Address`, etc...
  function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
        $output = NULL;
        if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
            $ip = $_SERVER["REMOTE_ADDR"];
            if ($deep_detect) {
                if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
        }
        $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
        $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
        $continents = array(
            "AF" => "Africa",
            "AN" => "Antarctica",
            "AS" => "Asia",
            "EU" => "Europe",
            "OC" => "Australia (Oceania)",
            "NA" => "North America",
            "SA" => "South America"
        );
        if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
            $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
            if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
                switch ($purpose) {
                    case "location":
                        $output = array(
                            "city"           => @$ipdat->geoplugin_city,
                            "state"          => @$ipdat->geoplugin_regionName,
                            "country"        => @$ipdat->geoplugin_countryName,
                            "country_code"   => @$ipdat->geoplugin_countryCode,
                            "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                            "continent_code" => @$ipdat->geoplugin_continentCode
                        );
                        break;
                    case "address":
                        $address = array($ipdat->geoplugin_countryName);
                        if (@strlen($ipdat->geoplugin_regionName) >= 1)
                            $address[] = $ipdat->geoplugin_regionName;
                        if (@strlen($ipdat->geoplugin_city) >= 1)
                            $address[] = $ipdat->geoplugin_city;
                        $output = implode(", ", array_reverse($address));
                        break;
                    case "city":
                        $output = @$ipdat->geoplugin_city;
                        break;
                    case "state":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "region":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "country":
                        $output = @$ipdat->geoplugin_countryName;
                        break;
                    case "countrycode":
                        $output = @$ipdat->geoplugin_countryCode;
                        break;
                }
            }
        }
        return $output;
    }
    // Display collected informations
    echo '
      Your IP Address: ' . $_SERVER['REMOTE_ADDR'] . ' and your Browser is: ' . getBrowser() . '. Country: ' . ip_info("Visitor", "Country") . ' and Country Code: ' . ip_info("Visitor", "Country Code") . ' and State: ' . ip_info("Visitor", "State") . ' and Address: ' . ip_info("Visitor", "Address") . '
    ';

?>
