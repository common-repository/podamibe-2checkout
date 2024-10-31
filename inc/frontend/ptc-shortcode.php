<?php
if (!defined('ABSPATH')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    die('No script kiddies please!');
}

if ( !class_exists( 'PTC_Payment_Shortcode' ) ) {
    class PTC_Payment_Shortcode {
        
        private static $_instance = null;
        
        private $_errors;
        
        private $_form_structure = array();
        
        private $_values;
                
        private function __construct(){
            add_shortcode( 'P2Checkout', array( $this , 'ptc_shortcode_callback' ) );
            $this->charge();     
            $this->_form_structure = $this->_set_form_structure(); 
            $this->_values = array();
        }
        
        /**
         * PTC_Payment_Shortcode::init()
         * 
         * @return
         */
        public static function init(){
            if( is_null(self::$_instance) ){
                self::$_instance = new self;
            }
            return self::$_instance;
        }
        
        /**
         * PTC_Payment_Shortcode::ptc_shortcode_callback()
         * 
         * @param mixed $atts
         * @param string $content
         * @return
         */
        public function ptc_shortcode_callback($atts, $content = ""){
            ob_start();
            $atts = shortcode_atts( array(
        		'amount' => '0.00',
                'orderid' => get_the_ID(),
        	), $atts, 'P2Checkout' );
            ?>
                <style>
                    #field_block-shipping_address, #field_block-card_details {
                        display: none;
                    }
                </style>
                <form id="ptcCCForm" action="" method="post">
                    <?php
                        $sn = 1;
                        $total_sections = count($this->_form_structure);
                        
                        foreach( $this->_form_structure as $key => $sections ){
                            ?>
            					<div id="field_block-<?php echo $key;?>" class="field <?php if( isset( $sections["has_error"] ) && $sections["has_error"] ) echo "required"; ?>">
            						<div class="row">
            							<div class="col-one">
            								<label class="field_label" for="<?php echo esc_attr( $sections["label_for"] ); ?>"><?php echo $sections["title"]; ?><?php if( isset( $sections["required"] ) && $sections["required"] ) { ?> <span class="required">*</span><?php } ?></label>
            								<?php if( isset( $sections["description"] ) ){ ?>
            								<div class="field_description"><?php echo $sections["description"]; ?></div>
            								<?php } ?>
            							</div>							
            							<?php
            								$this->_show_input_fields( $sections["fields"] );
            							?>
            							<?php
            								if( isset( $sections["has_error"] ) &&  $sections["has_error"] ){
            									if( isset( $sections["error_message"] ) && trim( $sections["error_message"] != "" )){
            							?>
            								<div class="validation_message"><?php echo $sections["error_message"]; ?></div>
            							<?php }
            								}
                                           
            							?>
            						 </div>
                                     <?php if($sn == $total_sections){ ?>
                                            <div class="ptc-append-error validation_message"></div>
                                            <?php wp_nonce_field('ptc_pay_nonce', 'ptc_pay_nonce_field') ?>                    
                                            <input name="ptc_token" type="hidden" value="" />
                                            <input name="amount" type="hidden" value="<?php echo $atts['amount'];?>" />
                                            <input name="order_id" type="hidden" value="<?php echo $atts['orderid'];?>" />
                                     <?php } ?>
                                     <?php if( isset( $sections["prev_btn"] ) ){ ?>
                                            <input type="button" class="button ptc-prev-button" data-show="#field_block-<?php echo $sections["prev_btn"]["show"]; ?>" value="<?php esc_html_e('Prev', PTC_TEXT_DOMAIN);?>" />
                                     <?php } ?>
                                     <?php if( isset( $sections["nxt_btn"] ) && $sn != $total_sections){ ?>
                                                <input type="button" class="button ptc-next-button" data-show="#field_block-<?php echo $sections["nxt_btn"]["show"]; ?>" value="<?php esc_html_e('Next', PTC_TEXT_DOMAIN);?>" />
                                     <?php } ?>
                                     <?php if($sn == $total_sections){ ?>
                                                <!-- <input type="submit" name="ptc_submit_button" class="button ptc_submit_button" value="<?php esc_attr_e( "Submit Payment", PTC_TEXT_DOMAIN ); ?>" /> -->
                                                <input type="button" class="button ptc-next-button ptc_pay_button" value="<?php esc_attr_e( "Pay", PTC_TEXT_DOMAIN ); ?>" />
                                     <?php } /**/ ?>
            					 </div>	
            			     <?php
                            $sn++;    
                        }
                    ?>
                </form>
                <?php
                    $settings = get_option(PTC_SETTING_NAME);
                    $sid = $settings['seller_id'];
                    $privateKey = $settings['private_key'];
                    $currency_code = $settings['currency_code'];
                    $return_url = $settings['return_url'];
                    $notify_url = $settings['notify_url'];
                ?>
                <form id="ptc-paypal-form" action="https://www.2checkout.com/checkout/purchase" method="post">
                    <input name="sid" type="hidden" value="<?php echo $sid;?>" />
                    <input name="mode" type="hidden" value="2CO" />
                    
                    <input name="cart_order_id" type="hidden" value="<?php echo $atts['orderid'];?>" />
                    <input name="merchant_order_id" type="hidden" value="<?php echo $atts['orderid'];?>" />
                    <input name="total" type="hidden" value="<?php echo $atts['amount'];?>" />
                    <input name="return_url" type="hidden" value="<?php echo $return_url;?>" /> 
                    <input name="x_receipt_link_url" type="hidden" value="<?php echo $notify_url;?>" />
                    <input name="currency_code" type="hidden" value="<?php echo $currency_code;?>" />
                    <input name="li_0_price" type="hidden" value="<?php echo $atts['amount'];?>" />
                    
                    <input name="li_0_name" type="hidden" value="invoice123" />
                    <input name="card_holder_name" type="hidden" value="" />
                    <input name="street_address" type="hidden" value="" />
                    <input name="city" type="hidden" value="" />
                    <input name="state" type="hidden" value="" />
                    <input name="zip" type="hidden" value="" />
                    <input name="country" type="hidden" value="" />
                    <input name="email" type="hidden" value="" />
                    <input name="ship_name" class="ptc-shipping-paypal" type="hidden" value="" />
                    <input name="ship_street_address" class="ptc-shipping-paypal" type="hidden" value="" />
                    <input name="ship_city" type="hidden" class="ptc-shipping-paypal" value="" />
                    <input name="ship_state" type="hidden" class="ptc-shipping-paypal" value="" />
                    <input name="ship_zip" type="hidden" class="ptc-shipping-paypal" value="" />
                    <input name="ship_country" type="hidden" class="ptc-shipping-paypal" value="" />
                    <input name="paypal_direct" type="hidden"  class="ptc-shipping-paypal" value="Y" />
                </form>
            <?php
            return ob_get_clean();
        }
        
        /**
         * PTC_Payment_Shortcode::create()
         * Payment charge
         * @return void
         */
        public function charge(){
            if(isset($_POST['ptc_token'])){
                if( isset( $_POST['ptc_pay_nonce_field'] ) || wp_verify_nonce( $_POST['ptc_pay_nonce_field'], 'ptc_pay_nonce' ) ){
                    $checkout = new PTC_Checkout(get_option(PTC_SETTING_NAME));
                    
                    $billing_details = $_POST['billing_details'];
                    $shipping_details = $_POST['shipping_details'];
                    $token = $_POST['ptc_token'];
                    $amount = $_POST['amount'];
                    $orderId = $_POST['order_id'];
                    $shippingAddr = array();
                    
                    do_action('ptc_before_checkout', $amount, $orderId, $billing_details, $shipping_details);
                    
                    $billingAddr = array(
                        "name" => $billing_details['billing_name'],
                        "addrLine1" => $billing_details['billing_address'],
                        "city" => $billing_details['billing_city'],
                        "state" => $billing_details['billing_state_province'],
                        "zipCode" => $billing_details['billing_postalcode'],
                        "country" => $billing_details['billing_country'],
                        "email" => $billing_details['billing_email']
                    );
                    
                    if(!isset($shipping_details['same_as_billing']) && $shipping_details['same_as_billing'] != 1 ){
                        $shippingAddr = array(
                            "name" => $billing_details['billing_name'],
                            "addrLine1" => $billing_details['billing_address'],
                            "city" => $billing_details['billing_city'],
                            "state" => $billing_details['billing_state_province'],
                            "zipCode" => $billing_details['billing_postalcode'],
                            "country" => $billing_details['billing_country'],
                            "email" => $billing_details['billing_email']
                        );
                        $resp = $checkout->makePayment($token, $amount, $orderId, $billingAddr, $shippingAddr );
                    }else{
                        $resp = $checkout->makePayment($token, $amount, $orderId, $billingAddr );
                    }
                    do_action('ptc_after_checkout', $resp, $amount, $orderId, $billingAddr, $shippingAddr);
                }
            }
        }
        
        /**
         * PTC_Payment_Shortcode::_show_input_fields()
         * Display form in sructure
         * @param mixed $input_fields -> array of form filds
         * @return void
         */
        private function _show_input_fields( $input_fields ){
    		foreach( $input_fields as $fields ){
    			switch( $fields["type"] ){
    				case("text"):
    					$wrapper_attr = "";
    					foreach( $fields["wrapper_atts"] as $attr => $value ){
    						$wrapper_attr .= " " . $attr . "='" . $value . "'";
    					}				
    					
    					$attrs = "";
    					if( isset( $fields["attrs"] ) ){
    						foreach( $fields["attrs"] as $attr => $value ){
    							$attrs .= " " . $attr . "='" . $value . "'";
    						}
    					}
    				?>
    					<div <?php echo $wrapper_attr; ?>>
    						<?php if(isset( $fields["label"] )) { ?> <label class="input_label" for="<?php echo $fields["label_for"]; ?>"><?php echo $fields["label"]; ?></label><?php } ?>
    						<input type="text" <?php echo $attrs; ?> name="<?php echo esc_attr( $fields["name"] ); ?>" value="<?php echo esc_attr( $fields["value"] ); ?>" />
    					</div>
    				<?php
    				break;
    				case("textarea"):
    					$wrapper_attr = "";
    					foreach( $fields["wrapper_atts"] as $attr => $value ){
    						$wrapper_attr .= " " . $attr . "='" . $value . "'";
    					}				
    					
    					$attrs = "";
    					if( isset( $fields["attrs"] ) ){
    						foreach( $fields["attrs"] as $attr => $value ){
    							$attrs .= " " . $attr . "='" . $value . "'";
    						}
    					}
    				?>
    					<div <?php echo $wrapper_attr; ?>>
    						<textarea <?php echo $attrs; ?> name="<?php echo esc_attr( $fields["name"] ); ?>"><?php echo esc_attr( $fields["value"] ); ?></textarea>
    					</div>
    				<?php
    				break;
    				case("dropdown"):
    					$wrapper_attr = "";
    					foreach( $fields["wrapper_atts"] as $attr => $value ){
    						$wrapper_attr .= " " . $attr . "='" . $value . "'";
    					}				
    					
    					$attrs = "";
    					if( isset( $fields["attrs"] ) ){
    						foreach( $fields["attrs"] as $attr => $value ){
    							$attrs .= " " . $attr . "='" . $value . "'";
    						}
    					}
    				?>
    					<div <?php echo $wrapper_attr; ?>>
    						<?php if(isset( $fields["label"] )) { ?> <label class="input_label" for="<?php echo $fields["label_for"]; ?>"><?php echo $fields["label"]; ?></label><?php } ?>
    						<select <?php echo $attrs; ?> name="<?php echo esc_attr( $fields["name"] ); ?>">
    						<?php echo $fields["value"]; ?>
    						</select>
    					</div>
    				<?php
    				break;
    				case("checkbox"):
    					$wrapper_attr = "";
    					foreach( $fields["wrapper_atts"] as $attr => $value ){
    						$wrapper_attr .= " " . $attr . "='" . $value . "'";
    					}				
    					
    					$attrs = "";
    					if( isset( $fields["attrs"] ) ){
    						foreach( $fields["attrs"] as $attr => $value ){
    							$attrs .= " " . $attr . "='" . $value . "'";
    						}
    					}
    				?>
    					<div <?php echo $wrapper_attr; ?>>
                             <?php if(isset( $fields["label"] )) { ?><label class="radio_label" for="<?php echo $fields["label_for"]; ?>"><?php } ?>
    						 <input type="checkbox" name="<?php echo esc_attr( $fields["name"] ); ?>" value="<?php echo $fields["value"]; ?>"  <?php echo $attrs; ?> <?php if( $fields["checked"] ) echo "checked" ?> />
    						 <?php if(isset( $fields["label"] )) { ?><?php echo $fields["label"]; ?><?php } ?>
                             <?php if(isset( $fields["label"] )) { ?></label><?php } ?>
    					</div>
    				<?php
    				break;
    				case("radio"):
    					$wrapper_attr = "";
    						foreach( $fields["wrapper_atts"] as $attr => $value ){
    							$wrapper_attr .= " " . $attr . "='" . $value . "'";
    						}	
    					?>
    					<div <?php echo $wrapper_attr; ?>>
    					<?php
    					foreach( $fields["value"] as $field ){						
    						$attrs = "";						
    						if( isset( $field["attrs"] ) ){
    							foreach( $field["attrs"] as $attr => $value ){
    								$attrs .= " " . $attr . "='" . $value . "'";
    							}
    						}
    					?>
    						<div class="options_wrapper">
    							<label class="radio_label" for="<?php echo $field["label_for"]; ?>"><?php echo $field["label"]; ?></label>
    							<input type="radio" name="<?php echo esc_attr( $fields["name"] ); ?>" value="<?php echo $field["value"]; ?>"  <?php echo $attrs; ?> <?php if( $field["checked"] ) echo "checked" ?> />
    						</div>
    					<?php
    					}
    					?>
    					</div>
    					<?php					
    				break;
    				case("file"):
    					$wrapper_attr = "";
    					foreach( $fields["wrapper_atts"] as $attr => $value ){
    						$wrapper_attr .= " " . $attr . "='" . $value . "'";
    					}				
    					
    					$attrs = "";
    					if( isset( $fields["attrs"] ) ){
    						foreach( $fields["attrs"] as $attr => $value ){
    							$attrs .= " " . $attr . "='" . $value . "'";
    						}
    					}
    				?>
    					  <div class="field <?php if( isset( $fields["has_error"] ) && $fields["has_error"] ) echo "required"; ?>">
    						<div class="row">
    							<div <?php echo $wrapper_attr; ?>>
    								<label class="field_label" for="<?php echo $fields["label_for"]; ?>"><?php echo $fields["label"]; ?></label>
    								<input name="<?php echo $fields["name"]; ?>" <?php echo $attrs; ?> type="file">
    							</div>
    							<?php
    							if( isset( $fields["has_error"] ) &&  $fields["has_error"] ){
    								if( isset( $fields["error_message"] ) && trim( $fields["error_message"] != "" )){
    							?>
    								<div class="validation_message"><?php echo $fields["error_message"]; ?></div>
    							<?php }
    								}
    							?>
    						</div>
    						
    					</div>					
    				<?php
    				break;
    			}
    		}
    	}
        
        /**
         * PTC_Payment_Shortcode::_set_form_structure()
         * Set array of the form filds
         * @returns array contained key=>value pairs of the requested key and field
         */
        private function _set_form_structure(){
            $form_structure = array( 
                "address" => array(
    				"title" => esc_html__( "Billing Information", PTC_TEXT_DOMAIN ),
                    "description" => esc_html__( "The billing address must be required to complete payment proccess.", PTC_TEXT_DOMAIN ),				
    				"label_for" => "billing_country",
    				"required" => true,
    				"has_error" => ( isset( $this->_errors["billing_country"] ) || isset( $this->_errors["billing_name"] ) || isset( $this->_errors["billing_address"] ) || isset( $this->_errors["billing_city"] ) || isset( $this->_errors["billing_state_province"] )  || isset( $this->_errors["billing_postalcode"] ) || isset( $this->_errors["billing_country"] ) ),
    				"error_message" => esc_html__( "This field is required. Please enter a complete address.", PTC_TEXT_DOMAIN ),
    				"nxt_btn" => array(
                        "show" => "shipping_address"
                    ),
                    "fields" => array(
    					"country" => array(
    						"label_for" => "billing_country",
    						"label" => isset( $this->_errors["billing_country"] ) ? $this->_errors["billing_country"]:esc_html__( "Country", PTC_TEXT_DOMAIN ),
    						"type" => "dropdown",
    						"name" => "billing_details[billing_country]",
    						"value" => get_country_dropdown_list(ptc_get_array_value( $this->_values, "billing_country" ), true, apply_filters('remove_country_key', array())),
    						"wrapper_atts" => array(
    							"class" => "col-one"
    						),
    						"attrs" => array(
    							"id" => "billing_country",
    						)
    					),
    					"billing_name" => array(
    						"label" => isset( $this->_errors["billing_name"] ) ? $this->_errors["billing_name"]:esc_html__( "Full Name", PTC_TEXT_DOMAIN ),
    						"label_for" => "billing_name",
    						"type" => "text",
    						"name" => "billing_details[billing_name]",
    						"value" => ptc_get_array_value( $this->_values, "billing_name" ),
    						"wrapper_atts" => array(
    							"class" => "col-one"
    						),
    						"attrs" => array(								
    							"id" => "billing_name",
    						)
    					),
    					"billing_address" => array(
    						"label" => isset( $this->_errors["billing_address"] ) ? $this->_errors["billing_address"]:esc_html__( "Street Address", PTC_TEXT_DOMAIN ),
    						"label_for" => "billing_address",
    						"type" => "text",
    						"name" => "billing_details[billing_address]",
    						"value" => ptc_get_array_value( $this->_values, "billing_address" ),
    						"wrapper_atts" => array(
    							"class" => "col-one"
    						),
    						"attrs" => array(								
    							"id" => "billing_address",
    						)
    					),
    					"city" => array(
    						"label_for" => "billing_city",
    						"label" => isset( $this->_errors["billing_city"] ) ? $this->_errors["billing_city"]:esc_html__( "City", PTC_TEXT_DOMAIN ),
    						"type" => "text",
    						"name" => "billing_details[billing_city]",
    						"value" => ptc_get_array_value( $this->_values, "billing_city" ),
    						"wrapper_atts" => array(
    							"class" => "col-two"
    						),
    						"attrs" => array(								
    							"id" => "billing_city",
    						)
    					),
    					"state" => array(
    						"label_for" => "billing_state_province",
    						"label" => isset( $this->_errors["billing_state_province"] ) ? $this->_errors["billing_state_province"]:esc_html__( "State / Province / Region", PTC_TEXT_DOMAIN ),
    						"type" => "text",
    						"name" => "billing_details[billing_state_province]",
    						"value" => ptc_get_array_value( $this->_values, "billing_state_province" ),
    						"wrapper_atts" => array(
    							"class" => "col-two"
    						),
    						"attrs" => array(								
    							"id" => "billing_state_province",
    						)
    					),
    					"zip" => array(
    						"label_for" => "billing_postalcode",
    						"label" => isset( $this->_errors["billing_postalcode"] ) ? $this->_errors["billing_postalcode"]:esc_html__( "ZIP / Postal Code", PTC_TEXT_DOMAIN ),
    						"type" => "text",
    						"name" => "billing_details[billing_postalcode]",
    						"value" => ptc_get_array_value( $this->_values, "billing_postalcode" ),
    						"wrapper_atts" => array(
    							"class" => "col-two"
    						),
    						"attrs" => array(								
    							"id" => "billing_postalcode",
    						)
    					),
    					"email" => array(
    						"label_for" => "billing_email",
    						"label" => isset( $this->_errors["billing_email"] ) ? $this->_errors["billing_email"]:esc_html__( "Email Address", PTC_TEXT_DOMAIN ),
    						"type" => "text",
    						"name" => "billing_details[billing_email]",
    						"value" => ptc_get_array_value( $this->_values, "billing_email" ),
    						"wrapper_atts" => array(
    							"class" => "col-two"
    						),
    						"attrs" => array(								
    							"id" => "billing_email",
    						)
    					)
    				)
    			), 
                "shipping_address" => array(
    				"title" => esc_html__( "Shipping Information", PTC_TEXT_DOMAIN ),
                    "description" => esc_html__( "The shipping address must not be required to payment. If this address is same with billing address please tick same as billing information button.", PTC_TEXT_DOMAIN ),				
    				"label_for" => "shipping_country",
    				"required" => false,
    				"has_error" => ( isset( $this->_errors["shipping_name"] ) || isset( $this->_errors["shipping_address"] ) || isset( $this->_errors["shipping_city"] ) || isset( $this->_errors["shipping_state_province"] )  || isset( $this->_errors["shipping_postalcode"] ) || isset( $this->_errors["shipping_country"] ) ),
    				"error_message" => esc_html__( "This field is required. Please enter a complete address.", PTC_TEXT_DOMAIN ),
    				"nxt_btn" => array(
                        "show" => "card_details"
                    ),
                    "prev_btn" => array(
                        "show" => "address"
                    ),
                    "fields" => array(
    					"same_as_billing" => array(
    						"label_for" => "same_as_billing",
    						"label" => isset( $this->_errors["same_as_billing"] ) ? $this->_errors["same_as_billing"]:esc_html__( "My billing information is the same as my shipping information.", PTC_TEXT_DOMAIN ),
    						"type" => "checkbox",
    						"name" => "shipping_details[same_as_billing]",
    						"value" => 1,
                            "checked" => ( ptc_get_array_value( $this->_values, "same_as_billing" ) == 1 ),
    						"wrapper_atts" => array(
    							"class" => "col-one"
    						),
    						"attrs" => array(
    							"id" => "same_as_billing",
    						)
    					),
    					"country" => array(
    						"label_for" => "shipping_country",
    						"label" => isset( $this->_errors["shipping_country"] ) ? $this->_errors["shipping_country"]:esc_html__( "Country", PTC_TEXT_DOMAIN ),
    						"type" => "dropdown",
    						"name" => "shipping_details[shipping_country]",
    						"value" => get_country_dropdown_list(ptc_get_array_value( $this->_values, "shipping_country" ), true, apply_filters('remove_country_key', array())),
    						"wrapper_atts" => array(
    							"class" => "ptc-shipping-items col-one"
    						),
    						"attrs" => array(
    							"id" => "shipping_country",
    						)
    					),
    					"shipping_name" => array(
    						"label" => isset( $this->_errors["shipping_name"] ) ? $this->_errors["shipping_name"]:esc_html__( "Full Name", PTC_TEXT_DOMAIN ),
    						"label_for" => "shipping_name",
    						"type" => "text",
    						"name" => "shipping_details[shipping_name]",
    						"value" => ptc_get_array_value( $this->_values, "shipping_name" ),
    						"wrapper_atts" => array(
    							"class" => "ptc-shipping-items col-one"
    						),
    						"attrs" => array(								
    							"id" => "shipping_name",
    						)
    					),
    					"shipping_address" => array(
    						"label" => isset( $this->_errors["shipping_address"] ) ? $this->_errors["shipping_address"]:esc_html__( "Street Address", PTC_TEXT_DOMAIN ),
    						"label_for" => "shipping_address",
    						"type" => "text",
    						"name" => "shipping_details[shipping_address]",
    						"value" => ptc_get_array_value( $this->_values, "shipping_address" ),
    						"wrapper_atts" => array(
    							"class" => "ptc-shipping-items col-one"
    						),
    						"attrs" => array(								
    							"id" => "shipping_address",
    						)
    					),
    					"city" => array(
    						"label_for" => "shipping_city",
    						"label" => isset( $this->_errors["shipping_city"] ) ? $this->_errors["shipping_city"]:esc_html__( "City", PTC_TEXT_DOMAIN ),
    						"type" => "text",
    						"name" => "shipping_details[shipping_city]",
    						"value" => ptc_get_array_value( $this->_values, "shipping_city" ),
    						"wrapper_atts" => array(
    							"class" => "ptc-shipping-items col-two"
    						),
    						"attrs" => array(								
    							"id" => "shipping_city",
    						)
    					),
    					"state" => array(
    						"label_for" => "shipping_state_province",
    						"label" => isset( $this->_errors["shipping_state_province"] ) ? $this->_errors["shipping_state_province"]:esc_html__( "State / Province / Region", PTC_TEXT_DOMAIN ),
    						"type" => "text",
    						"name" => "shipping_details[shipping_state_province]",
    						"value" => ptc_get_array_value( $this->_values, "shipping_state_province" ),
    						"wrapper_atts" => array(
    							"class" => "ptc-shipping-items col-two"
    						),
    						"attrs" => array(								
    							"id" => "shipping_state_province",
    						)
    					),
    					"zip" => array(
    						"label_for" => "shipping_postalcode",
    						"label" => isset( $this->_errors["shipping_postalcode"] ) ? $this->_errors["shipping_postalcode"]:esc_html__( "ZIP / Postal Code", PTC_TEXT_DOMAIN ),
    						"type" => "text",
    						"name" => "shipping_details[shipping_postalcode]",
    						"value" => ptc_get_array_value( $this->_values, "shipping_postalcode" ),
    						"wrapper_atts" => array(
    							"class" => "ptc-shipping-items col-two"
    						),
    						"attrs" => array(								
    							"id" => "shipping_postalcode",
    						)
    					),
    					"email" => array(
    						"label_for" => "shipping_email",
    						"label" => isset( $this->_errors["shipping_email"] ) ? $this->_errors["shipping_email"]:esc_html__( "Email Address", PTC_TEXT_DOMAIN ),
    						"type" => "text",
    						"name" => "shipping_details[shipping_email]",
    						"value" => ptc_get_array_value( $this->_values, "shipping_email" ),
    						"wrapper_atts" => array(
    							"class" => "ptc-shipping-items col-two"
    						),
    						"attrs" => array(								
    							"id" => "shipping_email",
    						)
    					)
    				)
    			)     
    		);		
    		return $form_structure;
    	}
        
        
    }
    //PTC_Payment_Shortcode termination
}