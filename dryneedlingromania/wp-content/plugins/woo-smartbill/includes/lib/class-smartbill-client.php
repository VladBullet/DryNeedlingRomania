<?php

class SmartBill_Client {
    const INVOICE_URL             = 'https://ws.smartbill.ro/SBORO/api/invoice';
    const STATUS_INVOICE_URL      = 'https://ws.smartbill.ro/SBORO/api/invoice/paymentstatus';
    const PROFORMA_URL            = 'https://ws.smartbill.ro/SBORO/api/estimate';
    const PROFORMA_URL_WITH_DOCUMENT_ADDRESS = 'https://ws.smartbill.ro/SBORO/api/estimate/v2';
    const STATUS_PROFORMA_URL     = 'https://ws.smartbill.ro/SBORO/api/estimate/invoices';
    const INVOICE_URL_WITH_DOCUMENT_ADDRESS = 'https://ws.smartbill.ro/SBORO/api/invoice/v2';
    const PAYMENT_URL             = 'https://ws.smartbill.ro/SBORO/api/payment';
    const EMAIL_URL               = 'https://ws.smartbill.ro/SBORO/api/document/send';
    const TAXES_URL               = 'https://ws.smartbill.ro/SBORO/api/tax?cif=%s';
    const SERIES_URL              = 'https://ws.smartbill.ro/SBORO/api/series?cif=%s&type=%s';
    const PRODUCTS_STOCK_URL      = 'https://ws.smartbill.ro/SBORO/api/stocks?cif=%s&date=%s&warehouseName=%s&productName=%s&productCode=%s';
    const MEASURING_UNITS_URL     = 'https://ws.smartbill.ro/SBORO/api/company/mu?cif=%s';
    const PARAMS_PDF              = '/pdf?cif=%s&seriesname=%s&number=%s';
    const PARAMS_DELETE           = '?cif=%s&seriesname=%s&number=%s';
    const PARAMS_DELETE_RECEIPT   = '/chitanta?cif=%s&seriesname=%s&number=%s';
    const PARAMS_CANCEL           = '/cancel?cif=%s&seriesname=%s&number=%s';
    const PARAMS_RESTORE          = '/restore?cif=%s&seriesname=%s&number=%s';
    const PARAMS_STATUS           = '?cif=%s&seriesname=%s&number=%s';
    const PARAMS_FISCAL_RECEIPT   = '/text?cif=%s&id=%s';

    const PaymentType_OrdinPlata  = 'Ordin plata';
    const PaymentType_Chitanta    = 'Chitanta';
    const PaymentType_Card        = 'Card';
    const PaymentType_CEC         = 'CEC';
    const PaymentType_BiletOrdin  = 'Bilet ordin';
    const PaymentType_MandatPostal= 'Mandat postal';
    const PaymentType_Other       = 'Alta incasare';
    const PaymentType_BonFiscal   = 'Bon';

    const DiscountType_Valoric    = 1;
    const DiscountType_Value      = 1; // en
    const DiscountType_Procentual = 2;
    const DiscountType_Percent    = 2; // en

    const DocumentType_Invoice    = 'factura'; // en
    const DocumentType_Factura    = 'factura';
    const DocumentType_Proforma   = 'proforma';
    const DocumentType_Receipt    = 'chitanta'; // en
    const DocumentType_Chitanta   = 'chitanta';

    const DEBUG_ON_ERROR = false; // use this only in development phase; DON'T USE IN PRODUCTION !!!
    private $hash   = '';
    private $cui = '';
    private $serie = '';
    private $cals = array();

    function __construct() {

        $woo_smartbill = get_option( 'woo_smartbill', array() );

        if ( isset( $woo_smartbill['user'] ) && '' != $woo_smartbill['user'] && isset( $woo_smartbill['token'] ) && '' != $woo_smartbill['token'] ) {
            $this->hash = base64_encode( $woo_smartbill['user'] . ':' . $woo_smartbill['token'] );
        }

        if ( isset( $woo_smartbill['codfiscal'] ) && '' != $woo_smartbill['codfiscal'] ){
            $this->cui = $woo_smartbill['codfiscal'];
        }
        
    }

    public function isValid(){

        if ( '' != $this->hash && '' != $this->cui ) {
            return true;
        }

        return false;

    }

    public static function get_instance() {
        static $inst;

        if ( ! $inst ) {
            $inst = new SmartBill_Client();
        }

        return $inst;
    }

    private function _cURL($url, $data, $request, $headAccept) {

        $headers = array($headAccept, "Authorization: Basic " . $this->hash);

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
            $headers[] = "Content-Type: application/json; charset=utf-8";
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        if ( !empty($request)) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // debugging
        $isDebug = self::DEBUG_ON_ERROR;
        if ( !empty($isDebug) ) {
            $debug = array(
                'URL: '     => $url,
                'data: '    => $data,
                'headers: ' => $headAccept,
            );
            echo '<pre>' , print_r($debug, true), '</pre>';
        }

        return $ch;
    }

    private function _callServer( $url, $data='', $request='', $headAccept="Accept: application/json" ) {
        if (empty($url))   return FALSE;

        $time = date( 'i-s' );
        if ( isset( $this->cals[$time] ) ) {
            if ( $this->cals[$time] < 3 ) {
                $this->cals[$time] ++;
            }else{
                sleep( 1 );
                $time = date( 'i-s' );
                $this->cals[$time] = 1;
            }
        }else{
            $this->cals[$time] = 1;
        }

        $ch     = $this->_cURL($url, $data, $request, $headAccept);
        $return = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($status!=200) {
            $errorMessage = json_decode($return, true);

            if ( false !== strpos($url, self::EMAIL_URL) ) {
                $errorMessage = !empty($errorMessage['status']['code']) ? $errorMessage['status']['message'] : $return;
            } else {
                $errorMessage = !empty($errorMessage['errorText']) ? $errorMessage['errorText'] : $return;
            }

            // throw new Exception($errorMessage);
            // empty response
            $return = array(
                'status'  => 'nok',
                'message' => $errorMessage
            );
        } elseif ( false === strpos($url, '/pdf?') ) {
            $return = json_decode($return, true);
            $return['status'] = 'ok';
        }

        return $return;
    }

    private function _prepareDocumentData(&$data) {
        if ( !empty($data['subject']) ) {
            $data['subject'] = base64_encode($data['subject']);
        }
        if ( !empty($data['bodyText']) ) {
            $data['bodyText'] = base64_encode($data['bodyText']);
        }
    }

    public function createInvoice( $data ) {
        return $this->_callServer(self::INVOICE_URL, $data);
    }

    public function createInvoiceWithDocumentAddress( $data ) {
        $data['companyVatCode'] = $this->cui;
        return $this->_callServer(self::INVOICE_URL_WITH_DOCUMENT_ADDRESS, $data);
    }

    public function createProforma( $data ) {
        $data['companyVatCode'] = $this->cui;
        return $this->_callServer(self::PROFORMA_URL_WITH_DOCUMENT_ADDRESS, $data);
    }

    public function createPayment($data) {
        $data['companyVatCode'] = $this->cui;
        return $this->_callServer(self::PAYMENT_URL, $data);
    }

    public function PDFInvoice($companyVatCode, $seriesName, $number) {
        $seriesName = urlencode($seriesName);
        $url = sprintf(self::INVOICE_URL . self::PARAMS_PDF, $companyVatCode, $seriesName, $number);
        return $this->_callServer($url, '', '', "Accept: application/octet-stream");
    }

    public function PDFProforma($companyVatCode, $seriesName, $number) {
        $seriesName = urlencode($seriesName);
        $url = sprintf(self::PROFORMA_URL . self::PARAMS_PDF, $companyVatCode, $seriesName, $number);
        return $this->_callServer($url, '', '', "Accept: application/octet-stream");
    }

    public function deleteInvoice( $seriesName, $number ) {
        $seriesName = urlencode($seriesName);
        $url = sprintf(self::INVOICE_URL . self::PARAMS_DELETE, $this->cui, $seriesName, $number);
        return $this->_callServer($url, '', 'DELETE');
    }

    public function deleteProforma( $seriesName, $number ) {
        $seriesName = urlencode($seriesName);
        $url = sprintf(self::PROFORMA_URL . self::PARAMS_DELETE, $this->cui, $seriesName, $number);
        return $this->_callServer($url, '', 'DELETE');
    }

    public function deleteReceipt($companyVatCode, $seriesName, $number) {
        $seriesName = urlencode($seriesName);
        $url = sprintf(self::PAYMENT_URL . self::PARAMS_DELETE_RECEIPT, $companyVatCode, $seriesName, $number);
        return $this->_callServer($url, '', 'DELETE');
    }

    public function deletePayment($payment) {
        return $this->_callServer(self::PAYMENT_URL, $payment, 'DELETE');
    }

    public function cancelInvoice( $seriesName, $number) {
        $seriesName = urlencode($seriesName);
        $url = sprintf(self::INVOICE_URL . self::PARAMS_CANCEL, $this->cui, $seriesName, $number);
        return $this->_callServer($url, '', 'PUT');
    }

    public function cancelProforma( $seriesName, $number ) {
        $seriesName = urlencode($seriesName);
        $url = sprintf(self::PROFORMA_URL . self::PARAMS_CANCEL, $this->cui, $seriesName, $number);
        return $this->_callServer($url, '', 'PUT');
    }

    public function cancelPayment($companyVatCode, $seriesName, $number) {
        $seriesName = urlencode($seriesName);
        $url = sprintf(self::PAYMENT_URL . self::PARAMS_CANCEL, $companyVatCode, $seriesName, $number);
        return $this->_callServer($url, '', 'PUT');
    }

    public function restoreInvoice($companyVatCode, $seriesName, $number) {
        $seriesName = urlencode($seriesName);
        $url = sprintf(self::INVOICE_URL . self::PARAMS_RESTORE, $companyVatCode, $seriesName, $number);
        return $this->_callServer($url, '', 'PUT');
    }

    public function restoreProforma($companyVatCode, $seriesName, $number) {
        $seriesName = urlencode($seriesName);
        $url = sprintf(self::PROFORMA_URL . self::PARAMS_RESTORE, $companyVatCode, $seriesName, $number);
        return $this->_callServer($url, '', 'PUT');
    }

    public function sendDocument($data) {
        $this->_prepareDocumentData($data);
        return $this->_callServer(self::EMAIL_URL, $data);
    }

    public function getTaxes() {
        $url = sprintf( self::TAXES_URL, $this->cui );
        return $this->_callServer($url);
    }

    public function getMeasuringUnits() {
        $url = sprintf( self::MEASURING_UNITS_URL, $this->cui );
        return $this->_callServer($url);
    }

    public function getDocumentSeries( $documentType='f' ) {
        $url          = sprintf(self::SERIES_URL, $this->cui, $documentType);
        return $this->_callServer($url);
    }

    public function statusInvoicePayments( $seriesName, $number ) {
        $seriesName = urlencode($seriesName);
        $url = sprintf(self::STATUS_INVOICE_URL . self::PARAMS_STATUS, $this->cui, $seriesName, $number);
        return $this->_callServer($url);       
    }

    public function statusProforma($companyVatCode, $seriesName, $number) {
        $seriesName = urlencode($seriesName);
        $url = sprintf(self::STATUS_PROFORMA_URL . self::PARAMS_STATUS, $companyVatCode, $seriesName, $number);
        return $this->_callServer($url);
    }

    public function detailsFiscalReceipt($companyVatCode, $id) {
        $url  = sprintf(self::PAYMENT_URL . self::PARAMS_FISCAL_RECEIPT, $companyVatCode, $id);
        $text = $this->_callServer($url);
        try {
            $text = base64_decode($text['message']);
        } catch (Exception $ex) {
            throw new Exception('invalid / empty response');
        }

        return $text;
    }

    public function productsStock($data) {
        $this->_validateProductsStock($data);
        $url  = $this->_urlProductsStock($data);

        $response = $this->_callServer($url);
        if ( 'ok' == $response['status'] ) {
           return $response['list'];
        }

        return false;
    }
    private function _validateProductsStock(&$data) {
        // append required keys in case they are missing
        $data += array(
            'cif'           => $this->cui,
            'date'          => date('Y-m-d'),
            'warehouseName' => '',
            'productName'   => '',
            'productCode'   => '',
        );
        // urlencode values
        foreach ($data as &$value) {
            $value = urlencode($value);
        }
    }
    private static function _urlProductsStock($data) {
        return sprintf(self::PRODUCTS_STOCK_URL, $data['cif'], $data['date'], $data['warehouseName'], $data['productName'], $data['productCode']);
    }

}