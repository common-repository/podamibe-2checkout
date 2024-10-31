<?php
if (!defined('ABSPATH')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    die('No script kiddies please!');
}
if ( !class_exists( 'PTC_Checkout' ) ) {
    class PTC_Checkout {
    	private $_user;
    	
    	private $_pass;
        
        private $_privateKey = '';
        
        private $_sid = '';
    	
    	private $_ptc_settings;
    	
    	private $_sandbox = false;
        
        private $_api = true;
        
        public $verifySSL = true;
    	
    	private $_checkoutUrl = 'https://www.2checkout.com';
        
        private $_currency_code = 'USD';
    	
    	public function __construct( $ptc_settings ){
    	   
    		$this->_ptc_settings = $ptc_settings;
            
            if((isset($this->_ptc_settings['sandbox_power']) && $this->_ptc_settings['sandbox_power']==1)){
                $this->_sandbox = true;
                $this->verifySSL = false;
            }
            
            $this->_sid = $this->_ptc_settings['seller_id'];
            $this->_privateKey = $this->_ptc_settings['private_key'];
            $this->_currency_code = $this->_ptc_settings['currency_code'];
            
    		if( $this->_sandbox){
    			$this->_checkoutUrl = 'https://sandbox.2checkout.com';
                $this->_sid = $this->_ptc_settings['sandbox_seller_id'];
                $this->_privateKey = $this->_ptc_settings['sandbox_private_key'];
    		}
            
            $this->url = $this->_checkoutUrl . '/checkout/api/1/'.$this->_sid.'/rs/authService';
            
    	}
        
        public function doCall($data=array())
        {
            $ch = curl_init($this->url);
            if ($this->_api) {
                $data['privateKey'] = $this->_privateKey;
                $data['sellerId'] = $this->_sid;
                $data = json_encode($data);
                $header = array("content-type:application/json","content-length:".strlen($data));
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            } else {
                $header = array("Accept: application/json");
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_USERPWD, "{$this->_user}:{$this->_pass}");
            }
            if ($this->verifySSL == false) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_USERAGENT, "2Checkout PHP/0.1.0%s");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $resp = curl_exec($ch);
            curl_close($ch);
            if ($resp === FALSE) {
                throw new PTC_Error("cURL call failed", "403");
            } else {
                return utf8_encode($resp);
            }
    	}
        
        public function makePayment($token, $amount, $orderId = null, $billingAddr = array(), $shippingAddr = array()){
            $post_data = array(
                    "merchantOrderId" => $orderId,
                    "token" => $token,
                    "currency" => strtoupper($this->_currency_code),
                    "total" => $amount,
                    "payment_type" => "paypal ec",
                    "billingAddr" => $billingAddr
            );
            if(!empty($shippingAddr)){
                $post_data['shippingAddr'] = $shippingAddr;
            }
            try{
                $result = $this->doCall($post_data);
                return $result;
            }catch(Exception $e){
                return $e;
            }
        }
    }
    //Countries_List termination
}