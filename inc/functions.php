<?php
function ptc_get_array_value( $array, $key ){
	if( is_array( $array ) ){
		if( array_key_exists( $key, $array ) ){
			return $array[$key];
		}
		else{
			return "";
		}
	}
	else{
		return "";
	}
}

function get_country_dropdown_list( $selected_value = null, $use_key = true, $remove_countries = array() ){
	$country_list = '';
    $countries = Countries_List::get2d( 'alpha3', array( 'name' ) );
	if( ! empty( $remove_countries ) ){
		foreach( $remove_countries as $country_key ){
			if( array_key_exists( $country_key, $countries ) ){
				unset( $countries[$country_key] );
			}
		}
	}
    $country_list .= '<option value="">'.apply_filters('ptc_country_label',esc_html__('Select Country', PTC_TEXT_DOMAIN)).'</option>';
	foreach( $countries as $key => $country ){
		if( $use_key ){
			$value = $key;				
		}
		else{
			$value = $country['name'];
		}			
		$selected = ( $value ==  $selected_value );
	
		$country_list .= '<option value="'.$value.'" '. ( ( $selected ) ? "selected": "" ).'>'.$country['name'].'</option>';		
	}
	return $country_list;
}

function p2c_billing_validation_rules(){
    return array(
    			"billing_country" => array(
    				"rule" => "required",
    				"notice" => __( "Select billing country", PTC_TEXT_DOMAIN )
    			),
    			"billing_name" => array(
    				"rule" => "required",
    				"notice" => __( "Enter billing name", PTC_TEXT_DOMAIN )
    			),
    			"billing_address" => array(
    				"rule" => "required",
    				"notice" => __( "Enter billing address", PTC_TEXT_DOMAIN )
    			),
    			"billing_city" => array(
    				"rule" => "required",
    				"notice" => __( "Enter billing city", PTC_TEXT_DOMAIN )
    			),
    			"billing_state_province" => array(
    				"rule" => "required",
    				"notice" => __( "Enter billing state name", PTC_TEXT_DOMAIN )
    			),
    			"billing_postalcode" => array(
    				"rule" => "required",
    				"notice" => __( "Enter billing zipcode", PTC_TEXT_DOMAIN )
    			),
    			"billing_email" => array(
    				"rule" => "required|email",
    				"notice" => __( "Enter your billing email address|Invalid email", PTC_TEXT_DOMAIN )
    			)               
    		);
    
}
function p2c_shipping_validation_rules(){
    return array(
    			"shipping_country" => array(
    				"rule" => "required",
    				"notice" => __( "Select shipping country", PTC_TEXT_DOMAIN )
    			),
    			"shipping_name" => array(
    				"rule" => "required",
    				"notice" => __( "Enter shipping name", PTC_TEXT_DOMAIN )
    			),
    			"shipping_address" => array(
    				"rule" => "required",
    				"notice" => __( "Enter shipping address", PTC_TEXT_DOMAIN )
    			),
    			"shipping_city" => array(
    				"rule" => "required",
    				"notice" => __( "Enter shipping city", PTC_TEXT_DOMAIN )
    			),
    			"shipping_state_province" => array(
    				"rule" => "required",
    				"notice" => __( "Enter shipping state name", PTC_TEXT_DOMAIN )
    			),
    			"shipping_postalcode" => array(
    				"rule" => "required",
    				"notice" => __( "Enter shipping zipcode", PTC_TEXT_DOMAIN )
    			)
       );
}
add_action( 'wp_ajax_ptc_form_validation', 'ptc_form_validation_callback' );
add_action( 'wp_ajax_nopriv_ptc_form_validation', 'ptc_form_validation_callback' );

function ptc_form_validation_callback(){
    $datas = array();
    parse_str($_POST['form_data'], $datas);
   
    if(array_key_exists('billing_details', $datas)){
        $data = $datas['billing_details'];
        add_filter("ptc_validation_rules", "p2c_billing_validation_rules" );
    }elseif(array_key_exists('shipping_details', $datas)){
        $data = $datas['shipping_details'];
        if(!isset($data['same_as_billing']) && $data['same_as_billing'] != 1){
            add_filter("ptc_validation_rules", "p2c_shipping_validation_rules" );
        }        
    }
    
    $formValidator = new PTC_2C_Validation();
   
    if( ! $formValidator->validate( $data ) ){
        $errors = $formValidator->get_errors();
        $result = array(
            "status" => "error",
            "errors" => $errors
        );
    }else{
         $result = array(
            "status" => "success",
            "errors" => null            
        );  
    }
   
    echo json_encode($result);   
    
    exit;  
}

// This hook is for check the return response of payment
/**
add_action( 'ptc_after_checkout', 'ptc_after_checkout_callback', 10, 5 );
function ptc_after_checkout_callback($response_results, $amount, $orderId, $billingAddr, $shippingAddr){
    global $wpdb;
    echo '<pre>';
    print_r(json_decode($response_results));
    print_r($amount);
    print_r($orderId);
    print_r($billingAddr);
    print_r($shippingAddr);
     //$args = process this ($response_results, $amount, $orderId, $billingAddr, $shippingAddr) values.
    //$wpdb->insert($table_name, $args);
    echo '</pre>';exit;
}/**/