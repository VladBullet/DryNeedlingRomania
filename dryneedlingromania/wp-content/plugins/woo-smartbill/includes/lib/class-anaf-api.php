<?php

class ANAF_API {
    const ANAF_API_URL = "https://webservicesp.anaf.ro/PlatitorTvaRest/api/v3/ws/tva";

    private static function _cURL($url, $data) {

        $ch = curl_init($url);
        // curl_setopt($ch, CURLOPT_MUTE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if ( !empty($data) ) {
            $headers[] = "Content-Type: application/json";
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        return $ch;
    }

    private static function _callServer($url, $data='') {
        if (empty($url))   return FALSE;

        $ch     = self::_cURL($url, $data);
        $return = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status!=200) {
            $return = false;
        } else{
            $return = json_decode($return, true);
            $return = $return['found'][0];
        }

        return $return;
    }

    /**
     * Functia returneaza raspunsul de la serverul ANAF pentru un anumit cod fiscal
     *
     * @return array|false
     */
    private static function get_VAT_info( $vat = null ) {
        if (! $vat ) return false;

        //doar CUI-uri numerice
        $vat = preg_replace("/[^0-9]/", "", $vat);
        $data = array(
            array( "cui" => $vat, "data" => date("Y-m-d") )
        );

        try {
            return self::_callServer(self::ANAF_API_URL, $data);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Functia returneaza un status boolean daca firma este platitoare de TVA sau nu
     *
     * @return boolean
     */
    public static function is_tax_payer( $vat ) {
        $vat_info = self::get_VAT_info( $vat );

        if ( !$vat_info ) {
            return 0;
        }

        return $vat_info['scpTVA'] == true ? 1 : 0;
    }

}
