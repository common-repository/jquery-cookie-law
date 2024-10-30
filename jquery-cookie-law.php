<?php
/*
Plugin Name: MT Cookie Consent
Plugin URI: https://www.motostorie.blog
Description: This plugin add the javascript code for Cookie Law
Version: 4.0.0
Author: brjhcxnnwqjevwc
Author URI: https://profiles.wordpress.org/brjhcxnnwqjevwc/
Text Domain: jquery-cookie-law
*/

if ( ! defined( 'ABSPATH' ) ) {
	        die( 'Invalid request.' );
	}

if ( ! class_exists( 'jcookielawJS' ) ) :
class jcookielawJS {
    
    private $default     = array(
				'languages' 			=> '/languages/',
				'version'				=> '4.0',
				'plugin_options_key'    => 'jquery-cookie-law',
                'message'               => 'This website uses cookies to ensure you get the best experience on our website.',
                'acceptText'            => 'Got it!',
                'policyText'            => 'Learn more',
                'policyURL'             => 'https://cookiesandyou.com/',
				'expireDays'            => 90,
				'theme'					=> 'classic',
				'target'				=> '_self',
				'position'				=> 'bottom',
				'pbackground'			=> '#eaf7f7',
				'ptext'					=> '#5c7291',
				'bbackground'			=> '#56cbdb',
				'btext'					=> '#ffffff',
				'secure'				=> 'false',
				);    

	private function __construct() {}

	public static function my_jcookielaw_init() {
      	load_plugin_textdomain( 'jquery-cookie-law');
		add_action( 'wp_enqueue_scripts', 		array( __CLASS__, 'get_jcookielaw_head') );
		add_action( 'admin_menu',    			array( __CLASS__, 'jcookielaw_admin_menu') );
		add_action( 'admin_init',    			array( __CLASS__, 'jcookielaw_admin_init') );
		add_action( 'admin_enqueue_scripts', 	array( __CLASS__, 'add_color_picker') );
		}

	public static function add_color_picker( $hook ) {
            
        	wp_enqueue_style( 'wp-color-picker' ); 
        	wp_enqueue_script( 'color-cookiebar', plugins_url( '/js/color.js', __FILE__ ), array( 'wp-color-picker' ), false, true ); 

	}

	public static function get_jcookielaw_head() {

		wp_register_style( 'cookiebar', plugins_url( '/css/cookieconsent.min.css', __FILE__ ), array(), null );
		wp_enqueue_style( 'cookiebar' );

		wp_register_script( 'cookiebar', plugins_url( '/js/cookieconsent.min.js', __FILE__ ), array(), null, true );
		wp_enqueue_script( 'cookiebar' );
	
		$setting = array(                
				'message'    	=> esc_js(get_option('message')),
				'acceptText'    => esc_js(get_option('acceptText')),
                'policyText'    => esc_js(get_option('policyText')),
                'policyURL'    	=> esc_url(get_option('policyURL')),
				'expireDays'    => wp_kses(get_option('expireDays'), ''),
				'theme'        	=> wp_kses(get_option('theme'), ''),
				'target'        => wp_kses(get_option('target'), ''),
				'postion'       => wp_kses(get_option('position'), ''),
				'pbackground'   => wp_kses(get_option('pbackground'), ''),
				'ptext'			=> wp_kses(get_option('ptext'), ''),
				'bbackground'   => wp_kses(get_option('bbackground'), ''),
				'btext'       	=> wp_kses(get_option('btext'), ''),
				'secure'		=> wp_kses(get_option('secure'), ''),
				);
						
		foreach( $setting as $k => $settings )
			if( false == $settings )
				unset( $setting[$k]);
						
		$actual = apply_filters( 'jcookielaw_actual', wp_parse_args( $setting, self::$default ) );

		$jcookielaw = 	"
							window.addEventListener(\"load\", function(){
							window.cookieconsent.initialise({
  								\"palette\": {
    								\"popup\": {
      									\"background\": '{$actual['pbackground']}',
										\"text\": '{$actual['ptext']}'
    								},
    								\"button\": {
      									\"background\": '{$actual['bbackground']}',
										\"text\": '{$actual['btext']}'
    								}
  								},
								\"theme\": '{$actual['theme']}',
								\"position\": '{$actual['position']}',
                				\"cookie\": {
									\"expiryDays\": {$actual['expireDays']},
									\"secure\": {$actual['secure']}
                    			},
  								\"content\": {
    								\"message\": '{$actual['message']}',
    								\"dismiss\": '{$actual['acceptText']}',
    								\"link\": '{$actual['policyText']}',
									\"href\": '{$actual['policyURL']}'
  								},
								elements: {
  									\"header\": '<span class=\"cc-header\">{{header}}</span>&nbsp;',
  									\"message\": '<span id=\"cookieconsent:desc\" class=\"cc-message\">{{message}}</span>',
  									\"messagelink\": '<span id=\"cookieconsent:desc\" class=\"cc-message\">{{message}} <a aria-label=\"learn more about cookies\" tabindex=\"0\" class=\"cc-link\" href=\"{{href}}\" target=\"_blank\">{{link}}</a></span>',
  									\"dismiss\": '<a aria-label=\"dismiss cookie message\" tabindex=\"0\" class=\"cc-btn cc-dismiss\">{{dismiss}}</a>',
  									\"allow\": '<a aria-label=\"allow cookies\" tabindex=\"0\" class=\"cc-btn cc-allow\">{{allow}}</a>',
  									\"deny\": '<a aria-label=\"deny cookies\" tabindex=\"0\" class=\"cc-btn cc-deny\">{{deny}}</a>',
  									\"link\": '<a aria-label=\"learn more about cookies\" tabindex=\"0\" class=\"cc-link\" href=\"{{href}}\" target=\"{$actual['target']}\">{{link}}</a>',
  									\"close\": '<span aria-label=\"dismiss cookie message\" tabindex=\"0\" class=\"cc-close\">{{close}}</span>',
								}
							})});
						";

        $inline_jcookielaw = apply_filters( 'jcookielaw_html', $jcookielaw );
		
		wp_add_inline_script( 'cookiebar', $inline_jcookielaw );

	}

    public static function jquery_cookie_law_add_help_tab () {
        $screen = get_current_screen();

        $screen->add_help_tab( array(
              	      	'id'		=> '1_help_tab',
              	      	'title'		=> esc_html__('Law info', 'jquery-cookie-law'),
              	      	'content'	=> '<p><a href="http://ec.europa.eu/ipg/basics/legal/cookies/index_en.htm" target="_blank">' . esc_html__( 'Here', 'jquery-cookie-law') . '</a> ' . esc_html__( 'the information from European Commission', 'jquery-cookie-law') . '</p>',
          	      	) );

        $screen->set_help_sidebar(
               			'<p><strong>' . esc_html__('Other Resources', 'jquery-cookie-law') . '</strong></p><p><a href="https://silktide.com/tools/cookie-consent/" target="_blank">' . esc_html__('Cookie Consent Official Site', 'jquery-cookie-law') . '</a></p><p><a href="https://www.motostorie.blog" target="_blank">' . esc_html__('MT Site', 'jquery-cookie-law') . '</a></p>'
                             );
      	      	}

    public static function jcookielaw_admin_menu() {
        $my_admin_page = add_options_page( esc_html__('MT Cookie Consent Options', 'jquery-cookie-law'), esc_html__('MT Cookie Consent', 'jquery-cookie-law'), 'manage_options', self::$default['plugin_options_key'], array(__CLASS__, 'jcookielaw_options') );
		add_action('load-'.$my_admin_page, array( __CLASS__, 'jquery_cookie_law_add_help_tab') );
    }

    public static function jcookielaw_admin_init() {
		//register our settings
		register_setting('jquery-cookie-law', 'message');
		register_setting('jquery-cookie-law', 'acceptText');
        register_setting('jquery-cookie-law', 'policyText');
        register_setting('jquery-cookie-law', 'policyURL');
		register_setting('jquery-cookie-law', 'expireDays');
		register_setting('jquery-cookie-law', 'theme');
		register_setting('jquery-cookie-law', 'target');
		register_setting('jquery-cookie-law', 'position');
		register_setting('jquery-cookie-law', 'pbackground');
		register_setting('jquery-cookie-law', 'ptext');
		register_setting('jquery-cookie-law', 'bbackground');
		register_setting('jquery-cookie-law', 'btext');
		register_setting('jquery-cookie-law', 'secure');
    }

    public static function jcookielaw_options() {
        if (!current_user_can('manage_options'))  {
            wp_die( esc_html__('You do not have sufficient permissions to access this page.', 'jquery-cookie-law') );
        }
?>
    <div class="wrap">
        <h1><?php esc_html_e('MT Cookie Consent', 'jquery-cookie-law'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'jquery-cookie-law' ); ?>
                <table class="form-table">
    	            <tr valign="top">
                        <th scope="row"><label for="message"><?php esc_html_e('Message', 'jquery-cookie-law'); ?></label></th>
                            <td>
				<textarea aria-describedby="message-description" name="message" id="message" class="large-text code" rows="3"><?php echo esc_textarea( get_option('message') ); ?></textarea>
                                <p class="description" id="message-description"><?php esc_html_e('The message show in cookie bar.', 'jquery-cookie-law'); ?></p>
                            </td>
                    </tr>  
                    <tr valign="top">
                        <th scope="row"><label for="pbackground"><?php esc_html_e('Background Color', 'jquery-cookie-law'); ?></label></th>
                            <td>
                                <input class="pbackground" aria-describedby="pbackground-description" type="text" id="pbackground" name="pbackground" value="<?php echo get_option('pbackground'); ?>" data-default-color="<?php echo self::$default['pbackground']; ?>" />
                            </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="ptext"><?php esc_html_e('Text Color', 'jquery-cookie-law'); ?></label></th>
                            <td>
                                <input class="ptext" aria-describedby="ptext-description" type="text" id="ptext" name="ptext" value="<?php echo get_option('ptext'); ?>" data-default-color="<?php echo self::$default['ptext']; ?>" />
                            </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="acceptText"><?php esc_html_e('Text Accept Button', 'jquery-cookie-law'); ?></label></th>
                            <td>
                                <input class="regular-text code" aria-describedby="acceptText-description" type="text" id="acceptText" name="acceptText" value="<?php echo get_option('acceptText'); ?>" />
                            </td>
                    </tr> 
                    <tr valign="top">
                        <th scope="row"><label for="bbackground"><?php esc_html_e('Button Color', 'jquery-cookie-law'); ?></label></th>
                            <td>
                                <input class="bbackground" aria-describedby="bbackground-description" type="text" id="bbackground" name="bbackground" value="<?php echo get_option('bbackground'); ?>" data-default-color="<?php echo self::$default['bbackground']; ?>" />
                            </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="btext"><?php esc_html_e('Button Text Color', 'jquery-cookie-law'); ?></label></th>
                            <td>
                                <input class="btext" aria-describedby="btext-description" type="text" id="btext" name="btext" value="<?php echo get_option('btext'); ?>" data-default-color="<?php echo self::$default['btext']; ?>" />
                            </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label><?php esc_html_e('More Information Link', 'jquery-cookie-law'); ?></label></th>
                            <td>
                                <input class="regular-text code" aria-describedby="policyText-description" type="text" id="policyText" name="policyText" value="<?php echo get_option('policyText'); ?>" />
                                <p class="description" id="policyText-description"><?php esc_html_e('The More Information Button Text.', 'jquery-cookie-law'); ?></p><br/>
				<select name="policyURL" id="policyURL" aria-describedby="policyURL-description"> 
 					<option value=""><?php echo esc_attr( esc_html__( 'Select page', 'jquery-cookie-law' ) ); ?></option>
                    			<option value="<?php echo get_privacy_policy_url(); ?>" ><?php esc_html_e('Standard Privacy Policy Page', 'jquery-cookie-law'); ?></option> 
 					<option value="http://cookiesandyou.com/" <?php selected('http://cookiesandyou.com/', get_option('policyURL')); ?>>cookiesandyou.com</option> 
 				<?php 
  					$pages = get_pages(); 
  					foreach ( $pages as $page ) {
  						$option = '<option value="' . get_page_link( $page->ID ) . '" '. selected(get_page_link( $page->ID ), get_option('policyURL')) .'>';
						$option .= $page->post_title;
						$option .= '</option>';
						echo $option;
  					}
				?>
				</select> 				<select id="target" name="target">
					<option value="_self" <?php selected('_self', get_option('target')); ?>><?php esc_html_e('Same Window', 'jquery-cookie-law'); ?></option>
					<option value="_blank" <?php selected('_blank', get_option('target')); ?>><?php esc_html_e('New Window', 'jquery-cookie-law'); ?></option>
				</select>
                <p class="description" id="policyURL-description"><?php esc_html_e('Select the page of Cookies/Privacy Policy.', 'jquery-cookie-law'); ?> - <?php esc_html_e('Not have a Privacy Policy? Generate one with', 'jquery-cookie-law'); ?> <a href="http://iubenda.refr.cc/7J8242P" target="_blank">iubenda.com</a> <?php esc_html_e('with 10% discount.', 'jquery-cookie-law'); ?>
                <br/>
                <?php 
                
                $url = get_privacy_policy_url();
                $link = sprintf( wp_kses( __( 'The Standard <a href="%s">Privacy Policy</a> Page made with WordPress.', 'jquery-cookie-law' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( $url ) );
                echo $link;
                
                ?>
                </p>
				</td>
            </tr>
			
            <tr valign="top">
                <th scope="row"><label for="expireDays"><?php esc_html_e('Expire Days', 'jquery-cookie-law'); ?></label></th>
                <td>
					<input class="small-text" aria-describedby="expireDays-description" type="number" id="expireDays" name="expireDays" value="<?php echo get_option('expireDays'); ?>" min="90"/>
                    <p class="description" id="expireDays-description"><?php esc_html_e('After these days the bar is show again and the user must accept again. Min. 90 days, default 365 days.', 'jquery-cookie-law'); ?></p>
				</td>
            </tr>
			
			<tr valign="top">
                <th scope="row"><label for="secure"><?php esc_html_e('Cookie Secure', 'jquery-cookie-law'); ?></label></th>
                <td>
					<input aria-describedby="secure-description" type="checkbox" id="secure" name="secure" value="true" <?php checked('true', get_option('secure')); ?>/>
                    <p class="description" id="secure-description"><?php esc_html_e('If secure is true, the cookies will only be allowed over https', 'jquery-cookie-law'); ?></p>
				</td>
            </tr>

        	<tr valign="top">
            	<th scope="row"><label for="theme"><?php esc_html_e('Theme', 'jquery-cookie-law'); ?></label></th>
        		<td>
					<select id="theme" name="theme">
						<option value="classic" <?php selected('classic', get_option('theme')); ?>><?php esc_html_e('Classic', 'jquery-cookie-law'); ?></option>
						<option value="edgeless" <?php selected('edgeless', get_option('theme')); ?>><?php esc_html_e('Edgeless', 'jquery-cookie-law'); ?></option>
					</select>
				</td>
        	</tr>

        	<tr valign="top">
            	<th scope="row"><label for="position"><?php esc_html_e('Position', 'jquery-cookie-law'); ?></label></th>
        		<td>
					<select id="position" name="position">
						<option value="bottom" <?php selected('bottom', get_option('position')); ?>><?php esc_html_e('Bottom', 'jquery-cookie-law'); ?></option>
						<option value="top" <?php selected('top', get_option('position')); ?>><?php esc_html_e('Top', 'jquery-cookie-law'); ?></option>
						<option value="bottom-left" <?php selected('bottom-left', get_option('position')); ?>><?php esc_html_e('Floating left', 'jquery-cookie-law'); ?></option>
						<option value="bottom-right" <?php selected('bottom-right', get_option('position')); ?>><?php esc_html_e('Floating right', 'jquery-cookie-law'); ?></option>
					</select>
				</td>
        	</tr> 

                </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
    }

}
add_action( 'plugins_loaded', array(  'jcookielawJS', 'my_jcookielaw_init' ) );
endif;