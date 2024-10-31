<?php
if (!defined('ABSPATH')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    die('No script kiddies please!');
}

if( !class_exists( 'PTC_Main_Page' ) ){
    
    class PTC_Main_Page {
        
        private $settings;
        
        function __construct(){
            $this->create();
            $this->settings = get_option(PTC_SETTING_NAME);
        }
        
        /**
         * This method is called to display setting page
     	 * @param null
     	 * @return void
     	 * @since 1.0
         */        
        public function main_view(){
            ?>
                <div class="ptc-main-wrapper wrap">                                   
                	<div class="ptc-main-title">
                		<?php esc_html_e('Podamibe 2Checkout Settings', PTC_TEXT_DOMAIN);?>
                	</div>

					<p class="elementor-message-actions">
						<a href="<?php echo esc_url( 'http://shop.podamibenepal.com/forums/forum/support/' ); ?>" class="button button-primary"><?php esc_html_e('Documentation', 'ptc');?></a>
						<a href="<?php echo esc_url( 'http://shop.podamibenepal.com/downloads/podamibe-2checkout/' ); ?>" class="button button-primary"><?php esc_html_e('Details', 'ptc');?></a>
						<a href="<?php echo esc_url( 'http://shop.podamibenepal.com/forums/forum/support/' ); ?>" class="button button-primary"><?php esc_html_e('Live Support', 'ptc');?></a>
					</p>
                    
          			<ul class="ptc-tabs">
          				<li class="tab-link" data-tab="ptc-tab-1">
          					<?php esc_html_e("2Checkout Settings", PTC_SETTING_NAME); ?>
          				</li>
          				<li class="tab-link" data-tab="ptc-tab-2">
          					<?php esc_html_e("How To Use", PTC_SETTING_NAME); ?>
          				</li>
          				<!--li class="tab-link" data-tab="ptc-tab-3">
          					<?php //esc_html_e("About", PTC_SETTING_NAME); ?>
          				</li>-->
          			</ul>
          			<div id="ptc-tab-1" class="ptc-tab-content">
          				<div id="poststuff" class="ptc-settings-wrap">
                            <form action="" method="post">
                                <div class="postbox">
                        			<h3 class="hndle"><span><?php esc_html_e('Form fields options', PTC_TEXT_DOMAIN);?></span></h3>
                        			<div class="inside">
                        				<table class="form-table">
                        					<tbody>
                        						<tr>
                        							<th>
                        								<label><?php esc_html_e('Publishable Key');?></label>
                        							</th>
                        							<td>
                        								<input type="text" name="ptc_settings[publishable_key]" class="ptc-text" value="<?php if($this->settings['publishable_key']){echo $this->settings['publishable_key'];}?>" />
                        							</td>
                        						</tr>
                        						<tr>
                        							<th>
                        								<label><?php esc_html_e('Private Key');?></label>
                        							</th>
                        							<td>
                        								<input type="text" name="ptc_settings[private_key]" class="ptc-text" value="<?php if($this->settings['private_key']){echo $this->settings['private_key'];}?>" />
                        							</td>
                        						</tr>
                        						<tr>
                        							<th>
                        								<label><?php esc_html_e('Seller Id');?></label>
                        							</th>
                        							<td>
                        								<input type="text" name="ptc_settings[seller_id]" class="ptc-text" value="<?php if($this->settings['seller_id']){echo $this->settings['seller_id'];}?>" />
                        							</td>
                        						</tr>
                        					</tbody>
                        				</table>
                        			</div>
                                    <div class="inside">
                        				<table class="form-table ptc-sandbox">
                                            <thead>
                                                <tr>
                        							<th>
                        								<label for="ptc-sandbox-power"><?php esc_html_e('Use Sandbox');?></label>
                        							</th>
                                                    <td>
                                                        <input type="checkbox" id="ptc-sandbox-power" name="ptc_settings[sandbox_power]" value="1" <?php  if(isset($this->settings['sandbox_power']) && $this->settings['sandbox_power']==1){echo 'checked';}?> />
                                                    </td>
                        						</tr>
                                            </thead>
                        					<tbody>
                        						<tr>
                        							<th>
                        								<label><?php esc_html_e('Sandbox Publishable Key');?></label>
                        							</th>
                        							<td>
                        								<input type="text" name="ptc_settings[sandbox_publishable_key]" class="ptc-text" value="<?php if($this->settings['sandbox_publishable_key']){echo $this->settings['sandbox_publishable_key'];}?>" />
                        							</td>
                        						</tr>
                        						<tr>
                        							<th>
                        								<label><?php esc_html_e('Sandbox Private Key');?></label>
                        							</th>
                        							<td>
                        								<input type="text" name="ptc_settings[sandbox_private_key]" class="ptc-text" value="<?php if($this->settings['sandbox_private_key']){echo $this->settings['sandbox_private_key'];}?>" />
                        							</td>
                        						</tr>
                        						<tr>
                        							<th>
                        								<label><?php esc_html_e('Sandbox Seller Id');?></label>
                        							</th>
                        							<td>
                        								<input type="text" name="ptc_settings[sandbox_seller_id]" class="ptc-text" value="<?php if($this->settings['sandbox_seller_id']){echo $this->settings['sandbox_seller_id'];}?>" />
                        							</td>
                        						</tr>
                        					</tbody>
                        				</table>
                        			</div>
                                    <div class="inside">
                                        <table class="form-table">
                                            <tbody>
                        						<tr>
                        							<th>
                        								<label><?php esc_html_e('Currency Code');?></label>
                        							</th>
                        							<td>
                        								<input type="text" name="ptc_settings[currency_code]" placeholder="<?php esc_html_e('3-Letter ISO code for seller currency', PTC_TEXT_DOMAIN);?>" class="ptc-text" value="<?php if(isset($this->settings['currency_code']) && $this->settings['currency_code']){echo $this->settings['currency_code'];}?>" />
                        							</td>
                        						</tr>
                        						<tr>
                        							<th>
                        								<label><?php esc_html_e('Return Url');?></label>
                        							</th>
                        							<td>
                        								<input type="text" name="ptc_settings[return_url]" class="ptc-text" value="<?php if(isset($this->settings['return_url']) && $this->settings['return_url']){echo $this->settings['return_url'];}?>" />
                                                        <p><strong><?php esc_html_e('Note:');?></strong> <span><?php esc_html_e('https://www.example.com/cart/ to your return URL (URL for unsuccessful purchase)', PTC_TEXT_DOMAIN);?></span></p>
                        							</td>
                        						</tr>
                        						<tr>
                        							<th>
                        								<label><?php esc_html_e('Success Url');?></label>
                        							</th>
                        							<td>
                        								<input type="text" name="ptc_settings[notify_url]" class="ptc-text" value="<?php if(isset($this->settings['notify_url']) && $this->settings['notify_url']){echo $this->settings['notify_url'];}?>" />
                                                        <p><strong><?php esc_html_e('Note:');?></strong> <span><?php esc_html_e('Used to specify an approved URL on-the-fly, but is limited to the same domain that is used for your 2Checkout account, otherwise it will fail. This parameter will over-ride any URL set on the Site Management page. (no limit)', PTC_TEXT_DOMAIN);?></span></p>
                        							</td>
                        						</tr>
                        						<tr>
                        							<td>
                        								<?php wp_nonce_field('ptc_seting_nonce', 'ptc_seting_nonce_field') ?>
        								                <input type="submit" name="ptcSetting" class="button-primary" value="Save" />
                        							</td>
                        						</tr>
                                            </tbody>
                                        </table>
                                    </div>
                        		</div>
                            </form>
                        </div>
          			</div>
          			<div id="ptc-tab-2" class="ptc-tab-content">
          				<h3><?php esc_html_e('Follow the instruction to use', PTC_TEXT_DOMAIN);?></h3>
                        <p><?php esc_html_e('To display the', PTC_TEXT_DOMAIN);?> <strong><?php esc_html_e('2Checkout Form', PTC_TEXT_DOMAIN);?></strong> <?php esc_html_e('in your web site, you can use', PTC_TEXT_DOMAIN);?> <input type="text" value='[P2Checkout amount="give your pay amount here" orderid="give order id here"]' readonly="true" size="70" /> <?php esc_html_e( 'Shortcode', PTC_TEXT_DOMAIN );?>. <br /><br /><br /><strong><?php esc_html_e('For example: ');?></strong><br /><br />
                        <?php esc_html_e('Use this', PTC_TEXT_DOMAIN);?> <code>[P2Checkout amount="give your pay amount here" orderid="give order id here"]</code> <?php esc_html_e('shortcode to display in content', PTC_TEXT_DOMAIN);?>. <?php esc_html_e('For template use', PTC_TEXT_DOMAIN);?> <code>&lt;?php echo do_shortcode("[P2Checkout amount="give your pay amount here" orderid="give order id here"]");?&gt;</code>
                        <br /><br /><?php esc_html_e('Note,', PTC_TEXT_DOMAIN);?><br />
                        <strong><?php esc_html_e('amount: ');?></strong> <i><?php esc_html_e('Set the total amout that you want to charge for.', PTC_TEXT_DOMAIN);?></i><br />
                        <strong><?php esc_html_e('orderid: ');?></strong> <i><?php esc_html_e('Set the order id, if you have any for your order.', PTC_TEXT_DOMAIN);?></i>
                        </p>
          			</div>
          			<!--<div id="ptc-tab-3" class="ptc-tab-content">
          				about us
          			</div>-->
                    
                </div>
            <?php
        }
        
        public function create(){
            if(isset($_POST['ptcSetting'])){
                if( isset( $_POST['ptc_seting_nonce_field'] ) || wp_verify_nonce( $_POST['ptc_seting_nonce_field'], 'ptc_seting_nonce' ) ){
                    $ptc_settings = $_POST['ptc_settings'];
                    foreach( $ptc_settings as $key => $val ){
                        $ptc_settings[$key] = sanitize_text_field($val);
                    }
                    update_option(PTC_SETTING_NAME, $ptc_settings);
                }
            }
        }
        
        
        
        
    }
}