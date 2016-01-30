<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Admin page Class
 *
 *
 *
 * @author		Hermanto Lim
 * @copyright	Copyright (c) Hermanto Lim
 * @link		http://bonfirelab.com
 * @since		Version 1.0
 * @package 	BonFramework
 * @category 	Core
 *
 *
*/ 
 
if( ! class_exists( 'BON_Admin' ) )
{
	class BON_Admin
	{
		/**
		 * @var obj
		 */
		public $bon;
		
		/**
		 * @var obj
		 */
		public $of_page = array();


		/**
		 * @var string
		 */
		public $backup_token = 'bon_backup';

		/**
		 * @var string
		 */
		public $framework_token = 'bon_framework';

		/**
		 * @var string
		 */
		public $author_name = 'Hermanto Lim';


		/**
		 * @var array
		 */
		public $option_pages = array();

		/**
		 * @var array
		 */
		public $option_data = array();

		/**
		 * @var string
		 */
		public $html;

		/**
		 * BON_Machine main constructor
		 *
		 */
		public function __construct(&$bon) {
			$this->bon = $bon;
			$this->option_pages = $this->bon->option_pages;
			add_action( 'init', array( $this, 'rolescheck' ) );
			add_action( 'admin_init', array( $this, 'theme_updater' ) );

			do_action( 'bon_admin_init', $this );
		}

		/**
		 * Rolescheck for initing admin page only for specific user role. 
		 * 
		 * @access public
		 * @return void
		 */
		public function rolescheck() {

			if ( current_user_can( 'edit_theme_options' ) ) {
				//$this->html = $this->render_page();
				add_action( 'admin_menu', array($this, 'add_page') );
				add_action( 'admin_init', array($this, 'init') );
			}
		}
	
		public function init() {
			
			// Updates the unique option id in the database if it has changed
			$this->option_name();

			foreach( $this->option_pages as $pages ) {

				$settings = get_option( $pages['option_key'] );

				if( isset( $settings['id'] ) ) {
					$option_name = $settings['id'];
				} else {
					$option_name = $pages['option_key'] . '_options';
				}

				if( ! get_option( $option_name ) ) {
					$this->option_setdefaults( $pages['option_key'], $pages['slug'] );
				}

				register_setting( $pages['option_key'] , $option_name, array( $this, 'option_validate' ) );

			}

			add_action( 'bon_optionsframework_after_validate', array($this, 'save_options_notice') );

		}

		public function option_validate( $input ) {

			/*
			 * Update Settings
			 *
			 * This used to check for $_POST['update'], but has been updated
			 * to be compatible with the theme customizer introduced in WordPress 3.4
			 */

			$options = array();
			$option_page = '';

			if(isset($_POST['option_page'])) {
				$option_page = $_POST['option_page'];
			}

			/*
			 * Restore Defaults.
			 *
			 * In the event that the user clicked the "Restore Defaults"
			 * button, the options defined in the theme's options.php
			 * file will be added to the option for the active theme.
			 */

			if ( isset( $_POST['reset'] ) ) {
				add_settings_error( $option_page , 'restore_defaults', __( 'Default options restored.', 'bon' ), 'updated fade' );
				return $this->_get_default_values( $option_page );
			}
			
			foreach( $this->option_pages as $pages ) {
				if( isset( $pages['customizer'] ) && $pages['customizer'] == true && $_POST['action'] == 'customize_save' ) {
					$options = array_merge( $options, $pages['option_set'] );
				}
				if( $pages['option_key'] === $option_page ) {
					$options = $pages['option_set'];
					break;
				}
			}
			 
			$clean = array();

			foreach ( $options as $option ) {
				if ( ! isset( $option['id'] ) ) {
					continue;
				}

				if ( ! isset( $option['type'] ) ) {
					continue;
				}

				$id = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower( $option['id'] ) );

				// Set checkbox to false if it wasn't sent in the $_POST
				if ( 'checkbox' == $option['type'] && ! isset( $input[$id] ) ) {
					$input[$id] = '0';
				}
				// Set each item in the multicheck to false if it wasn't sent in the $_POST
				if ( 'multicheck' == $option['type'] && ! isset( $input[$id] ) ) {
					foreach ( $option['options'] as $key => $value ) {
						$input[$id][$key] = '0';
					}
				}

				if( 'repeatable' != $option['type']) {
					if ( has_filter( 'bon_sanitize_' . $option['type'] ) ) {
						$clean[$id] = apply_filters( 'bon_sanitize_' . $option['type'], $input[$id], $option );
					}
				} else {
					$sanitizer = isset( $option['sanitizer'] ) ? $option['sanitizer'] : 'sanitize_text_field';
					$clean[$id] = bon_array_map_r( 'bon_sanitize', $input[$id], $option );
				}
				
			}

			// Hook to run after validation
			do_action( 'bon_optionsframework_after_validate', $clean );

			return $clean;
		}

		public function save_options_notice() {

			if( !isset( $_POST['option_page'] ) ) {
				return;
			}
			add_settings_error( $_POST['option_page'] , 'save_options', __( 'Options saved.', 'bon' ), 'updated fade' );
		}

		public function option_name() {

			$manualurl = $this->bon->manual_url;
			$themename = $this->bon->theme_name;

			foreach( $this->option_pages as $page ) {
				$settings = get_option( $page['option_key'] );
				$suffix = isset( $page['option_suffix'] ) ? $page['option_suffix'] : '';
				$default_themename = preg_replace( "/\W/", "_", strtolower( $themename ) ). $suffix;

				if( isset( $settings['id'] ) ) {

						if ( $settings['id'] == $default_themename ) {
							// All good, using default theme id
						} else {
							$settings['id'] = $default_themename;
							update_option( $page['option_key'], $settings );
						}

				} else {
					$settings['id'] = $default_themename;
					update_option( $page['option_key'], $settings );
				}
			}

			if ( get_option( 'bon_manual') != $manualurl ) update_option( 'bon_manual', $manualurl );
		}

		public function option_page_capability( $capability ) {
			return 'edit_theme_options';
		}

		public function option_setdefaults( $opt_group = '' ) {

			$optionsframework_settings = get_option( $opt_group );
			// Gets the unique option id
			$option_name = $optionsframework_settings['id'];
			
			if ( isset( $optionsframework_settings['knownoptions'] ) ) {
				$knownoptions =  $optionsframework_settings['knownoptions'];
				if ( !in_array( $option_name, $knownoptions ) ) {
					array_push( $knownoptions, $option_name );
					$optionsframework_settings['knownoptions'] = $knownoptions;
					update_option( $opt_group , $optionsframework_settings );
				}
			} 

			else {
				$newoptionname = array( $option_name );
				$optionsframework_settings['knownoptions'] = $newoptionname;
				update_option( $opt_group , $optionsframework_settings );
			}
			
			// If the options haven't been added to the database yet, they are added now
			$values = $this->_get_default_values( $opt_group );

			if ( isset( $values ) ) {
				add_option( $option_name, $values ); // Add option with default settings
			}

		}
		
		/**
		 * Format Configuration Array.
		 *
		 * Get an array of all default values as set in
		 * options.php. The 'id','std' and 'type' keys need
		 * to be defined in the configuration array. In the
		 * event that these keys are not present the option
		 * will not be included in this function's output.
		 *
		 * @return    array     Rey-keyed options configuration array.
		 *
		 * @access    private
		 */
		 
		private function _get_default_values( $opt_group = '' ) {

			foreach( $this->option_pages as $pages ) {
				if( $pages['option_key'] == $opt_group ) {
					$config = $pages['option_set'];
					break;
				}
			}
			
			foreach ( (array) $config as $option ) {
				if ( ! isset( $option['id'] ) ) {
					continue;
				}
				if ( ! isset( $option['std'] ) ) {
					continue;
				}
				if ( ! isset( $option['type'] ) ) {
					continue;
				}

				if( 'repeatable' != $option['type']) {
					if ( has_filter( 'bon_sanitize_' . $option['type'] ) ) {
						$output[$option['id']] = apply_filters( 'bon_sanitize_' . $option['type'], $option['std'], $option );
					}
				} else {
					$sanitizer = isset( $option['sanitizer'] ) ? $option['sanitizer'] : 'sanitize_text_field';
					$output[$option['id']] = bon_array_map_r( 'bon_sanitize', $output[$option['id']], $option );
				}
			}
			return $output;
		}


		/**
		 * This is the function that is called when a framework option page gets opened. 
		 * It checks the current page slug and based on that slug filters the $this->bon->option_pages options array.
		 * @access public
		 * @return string
		 */
		public function add_page() {

			global $current_user;

			if( !isset($this->option_pages) ) return;

			$add_page = 'add_object_page';

			if(!function_exists( $add_page ) ) { $add_page = 'add_menu_page'; }
		    
			$current_user_id = $current_user->user_login;
			$icon = bon_get_framework_option('bon_framework_backend_icon');

			if(empty($icon)) {
				$icon = BON_IMAGES . '/bon-icon.png';
			}
			
			$super_user = bon_get_framework_option('bon_framework_super_user');

			foreach( $this->option_pages as $_key => $_value ) {
				
				//if its the very first option item make it a main menu with theme name, then as first submenu add it again with real menu name 
				if( $_key === 0 ) {

					$title = ucwords( $this->bon->theme_name );
					$level = $_value['slug'];
					$this->of_page[] = $of_page = $add_page( $title, $title, $_value['role'], $level, array( &$this, 'render_element' ), $icon );
				}
	
				if( $_value['parent'] == $_value['slug'] ) {	

					if( $_value['role'] === 'superuser' ) {
						if( !empty($super_user) && $super_user != $current_user_id ) {
							continue;
						} else {
							$_value['role'] = 'update_core';
						}
					}

					$this->option_data[$_value['slug']]['options'] = $_value['option_set'];
					$this->option_data[$_value['slug']]['key'] = $_value['option_key'];

					$this->of_page[] = $of_page = add_submenu_page (	$level,	$_value['title'], $_value['title'], $_value['role'], $_value['slug'], array(&$this, 'render_element') );
					$this->option_data[$of_page] = $_value['option_set'];
				}

				if( !empty($of_page) ) {
					//add scripts and styles to all option pages
					add_action('admin_enqueue_scripts', array($this, 'load_scripts'));
					add_action('admin_enqueue_scripts', array($this, 'load_styles'));
				}
			}
		}


		public function render_element( $type = '' ) {
			$current_slug = $_GET['page'];

			/* the key for get_option() */
			$option_key = $this->option_data[$current_slug]['key'];
			$options = $this->option_data[$current_slug]['options'];


			$html = new BON_Machine($options, 'options_page', $option_key);
			
			if($html && isset($html->output)) {
				echo '<!-- bon options start -->';

				$this->page_header($option_key);

				echo '<div id="main">';

					if(isset($html->menu)) {

						echo '<div id="bon-nav">' .
									'<ul>' .
										$html->menu .
									'</ul>
							  </div>';
					}
				 
				 	echo '<div id="content">';
					echo $html->output;
					echo '</div>';
				
				echo '<div class="clear"></div></div>';
				echo $this->page_footer($option_key);
				echo '<!-- bon options end -->';
			}

		}
		/**
		 * Ouput admin page header such as logo, nonce field, etc
		 *
		 * @access public
		 * @return string
		 */
		public function page_header( $option_group ) { ?>
			

			<div class="wrap" id="bon_container">
			
				<?php settings_errors( $option_group ); ?>

				<form action="options.php" id="bonform" method="post">
				
					<?php settings_fields( $option_group ); ?>

        			<div id="header">
		        		<div class="logo">
							<?php if( bon_get_framework_option( 'bon_framework_backend_header_image' ) ) { ?>
								<img alt="" src="<?php echo esc_url( bon_get_framework_option( 'bon_framework_backend_header_image' ) ); ?>"/>
					        <?php } else { ?>
					        	<img alt="bonfirelab" src="<?php echo esc_url( BON_IMAGES . '/logo.png' ); ?>"/>
					        <?php } ?>      
		       			</div>
		        		<div class="theme-info">
		        			<?php $this->display_theme_version_data( true ); ?>
		        		</div>
		        		<div class="clear"></div>
		        	</div>

	       			<?php $this->support_link(); ?>

	    <?php
		}

		public function page_footer( $option_group ) { ?>

					 <div class="save_bar_top">
					 	<input type="submit" value="<?php _e( 'Reset Options','bon'); ?>" style="float: left" class="button button-secondary" name="reset" onclick="return confirm('<?php echo esc_js( __( 'Click OK to reset. Any theme settings will be lost!', 'bon' ) ); ?>');"/>
				     	<input type="submit" value="<?php _e( 'Save All Changes','bon'); ?>" class="button button-primary submit-button" name="update" />
				     </div>
			     </form>
			</div>

		<?php
		}


		/**
		 * Get Bonfirelab specific link for support, changelog and theme docs
		 *
		 * @access public
		 * @return string
		 */
		public function support_link() { 
			$manualurl = get_option('bon_manual');
			$pos = strpos( $manualurl, 'documentation' );
			$theme_slug = str_replace( "/", "", substr( $manualurl, ( $pos + 13 ) ) ); //13 for the word documentation
			?>

			<div id="support-links">
				<ul>
					<li class="docs">
						<a title="Theme Documentation" href="'.esc_url( $manualurl ).'"><?php _e( 'View Theme Documentation', 'bon' ); ?></a>
					</li>
					<li class="forum">
						<a href="<?php echo esc_url('http://support.bonfirelab.com/'); ?>" target="_blank"><?php _e( 'Visit Support Forum', 'bon' ); ?></a>
					</li>
		            <li class="right">
						<input type="submit" value="Save All Changes" class="button button-primary submit-button" name="update" />
					</li>
				</ul>
			</div>
					
			<?php
		}
		
		/**
		 * Load Admin Javscript
		 *
		 * @access public
		 * @return void
		 */
		public function load_scripts( $hook ) {

			if( !in_array( $hook, $this->of_page ) ) {
				return;
			}

			$options = $this->option_data[$hook];

			$deps = array( 'jquery', 'jquery-ui-sortable' );

			if ( bon_find_field_type( 'date', $options ) )
				$deps[] = 'jquery-ui-datepicker';
			if ( bon_find_field_type( 'slider', $options ) )
				$deps[] = 'jquery-ui-slider';
			if ( bon_find_field_type( 'color2', $options ) )
				$deps[] = 'farbtastic';
			
			if ( in_array( true, array(
				bon_find_field_type( 'chosen', $options ),
				bon_find_field_type( 'post_chosen', $options )
			) ) ) {
				wp_register_script( 'bon-chosen', BON_JS . '/chosen.js', array( 'jquery' ) );
				$deps[] = 'bon-chosen';
				wp_enqueue_style( 'bon-chosen', BON_CSS . '/chosen.css' );
			}
			
			if ( bon_find_field_type( 'upload', $options ) ) {
				if ( function_exists( 'wp_enqueue_media' ) )
					wp_enqueue_media();
					wp_register_script( 'bon-media-uploader', BON_JS . '/media-uploader.js', array( 'jquery' ) );

					wp_enqueue_script( 'bon-media-uploader' );

					wp_localize_script( 'bon-media-uploader', 'optionsframework_l10n', array(
					'upload' => __( 'Upload', 'bon' ),
					'remove' => __( 'Remove', 'bon' )
				) );
				$deps[] = 'bon-media-uploader';
			}

			if ( bon_find_field_type( 'color', $options ) ) {

				wp_register_script( 'iris', BON_JS . '/iris.min.js', array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
				wp_register_script( 'wp-color-picker', BON_JS . '/color-picker.min.js' );
				wp_enqueue_script('wp-color-picker');
				wp_enqueue_script('iris');
				
				$colorpicker_l10n = array(
					'clear' => __( 'Clear','bon' ),
					'defaultString' => __( 'Default', 'bon' ),
					'pick' => __( 'Select Color', 'bon' )
				);
				wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );
				
				$deps[] = 'iris';
				$deps[] = 'wp-color-picker';
				
			}
				
			wp_enqueue_script( 'bon-cm', BON_URI . '/assets/codemirror/lib/codemirror.js');
			wp_enqueue_script( 'bon-cm-mode', BON_URI . '/assets/codemirror/mode/css/css.js', array('jquery'), '2.24.0');
			wp_enqueue_script( 'bon-cm-js', BON_URI . '/assets/codemirror/mode/javascript/javascript.js', array('jquery'),'2.24.0');
			wp_enqueue_script( 'bon-admin', BON_JS . '/admin.js', $deps );

		}

		/**
		 * Admin action hook for custom scripts
		 *
		 * @access public
		 * @return void
		 */
		public function admin_head() {
			do_action( 'bon_admin_custom_scripts' );
		} 

		/**
		 * Load Admin Stylesheet
		 *
		 * @access public
		 * @return void
		 */
		public function load_styles( $hook ) {

			if( !in_array( $hook, $this->of_page ) ) {
				return;
			}

			$options = $this->option_data[$hook];

			if ( bon_find_field_type( 'color2', $options ) ) {
				wp_enqueue_style( 'farbtastic' );
			}
			if ( bon_find_field_type( 'color', $options ) ) {
				wp_register_style( 'wp-color-picker', BON_CSS .'/color-picker.min.css' );
				wp_enqueue_style( 'wp-color-picker' );
			}

			wp_enqueue_style( 'bon-cm', BON_URI . '/assets/codemirror/lib/codemirror.css');
			wp_enqueue_style( 'bon-admin', BON_CSS . '/admin.css' );
				
		}
		
		/**
		 * Display the version data for the currently active theme.
		 * @access public
		 * @return void
		 */
		public function display_theme_version_data ( $echo = false ) {
			$data = $this->get_theme_version_data();
			$html = '';

			// Theme Version
			if ( true == $data['is_child'] ) {
				$html .= '<span class="theme">' . esc_html( $data['child_theme_name'] . ' ' . $data['child_theme_version'] ) . '</span>' . "\n";
				$html .= '<span class="parent-theme">' . esc_html( $data['theme_name'] . ' ' . $data['theme_version'] ) . '</span>' . "\n";
			} else {
				$html .= '<span class="theme">' . esc_html( $data['theme_name'] . ' ' . $data['theme_version'] ) . '</span>' . "\n";
			}
			
			// Framework Version
			$html .= '<span class="framework">' . esc_html( sprintf( __( 'BonFramework %s', 'bon' ), $data['framework_version'] ) ) . '</span>' . "\n";

			if ( true == $echo ) { echo $html; } else { return $html; }
		} 

		/**
		 * Get the version data for the currently active theme.
		 * @access  public
		 * @return array [theme_version, theme_name, framework_version, is_child, child_theme_version, child_theme_name]
		 */
		public function get_theme_version_data() {
			global $bon;
			$response = array(
							'theme_version' => '', 
							'theme_name' => '', 
							'framework_version' => $bon->version, 
							'is_child' => is_child_theme(), 
							'child_theme_version' => '', 
							'child_theme_name' => ''
							);

			if ( function_exists( 'wp_get_theme' ) ) {
				$theme_data = wp_get_theme();
				if ( true == $response['is_child'] ) {
					$response['theme_version'] = $theme_data->parent()->Version;
					$response['theme_name'] = $theme_data->parent()->Name;
					$response['child_theme_version'] = $theme_data->Version;
					$response['child_theme_name'] = $theme_data->Name;
				} else {
					$response['theme_version'] = $theme_data->Version;
					$response['theme_name'] = $theme_data->Name;
				}
			} 

			return $response;
		}
		
		// this function for checking update in ThemeForest! Please do not edit the code
		public function theme_updater() {

			if( bon_get_framework_option('bon_framework_update_notification') == true ) {
				
				require_once( trailingslashit( BON_CLASSES ) . 'class-pixelentity-theme-update.php');

				$username = bon_get_framework_option('bon_framework_envato_username');

				$apikey = bon_get_framework_option('bon_framework_envato_api');

				$author = $this->author_name;

				if( !empty( $username ) && !empty( $apikey ) ) {
					PixelentityThemeUpdate::init($username, $apikey, $author); 
				}
			}
		}

	}
}




