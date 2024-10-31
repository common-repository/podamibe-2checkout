<?php

defined( 'ABSPATH' ) or die('No script kiddies please!'); // Exit if accessed directly.

/*
  Plugin name: Podamibe 2Checkout
  Plugin URI: http://podamibenepal.com/wordpress-plugins/
  Description: A perfect plugin for online payment using 2Checkout.
  Version: 1.0.3
  Author: Podamibe Nepal
  Author URI: http://podamibenepal.com
  Text Domain: ptc
  Domain Path: /languages/
  License: GPLv2 or later
 */
 
 
//Decleration of the necessary constants for plugin
!defined( 'PTC_PLUGIN_DIR' ) ? define( 'PTC_PLUGIN_DIR', plugin_dir_url( __FILE__ ) ):null;
!defined( 'PTC_IMAGE_DIR' ) ? define( 'PTC_IMAGE_DIR', plugin_dir_url( __FILE__ ) . 'images/' ): null;
!defined( 'PTC_CSS_DIR' ) ? define( 'PTC_CSS_DIR', plugin_dir_url( __FILE__ ) . 'css/' ): null;
!defined( 'PTC_JS_DIR' ) ? define( 'PTC_JS_DIR', plugin_dir_url( __FILE__ ) . 'js/' ): null;
!defined( 'PTC_INC_BAC_DIR' ) ? define( 'PTC_INC_BAC_DIR', plugin_dir_path( __FILE__ ) . 'inc/backend/' ):null;
!defined( 'PTC_INC_FRN_DIR' ) ? define( 'PTC_INC_FRN_DIR', plugin_dir_path( __FILE__ ) . 'inc/frontend/' ):null;
!defined( 'PTC_LIB_DIR' ) ? define( 'PTC_LIB_DIR', plugin_dir_path( __FILE__ ) . 'lib/' ):null;
!defined( 'PTC_LANG_DIR' ) ? define( 'PTC_LANG_DIR', basename( dirname( __FILE__ ) ) . '/languages/' ):null;

!defined('PTC_VERSION') ? define( 'PTC_VERSION', '1.0.2' ) : null;

!defined('PTC_TEXT_DOMAIN') ? define( 'PTC_TEXT_DOMAIN', 'ptc' ) : null;
!defined('PTC_SETTING_NAME') ? define( 'PTC_SETTING_NAME', 'ptc_setting' ) : null;

/**
 * 
 * Decleration of the class for necessary configuration of a plugin
 * @package     Podamibe Advertisement Management
 * @subpackage  admin
 * @copyright   Copyright (c) 2016, Podamibe
 * @author      Prakash Sunuwar
 * @since       1.0  
 */
if ( !class_exists( 'PTC_Class' ) ) {
         
    class PTC_Class {
        /**
    	 * default settings for Podamibe 2Checkout.
    	 *
    	 * @since 1.0
    	 * @var string
    	 */
        public $_ptc_settings = '';
        
        private $mainPage;
        
        function __construct(){
            $this->_ptc_settings = get_option( PTC_SETTING_NAME ); //get the plugin variable contents from the options table.
            register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) ); //load the default setting for the plugin while activating
			add_action( 'init', array( $this, 'session_init' ) ); //start the session if not started yet.
            $this->_includes();
            add_action('admin_menu', array($this, 'ptc_admin_menu'));
            add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_assets' ) ); //registers all the assets required for wp-admin
            add_action( 'wp_enqueue_scripts', array($this,'register_front_assets') ); //registers all the assets required for Frontend
            
            add_action( 'after_setup_theme',array( $this, "initialize_shortcodes" ));	//register shortcodes     
            
            
            add_filter("plugin_row_meta", array($this, 'get_extra_meta_links'), 10, 4);       
           
        }
        
        //called when plugin is activated
		function plugin_activation() {

			global $wpdb;
            if ( is_multisite() ) {
                $current_blog = $wpdb->blogid;
                // Get all blogs in the network and activate plugin on each one
                $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
                foreach ( $blog_ids as $blog_id ) {
                    switch_to_blog( $blog_id );
                    if ( !get_option( PTC_SETTING_NAME ) ) {
						require( 'inc/backend/activation.php' );
					}
                }
            }else{
                if ( !get_option( PTC_SETTING_NAME ) ) {
					require( 'inc/backend/activation.php' );
				}
            }           
		}
        
        //starts the session with the call of init hook
        function session_init() {
			if ( !session_id() && !headers_sent() ) {
				session_start();
			}
		}
        
        public function initialize_shortcodes(){
    		PTC_Payment_Shortcode::init();
    	}                
        
        /**
         *
         * 	This function is used to create admin menus 
         * 	for auto login plugin
         * 	@access public
         * 	@author 
         * 	@since  1.0
         * 	@return void
         * 	
         */
        public function ptc_admin_menu() {
               
            $mainMenu = __('2Checkout', PTC_TEXT_DOMAIN);
            add_menu_page($mainMenu, $mainMenu, 'manage_options', 'ptc-main-page', array( $this, 'ptc_main_page' ), PTC_IMAGE_DIR . '2checkout.png');
        }
        
        /**
         *
         * 	This function is used for manage the settings
         * 	for auto login form
         * 	@access public
         * 	@author 
         * 	@since  1.0
         * 	@return void
         * 	
         */
        public function ptc_main_page() {
            $mainPage = new PTC_Main_Page();         
            $mainPage->main_view();
        }
        
        /**
     	 * This method is called to include required file
     	 * @param null
     	 * @return void
     	 * @since 1.0
     	 * 
     	 **/
        private function _includes(){
            require_once( 'inc/functions.php' );
            require_once( PTC_LIB_DIR.'class-countries.php' );
            require_once( PTC_INC_BAC_DIR.'class-ptc-main-page.php' );
            require_once( PTC_INC_FRN_DIR.'ptc-shortcode.php' );
            require_once( PTC_LIB_DIR.'2checkout.php' );
            require_once( PTC_LIB_DIR.'class-2c-validation.php' );
            require_once( PTC_LIB_DIR.'class-validate-form.php' );        
        }
        
        //functions to register admin styles and scripts
		function register_admin_assets() {
			wp_enqueue_style( 'ptc-admin-style', PTC_CSS_DIR . 'backend.css', false, PTC_VERSION ); //registering plugin admin css
            
			wp_enqueue_script( 'ptc-admin-scripts', PTC_JS_DIR . 'backend.js', array( 'jquery' ), PTC_VERSION ); //registering plugin's admin js
		}
        
        //functions to register frontend styles and scripts
        function register_front_assets(){
            wp_enqueue_style( 'ptc-front-style', PTC_CSS_DIR . 'frontend.css', false, PTC_VERSION ); //registering plugin frontend css
            
            wp_enqueue_script( 'jquery');
            wp_enqueue_script( 'ptc-2co-scripts', PTC_JS_DIR . '2co.min.js', array( 'jquery' ), PTC_VERSION );
            wp_enqueue_script( 'ptc-front-scripts', PTC_JS_DIR . 'frontend.js', false, PTC_VERSION ); //registering plugin slick slider scripts
            
            // Localize the script with new data
            $ptc_script_variable = array('ajaxurl' => admin_url( 'admin-ajax.php' ));
            if( isset($this->_ptc_settings['sandbox_power']) && $this->_ptc_settings['sandbox_power']==1 ){
                $ptc_script_variable['publishable_key'] = isset($this->_ptc_settings['sandbox_publishable_key'])?$this->_ptc_settings['sandbox_publishable_key']:'';
                $ptc_script_variable['private_key'] = isset($this->_ptc_settings['sandbox_private_key'])?$this->_ptc_settings['sandbox_private_key']:'';
                $ptc_script_variable['seller_id'] = isset($this->_ptc_settings['sandbox_seller_id'])?$this->_ptc_settings['sandbox_seller_id']:'';
            }else{
                $ptc_script_variable['publishable_key'] = isset($this->_ptc_settings['publishable_key'])?$this->_ptc_settings['publishable_key']:'';
                $ptc_script_variable['private_key'] = isset($this->_ptc_settings['private_key'])?$this->_ptc_settings['private_key']:'';
                $ptc_script_variable['seller_id'] = isset($this->_ptc_settings['seller_id'])?$this->_ptc_settings['seller_id']:'';
            }
            
            wp_localize_script( 'ptc-front-scripts', 'ptc', $ptc_script_variable );

        }    
        
        

        /**
         * Adds extra links to the plugin activation page
         */
        public function get_extra_meta_links($meta, $file, $data, $status) {

            if (plugin_basename(__FILE__) == $file) {
                $meta[] = "<a href='http://shop.podamibenepal.com/forums/forum/support/' target='_blank'>" . __('Support', 'ptc') . "</a>";
                $meta[] = "<a href='http://shop.podamibenepal.com/downloads/podamibe-2checkout/' target='_blank'>" . __('Documentation  ', 'ptc') . "</a>";
                $meta[] = "<a href='https://wordpress.org/support/plugin/podamibe-2checkout/reviews#new-post' target='_blank' title='" . __('Leave a review', 'ptc') . "'><i class='ml-stars'><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg></i></a>";
            }
            return $meta;
        }
        
        
    }
    //PTC_Class termination

	$ptc_object = new PTC_Class();  
}