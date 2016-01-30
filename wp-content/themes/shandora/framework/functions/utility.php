<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Utility Functions
 *
 *
 *
 * @author		Hermanto Lim
 * @copyright	Copyright (c) Hermanto Lim
 * @link		http://bonfirelab.com
 * @since		Version 1.0
 * @package 	BonFramework
 * @category 	Fuctions
 *
 *
*/ 

/* Add extra support for post types. */
add_action( 'init', 'bon_add_post_type_support' );
add_action( 'after_switch_theme', 'bon_flush_rewrite_rules' );



/* Flush your rewrite rules */
function bon_flush_rewrite_rules() {
     flush_rewrite_rules();
}

/**
 * This function is for adding extra support for features not default to the core post types.
 * Excerpts are added to the 'page' post type.  Comments and trackbacks are added for the
 * 'attachment' post type.  Technically, these are already used for attachments in core, but 
 * they're not registered.
 *
 * @since 1.0
 * @access public
 * @return void
 */
function bon_add_post_type_support() {

	/* Add support for excerpts to the 'page' post type. */
	add_post_type_support( 'page', array( 'excerpt' ) );

	/* Add support for trackbacks to the 'attachment' post type. */
	add_post_type_support( 'attachment', array( 'trackbacks' ) );
}

/**
 * Checks if a post of any post type has a custom template.  This is the equivalent of WordPress' 
 * is_page_template() function with the exception that it works for all post types.
 *
 * @since 1.0
 * @access public
 * @param string $template The name of the template to check for.
 * @return bool Whether the post has a template.
 */
function bon_has_post_template( $template = '' ) {

	/* Assume we're viewing a singular post. */
	if ( is_singular() ) {

		/* Get the queried object. */
		$post = get_queried_object();

		/* Get the post template, which is saved as metadata. */
		$post_template = get_post_meta( get_queried_object_id(), "_wp_{$post->post_type}_template", true );

		/* If a specific template was input, check that the post template matches. */
		if ( !empty( $template) && ( $template == $post_template ) )
			return true;

		/* If no specific template was input, check if the post has a template. */
		elseif ( empty( $template) && !empty( $post_template ) )
			return true;
	}

	/* Return false for everything else. */
	return false;
}

/**
 * Retrieves the file with the highest priority that exists.  The function searches both the stylesheet 
 * and template directories.  This function is similar to the locate_template() function in WordPress 
 * but returns the file name with the URI path instead of the directory path.
 *
 * @since 1.0
 * @access public
 * @link http://core.trac.wordpress.org/ticket/18302
 * @param array $file_names The files to search for.
 * @return string
 */
function bon_locate_theme_file( $file_names ) {

	$located = '';

	/* Loops through each of the given file names. */
	foreach ( (array) $file_names as $file ) {

		/* If the file exists in the stylesheet (child theme) directory. */
		if ( is_child_theme() && file_exists( trailingslashit( get_stylesheet_directory() ) . $file ) ) {
			$located = trailingslashit( get_stylesheet_directory_uri() ) . $file;
			break;
		}

		/* If the file exists in the template (parent theme) directory. */
		elseif ( file_exists( trailingslashit( get_template_directory() ) . $file ) ) {
			$located = trailingslashit( get_template_directory_uri() ) . $file;
			break;
		}
	}

	return $located;
}
/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return void
 */
function bon_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}

	$located = bon_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
		return;
	}

	do_action( 'bon_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'bon_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *		yourtheme		/	$template_path	/	$template_name
 *		yourtheme		/	$template_name
 *		$default_path	/	$template_name
 *
 * @access public
 * @param string $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function bon_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = apply_filters('bon_template_path', 'templates/');
	}

	if ( ! $default_path ) {
		$default_path = trailingslashit( BON_DIR ) . 'templates/';
	}

	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	// Return what we found
	return apply_filters('bon_locate_template', $template, $template_name, $template_path);
}

/**
 * @return array
 * @param string $post_type
 */
if( !function_exists('bon_get_post_id_lists') ) {
	function bon_get_post_id_lists( $post_type, $numberposts = 100 ){
		
		$posts = get_posts(array('post_type' => $post_type, 'numberposts'=> $numberposts));
		
		$posts_title = array();

		if(!empty($posts)) {
			foreach ($posts as $post) {
				$posts_title[$post->ID] = $post->post_title;
			}

		}
		return $posts_title;

	}
}

/**
 * Get post type terms
 * @return array
 * @param string $name
 * @param string $parent
 */
if( !function_exists('bon_get_categories') ) {
	function bon_get_categories( $name, $parent = '' ){
			
		if( empty($parent) ){ 
			$get_category = get_categories( array( 'taxonomy' => $name, 'hide_empty' => 0	));
			$category_list = array();
			$category_list['all'] = 'All';
			if( !empty($get_category) ){
				foreach( $get_category as $category ){
					if(is_object($category)) {
						$category_list[$category->slug] = $category->name;
					}
				}
			}
				
			return $category_list;

		} else {
			$parent_id = get_term_by('slug', $parent, $category_name);
			$get_category = get_categories( array( 'taxonomy' => $name, 'child_of' => $parent_id->term_id, 'hide_empty' => 0	));
			$category_list = array( '0' => $parent );
			
			if( !empty($get_category) ){
				foreach( $get_category as $category ){
					if(is_object($category)) {
						$category_list[$category->slug] = $category->name;
					}
				}
			}
				
			return $category_list;		
		}
	}
}

/**
 *  calculates a darker or lighter color variation of a color
 *  @param string $color hex color code
 *  @param string $opacity the opacity level ( default false )
 *  @return string returns the converted string
 */

if( !function_exists('bon_hex_to_rgba') ) {

	function bon_hex_to_rgba($color, $opacity = false) {

		/* Convert hexdec color string to rgb(a) string */

		$default = 'rgb(0,0,0)';

		//Return default if no color provided
		if(empty($color))
	          return $default; 

		//Sanitize $color if "#" is provided 
	        if ($color[0] == '#' ) {
	        	$color = substr( $color, 1 );
	        }

	        //Check if color has 6 or 3 characters and get values
	        if (strlen($color) == 6) {
	                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
	        } elseif ( strlen( $color ) == 3 ) {
	                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
	        } else {
	                return $default;
	        }

	        //Convert hexadec to rgb
	        $rgb =  array_map('hexdec', $hex);

	        //Check if opacity is set(rgba or rgb)
	        if($opacity){
	        	if(abs($opacity) > 1)
	        		$opacity = 1.0;
	        	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
	        } else {
	        	$output = 'rgb('.implode(",",$rgb).')';
	        }

	        //Return rgb(a) color string
	        return $output;
	}
}

/**
 *  calculates a darker or lighter color variation of a color
 *  @param string $color hex color code
 *  @param string $shade darker or lighter
 *  @param int $amount how much darker or lighter
 *  @return string returns the converted string
 */

if( !function_exists('bon_color_mod') ) {
	
 	function bon_color_mod($color, $shade, $amount) {
 		//remove # from the begiining if available and make sure that it gets appended again at the end if it was found
 		$newcolor = "";
 		$prepend = "";
 		if(strpos($color,'#') !== false) 
 		{ 
 			$prepend = "#";
 			$color = substr($color, 1, strlen($color)); 
 		}
 		
 		//iterate over each character and increment or decrement it based on the passed settings
 		$nr = 0;
		while (isset($color[$nr])) 
		{
			$char = strtolower($color[$nr]);
			
			for($i = $amount; $i > 0; $i--)
			{
				if($shade == 'lighter')
				{
					switch($char)
					{
						case 9: $char = 'a'; break;
						case 'f': $char = 'f'; break;
						default: $char++;
					}
				}
				else if($shade == 'darker')
				{
					switch($char)
					{
						case 'a': $char = '9'; break;
						case '0': $char = '0'; break;
						default: $char = chr(ord($char) - 1 );
					}
				}
			}
			$nr ++;
			$newcolor.= $char;
		}
 		
		$newcolor = $prepend.$newcolor;
		return $newcolor;
	}
}


function bon_author_rewrite_rules(){

	$rules = array(
		'subscriber',
		'contributor',
		'author',
		'editor',
		'administrator',
	);

	foreach ( $rules as $rule ) {
		add_rewrite_rule(
			$rule . '/([^/]+)/?$','index.php?author_name=$matches[1]',
			'top'
		);
	}

}

add_action( 'init', 'bon_author_rewrite_rules' );

function bon_custom_author_link( $link, $author_id, $author_nicename ){

	$rules = array(
		'subscriber',
		'contributor',
		'author',
		'editor',
		'administrator',
	);


	foreach( $rules as $rule ) {
		if ( user_can( $author_id, $rule ) ) {
			return home_url( $rule . '/' . $author_nicename . '/' );
		}
	}
   
    return $link;

}
add_filter( 'author_link', 'bon_custom_author_link', 10, 3 );


/**
 * Retrieve icon lists options from array
 *
 * @access public
 * @since 1.0 
 * @return html string
*/

function bon_get_icon_lists() {
	
	if ( false === ( $select = get_transient( 'bon_icon_selections' ) ) ) {

		$icons = array("bi-glass","bi-music","bi-search","bi-envelope-o","bi-heart","bi-star","bi-star-o","bi-user","bi-film","bi-th-large","bi-th","bi-th-list","bi-check","bi-times","bi-search-plus","bi-search-minus","bi-power-off","bi-signal","bi-cog","bi-trash-o","bi-home","bi-file-o","bi-clock-o","bi-road","bi-download","bi-arrow-circle-o-down","bi-arrow-circle-o-up","bi-inbox","bi-play-circle-o","bi-repeat","bi-refresh","bi-list-alt","bi-lock","bi-flag","bi-headphones","bi-volume-off","bi-volume-down","bi-volume-up","bi-qrcode","bi-barcode","bi-tag","bi-tags","bi-book","bi-bookmark","bi-print","bi-camera","bi-font","bi-bold","bi-italic","bi-text-height","bi-text-width","bi-align-left","bi-align-center","bi-align-right","bi-align-justify","bi-list","bi-outdent","bi-indent","bi-video-camera","bi-picture-o","bi-pencil","bi-map-marker","bi-adjust","bi-tint","bi-pencil-square-o","bi-share-square-o","bi-check-square-o","bi-arrows","bi-step-backward","bi-fast-backward","bi-backward","bi-play","bi-pause","bi-stop","bi-forward","bi-fast-forward","bi-step-forward","bi-eject","bi-chevron-left","bi-chevron-right","bi-plus-circle","bi-minus-circle","bi-times-circle","bi-check-circle","bi-question-circle","bi-info-circle","bi-crosshairs","bi-times-circle-o","bi-check-circle-o","bi-ban","bi-arrow-left","bi-arrow-right","bi-arrow-up","bi-arrow-down","bi-share","bi-expand","bi-compress","bi-plus","bi-minus","bi-asterisk","bi-exclamation-circle","bi-gift","bi-leaf","bi-fire","bi-eye","bi-eye-slash","bi-exclamation-triangle","bi-plane","bi-calendar","bi-random","bi-comment","bi-magnet","bi-chevron-up","bi-chevron-down","bi-retweet","bi-shopping-cart","bi-folder","bi-folder-open","bi-arrows-v","bi-arrows-h","bi-bar-chart-o","bi-twitter-square","bi-facebook-square","bi-camera-retro","bi-key","bi-cogs","bi-comments","bi-thumbs-o-up","bi-thumbs-o-down","bi-star-half","bi-heart-o","bi-sign-out","bi-linkedin-square","bi-thumb-tack","bi-external-link","bi-sign-in","bi-trophy","bi-github-square","bi-upload","bi-lemon-o","bi-phone","bi-square-o","bi-bookmark-o","bi-phone-square","bi-twitter","bi-facebook","bi-github","bi-unlock","bi-credit-card","bi-rss","bi-hdd-o","bi-bullhorn","bi-bell","bi-certificate","bi-hand-o-right","bi-hand-o-left","bi-hand-o-up","bi-hand-o-down","bi-arrow-circle-left","bi-arrow-circle-right","bi-arrow-circle-up","bi-arrow-circle-down","bi-globe","bi-wrench","bi-tasks","bi-filter","bi-briefcase","bi-arrows-alt","bi-users","bi-link","bi-cloud","bi-flask","bi-scissors","bi-files-o","bi-paperclip","bi-floppy-o","bi-square","bi-bars","bi-list-ul","bi-list-ol","bi-strikethrough","bi-underline","bi-table","bi-magic","bi-truck","bi-pinterest","bi-pinterest-square","bi-google-plus-square","bi-google-plus","bi-money","bi-caret-down","bi-caret-up","bi-caret-left","bi-caret-right","bi-columns","bi-sort","bi-sort-asc","bi-sort-desc","bi-envelope","bi-linkedin","bi-undo","bi-gavel","bi-tachometer","bi-comment-o","bi-comments-o","bi-bolt","bi-sitemap","bi-umbrella","bi-clipboard","bi-lightbulb-o","bi-exchange","bi-cloud-download","bi-cloud-upload","bi-user-md","bi-stethoscope","bi-suitcase","bi-bell-o","bi-coffee","bi-cutlery","bi-file-text-o","bi-building-o","bi-hospital-o","bi-ambulance","bi-medkit","bi-fighter-jet","bi-beer","bi-h-square","bi-plus-square","bi-angle-double-left","bi-angle-double-right","bi-angle-double-up","bi-angle-double-down","bi-angle-left","bi-angle-right","bi-angle-up","bi-angle-down","bi-desktop","bi-laptop","bi-tablet","bi-mobile","bi-circle-o","bi-quote-left","bi-quote-right","bi-spinner","bi-circle","bi-reply","bi-github-alt","bi-folder-o","bi-folder-open-o","bi-smile-o","bi-frown-o","bi-meh-o","bi-gamepad","bi-keyboard-o","bi-flag-o","bi-flag-checkered","bi-terminal","bi-code","bi-reply-all","bi-mail-reply-all","bi-star-half-o","bi-location-arrow","bi-crop","bi-code-fork","bi-chain-broken","bi-question","bi-info","bi-exclamation","bi-superscript","bi-subscript","bi-eraser","bi-puzzle-piece","bi-microphone","bi-microphone-slash","bi-shield","bi-calendar-o","bi-fire-extinguisher","bi-rocket","bi-maxcdn","bi-chevron-circle-left","bi-chevron-circle-right","bi-chevron-circle-up","bi-chevron-circle-down","bi-html5","bi-css3","bi-anchor","bi-unlock-alt","bi-bullseye","bi-ellipsis-h","bi-ellipsis-v","bi-rss-square","bi-play-circle","bi-ticket","bi-minus-square","bi-minus-square-o","bi-level-up","bi-level-down","bi-check-square","bi-pencil-square","bi-external-link-square","bi-share-square","bi-compass","bi-caret-square-o-down","bi-caret-square-o-up","bi-caret-square-o-right","bi-eur","bi-gbp","bi-usd","bi-inr","bi-jpy","bi-rub","bi-krw","bi-btc","bi-file","bi-file-text","bi-sort-alpha-asc","bi-sort-alpha-desc","bi-sort-amount-asc","bi-sort-amount-desc","bi-sort-numeric-asc","bi-sort-numeric-desc","bi-thumbs-up","bi-thumbs-down","bi-youtube-square","bi-youtube","bi-xing","bi-xing-square","bi-youtube-play","bi-dropbox","bi-stack-overflow","bi-instagram","bi-flickr","bi-adn","bi-bitbucket","bi-bitbucket-square","bi-tumblr","bi-tumblr-square","bi-long-arrow-down","bi-long-arrow-up","bi-long-arrow-left","bi-long-arrow-right","bi-apple","bi-windows","bi-android","bi-linux","bi-dribbble","bi-skype","bi-foursquare","bi-trello","bi-female","bi-male","bi-gittip","bi-sun-o","bi-moon-o","bi-archive","bi-bug","bi-vk","bi-weibo","bi-renren","bi-pagelines","bi-stack-exchange","bi-arrow-circle-o-right","bi-arrow-circle-o-left","bi-caret-square-o-left","bi-dot-circle-o","bi-wheelchair","bi-vimeo-square","bi-try","bi-plus-square-o");

		$icons = apply_filters( 'bon_get_icon_lists', $icons );

		$select = '<span class="bon-icon-font-holder">';

		foreach( $icons as $ic ) {
			$select .= '<span class="bon-icon-cell" data-icon="'.$ic.'">';
			$select .= '<i class="bonicons '.$ic.'" title="'. ucwords( str_replace('-', ' ', str_replace('bi-', '', $ic) ) ).'"></i>';
			$select .= '</span>';
  		}

  		$select .= '</span>';

  		set_transient( 'bon_icon_selections' , $select, 4 * WEEK_IN_SECONDS );
	}

	return $select;
}


/**
 * Output font icon selection modal upon ajax request
 *
 * @access public
 * @since 1.0 
 * @return void
*/

function bon_get_icon_modal() {

	if ( !function_exists( 'check_admin_referer' ) ) {				
		return false;
		exit;
	}

	if( check_admin_referer( 'bon_icon_selection', 'nonce' ) ) {
		
		//$item_id = esc_attr( $_POST['item_id'] );

		$o = bon_get_icon_lists();

		echo $o;

		exit;
	}
}


add_action( 'wp_ajax_bon_icon_selection', 'bon_get_icon_modal' );
add_action( 'wp_ajax_nopriv_bon_icon_selection', 'bon_get_icon_modal' );

function bon_enqueue_utility_script() {
	if( !is_admin() )
		return;

	wp_enqueue_style( 'bonicons', trailingslashit( BON_CSS ) . 'frontend/bonicons.css' );
	wp_enqueue_script( 'bon-admin-util', trailingslashit( BON_JS ) . 'utility.js', array( 'jquery' ), '1.0.0', true );

	wp_localize_script( 'bon-admin-util', 'bon_util_ajax', array(
		'url' => admin_url('admin-ajax.php'),
		'choose_icon' => __( 'Choose Icon', 'bon'),
		'remove_icon' => __( 'Remove Icon', 'bon')
	) );
}

add_action( 'admin_enqueue_scripts', 'bon_enqueue_utility_script', 1000 );

function bon_icon_select_field( $item_id, $item_name, $button_id, $val = '', $fieldset_class = array(), $input_class = array() ) {

	$o = '<span class="bon-icon-fieldset '. join( ' ', $fieldset_class ) .'">';

	$o .= wp_nonce_field( 'bon_icon_selection', 'bon_icon_nonce', false, false );

	$ic_cls = '';
	if( $val == '' ) { $ic_cls = 'bon-no-icon'; }

	$o .= '<span class="bon-icon-placeholder '.$ic_cls.'"><i class="bonicons '. $val .'"></i></span>';

	$o .= '<input placeholder="'.__('No icon chosen','bon').'" class="'. join( ' ', $input_class ).' bon-icon-input" type="text" value="'. $val .'" id="'.$item_id.'" name="'.$item_name.'" />';
	
	if ( empty( $val ) ) {
		$o .= '<input class="bon-choose-icon button" data-id="'.$button_id.'" type="button" value="' . __( 'Choose Icon', 'bon' ) . '" />' . "\n";
	} else {
		$o .= '<input class="bon-remove-icon button" data-id="'.$button_id.'" type="button" value="' . __( 'Remove', 'bon' ) . '" />' . "\n";
	}

	$o .= '</span>';

	return $o;
}


function bon_process_icon_output( $icon ) {

	if( substr( $icon, 0, 4 ) == 'awe-' ) {
		$icon = str_replace( 'awe-', 'bi-', $icon );
    }

	if( substr( $icon, 0, 3 ) == 'bi-' && strpos( $icon , 'bonicons') == false ) {
        $icon = 'bonicons ' . $icon;
    }

    return apply_filters( 'bon_process_icon_output', $icon );
}


function bon_trim_excerpt( $text, $excerpt_length = '', $excerpt_more = '' ) {

    $text = strip_shortcodes( $text );
    $text = str_replace(']]>', ']]&gt;', $text);
    
    if( '' == $excerpt_length )
    	$excerpt_length = apply_filters('excerpt_length', 55);

    if( '' == $excerpt_more )
    	$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');

    return wp_trim_words( $text, $excerpt_length, $excerpt_more );
}


function bon_generate_meta_keys( $post_type = 'post' ) {

	if( empty( $post_type ) ) 
		return;

	$post_type = esc_attr( $post_type );

    global $wpdb;

    $query = "SELECT DISTINCT($wpdb->postmeta.meta_key) 
	        FROM $wpdb->posts 
	        LEFT JOIN $wpdb->postmeta 
	        ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
	        WHERE $wpdb->posts.post_type = '%s' 
	        AND $wpdb->postmeta.meta_key != '' 
	        AND $wpdb->postmeta.meta_key NOT RegExp '(^[_0-9].+$)' 
	        AND $wpdb->postmeta.meta_key NOT RegExp '(^[0-9]+$)'";

    $meta_keys = $wpdb->get_col( $wpdb->prepare( $query, $post_type ) );

    $meta_keys = array_combine( $meta_keys, $meta_keys ); // looks dumb but this to make sure no duplicate key

    set_transient( "bon_{$post_type}_meta_key_lists" , $meta_keys, 60*60*24); //# 1 Day Expiration

    return $meta_keys;
}

function bon_get_meta_key_lists( $post_type = 'post' ) {
	if( empty( $post_type ) )
		return;

	$post_type = esc_attr( $post_type );

    $cache = get_transient( "bon_{$post_type}_meta_key_lists" );

    $meta_keys = $cache ? $cache : bon_generate_meta_keys( $post_type );

    return $meta_keys;
}

?>