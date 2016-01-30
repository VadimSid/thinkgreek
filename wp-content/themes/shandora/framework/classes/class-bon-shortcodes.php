<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Class BON Shortcodes
 *
 *
 *
 * @author		Hermanto Lim
 * @copyright	Copyright (c) Hermanto Lim
 * @link		http://bonfirelab.com
 * @since		Version 1.2
 * @package 	BonFramework
 * @category 	Extension
 *
 *
*/ 
if( !class_exists( 'BON_Shortcodes' ) ) {

	class BON_Shortcodes {

		/**
		 * Init shortcodes
		 */
		public static function init() {
			// Define shortcodes

			$shortcodes = apply_filters( 'bon_shortcode_lists', array(
				/* Add theme-specific shortcodes. */
				'the-year'            => __CLASS__ . '::the_year',
				'site-link'           => __CLASS__ . '::site_link',
				'wp-link'             => __CLASS__ . '::wp_link',
				'theme-link'          => __CLASS__ . '::theme_link',
				'child-link'          => __CLASS__ . '::child_link',
				'loginout-link'       => __CLASS__ . '::loginout_link',
				'query-counter'       => __CLASS__ . '::query_counter',
				'nav-menu'            => __CLASS__ . '::nav_menu',

				/* Add entry-specific shortcodes. */
				'entry-title'         => __CLASS__ . '::entry_title',
				'entry-author'        => __CLASS__ . '::entry_author',
				'entry-author-avatar' => __CLASS__ . '::entry_author_avatar',
				'entry-terms'         => __CLASS__ . '::entry_terms',
				'entry-comments-link' => __CLASS__ . '::entry_comments_link',
				'entry-published'     => __CLASS__ . '::entry_published',
				'entry-edit-link'     => __CLASS__ . '::entry_edit_link',
				'entry-shortlink'     => __CLASS__ . '::entry_shortlink',
				'entry-permalink'     => __CLASS__ . '::entry_permalink',
				'entry-icon'          => __CLASS__ . '::entry_icon',
				'post-format-link'    => __CLASS__ . '::post_format_link',

				/* Add comment-specific shortcodes. */
				'comment-published'   => __CLASS__ . '::comment_published',
				'comment-author'      => __CLASS__ . '::comment_author',
				'comment-edit-link'   => __CLASS__ . '::comment_edit_link',
				'comment-reply-link'  => __CLASS__ . '::comment_reply_link',
				'comment-permalink'   => __CLASS__ . '::comment_permalink',

				/* Add singular-specific shortcodes. */
				'gallery-carousel'    => __CLASS__ . '::gallery_carousel',

			) );

			foreach ( $shortcodes as $shortcode => $function ) {
				add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
			}

		}

		/**
		 * Shortcode Render
		 *
		 * @param mixed $function
		 * @param array $atts (default: array())
		 * @return string
		 */
		public static function render( $output, $wrapper, $func = false ) {

			$wrapper_defaults = array(
				'before' => '',
				'after'  => '',
			);

			$wrapper = shortcode_atts( $wrapper_defaults, $wrapper );

			ob_start();

			$before 	= empty( $wrapper['before'] ) ? '' : $wrapper['before'];
			$after 		= empty( $wrapper['after'] ) ? '' : $wrapper['after'];

			echo $before;

			if( !empty( $output ) ) {
				if( $func === true ) {
					call_user_func( $output, $wrapper );
				} else {
					echo $output;
				}
			}

			echo $after;

			return ob_get_clean();
		}

		/**
		 * Shortcode to display the current year.
		 *
		 * @since 1.0
		 * @access public
		 * @uses date() Gets the current year.
		 * @return string
		 */
		public static function the_year( $attr ) {
			return self::render( date( __( 'Y', 'bon' ) ), $attr );
		}

		/**
		 * Shortcode to display a link back to the site.
		 *
		 * @since 1.0
		 * @access public
		 * @uses get_bloginfo() Gets information about the install.
		 * @return string
		 */
		public static function site_link( $attr ) {
			return '<a class="site-link" href="' . home_url() . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home"><span>' . get_bloginfo( 'name' ) . '</span></a>';
		}

		/**
		 * Shortcode to display a link to WordPress.org.
		 *
		 * @since 1.0
		 * @access public
		 * @return string
		 */
		public static function wp_link( $attr ) {
			return '<a class="wp-link" href="http://wordpress.org" title="' . esc_attr__( 'State-of-the-art semantic personal publishing platform', 'bon' ) . '"><span>' . __( 'WordPress', 'bon' ) . '</span></a>';
		}

		/**
		 * Shortcode to display a link to the parent theme page.
		 *
		 * @since 1.0
		 * @access public
		 * @uses get_theme_data() Gets theme (parent theme) information.
		 * @return string
		 */
		public static function theme_link( $attr ) {
			$theme = wp_get_theme( get_template(), get_theme_root( get_template_directory() ) );
			$url = esc_url( $theme->get( 'ThemeURI' ) );
			$title = sprintf( esc_attr__( '%s WordPress Theme', 'bon' ), $theme->get( 'Name' ) );
			$name = esc_attr( $theme->get( 'Name' ) );
			$o = '<a class="theme-link" href="' . $url . '" title="' . $title . '"><span>' . $name . '</span></a>';

			return self::render( $o, $attr );
		}

		/**
		 * Shortcode to display a link to the child theme's page.
		 *
		 * @since 1.0
		 * @access public
		 * @uses get_theme_data() Gets theme (child theme) information.
		 * @return string
		 */
		public static function child_link( $attr ) {
			$theme = wp_get_theme( get_stylesheet(), get_theme_root( get_stylesheet_directory() ) );
			$url = esc_url( $theme->get( 'ThemeURI' ) );
			$title = esc_attr( $theme->get( 'Name' ) );
			$name = esc_html( $theme->get( 'Name' ) );
			$o = '<a class="child-link" href="' . $url . '" title="' . $title . '"><span>' . $name . '</span></a>';

			return self::render( $o, $attr );
		}

		/**
		 * Shortcode to display a login link or logout link.
		 *
		 * @since 1.0
		 * @access public
		 * @uses is_user_logged_in() Checks if the current user is logged into the site.
		 * @uses wp_logout_url() Creates a logout URL.
		 * @uses wp_login_url() Creates a login URL.
		 * @return string
		 */
		public static function loginout_link( $attr ) {
			if ( is_user_logged_in() )
				$o = '<a class="logout-link" href="' . esc_url( wp_logout_url( site_url( $_SERVER['REQUEST_URI'] ) ) ) . '" title="' . esc_attr__( 'Log out', 'bon' ) . '">' . __( 'Log out', 'bon' ) . '</a>';
			else
				$o = '<a class="login-link" href="' . esc_url( wp_login_url( site_url( $_SERVER['REQUEST_URI'] ) ) ) . '" title="' . esc_attr__( 'Log in', 'bon' ) . '">' . __( 'Log in', 'bon' ) . '</a>';

			return self::render( $o, $attr );
		}

		/**
		 * Displays query count and load time if the current user can edit themes.
		 *
		 * @since 1.0
		 * @access public
		 * @uses current_user_can() Checks if the current user can edit themes.
		 * @return string
		 */
		public static function query_counter( $attr ) {
			if ( current_user_can( 'edit_theme_options' ) )
				return self::render( sprintf( __( 'This page loaded in %1$s seconds with %2$s database queries.', 'bon' ), timer_stop( 0, 3 ), get_num_queries() ), $attr );
			return '';
		}

		/**
		 * Displays a nav menu that has been created from the Menus screen in the admin.
		 *
		 * @since 1.0
		 * @access public
		 * @uses wp_nav_menu() Displays the nav menu.
		 * @return string
		 */
		public static function nav_menu( $attr ) {

			$attr = shortcode_atts(
				array(
					'menu'            => '',
					'container'       => 'div',
					'container_id'    => '',
					'container_class' => 'nav-menu',
					'menu_id'         => '',
					'menu_class'      => '',
					'link_before'     => '',
					'link_after'      => '',
					'before'          => '',
					'after'           => '',
					'fallback_cb'     => 'wp_page_menu',
					'walker'          => ''
				),
				$attr
			);
			$attr['echo'] = false;

			return wp_nav_menu( $attr );
		}

		/**
		 * Displays the edit link for an individual post.
		 *
		 * @since 1.0
		 * @access public
		 * @param array $attr
		 * @return string
		 */
		public static function entry_edit_link( $attr ) {

			$post_type = get_post_type_object( get_post_type() );

			if ( !current_user_can( $post_type->cap->edit_post, get_the_ID() ) )
				return '';

			$attr = shortcode_atts( array( 'before' => '', 'after' => '' ), $attr );

			return self::render( '<span class="entry-edit-meta entry-post-meta"><a class="post-edit-link" href="' . esc_url( get_edit_post_link( get_the_ID() ) ) . '" title="' . sprintf( esc_attr__( 'Edit %1$s', 'bon' ), $post_type->labels->singular_name ) . '">' . __( 'Edit', 'bon' ) . '</a></span>', $attr );
		}

		/**
		 * Displays the published date of an individual post.
		 *
		 * @since 1.0
		 * @access public
		 * @param array $attr
		 * @return string
		 */
		public static function entry_published( $attr ) {
			$attr = shortcode_atts( array( 'before' => '', 'after' => '', 'text' => __('Posted on:','bon'), 'format' => get_option( 'date_format' ) ), $attr );

			$published = '<span class="entry-published-meta entry-post-meta"><strong class="published-text entry-meta-title">'.$attr['text'].'</strong> <abbr title="' . get_the_time( esc_attr__( 'l, F jS, Y, g:i a', 'bon' ) ) . '">' . get_the_time( $attr['format'] ) . '</abbr></span>';

			return self::render( $published, $attr );
		}

		/**
		 * Displays a post's number of comments wrapped in a link to the comments area.
		 *
		 * @since 1.0
		 * @access public
		 * @param array $attr
		 * @return string
		 */
		public static function entry_comments_link( $attr ) {

			$comments_link = '';
			$number = doubleval( get_comments_number() );
			$attr = shortcode_atts( array( 'before' => '', 'after' => '', 'zero' => __( 'Comment:', 'bon' ), 'one' => __( 'Comment:', 'bon' ), 'more' => __( 'Comments:', 'bon' ), 'css_class' => 'comments-link', 'none' => '' ), $attr );

			if ( 0 == $number && !comments_open() && !pings_open() ) {
				if ( $attr['none'] )
					$comments_link = '<span class="' . esc_attr( $attr['css_class'] ) . '">' . sprintf( $attr['none'], number_format_i18n( $number ) ) . '</span>';
			}
			elseif ( 0 == $number )
				$comments_link = '<span class="entry-comment-meta entry-post-meta"><strong class="comment-text entry-meta-title">'.$attr['zero'].'</strong> <a class="' . esc_attr( $attr['css_class'] ) . '" href="' . get_permalink() . '#respond" title="' . sprintf( esc_attr__( 'Comment on %1$s', 'bon' ), the_title_attribute( 'echo=0' ) ) . '">' . number_format_i18n( $number ) . '</a></span>';
			elseif ( 1 == $number )
				$comments_link = '<span class="entry-comment-meta entry-post-meta"><strong class="comment-text entry-meta-title">'.$attr['one'].'</strong> <a class="' . esc_attr( $attr['css_class'] ) . '" href="' . get_comments_link() . '" title="' . sprintf( esc_attr__( 'Comment on %1$s', 'bon' ), the_title_attribute( 'echo=0' ) ) . '">' . number_format_i18n( $number ) . '</a></span>';
			elseif ( 1 < $number )
				$comments_link = '<span class="entry-comment-meta entry-post-meta"><strong class="comment-text entry-meta-title">'.$attr['more'].'</strong> <a class="' . esc_attr( $attr['css_class'] ) . '" href="' . get_comments_link() . '" title="' . sprintf( esc_attr__( 'Comment on %1$s', 'bon' ), the_title_attribute( 'echo=0' ) ) . '">' . number_format_i18n( $number ) . '</a></span>';

			return self::render( $comments_link, $attr );
		}

		/**
		 * Displays an individual post's author with a link to his or her archive.
		 *
		 * @since 1.0
		 * @access public
		 * @param array $attr
		 * @return string
		 */
		public static function entry_author( $attr ) {
			$attr = shortcode_atts( array( 'before' => '', 'after' => '', 'text' => __('Author:','bon') ), $attr );
			$author = '<span class="entry-author-meta entry-post-meta"><strong class="author-text entry-meta-title">'.$attr['text'].'</strong> <a class="url fn n" rel="author" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author_meta( 'display_name' ) ) . '">' . get_the_author_meta( 'display_name' ) . '</a></span>';
			return self::render( $author, $attr );
		}

		/**
		 * Displays an individual post's author with avatar.
		 *
		 * @since 1.0
		 * @access public
		 * @param array $attr
		 * @return string
		 */
		public static function entry_author_avatar( $attr ) {
			$attr = shortcode_atts( array( 'before' => '', 'after' => '', 'text' => __('About %1s','bon') ), $attr );
			$avatar = '<figure class="author-bio vcard clear">'.get_avatar(get_the_author_meta('ID')).'<figcaption><span class="author-link"><a class="url fn n" rel="author" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author_meta( 'display_name' ) ) . '">' . sprintf( $attr['text'] , get_the_author_meta( 'display_name' )) . '</a></span>'. get_the_author_meta( 'description' ).'</figcaption></figure>';
			return self::render( $avatar, $attr );
		}


		/**
		 * Displays a list of terms for a specific taxonomy.
		 *
		 * @since 1.0
		 * @access public
		 * @param array $attr
		 * @return string
		 */
		public static function entry_terms( $attr ) {
			$attr = shortcode_atts( array( 'before' => '', 'after' => '', 'text' => __('Category:','bon'), 'exclude_child' => false, 'id' => get_the_ID(), 'limit' => -1, 'taxonomy' => 'post_tag', 'separator' => ', ' ), $attr );
			$termlists = '';
			$the_terms = get_the_terms($attr['id'], $attr['taxonomy'] );
			$len = count($the_terms);
			$i = 1 ;
			if($the_terms) {
				foreach($the_terms as $term) {
					if($attr['exclude_child'] == true) {
						if($term->parent != 0) {
							continue;
						}
					}

					if($i > $attr['limit'] && $attr['limit'] != -1 ) {
						break;
					} 

					$termlists .= '<a title="'. $term->name .'" ref="' . $term->taxonomy . '" href="'.  get_term_link( $term->slug, $term->taxonomy ) .'">'.$term->name.'</a>';
					$termlists .= ', ';
							
					$i++;
				}
			}
			
			if(substr($termlists, -2) == ', ') {
				$termlists = substr($termlists, 0 , -2);
			}
				
			if(!empty($termlists)){
				return self::render( '<span class="entry-term-meta entry-post-meta">' . '<strong class="term-text entry-meta-title">' . $attr['text'] . '</strong> ' . $termlists . '</span>', $attr );
			} else {
				return '';
			}
		}

		/**
		 * Displays a post's title with a link to the post.
		 *
		 * @since 1.0
		 * @access public
		 * @return string
		 */
		public static function entry_title( $attr ) {

			$attr = shortcode_atts(
				array( 
					'before' => '', 
					'after' => '',
					'permalink' => true, 
					'tag'       => is_singular() ? 'h1' : 'h2' 
				), 
			$attr );

			$tag = tag_escape( $attr['tag'] );
			$class = sanitize_html_class( get_post_type() ) . '-title entry-title';

			if ( false == (bool)$attr['permalink'] )
				$title = the_title( "<{$tag} class='{$class}'>", "</{$tag}>", false );
			else
				$title = the_title( "<{$tag} class='{$class}'><a href='" . get_permalink() . "'>", "</a></{$tag}>", false );

			if ( empty( $title ) && !is_singular() )
				$title = "<{$tag} class='{$class}'><a href='" . get_permalink() . "'>" . __( '(Untitled)', 'bon' ) . "</a></{$tag}>";

			return self::render( $title, $attr );
		}

		/**
		 * Displays the shortlink of an individual entry.
		 *
		 * @since 1.0
		 * @access public
		 * @return string
		 */
		public static function entry_shortlink( $attr ) {

			$attr = shortcode_atts(
				array(
					'before' => '', 
					'after' => '',
					'text' => __( 'Shortlink', 'bon' ),
					'title' => the_title_attribute( array( 'echo' => false ) ),
				),
				$attr
			);

			$shortlink = esc_url( wp_get_shortlink( get_the_ID() ) );

			return self::render( "<a class='shortlink' href='{$shortlink}' title='" . esc_attr( $attr['title'] ) . "' rel='shortlink'>{$attr['text']}</a>", $attr );
		}

		/**
		 * Returns the output of the [entry-permalink] shortcode, which is a link back to the post permalink page.
		 *
		 * @since 1.0
		 * @param array $attr The shortcode arguments.
		 * @return string A permalink back to the post.
		 */
		public static function entry_permalink( $attr ) {

			$attr = shortcode_atts( array( 'before' => '', 'after' => '', 'text' => __('Read More','bon'), 'class' => '' ), $attr );

			return self::render( '<a href="' . esc_url( get_permalink() ) . '" class="permalink '.$attr['class'].'" title="'. the_title_attribute( array( 'before' => __('Permalink to ', 'bon'), 'echo' => 0) ) .'">' . sprintf(__( '%s', 'bon' ), $attr['text'] ) . '</a>', $attr );
		}

		/**
		 * Returns the output of the [post-format-link] shortcode.  This shortcode is for use when a theme uses the 
		 * post formats feature.
		 *
		 * @since 1.0
		 * @param array $attr The shortcode arguments.
		 * @return string A link to the post format archive.
		 */
		public static function post_format_link( $attr ) {

			$attr = shortcode_atts( array( 'before' => '', 'after' => '' ), $attr );
			$format = get_post_format();
			$url = ( empty( $format ) ? get_permalink() : get_post_format_link( $format ) );

			return self::render( '<a href="' . esc_url( $url ) . '" class="post-format-link">' . get_post_format_string( $format ) . '</a>', $attr );
		}

		/**
		 * Displays the published date and time of an individual comment.
		 *
		 * @since 1.0
		 * @access public
		 * @return string
		 */
		public static function comment_published( $attr ) {
			$link = '<span class="published">' . sprintf( __( '%1$s at %2$s', 'bon' ), '<abbr class="comment-date" title="' . get_comment_date( esc_attr__( 'l, F jS, Y, g:i a', 'bon' ) ) . '">' . get_comment_date() . '</abbr>', '<abbr class="comment-time" title="' . get_comment_date( esc_attr__( 'l, F jS, Y, g:i a', 'bon' ) ) . '">' . get_comment_time() . '</abbr>' ) . '</span>';
			return self::render( $link, $attr );
		}

		/**
		 * Displays the comment author of an individual comment.
		 *
		 * @since 1.0
		 * @access public
		 * @global $comment The current comment's DB object.
		 * @return string
		 */
		public static function comment_author( $attr ) {
			global $comment;

			$attr = shortcode_atts(
				array(
					'before' => '',
					'after' => '',
					'tag' => 'span', // @deprecated 1.2.0 Back-compatibility. Please don't use this argument.
				),
				$attr
			);

			$author = esc_html( get_comment_author( $comment->comment_ID ) );
			$url = esc_url( get_comment_author_url( $comment->comment_ID ) );

			/* Display link and cite if URL is set. Also, properly cites trackbacks/pingbacks. */
			if ( $url )
				$output = '<cite class="fn" title="' . $url . '"><a href="' . $url . '" title="' . esc_attr( $author ) . '" class="url" rel="external nofollow">' . $author . '</a></cite>';
			else
				$output = '<cite class="fn">' . $author . '</cite>';

			$output = '<' . tag_escape( $attr['tag'] ) . ' class="comment-author vcard">' . apply_filters( 'get_comment_author_link', $output ) . '</' . tag_escape( $attr['tag'] ) . '><!-- .comment-author .vcard -->';

			return self::render( $output, $attr );
		}

		/**
		 * Displays the permalink to an individual comment.
		 *
		 * @since 1.0
		 * @access public
		 * @return string
		 */
		public static function comment_permalink( $attr ) {
			global $comment;

			$attr = shortcode_atts( array( 'before' => '', 'after' => '' ), $attr );
			$link = '<a class="permalink" href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '" title="' . sprintf( esc_attr__( 'Permalink to comment %1$s', 'bon' ), $comment->comment_ID ) . '">' . __( 'Permalink', 'bon' ) . '</a>';
			return self::render( $link, $attr );
		}

		/**
		 * Displays a comment's edit link to users that have the capability to edit the comment.
		 *
		 * @since 1.0
		 * @access public
		 * @return string
		 */
		public static function comment_edit_link( $attr ) {
			global $comment;

			$edit_link = get_edit_comment_link( $comment->comment_ID );

			if ( !$edit_link )
				return '';

			$attr = shortcode_atts( array( 'before' => '', 'after' => '' ), $attr );

			$link = '<a class="comment-edit-link" href="' . esc_url( $edit_link ) . '" title="' . sprintf( esc_attr__( 'Edit %1$s', 'bon' ), $comment->comment_type ) . '"><span class="edit">' . __( 'Edit', 'bon' ) . '</span></a>';
			$link = apply_filters( 'edit_comment_link', $link, $comment->comment_ID );

			return self::render( $link, $attr );
		}

		/**
		 * Displays a reply link for the 'comment' comment_type if threaded comments are enabled.
		 *
		 * @since 1.0
		 * @access public
		 * @return string
		 */
		public static function comment_reply_link( $attr ) {

			if ( !get_option( 'thread_comments' ) || 'comment' !== get_comment_type() )
				return '';

			$defaults = array(
				'before' => '', 
				'after' => '',
				'reply_text' => __( 'Reply', 'bon' ),
				'login_text' => __( 'Log in to reply.', 'bon' ),
				'depth' => intval( $GLOBALS['comment_depth'] ),
				'max_depth' => get_option( 'thread_comments_depth' ),
			);
			$attr = shortcode_atts( $defaults, $attr );

			return get_comment_reply_link( $attr );
		}

		/**
		 * Displays post format icon.
		 *
		 * @since 1.0
		 * @access public
		 * @return string
		 */
		public static function entry_icon( $attr ) {

			$defaults = array(
				'before' => '', 'after' => '',
				'class' => '',
				'format_url' => true,
			);

			$attr = shortcode_atts( $defaults, $attr );

			if( !wp_script_is( 'dashicons', 'enqueued' ) )
				wp_enqueue_style( 'dashicons' );

			$format = get_post_format();

			if( $attr['format_url'] && $attr['format_url'] === true && !empty( $format ) ) {
				$url = !get_post_format_link( $format );
				$title = sprintf( __('View all posts in: %s', 'bon'), ucwords( $format ) );
			} else {
				$url = get_permalink();
				$title = the_title_attribute( array( 'before' => __('Permalink to ', 'bon'), 'echo' => 0) );
			}

			$o = '<a class="entry-post-meta entry-icon-meta '.$attr['class'].'" href="'.$url.'" title="'. $title .'">';

			$icon_options = apply_filters( 'bon_post_format_icons', array(
				'link'        => 'dashicons dashicons-admin-links',
				'video'       => 'dashicons dashicons-format-video',
				'gallery'     => 'dashicons dashicons-format-gallery',
				'image'       => 'dashicons dashicons-format-image',
				'audio'       => 'dashicons dashicons-format-audio',
				'quote'       => 'dashicons dashicons-format-quote',
				'chat'        => 'dashicons dashicons-format-chat',
				'aside'       => 'dashicons dashicons-format-aside',
				'status'      => 'dashicons dashicons-format-status',
				'standard'    => 'dashicons dashicons-media-default',
			) );

			if( !empty( $format ) && array_key_exists( $format , $icon_options ) ) {
				$o .= '<i class="'.$icon_options[$format].'"></i>';
			} else {
				$o .= '<i class="'.$icon_options['standard'].'"></i>';
			}

			$o .= '</a>';

			return self::render( $o, $attr );

		}

		public static function gallery_carousel( $attr ) {

			if( wp_script_is('bootstrap', 'queue') === false ) {
				wp_enqueue_script( 'gallery-carousel' );
			}
			if( wp_style_is('bootstrap', 'queue') === false ) {
		      wp_enqueue_style( 'gallery-carousel' );
		    }

		    $post = get_post();

			static $instance = 0;
			$instance++;

			if ( ! empty( $attr['ids'] ) ) {
				// 'ids' is explicitly ordered, unless you specify otherwise.
				if ( empty( $attr['orderby'] ) )
					$attr['orderby'] = 'post__in';
				$attr['include'] = $attr['ids'];
			}

			// Allow plugins/themes to override the default gallery template.
			$o = apply_filters('post_gallery_carousel', '', $attr);

			if ( $o != '' )
				return $o;

			// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
			if ( isset( $attr['orderby'] ) ) {
				$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
				if ( !$attr['orderby'] )
					unset( $attr['orderby'] );
			}

			
			$defaults = array(
				'order'      => 'ASC',
				'orderby'    => 'menu_order ID',
				'id'         => $post ? $post->ID : 0,
				'size'       => 'large',
				'include'    => '',
				'before' => '', 
				'after' => '',
				'exclude'    => ''
			);

			$attr = shortcode_atts( $defaults, $attr );

			extract($attr);

			$id = intval($id);

			if ( 'RAND' == $order )
				$orderby = 'none';

			if ( !empty($include) ) {
				$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

				$attachments = array();
				foreach ( $_attachments as $key => $val ) {
					$attachments[$val->ID] = $_attachments[$key];
				}
			} elseif ( !empty($exclude) ) {
				$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
			} else {
				$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
			}

			if ( empty($attachments) )
				return '';


			$i = 0;
			$item = '';
			foreach ( $attachments as $id => $attachment ) {
				if ( ! empty( $attr['link'] ) && 'file' === $attr['link'] )
					$image_output = wp_get_attachment_link( $id, $size, false, false );
				elseif ( ! empty( $attr['link'] ) && 'none' === $attr['link'] )
					$image_output = wp_get_attachment_image( $id, $size, false );
				else
					$image_output = wp_get_attachment_link( $id, $size, true, false );

				$item .= '<div class="gallery-carousel-item item '. ($i == 0 ? 'active' : '') .'">';
				$item .= $image_output;

				if( !empty($attachment->post_excerpt) ) {
					$item .= '<div class="carousel-caption gallery-carousel-caption">';
					$item .= wptexturize( $attachment->post_excerpt );
					$item .= '</div>'; // close caption
				}
				
				$item .= '</div>'; // close gallery-carousel-item

				$i++;
			}
			

			$o .= '<div id="bon-gallery-carousel-'.$instance.'" data-interval="'.apply_filters( 'gallery_carousel_interval', 10000 ).'" class="bon-gallery-carousel carousel slide">';
		    $o .= '<div class="carousel-inner">';

		    $o .= $item;
		   
		    $o .= '</div>'; // close carousel inner
		    $o .= '<a class="left carousel-control" href="#bon-gallery-carousel-'.$instance.'" data-slide="prev"><i class="bonicons bi-angle-left"></i></a>';
		    $o .= '<a class="right carousel-control" href="#bon-gallery-carousel-'.$instance.'" data-slide="next"><i class="bonicons bi-angle-right"></i></a>';
		    $o .= '</div>'; //close carousel slide

		    return self::render( $o, $attr );
		}

		

	}

	
	add_action( 'init', array( 'BON_Shortcodes', 'init' ) );

}
