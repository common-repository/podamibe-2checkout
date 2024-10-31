<?php
if (!defined('ABSPATH')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    die('No script kiddies please!');
}
if ( !class_exists( 'PTC_Form_Validator' ) ) {
    class PTC_Form_Validator {
    	public static $regexes = array(
    		'required' => "/[\s\S]/",
    		'zipcode' => "/^[0-9a-zA-Z]{5}\$/",
    		'state' => "/^[a-zA-Z]{2}\$/",
    		'phone' => "/^[0-9]{10}\$/",
    	);
    	
    	private $_validation_rules = array();
    	
    	private $_errors = array();
    	
    	public function __construct( $validation_rules = array() ){
    		$this->_validation_rules = $validation_rules;
    	}
    	
    	public function validate( $data ){
    		foreach( $this->_validation_rules as $key => $rule ){
    			if( ! array_key_exists( $key, $data ) ) {
    				$this->_addError( $key, ucfirst($key . ' Not exist'));
    				continue;
    			}			
    			
    			$rules = explode( '|',$rule['rule'] );
    			$message = explode( '|',$rule['notice'] );
    			foreach ($rules as $k => $rule) { 
    				$result = $this->validateItem( $data[$key], $rule );
    				if($result === false) {
    					 $this->_addError( $key, $message[$k] );
                         break;
    				}
    			}		
    		}
           
    		return count($this->_errors) > 0 ? false : true;
    	}
    	
    	public function validateItem( $data, $rule ){
    		if( array_key_exists( $rule, self::$regexes) ) { 
    			return filter_var( $data, FILTER_VALIDATE_REGEXP, array(
    																		"options" => array(
    																			"regexp"=>self::$regexes[$rule]
    																		)
    																	) );
    		}
    		
    		$filter = false;
    		
    		 switch($rule)
            {
    			case 'email':
    			//	$var = substr($var, 0, 254);
    				$filter = FILTER_VALIDATE_EMAIL;        
    			break;
    			case 'int':
    				$filter = FILTER_VALIDATE_INT;
    			break;
    			case 'boolean':
    				$filter = FILTER_VALIDATE_BOOLEAN;
    			break;
    			case 'ip':
    				$filter = FILTER_VALIDATE_IP;
    			break;
    			case 'url':
    				$filter = FILTER_VALIDATE_URL;
    			break;
            }
    		
    		return filter_var($data, $filter);
    	}
    	
    	public function get_validation_errors() {		
    		return $this->_errors;
    	}
    	
    	private function _addError( $field, $message ) {   
    		$this->_errors[$field] = $message;
        }
    }
    //Countries_List termination
}