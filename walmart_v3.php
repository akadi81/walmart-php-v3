<?php

// simple walmart class v3 get inventory value, post inventory value(s)

class Walmart {
        private $clientId = "YOUR CLIENT ID";
        private $clientSecret = "YOUR CLIENT SECRET";
        private $tokenUrl = "https://marketplace.walmartapis.com/v3/token";

        private function getToken() {
        $authorization = base64_encode($this->clientId.":".$this->clientSecret);
        $qos = uniqid();
        $ch = curl_init();
        $options = array(
        CURLOPT_URL => $this->tokenUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_HEADER => false,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => "grant_type=client_credentials",
        CURLOPT_HTTPHEADER => array(
        "Authorization: Basic ".$authorization,
        "Content-Type: application/x-www-form-urlencoded",
        "Accept: application/json",
        "WM_SVC.NAME: Walmart Marketplace",
        "WM_QOS.CORRELATION_ID: ".$qos,
        "WM_SVC.VERSION: 1.0.0"));
        curl_setopt_array($ch, $options);
        $response = curl_exec ($ch);
        return $response; //json
        }

        public function getInventory($sku) {
        $auth = json_decode($this->getToken(),true);
        $token = $auth["access_token"];
        $authorization = base64_encode($this->clientId.":".$this->clientSecret);
        $qos = uniqid();
        $url = "https://marketplace.walmartapis.com/v3/inventory?sku=".$sku;
        $ch = curl_init();
        $options = array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_HEADER => false,
                CURLOPT_HTTPGET => true,
                CURLOPT_HTTPHEADER => array
                        (
                        "WM_SVC.NAME: Walmart Marketplace",
                        "WM_QOS.CORRELATION_ID: ".$qos,
                        "WM_SVC.VERSION: 1.0.0",
                        "Authorization: Basic ".$authorization,
                        "WM_SEC.ACCESS_TOKEN: ".$token,
                        "Content-Type: application/xml",
                        "Accept: application/xml"
                        )
                );
        curl_setopt_array($ch, $options);
        $response = curl_exec ($ch);
        return $response; //xml
        }
        
        public function postInventory ($fileName) {
        $auth = json_decode($this->getToken(),true);
        $token = $auth["access_token"];
        $authorization = base64_encode($this->clientId.":".$this->clientSecret);
        $qos = uniqid();

        $url = "https://marketplace.walmartapis.com/v3/feeds?feedType=inventory";
        $ch = curl_init();
        $options = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_HEADER => false,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => array ('file' => file_get_contents(dirname(__FILE__)."/".$fileName)), #'@' . dirname(__FILE__)."/".$fileName,
        CURLOPT_HTTPHEADER => array
                        (
                        "WM_SVC.NAME: Walmart Marketplace",
                        "WM_QOS.CORRELATION_ID: ".$qos,
                        "WM_SVC.VERSION: 1.0.0",
                        "Authorization: Basic ".$authorization,
                        "WM_SEC.ACCESS_TOKEN: ".$token,
                        "Content-Type: multipart/form-data",
                        "Accept: application/xml",
                        "Host: marketplace.walmartapis.com"
                        )
                );
        curl_setopt_array($ch, $options);
        $response = curl_exec ($ch);
        return $response; //xml
       }
}

//get inventory value from walmart
$x = new Walmart;
$sku = "SKU006604";
$xml_stock = $x->getInventory($sku);
var_dump($xml_stock);

//post inventory xml file for update
$xml_file = "inventory.xml";
var_dump($xml_file);

?>
