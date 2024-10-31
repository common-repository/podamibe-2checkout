<?php
if (!defined('ABSPATH')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    die('No script kiddies please!');
}
if ( !class_exists( 'PTC_2C_Validation' ) ) {
    class PTC_2C_Validation {
        public $validation_rules;
        private $form_validator;
        public function __construct(){
            $this->validation_rules = $this->_set_validation_rules();
            $this->form_validator = new PTC_Form_Validator( $this->validation_rules );
        }
        
        public function validate($data){
            return $this->form_validator->validate( $data );
        }
        
        public function get_errors(){
            return $this->form_validator->get_validation_errors();
        }
        /**
         * PTC_Payment_Shortcode::_set_validation_rules()
         * Set validation rules for the required filds
         * @returns array contained key=>value pairs of the requested key and field
         */
        private function _set_validation_rules(){
    		return apply_filters('ptc_validation_rules', array() );
    	}
        
        
    }
    //PTC_2C_Validation termination
}