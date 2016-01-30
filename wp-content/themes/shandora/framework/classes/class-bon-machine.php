<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Bon Framework Option Machine Class
 * This class handle the output form for Options page and meta box options
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

if(! class_exists('BON_Machine') ) {

	class BON_Machine {

		/**
		 * @var string 
		 */
		public $context = 'options_page';

		/**
		 * @var string 
		 */
		public $counter;

		/**
		 * @var string 
		 */
		public $group = '';

		/**
		 * @var string 
		 */
		public $form;

		/**
		 * @var string 
		 */
		public $option_name = '';


		public function __construct($options = array(), $context = '', $group = '') {

			$this->form = new BON_Form();

			// checking context 
			if($context != '') {
				$this->context = $context;
			}
			
			if($group != '') {
				$this->group = $group;
			}
			
			if( $options ) {
				$return = $this->options_machine($options, $group);
				$this->output = $return[0];
				$this->menu = $return[1];
				$this->menuitems = $return[2];
			}

		}

		/**
		 * Process options data and build option fields
		 *
		 * @uses get_option()
		 *
		 * @access public
		 * @since 1.0.0
		 *
		 * @return array
		 */
		public function options_machine($options, $group = '' ) {
		
		    global $allowedtags;

		    $this->counter = 0;
			$menu = '';
			$output = '';

			if($this->context == 'options_page') {
				$optionsframework_settings = get_option( $group );

				// Gets the unique option id
				if ( isset( $optionsframework_settings['id'] ) ) {
					$this->option_name = $optionsframework_settings['id'];
				}

				$settings = get_option($this->option_name);
			}
			// Create an array of menu items - multi-dimensional, to accommodate sub-headings.
			$menu_items = array();
			$headings = array();

			if($this->context == 'options_page') {
				foreach ( $options as $k => $v ) {
					if ( $v['type'] == 'heading' || $v['type'] == 'subheading' ) {
						$headings[] = $v;
					}
				}
				$prev_heading_key = 0;
				foreach ( $headings as $k => $v ) {
					$token = 'bon-option-' . preg_replace( '/[^a-zA-Z0-9\s]/', '', strtolower( trim( str_replace( ' ', '', $v['label'] ) ) ) );
					// Capture the token.
					$v['token'] = $token;
					if ( $v['type'] == 'heading' ) {
						$menu_items[$token] = $v;
						$prev_heading_key = $token;
					}
					if ( $v['type'] == 'subheading' ) {
						$menu_items[$prev_heading_key]['children'][] = $v;
					}
				}

				// Override the menu with a new multi-level menu.
				if ( count( $menu_items ) > 0 ) {
					foreach ( $menu_items as $k => $v ) {
						$class = '';
						
						if ( isset( $v['children'] ) && ( count( $v['children'] ) > 0 ) ) {
							$class .= ' has-children';
						}
						
						$menu .= '<li class="top-level ' . $class . '">' . "\n"; 
						if ( isset( $v['icon'] ) && ( $v['icon'] != '' ) )
						$menu .= '<a title="' . esc_attr( $v['label'] ) . '" href="#' . $v['token'] . '"><i class="dashicons '.$v['icon'].'"></i>' . esc_html( $v['label'] ) . '</a>' . "\n";
						
						if ( isset( $v['children'] ) && ( count( $v['children'] ) > 0 ) ) {
							$menu .= '<ul class="sub-menu">' . "\n";
								foreach ( $v['children'] as $i => $j ) {
									$menu .= '<li>' . "\n" . '<a title="' . esc_attr( $j['label'] ) . '" href="#' . $j['token'] . '">' . esc_html( $j['label'] ) . '</a></li>' . "\n";
								}
							$menu .= '</ul>' . "\n";
						}
						$menu .= '</li>' . "\n";
					}
				}
			}

			foreach ( $options as $value ) {

				$this->counter++;
				$val = '';

				if($this->context == 'options_page') {
					if( isset( $value['std'] ) ) {
						$val = $value['std'];
					}
					// If the option is already saved, ovveride $val
					if (  $value['type'] != 'heading'  && $value['type'] != 'subheading'  && $value['type'] != 'info' ) {
						if ( isset( $settings[($value['id'])]) ) {
							$val = $settings[($value['id'])];
							// Striping slashes of non-array options
							if ( !is_array($val) ) {
								$val = stripslashes( $val );

							}
						}
					}

					if ( $value['type'] != 'heading' && $value['type'] != 'subheading' ) {
						$class = ''; if( isset( $value['class'] ) ) { $class = ' ' . $value['class']; }
						$output .= '<div class="section section-' . esc_attr( $value['type'] ) . esc_attr( $class ) .'">'."\n";
						if( $value['type'] != 'info') {
							$output .= '<h3 class="heading">'. esc_html( $value['label'] ) .'</h3>'."\n";
						}
						if($value['type'] == 'editor') {
							$output .= '<div class="option">'."\n" . '<div class="controls with-editor">'."\n";
						} else {
							$output .= '<div class="option">'."\n" . '<div class="controls not-with-editor">'."\n";
						}
					} 
				} else if( $this->context == 'metabox' ) {
					$val = get_post_meta( get_the_ID(), $value['id'], true);

					if ( $value['type'] == 'section' ) {
						$output .= '<tr><td colspan="2"><h2>' . $value['label'] . '</h2></td></tr>';
					} 
					if ( $value['type'] == 'repeatable' ) {
						$output .= '<tr class="'. ( isset($value['class']) ? $class = ' ' . $value['class'] : '' ) .'"><td colspan="2">';
					} else {
						$output .= '<tr class="'. ( isset($value['class']) ? $class = ' ' . $value['class'] : '' ) .'"><th><label for="' . $value['id'] . '">' . $value['label'] . '</label></th><td>';
					}
				} else {

				}
				
				$output .= $this->render_element($value, $val);

				if($this->context == 'options_page') {
					if ( $value['type'] != "heading" && $value['type'] != "subheading" && $value['type'] != "checkbox" ) {
						$explain_value = ( isset( $value['desc'] ) ) ? $value['desc'] : '';
						$output .= '</div>'."\n".'<div class="explain">'. $explain_value .'</div>'."\n";
						$output .= '<div class="clear"></div>'."\n".'</div>'."\n".'</div>'."\n";
					} else if( $value['type'] == 'checkbox' ) {
						$output .= '</div>'."\n";
						$output .= '<div class="clear"></div>'."\n".'</div>'."\n".'</div>'."\n";
					}
				} else {
					if( $value['type'] != 'section' ) {
						$output .= '</td></tr>';
					}
				}
				
			}

			if ( isset( $_REQUEST['page'] ) ) {
				$output .= '</div>';
			}

		    return array($output, $menu, $menu_items);
		}

		
		/**
		 * recives data about a form field and spits out the proper html
		 *
		 * @param	array					$field			array with various bits of information about the field
		 * @param	string|int|bool|array	$meta			the saved data for this field
		 * @param	array					$repeatable		if is this for a repeatable field, contains parant id and the current integar
		 *
		 * @return	string									html for the field
		 */
		public function render_element( $field, $meta = null, $repeatable = null ) {
			if ( ! ( $field || is_array( $field ) ) )
				return;
			
			$output = '';
			$wrapper = '';

			global $allowedtags;
			
			$option_name = $this->option_name;

			$defaults = array(
				'id' => null,
				'type' => null,
				'label' => null,
				'desc' => '',
				'place' => null,
				'size' => null,
				'post_type' => null,
				'options' => null,
				'settings' => null,
				'std' => null,
				'icon' => null,
				'class' => null,
				'step' => null,
				'min' => null,
				'max' => null,
				'cols' => 10,
				'rows' => 4,
				'tax_type' => null,
			);

			$defaults = wp_parse_args( $field, $defaults );

			extract( $defaults );
			
			if($this->context == 'metabox') {
				$desc = isset( $field['desc'] ) ? '<span class="description">' . $field['desc'] . '</span>' : null;
			} else {
				$desc = '';
			}

			if($this->context == 'options_page') {
				$name = isset( $field['id'] ) ? $option_name . "[".$field['id']."]" : null;
			}
			else {
				$name = isset( $field['id'] ) ? $field['id'] : null;
			}
			if ( $repeatable ) {
				if($this->context == 'options_page') {
					$name = $option_name . '[' . $repeatable[0] . '][' . $repeatable[1] . '][' . $id . ']';
				} else {
					$name = $repeatable[0] . '[' . $repeatable[1] . '][' . $id .']';
				}
				$id = $repeatable[0] . '_' . $repeatable[1] . '_' . $id;
			}

			$f = $this->form;

			switch( $type ) {
				
				case 'text':
				case 'tel':
				case 'email':
				case 'number':
				case 'url':
				case 'password':
				default:
					if( method_exists( $f, $method = 'form_'.$type ) ) {

						$output .= $f->$method( array( 'name' => esc_attr( $name ), 'id' => esc_attr( $id ) ), esc_attr( $meta ), 'class="bon-input" size="30"' );
						$output .= $desc;

					} else {
						$output .= $f->form_input( array( 'name' => esc_attr( $name ), 'id' => esc_attr( $id ) ), esc_attr( $meta ), 'class="bon-input" size="30"' );
						$output .= $desc;
					}
				break;

				case 'textarea':
					$output .= $f->form_textarea( array( 'name' => esc_attr( $name ), 'id' => esc_attr( $id ), 'cols' => $cols, 'rows' => $rows ), $meta );
					$output .= $desc;
				break;

				case 'editor':
					$output .= '<div class="wp_editor_wrapper">';
					$default_editor_settings = array(
						'textarea_name' => $name,
						'media_buttons' => false,
						'tinymce' => array( 'plugins' => 'wordpress' )
					);

					$settings = array_merge( $default_editor_settings, $settings );

					ob_start();

					wp_editor( $meta, $id, $settings );

					$output .= ob_get_clean();
					$output .=  '</div><div class="clear"></div><br />' . $desc . '';
				break;

				case 'checkbox':
					$checked = false;
					if( checked( $meta, 1, false ) ) {
						$checked = 'checked';
					}

					$output .= $f->form_checkbox( array( 'name' => esc_attr( $name ), 'id' => esc_attr( $id ), 'checked' => $checked ), 1, $meta );
					if( isset( $field['desc'] ) ) {
						$output .= '<label for="' . esc_attr( $id ) . '">' . $field['desc'] . '</label>';
					}
				break;

				case 'select':
				case 'chosen':
				case 'post_select':
				case 'post_list':
				case 'post_chosen':
				case 'page_select':
				case 'page_chosen':
				case 'page_list':
				case 'tax_select':
				case 'cat_select':
				case 'tag_select':
				case 'tax_chosen':
				case 'cat_chosen':
				case 'tag_chosen':
				case 'tax_list':
				case 'cat_list':
				case 'tag_list':

					$multiple = '';
					$s_name = esc_attr( $name );

					$substr = substr( $type, 0, 5);
					$substr_tax = substr( $type, 0 , 4);

					if( $substr == 'post_' || $substr == 'page_' ) {

						$type = str_replace( $substr, '', $type );
						$post_opts = array( '' => __('Select One', 'bon') );

						$q = array( 
							'post_type' => $substr == 'page_' ? 'page' : $post_type, 
							'posts_per_page' => isset( $max ) ? $max : -1, 
							'orderby' => 'name', 
							'order' => 'ASC',
							'post_status' => array( 'publish', 'pending' ),
						);

						if( isset($field['filter_author']) && $field['filter_author'] === true && !current_user_can( 'manage_options' ) ) {
							$user_ID = get_current_user_id();

							if($user_ID > 0 ) {
								$q['author'] = $user_ID;
							}
						}

						$post_opts_obj = get_posts($q);
						if( !is_wp_error( $post_opts_obj ) ) {
							foreach ($post_opts_obj as $opt) {
								$post_opts[$opt->ID] = $opt->post_title;
							}
						}
						$options = $post_opts;

					} else if( $substr_tax == 'tax_' || $substr_tax == 'cat_' || $substr_tax == 'tag_') {

						$type = str_replace( $substr_tax, '', $type );
						$tax_opts = array( '' => __('Select One', 'bon' ) );

						if( $substr_tax == 'cat_' ) {
							$c_obj = get_categories();
							foreach ($c_obj as $c) { $tax_opts[$c->cat_ID] = $c->cat_name; }
							$options = $tax_opts;
						} else if( $substr_tax == 'tag_' ) {
							$t_obj = get_tags();
							foreach ( $t_obj as $t ) { $tax_opts[$t->term_id] = $t->name; }
							$options = $tax_opts;
						} else {
							$tx_obj = get_terms( $tax_type, array( 'get' => 'all' ) );
							if( !is_wp_error( $tx_obj ) ) {
								foreach ( $tx_obj as $tx ) { $tax_opts[$tx->term_id] = $tx->name; }
							}
							$options = $tax_opts;
						}
					}

					$class = 'bon-input ';

					if( isset( $field['multiple'] ) && $field['multiple'] == true ) {
						$multiple = 'multiple="multiple"';
						$s_name = esc_attr( $name ) . '[]';
						$class .= 'multiple';
					}
					if( $type == 'chosen' ) {
						$class .= ' chosen';
					}
					$output .= $f->form_select( array( 'name' => $s_name, 'id' => esc_attr( $id ) ), $options, $meta, 'class="'.$class.'" '.$multiple );
					$output .= $desc;

				break;

				case 'radio':
					$output .= '<ul class="meta_box_items">';
					foreach ( $options as $val => $option ) :
						$checked = false;
						if( checked( $meta, $val, false ) ) {
							$checked = 'checked';
						}
						$output .= '<li>';
						$output .= $f->form_radio( array( 'name' => esc_attr( $name ), 'id' => esc_attr( $id ) . '-' . $val, 'checked' => $checked ), $val, $meta );
						$output .= '<label for="' . esc_attr( $id ) . '-' . $val . '">' . $option . '</label>';
						$output .= '</li>';
					endforeach;
					$output .= '</ul>' . $desc;
				break;

				case 'post_checkboxes':
				case 'tax_checkboxes':
				case 'multicheck':

					if( $type == 'post_checkboxes' ) {
						$post_opts = array();
						$q = array( 
							'post_type' => $post_type, 
							'posts_per_page' => isset( $max ) ? $max : -1, 
							'orderby' => 'name', 
							'order' => 'ASC',
							'post_status' => array( 'publish', 'pending' ),
						);

						if( isset($field['filter_author']) && $field['filter_author'] === true ) {
							$user_ID = get_current_user_id();

							if($user_ID > 0 ) {
								$q['author'] = $user_ID;
							}
						}

						$post_opts_obj = get_posts($q);
						if( !is_wp_error( $post_opts_obj ) ) {
							foreach ($post_opts_obj as $opt) {
								$post_opts[$opt->ID] = $opt->post_title;
							}
						}
						$options = $post_opts;

					} else if( $type == 'tax_checkboxes' ) {
						$tax_opts = array();
						$tx_obj = get_terms( $tax_type, array( 'get' => 'all' ) );
						if( !is_wp_error( $tx_obj ) ) {
							foreach ( $tx_obj as $tx ) { $tax_opts[$tx->term_id] = $tx->name; }
						}
						$options = $tax_opts;	
					}

					$output .= '<ul class="meta_box_items">';
					foreach ( $options as $val => $option ) {
						$checked = false;
						$c_name = esc_attr( $name ) . '['.$val.']';
						if ( isset( $meta[$val] ) && $meta[$val] == true ) {
							$checked = true;
						}
						$output .= '<li>';
						$output .= $f->form_checkbox( array( 'name' => $c_name, 'id' => esc_attr( $id ) . '-' . $val ), $val, $checked );
						$output .= '<label for="' . esc_attr( $id ) . '-' . $val . '">' . $option . '</label>';
						$output .= '</li>';
					}
					$output .= '</ul>' . $desc; 
				break;
				
				case 'color':
					$default_color = '';
					if ( isset($field['std']) ) {
						if ( $meta !=  $field['std'] )
							$default_color = ' data-default-color="' .$field['std'] . '" ';
					}
					$output .= '<input name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" class="bon-color"  type="text" value="' . esc_attr( $meta ) . '"' . $default_color .' />';
				break;

				case 'radio-img':
					$output .= '<ul class="meta_box_items">';

					foreach ( $options as $val => $option ) {

						$selected = '';
						if ( $meta != '' && $meta == $val ) {
							$selected = ' radio-img-selected';
						}
						$output .= '<li class="radio-img"><input class="radio-img-radio" type="radio" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '-' . $val . '" value="' . $val . '" ' . checked( $meta, $val, false ) . ' />
							<label class="radio-img-label" for="' . esc_attr( $id ) . '-' . $val . '">' . $val . '</label>
							<img src="' . esc_url( $option ) . '" alt="' . $val .'" class="radio-img-img ' . $selected .'" onclick="document.getElementById(\''. esc_attr( $id ) . '-' . $val .'\').checked=true;" />
								</li>';
					}
					$output .= '</ul>' . $desc;
				break;

				// post_select, post_chosen will be deleted in next theme
				case 'old_post_select':
				case 'old_post_list':
				case 'old_post_chosen':
					$output .= '<select data-placeholder="'.__('Select One','bon').'" name="' . esc_attr( $name ) . '[]" id="' . esc_attr( $id ) . '"' . ( $type == 'post_chosen' ? ' class="chosen"' : '' ) . ( isset( $multiple ) && $multiple == true ? ' multiple="multiple"' : '' ) . '>
							<option value="">'.__('Select One','bon').'</option>'; // Select One
					$q = array( 
						'post_type' => $post_type, 
						'posts_per_page' => -1, 
						'orderby' => 'name', 
						'order' => 'ASC',
						'post_status' => array( 'publish', 'pending' ),
					);

					if( isset($field['filter_author']) && $field['filter_author'] === true ) {
						$user_ID = get_current_user_id();

						if($user_ID > 0 ) {
							$q['author'] = $user_ID;
						}
					}

					$posts = get_posts( $q );
					
					foreach ( $posts as $item )
						$output .= '<option value="' . $item->ID . '"' . selected( is_array( $meta ) && in_array( $item->ID, $meta ), true, false ) . '>' . $item->post_title . '</option>';
					
					$output .= '</select><br />' . $desc;
				break;

				case 'date':
					$output .= '<input type="text" class="datepicker" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="' . $meta . '" size="30" />
							<br />' . $desc;
				break;

				case 'slider':
					$value = $meta != '' ? intval( $meta ) : '0';
					$output .= '<div id="' . esc_attr( $id ) . '-slider" class="ui-slide" data-min="'.$min.'" data-max="'.$max.'" data-step="'.$step.'"></div>';
					$output .= $f->form_input( array( 'name' => esc_attr( $name ), 'id' => esc_attr( $id ) ), esc_attr( $value ), 'class="bon-small-input slider-input" size="5"' );
					$output .= $desc;
				break;

				case 'image':
					$image = BON_IMAGES . '/image.png';	
					$output .= '<div class="meta_box_image"><span class="meta_box_default_image" style="display:none">' . $image . '</span>';
					if ( $meta ) {
						$image = wp_get_attachment_image_src( intval( $meta ), 'medium' );
						$image = $image[0];
					}				
					$output .=	'<input name="' . esc_attr( $name ) . '" type="hidden" class="meta_box_upload_image" value="' . intval( $meta ) . '" />
								<img src="' . esc_attr( $image ) . '" class="meta_box_preview_image" alt="" />
									<a href="#" class="meta_box_upload_image_button button" rel="' . get_the_ID() . '">Choose Image</a>
									<small>&nbsp;<a href="#" class="meta_box_clear_image_button">Remove Image</a></small></div>
									<br clear="all" />' . $desc;
				break;

				case 'gallery':
					$output .= '<div class="gallery-images-container">
						<ul class="gallery-images">';
							
							if ( $meta ) {
								$attachments = array_filter( explode( ',', $meta ) );
								foreach ( $attachments as $attachment_id ) {
									$src = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
									$src = $src[0];
									$output .= '<li class="image" data-attachment_id="' . $attachment_id . '">
										<img src="' . esc_attr($src) . '" alt="image" />
										<ul class="actions">
											<li><a href="#" class="delete" title="' . __( 'Delete image', 'bon' ) . '">' . __( 'Delete', 'bon' ) . '</a></li>
										</ul>
									</li>';
								}
							 }
					$output .= '</ul>
						<input type="hidden" class="image-gallery-input" id="'. esc_attr( $name ).'" name="'. esc_attr( $name ).'" value="'. esc_attr( $meta ).'" />
					</div>';

					$output .= '<p class="add-gallery-images hide-if-no-js"><a href="#">'. __( 'Add gallery images', 'bon' ) .'</a></p>';
				break;

				case 'file':
					$iconClass = 'meta_box_file';
					if ( $meta ) $iconClass .= ' checked';
					$output .=	'<div class="meta_box_file_stuff"><input name="' . esc_attr( $name ) . '" type="hidden" class="meta_box_upload_file" value="' . esc_url( $meta ) . '" />
								<span class="' . $iconClass . '"></span>
								<span class="meta_box_filename">' . esc_url( $meta ) . '</span>
									<a href="#" class="meta_box_upload_file_button button" rel="' . get_the_ID() . '">Choose File</a>
									<small>&nbsp;<a href="#" class="meta_box_clear_file_button">Remove File</a></small></div>
									<br clear="all" />' . $desc;
				break;

				case 'upload':
					$output .= $this->options_uploader( $id, $meta, null );
				break;

				// repeatable
				case 'repeatable':
					
					$output .= '<table id="' . esc_attr( $id ) . '-repeatable" class="meta_box_repeatable" cellspacing="0">';

					$repeatable_fields = $field['repeatable_fields'];

					$i = 0;
					// create an empty array
					if ( $meta == '' || $meta == array() ) {
						$keys = wp_list_pluck( $repeatable_fields, 'id' );
						$meta = array ( array_fill_keys( $keys, null ) );
					}
					$meta = array_values( $meta );


					foreach( $meta as $row ) {
						$output .= '<tr>
								<td>';
						foreach ( $repeatable_fields as $repeatable_field ) {
							
							if ( !isset( $meta[$i] ) || ! array_key_exists( $repeatable_field['id'], $meta[$i] ) )
								$meta[$i][$repeatable_field['id']] = null;

							$output .= '<fieldset>';
							$output .= ( isset( $repeatable_field['label'] ) && $repeatable_field['label'] != '' ) ? '<label>' . $repeatable_field['label']  . '</label>' : '';
							$output .= '<div class="meta_box_field_wrap">';
							$repeated_field = $this->render_element( $repeatable_field, $meta[$i][$repeatable_field['id']], array( $id, $i ) );
							$output .= $repeated_field;
							$output .= '</div></fieldset>';
						} // end each field
						$output .= '</td><td><span class="sort hndle"><i class="dashicons dashicons-menu"></i></span><a class="meta_box_repeatable_remove" href="#"><i class="dashicons dashicons-dismiss"></i></a></td></tr>';
						$i++;
					} // end each row
					$output .= '</tbody>';
					$output .= '
						<tfoot>
							<tr>
								<th><a class="meta_box_repeatable_add" href="#"><i class="dashicons dashicons-plus-alt"></i></a></th>
							</tr>
						</tfoot>';
					$output .= '</table>
						' . $desc;
				break;

				case 'heading':
					if( $this->counter >= 2 ) {
						$output .= '</div>'."\n";
					}
					$jquery_click_hook = preg_replace( '/[^a-zA-Z0-9\s]/', '', strtolower( $label ) );
					$jquery_click_hook = str_replace( ' ', '', $jquery_click_hook );
					$jquery_click_hook = "bon-option-" . $jquery_click_hook;
					$output .= '<div class="group" id="'. esc_attr( $jquery_click_hook ) .'"><h1 class="subtitle">'. esc_html( $label ) .'</h1>'."\n";
				break;
				
				case 'subheading':
					if( $this->counter >= 2 ) {
						$output .= '</div>'."\n";
					}
					$jquery_click_hook = preg_replace( '/[^a-zA-Z0-9\s]/', '', strtolower( $label ) );
					$jquery_click_hook = str_replace( ' ', '', $jquery_click_hook );
					$jquery_click_hook = "bon-option-" . $jquery_click_hook;
					$output .= '<div class="group" id="'. esc_attr( $jquery_click_hook ) .'"><h1 class="subtitle">'. esc_html( $label ).'</h1>'."\n";
				break;

				case 'info':
					$output .= $std;
				break;

				case 'code':
					$output .= '<code>';
					$output .= $meta;
					$output .= '</code>';
				break;
				
			} //end switch
			

			return $output;
			
		}

		public function options_uploader( $_id, $_value, $_desc = '', $_name = '' ) {

			$option_name = $this->option_name;

			$output = '';
			$id = '';
			$class = '';
			$int = '';
			$value = '';
			$name = '';
			
			$id = strip_tags( strtolower( $_id ) );
			
			// If a value is passed and we don't have a stored value, use the value that's passed through.
			if ( $_value != '' && $value == '' ) {
				$value = $_value;
			}
			
			if ( $_name != '' ) {
				$name = $_name;
			}
			else {
				$name = $option_name.'['.$id.']';
			}
			
			if ( $value ) {
				$class = ' has-file';
			}
			$output .= '<input id="' . $id . '" class="bon-input upload' . $class . '" type="text" name="'.$name.'" value="' . $value . '" placeholder="' . __('No file chosen', 'bon') .'" />' . "\n";
			if ( function_exists( 'wp_enqueue_media' ) ) {
				if ( ( $value == '' ) ) {
					$output .= '<input id="upload-' . $id . '" class="upload-button button" type="button" name="upload-button" value="' . __( 'Upload', 'bon' ) . '" />' . "\n";
				} else {
					$output .= '<input id="remove-' . $id . '" class="remove-file button" type="button" name="remove-file" value="' . __( 'Remove', 'bon' ) . '" />' . "\n";
				}
			} else {
				$output .= '<p><i>' . __( 'Upgrade your version of WordPress for full media support.', 'bon' ) . '</i></p>';
			}
			
			if ( $_desc != '' ) {
				$output .= '<span class="of-metabox-desc">' . $_desc . '</span>' . "\n";
			}
			
			$output .= '<div class="screenshot" id="' . $id . '-image">' . "\n";
			
			if ( $value != '' ) { 
				$remove = '<a class="remove-image">Remove</a>';
				$image = preg_match( '/(^.*\.jpg|jpeg|png|gif|ico*)/i', $value );
				if ( $image ) {
					$output .= '<img src="' . $value . '" alt="" />'.$remove.'';
				} else {
					$parts = explode( "/", $value );
					for( $i = 0; $i < sizeof( $parts ); ++$i ) {
						$title = $parts[$i];
					}

					// No output preview if it's not an image.			
					$output .= '';
				
					// Standard generic output if it's not an image.	
					$title = __( 'View File', 'bon' );
					$output .= '<div class="no-image"><span class="file_link"><a href="' . $value . '" target="_blank" rel="external">'.$title.'</a></span></div>';
				}	
			}
			$output .= '</div>' . "\n";
			return $output;
		}

		
	} //end Machine class
}
?>
