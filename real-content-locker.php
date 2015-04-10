<?php
/*
Plugin Name: Real Content Locker
Plugin URI:
Description: Share your viral <strong>Content</strong> and get traffic to your website.
Version: 1.6
Author: iLen
Author URI:
*/


if ( !class_exists('real_content_lock') ) {

require_once 'assets/ilenframework/assets/lib/utils.php'; // get utils
require_once 'assets/functions/options.php'; // get options plugins
require_once "assets/ilenframework/assets/lib/Mobile_Detect.php";

class real_content_lock extends real_content_lock_make{

    var $mobil_detect = null;
    function __construct(){

        global $if_utils;
        // ajax nonce for stats
        add_action( 'wp_ajax_nopriv_ajax-content', array( &$this, 'my_shared_content' ) );
        add_action( 'wp_ajax_ajax-content', array( &$this, 'my_shared_content' ) );

        parent::__construct(); 

        if( is_admin() ){

            // add support feature image
            //add_theme_support( 'post-thumbnails' );

            // set styles and script back-end
            add_action( 'admin_enqueue_scripts', array( &$this,'script_and_style_admin' ) );

            // when active plugin verify db 
            register_activation_hook( __FILE__, array( &$this,'RealContentLocker_install' ) );

            // add button 'video lock' to editor
            add_action('init', array( &$this,'add_button_content_locker') );

            // validate error (only developer)
            //add_action('activated_plugin',array( &$this,'save_error'));
            //echo get_option('plugin_error');

        }elseif( ! is_admin() ) {

            global $options_content_locker;

            // mobil detect
            $this->mobil_detect = new Mobile_Detect;

            // create shortcode
            add_shortcode('realcontentlocker', array( &$this,'show_RealContentLocker') );

            // get option plugin
            $options_content_locker = $if_utils->IF_get_option( $this->parameter['name_option'] );

            // add filter on hook wp_head
            if( (isset($options_content_locker->button_fb) && $options_content_locker->button_fb) || isset($options_content_locker->button_fb_mobil) && $options_content_locker->button_fb_mobil ){
                add_filter('wp_head', array( &$this,'add_fb_meta_image') );
            }

            // set styles and script front-end
            add_action( 'wp_enqueue_scripts', array( &$this,'script_and_style_front' ) );

        }

    }


    function save_error(){
        //update_option('plugin_error',  ob_get_contents());
        //update_option('plugin_error',  '');
    }


    /* FUNCTION AJAX */
    function my_shared_content() {
        

        //if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'shared-videos-nonce' ) ) {
            //die( 'Security check' ); 
        //}

        require_once("assets/ilenframework/assets/lib/geo.php");
       
        global $IF_MyGEO,$wpdb;

        $table_name = $wpdb->prefix . 'realcontentlocker';
        $post_id    = $_REQUEST["post_id"];
        $social     = $_REQUEST["social"];

        $la = $IF_MyGEO->latitude;
        $lo = $IF_MyGEO->longitude;
        $cc = $IF_MyGEO->countryCode;
        $cn = $IF_MyGEO->countryName;
        $re = $IF_MyGEO->region;
        $ci = $IF_MyGEO->city;
        $ip = $IF_MyGEO->ip;

        $wpdb->insert(
            $table_name, 
            array(
                'creation'     => current_time('timestamp'), 
                'creation2'    => current_time('mysql'), 
                'post_id'      => $post_id, 
                'la'           => "$la", 
                'lo'           => "$lo", 
                'country_code' => "$cc", 
                'country'      => "$cn", 
                'region'       => "$re", 
                'city'         => "$ci", 
                'ip'           => "$ip",
                'type_social'  => "$social",
            )
        );

        // send some information back to the javascipt handler
        header( "Content-Type: application/json" );
        echo json_encode( array(
          'success' => 'ok '.$table_name,
          'times'   => time()
        ) );
        exit;

    }


    function script_and_style_front(){

        global $options_content_locker;

        if( (isset($options_content_locker->button_fb) && $options_content_locker->button_fb) || isset($options_content_locker->button_fb_mobil) && $options_content_locker->button_fb_mobil ){
            wp_enqueue_script('facebook-js', 'http://connect.facebook.net/en_US/sdk.js#version=v2.3&xfbml=1', array('jquery'),$this->parameter['version'],FALSE);
        }


        wp_enqueue_style( 'front-'.$this->parameter["name_option"], plugins_url('/assets/css/front.css',__FILE__),'all',$this->parameter['version']);
        wp_enqueue_script( 'front-js-'.$this->parameter["name_option"], plugins_url('/assets/js/front.js',__FILE__), array( 'jquery' ), $this->parameter['version'], true );

        /* AJAX */
        wp_enqueue_script( 'ajax-content', plugin_dir_url( __FILE__ ) . 'assets/js/ajax.js', array( 'jquery' ) );
        wp_localize_script( 'ajax-content', 'AjaxContent', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'ajax-example-nonce' ),
            'facebook_api' => isset($options_content_locker->fb_api_id)? (int)$options_content_locker->fb_api_id:'',
        ) );

    }



    function script_and_style_admin(){

        wp_enqueue_style( 'admin-'.$this->parameter["name_option"], plugins_url('/assets/css/admin.css',__FILE__),'all',$this->parameter['version']);
        wp_enqueue_script('admin-js-'.$this->parameter["name_option"], plugins_url('/assets/js/admin.js',__FILE__), array( 'jquery' ), $this->parameter['version'], true );

    }
    
    
    function is_MobileOrTable(){

		if( $this->mobil_detect->isMobile() || $this->mobil_detect->isTablet() )
		 	return true;
		else
			return false;

	}


    function show_RealContentLocker( $atts, $content = null ){


        if( is_feed() ){
            return "<br />Watch it on the website/ Miralo en la pagina web<br />";
        }
        if ( !is_singular() ){
            return;
        }
        
        global $post, $options_content_locker;

        extract( shortcode_atts( array(
                'title' => '',
                'style' => '',
        ), $atts ) );

 


        // hash Youtube for identify each video
        $content_id_hash = sha1( rand(1,5000) );
        
        // url
        //$url_path = plugins_url().'/'.$this->parameter["name_plugin_url"]; //esc_url( home_url( '/' ) ).'wp-content/plugins/'.$this->parameter["name_plugin_url"];
        $url_path = null;
        $url_post = get_permalink();
        $style = isset($options_content_locker->style)?$options_content_locker->style:'';
        $title = isset($title) && $title?$title:$options_content_locker->title;

        $_html = "";
        $_html.= "<div class='realcontentlocker $style realcontentlocker_id_$content_id_hash ' data-version='".$this->parameter["version"]."'>";
        $_html.= "<div class='realcontentlocker__unlock'>";
        $_html.= "<div class='realcontentlocker__unlock_text'>".html_entity_decode( $title )."</div>";
        $_html.= "<div class='realcontentlocker__unlock_button_social'>";
                        $_html .= "
                        <script>
                        var isMobile = function() {
            
                    if( navigator.userAgent.match(/Android/i)
                    || navigator.userAgent.match(/webOS/i)
                    || navigator.userAgent.match(/iPhone/i)
                    || navigator.userAgent.match(/iPad/i)
                    || navigator.userAgent.match(/iPod/i)
                    || navigator.userAgent.match(/BlackBerry/i)
                    || navigator.userAgent.match(/Windows Phone/i)
                    ){
                        return true;
                    }
                    else {
                        return false;
                    }
            }
            
            if( isMobile() && AjaxContent.facebook_api ){
                //alert('entro:'+AjaxContent.facebook_api);
                //window.fbAsyncInit = function() {
                    FB.init({
                      appId      : AjaxContent.facebook_api,
                      xfbml      : true,
                      version    : 'v2.3'
                    });
                //};
            }
                        </script>
                        ";



                        // SET BUTTON FACBOOK
                        if( isset($options_content_locker->button_fb) && $options_content_locker->button_fb && !self::is_MobileOrTable() ){

                            $_html .="<div class='facebook-realcontentlocker facebook-realcontentlocker-id-$content_id_hash'>";

                            $_html .="<a href='#' onclick=\"unlocker_fb(".$post->ID.",'".$url_path."','$content_id_hash', '')\"> ";
                                    $_html .="<img src='{$this->parameter["theme_imagen"]}/facebook-button-shared-2014.png' style='margin-bottom: -1px;' />";
                                $_html .="</a>";

                            $_html .="</div>";

                        }elseif( self::is_MobileOrTable() &&  isset($options_content_locker->button_fb_mobil) && $options_content_locker->button_fb_mobil  ){
                            
                            if( isset($options_content_locker->fb_api_id) && $options_content_locker->fb_api_id ){
                            $_html .="";
                            }
                            $_html .="<div class='facebook-realcontentlocker facebook-realcontentlocker-id-$content_id_hash'>";

                                $_html .="<a href='#' onclick=\"unlocker_fb(".$post->ID.",'".$url_path."','$content_id_hash', '$options_content_locker->fb_api_id')\"> ";
                                    $_html .="<img src='{$this->parameter["theme_imagen"]}/facebook-button-shared-2014.png' style='margin-bottom: -1px;' />";
                                $_html .="</a>";

                                    
                                $_html .="</div>";
                            
                        }
                        
            $_html .= "</div>";
            $_html .= "</div>";
            $_html .= "<div class='realcontentlocker__content'>$content</div>";
        $_html .= "</div>";
 
        return $_html;
        

    }


    // Add meta og:image to the header!
    function add_fb_meta_image() {

        global $if_utils;

        if(  is_singular() ){

            // @see http://codex.wordpress.org/Function_Reference/get_shortcode_regex
            global $post;
            $pattern = get_shortcode_regex();
            if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
                && array_key_exists( 2, $matches )
                && in_array( 'realcontentlocker', $matches[2] ) )
            {

                if( isset($matches[5][0]) )
                    $url_youtube = $matches[5][0];

                    $image_share =   $if_utils->IF_get_image( "medium" );
                    $image_share = $image_share["src"];
                    echo '<meta property="og:image" content="'.$image_share.'"/>';    
            }
 
        }
        
    }


    



    // add button in the editor [shortcode (Dice)]
    /**
    * @link http://www.wpexplorer.com/wordpress-tinymce-tweaks/
    */
    function add_button_content_locker() {

        if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') )  
        {
          add_filter('mce_external_plugins', array( &$this,'add_plugin_content_locker') );  
          add_filter('mce_buttons', array( &$this,'register_button_content_locker') );  
        }
    }  
    function register_button_content_locker($buttons) {  
       array_push($buttons, "realcontentlocker");  
       return $buttons;  
    }  
    function add_plugin_content_locker($plugin_array) {  
       //$plugin_array['realcontentlocker'] = plugins_url()."/real-content-locker/assets/js/button.js";
       $plugin_array['realcontentlocker'] = plugins_url( 'assets/js/button.js', __FILE__ );
       
       return $plugin_array;  
    }

 
    /**
    * @see http://codex.wordpress.org/Creating_Tables_with_Plugins
    */
    function RealContentLocker_install(){

        global $wpdb;
        global $ivl_db_version;

        $rcl_db_version =  $this->parameter["db_version"];
        $table_name = $wpdb->prefix . 'realcontentlocker';

        $installed_ver = get_option( $this->parameter["name_option"].'_db_version' );

        // checks whether the table exists in the database
        $table_exits = false;
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
            $table_exits = true;
        }

        if ( $installed_ver != $rcl_db_version || $table_exits == false ) {

            /*
             * We'll set the default character set and collation for this table.
             * If we don't do this, some characters could end up being converted 
             * to just ?'s when saved in our table.
             */

            $charset_collate = '';

            if ( ! empty( $wpdb->charset ) ) {
              $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
            }

            if ( ! empty( $wpdb->collate ) ) {
              $charset_collate .= " COLLATE {$wpdb->collate}";
            }

            $sql = "CREATE TABLE $table_name (

                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    creation int(11) NOT NULL,
                    creation2 datetime NOT NULL,
                    post_id int(11) NOT NULL,
                    la varchar(50) NULL,
                    lo varchar(50) NULL,
                    country_code varchar(50) NULL,
                    country varchar(100) NULL,
                    region  varchar(100) NULL,
                    city  varchar(100) NULL,
                    ip  varchar(50) NULL,
                    type_social VARCHAR(40) DEFAULT 'facebook' NOT NULL,

                UNIQUE KEY id (id)
            ) $charset_collate;";
            
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );

            if( ! $installed_ver ){
                add_option( $this->parameter["name_option"].'_db_version', $rcl_db_version );
            }else{
                update_option( $this->parameter["name_option"].'_db_version', $rcl_db_version );
            }
        }

    }



} // end class
} // end if

global $IF_CONFIG;
unset($IF_CONFIG);
$IF_CONFIG = null;
$IF_CONFIG = new real_content_lock;

require_once "assets/ilenframework/core.php";
?>