<?php
/**
 * Options Plugin
 * Make configutarion
*/

if ( !class_exists('real_content_lock_make') ) {

class real_content_lock_make{
    
    var $components = array();

    function __construct(){

        if( is_admin() ){
            self::configuration_plugin();
        }else{
            self::parameters();
        }

    }


    function getHeaderPlugin(){

        $url_plugin = plugins_url();





        return array('id'             =>'real_content_locker_id',
                     'id_menu'        =>'real-content-locker',
                     'name'           =>'Real Content Locker',
                     'name_long'      =>'Real Content Locker',
                     'name_option'    =>'real_content_locker',
                     'name_plugin_url'=>'real-content-locker',
                     'descripcion'    =>'Share your viral content and get traffic to your website.',
                     'version'        =>'1.9.2',
                     'db_version'     =>'1.0',
                     'url'            =>'', 
                     'logo'           =>'<i class="fa fa-file-excel-o" style="padding:11px 13px 12px 15px;color: rgb(150, 150, 150);"></i>',
                     'logo_text'      =>'', // alt of image
                     'slogan'         =>'', // powered by <a href="">iLenTheme</a>
                     'url_framework'  => "$url_plugin/real-content-locker/assets/ilenframework",
                     'theme_imagen'   => "$url_plugin/real-content-locker/assets/images",
                     'languages'      => "$url_plugin/real-content-locker/assets/languages",
                     'link_donate'    => 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LJ8X9UKM3Q7AU',
                     'wp_support'     =>'http://support.ilentheme.com/forums/forum/plugins/real-content-locker/',
                     'twitter'        => '',
                     'wp_review'      => '',
                     'type'           =>'plugin-tabs',
                     'method'         =>'free',
                     'themeadmin'     =>'fresh',
                     'scripts_admin'  =>array( 'page' => array('real-content-locker' => array('jquery_ui_reset')), ));
    }


    function getOptionsPlugin(){

    global ${'tabs_plugin_' . $this->parameter['name_option']};
    $url = admin_url('options-general.php?page='.$this->parameter['id_menu']); 
    ${'tabs_plugin_' . $this->parameter['name_option']} = array();
    ${'tabs_plugin_' . $this->parameter['name_option']}['tab01']=array('id'=>'general','name'=>'General','icon'=>'','link'=>$url,'columns'=>2,'sidebar-file'=> plugin_dir_path( __FILE__ ).'/sidebar-general.php','width'=>120 );

    return array('a'=>array(                'title'      => __('Basic',$this->parameter['name_option']), 
                                            'title_large'=> __('',$this->parameter['name_option']), 
                                            'description'=> '',  
                                            'icon'       => '',
                                            'tab'        => 'general',
                                            'default'    => 1,
                                            

                                            'options'    => array( 
                                                                

                                                                array(  'title' =>__('Title',$this->parameter['name_option']),
                                                                        'help'  =>__('Enter the title by default that users will read to share the content.',$this->parameter['name_option']),
                                                                        'type'  =>'text',
                                                                        'value' =>'<h3>Share with your friends to unlock the content</h3>',
                                                                        'id'    =>$this->parameter['name_option']. '_' . 'title',
                                                                        'name'  =>$this->parameter['name_option']. '_' . 'title',
                                                                        'class' =>'',
                                                                        'row'   =>array('a','b')),

                                                                array(  'title' =>__('Facebook share button (WEB)',$this->parameter['name_option']),
                                                                        'help'  =>__('Activates the facebook share button.',$this->parameter['name_option']),
                                                                        'type'  =>'checkbox',
                                                                        'value' =>'1',
                                                                        'value_check'=>1,
                                                                        'id'    =>$this->parameter['name_option']. '_' . 'button_fb',
                                                                        'name'  =>$this->parameter['name_option']. '_' . 'button_fb',
                                                                        'class' =>'',  
                                                                        'row'   =>array('a','b')),
                                                                

                                                                array(  'title' =>__('Style',$this->parameter['name_option']),
                                                                            'help'  =>__('Select the style you want for your locked content',$this->parameter['name_option']),
                                                                            'type'  =>'select',
                                                                            'value' =>'',
                                                                            'items' =>array(''=>__('Default',$this->parameter['name_option']),
                                                                                            'style1'=>__('Style 1',$this->parameter['name_option']),
                                                                                            /*'style2'=>__('Style 2',$this->parameter['name_option']),
                                                                                            'style3'=>__('Style 3',$this->parameter['name_option']),
                                                                                            'style4'=>__('Style 4',$this->parameter['name_option']),
                                                                                            'style5'=>__('Style 5',$this->parameter['name_option']),
                                                                                            'style6'=>__('Style 6',$this->parameter['name_option']),
                                                                                            'style7'=>__('Style 7',$this->parameter['name_option']),
                                                                                            'style8'=>__('Style 8',$this->parameter['name_option']),
                                                                                            'style9'=>__('Style 9',$this->parameter['name_option']),
                                                                                            'style10'=>__('Style 10',$this->parameter['name_option']),*/
                                                                                            ),
                                                                            'id'    =>$this->parameter['name_option']. '_' . 'style',
                                                                            'name'  =>$this->parameter['name_option']. '_' . 'style',
                                                                            'class' =>'',
                                                                            'row'   =>array('a','b')),
                                                                

                                                                array(  'title' =>__('Days cache',$this->parameter['name_option']),
                                                                        'help'  =>__("Enter the number of days you want the contents are no longer locked eleven o'clock it has-been shared.",$this->parameter['name_option']),
                                                                        'type'  =>'text',
                                                                        'value' =>'0',
                                                                        'id'    =>$this->parameter['name_option']. '_' . 'day_cache',
                                                                        'name'  =>$this->parameter['name_option']. '_' . 'day_cache',
                                                                        'class' =>'',
                                                                        'row'   =>array('a','b')),


                                                            ),
                ),
                'last_update'=>time()

            );
        
    }













    /* NO REMOVE */

    function parameters(){
 
        $this->parameter = self::getHeaderPlugin();
    }

    function myoptions_build(){

        $this->options = self::getOptionsPlugin();

        return $this->options;
        
    }

    function use_components(){
        //code 
        $this->components = array();
        $this->components = array('bootstrap','flags','');

    }

    function configuration_plugin(){
        // set parameter 
        self::parameters();

        // my configuration 
        self::myoptions_build();

        // my component to use
        self::use_components();
    }
    /* !-- NO REMOVE */

}
}


?>