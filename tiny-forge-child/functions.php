<?php
/**
 * Tiny Forge Child Example functions and definitions.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook.
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @since Tiny Forge Child Example 1.0
 */

/**
 * Table of Contents:
 *
 *  1.0 - Parent theme's functions you can override.
 *    1.1 - Tip29 - Style navigation arrows for post listing (next/previous page navigation).
 *    1.2 - Tip13 - Remove Open Sans (from Google Fonts) as default font - disabled by default.
 *    1.3 - Allow HTML in post title. Original parent theme's function changes title for protected and private posts - disabled by default.
 *  2.0 - Custom Child Theme functions.
 *    2.1 - Tip01 - Properly include CSS and JavaScript files via functions.php - http://mtomas.com/27/
 *    2.2 - Add optional meta tags, scripts to head - disabled by default.
 *    2.2b - Tip02 - Optional code to enable favicon for the website, admin area and login page. Add favicon.ico file to the theme's /images folder - disabled by default.
 *    2.3 - Tip07 - Add new image size for custom post/page headers and select default header image - disabled by default.
 *    2.4 - Tip10 - Add Twenty Thirteen search form to WordPress nav menu, also see style.css
 *  3.0 - Other functions.
 *    3.1 - Tip81 - Completely disable the Post Formats UI in the post editor screen - disabled by default.
 *    3.2 - Tip08 - Remove junk from head - disabled by default.
 *    3.3 - Tip09 - Remove WordPress version info from head and feeds - better for security reasons - disabled by default.
 *    3.4 - Tip82 - No more jumping for read more link - disabled by default.
 *    3.5 - Tip84 - Remove error message on the login page - better for security reasons - disabled by default.
 *    3.6 - Tip34 - Display author info card at the bottom of posts on a single author website - disabled by default.
 *
 * ----------------------------------------------------------------------------
 */

/**
 * 1.0 - Parent theme's functions you can override.
 *
 * 1.1 - Tip29 - Style navigation arrows for post listing (next/previous page navigation).
 */
if ( ! function_exists( 'tinyforge_content_nav' ) ) :

function tinyforge_content_nav( $html_id ) {
	global $wp_query;
	
	$html_id = esc_attr( $html_id );

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo $html_id; ?>" class="navigation" role="navigation">
			<h3 class="assistive-text"><?php _e( 'Post navigation', 'tinyforgechild' ); ?></h3>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&laquo;</span> OLDER ARTICLES', 'tinyforge' ) ); ?></div>
			<div class="nav-next"><?php previous_posts_link( __( 'NEWER ARTICLES <span class="meta-nav">&raquo;</span>', 'tinyforge' ) ); ?></div>
		</nav><!-- #<?php echo $html_id; ?> .navigation -->
	<?php endif;
}
endif;

// 1.2 - Tip13 - Remove Open Sans (from Google Fonts) as default font.

/*
function tinyforgechild_remove_open_sans() {
   wp_dequeue_style( 'tinyforge-fonts' );
}
add_action('wp_print_styles','tinyforgechild_remove_open_sans');
*/

/**
 * 1.3 - Allow HTML in post title. Original parent theme's function changes title for protected and private posts - words "protected" and "private" are replaced by lock symbol.
 *
 * Please be aware, that incorrect HTML code in the title potentially can break whole site. It is also possible that in some cases it could affect the security of your site.
 *
 * You can also use this function if you're using localized WordPress and want to have lock symbol for the protected posts.
 *
 * In this case uncomment this function and replace words 'Protected' and 'Private' with the corresponding words in your language.
 */

/*
if ( ! function_exists( 'tinyforge_the_title_trim' ) ) :

function tinyforge_the_title_trim($title) {
	// $title = esc_attr($title); // Sanitize HTML characters in the title. Comment out this line if you want to use HTML in post titles.
	$findthese = array(
		'#Protected:#',
		'#Private:#'
	);
	$replacewith = array(
		'<span class="icon-webfont el-icon-lock"></span>', // What to replace "Protected:" with
		'<span class="icon-webfont el-icon-lock"></span> <span class="icon-webfont el-icon-user"></span>' // What to replace "Private:" with
	);
	$title = preg_replace($findthese, $replacewith, $title);
	return $title;
}
endif;
add_filter('the_title', 'tinyforge_the_title_trim');
*/

/**
 * 2.0 - Custom Child Theme functions.
 *
 * 2.1 - Tip01 - Properly include additional CSS and JavaScript files via functions.php.
 */
function tinyforgechild_scripts_styles() {

	/**
	 * Tip31 - Google Fonts support. Load Google Fonts stylesheet. Google recommends to load this stylesheet before any other stylesheet.
	 *
	 * Get the link to your fonts at: http://www.google.com/webfonts
	 *
	 * Remember, using many font styles can slow down your webpage, so only select the font styles that you actually need on your webpage. We recommend using no more than 3 fonts styles.
	 *
	 * If you want to register several fonts, use symbol | as a separator: http://fonts.googleapis.com/css?family=Oswald|Lora
	 *
	 * If you only want light style for Oswald and bold style for Lora, then use it this way: http://fonts.googleapis.com/css?family=Oswald:400|Lora:700
	 *
	 * One more usage example: http://fonts.googleapis.com/css?family=Neuton:400,400italic,700
	 *
	 * To test font, paste this to your post: <p style="font-family: 'Bigelow Rules', cursive; font-weight: 400; font-size: 30px;">Testing google fonts</p>
	 *
	 * Uncomment next PHP block to enable Google Fonts support:
	 */

	/*
	if ( !is_admin() ) { // we do not want this to load in the dashboard
	wp_register_style( 'tinyforgechild-google-fonts', 'http://fonts.googleapis.com/css?family=Bigelow+Rules', '', '', 'screen' );
	}

	// Enqueue Google Fonts stylesheet
	wp_enqueue_style( 'tinyforgechild-google-fonts' );
	*/

	// First adding CSS file of the Parent theme.
	wp_register_style( 'tinyforge-style', 
    get_template_directory_uri() . '/style.css', 
    array(), 
    '1.5.6', 
    'all' );

	// If you are using Google fonts, use instead next line to load main stylesheet (of course comment-out the one above):
	
	/*
	wp_register_style( 'tinyforge-style', 
    get_template_directory_uri() . '/style.css', 
	array( 'tinyforge-google-fonts' ), 
	'1.5.6', 
	'all' );
	*/
	
	// Adding CSS file of the Child theme. This style sheet stands last so it would override parent theme and other stylesheets.
	wp_register_style( 'tinyforgechild-style', 
    get_stylesheet_uri(), 
    array(), 
    '1.5.6', 
    'all' );
	
	// Enqueing:
	wp_enqueue_style( 'tinyforge-style' );
	wp_enqueue_style( 'tinyforgechild-style' );

	// Below is an example how to enqueue the script.
	// wp_enqueue_script( 'your-script-name', get_stylesheet_directory_uri() . '/js/your-script-file-name.js' );
}
add_action( 'wp_enqueue_scripts', 'tinyforgechild_scripts_styles' );

// 2.2 - Add optional meta tags, scripts to head.
function tinyforgechild_add_meta_to_head () {
	// Tip03 - We are people, not machines. Read more at: humanstxt.org.  Edit file humans.txt to include your information.

	// echo "\n"; echo '<!-- Find out who built this website! Read humans.txt for more information. -->'; echo "\n"; echo '<link rel="author" type="text/plain" href="'.get_stylesheet_directory_uri().'/inc/humans.txt" />'; echo "\n";

	// Project author's information

	// echo '<meta name="author" content="Your name here">'; echo "\n\n";

	// Jquery - Google, then wordpress's

	/*
	if (!is_admin()) {
		wp_deregister_script('jquery');
		wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js', false, '1.8.3', true);
		wp_enqueue_script('jquery');
	}
	*/
}
add_action('wp_head', 'tinyforgechild_add_meta_to_head');

// 2.2b - Tip02 - Optional code to enable favicon for the website, admin area and login page. Add favicon.ico file to the theme's /images folder.

/*	
function tinyforge_favicon() {
	$favicon_url = get_stylesheet_directory_uri() . '/images/favicon.ico';
	echo "\n"; echo '<link rel="shortcut icon" type="image/x-icon" href="' . $favicon_url . '" />'; echo "\n";
}
add_action('wp_head', 'tinyforge_favicon'); // Favicon for main website
add_action('admin_head', 'tinyforge_favicon'); // Favicon for admin area
add_action('login_head', 'tinyforge_favicon'); // Favicon for login page
*/

/** 
 * 2.3 - Tip07 - Add new image size for custom post/page headers and select default header image (also see Tip39 in style.css).
 *
 * $args in add_theme_support() in child theme will auto override what defined in parent's.
 *
 * See http://core.trac.wordpress.org/browser/tags/3.5/wp-includes/theme.php#L1292
 *
 * Also see http://wordpress.stackexchange.com/questions/108572/set-post-thumbnail-size-vs-add-image-size
 */
function tinyforgechild_custom_header_setup() {
	// Set custom default header. Uncomment if you need to change name, etc.

	/*
	$args = array(
		'default-image' => get_stylesheet_directory_uri() . '/images/headers/TinyForge-header.jpg',
	);
	*/
	
	// Add new custom image size, so later you could call it in the theme. Unique image name should be specified, eg. custom-featured-image-small, custom-header-image-large, etc.
	// add_image_size( 'custom-header-image-large', 1600, 9999 ); // 1600 pixels wide (and unlimited height)
}
add_action( 'after_setup_theme', 'tinyforgechild_custom_header_setup' );

/**
 * 2.4 - Tip10 - Add Twenty Thirteen search form to WordPress nav menu, also see style.css
 *
 * http://diythemes.com/thesis/rtfm/add-search-form-wp-wordpress-nav-menus/
 */
function tinyforgechild_add_search_to_wp_menu ( $items, $args ) {
	if( 'primary' === $args -> theme_location ) {
	$items .= '<li class="menu-item menu-item-search">';
	$items .= '<form role="search" method="get" class="searchform" action="' . home_url( '/' ) . '"><label><span class="screen-reader-text">Search for:</span></label>';
	$items .= '<input class="text_input" type="search" value="" name="s" id="s" placeholder="Search &hellip;" title="Search for:" /><input type="submit" class="searchsubmit" value="Search" /></form>';
	$items .= '</li>';
	}
	return $items;
}
add_filter('wp_nav_menu_items','tinyforgechild_add_search_to_wp_menu',10,2);

/**
 * 3.0 - Other functions.
 *
 * 3.1 - Tip81 - Completely disable the Post Formats support in the theme and Post Formats UI in the post editor screen.
 *
 * Have a normal/business website and do not really use or need Post Formats? http://wordpress.org/support/topic/remove-post-formats-alltogether
 */

/*
function tinyforgechild_remove_post_formats() {
    remove_theme_support('post-formats');
}
add_action('after_setup_theme', 'tinyforgechild_remove_post_formats', 11);
*/

// 3.2 - Tip08 - Remove junk from head.

/*
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'index_rel_link');
remove_action( 'wp_head', 'start_post_rel_link' );
remove_action( 'wp_head', 'adjacent_posts_rel_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
*/

// 3.3 - Tip09 - Remove WordPress version info from head and feeds - better for security reasons.


function tinyforgechild_complete_version_removal() {
	return '';
}
add_filter('the_generator', 'tinyforgechild_complete_version_removal');


/**
 * 3.4 - Tip82 - No more jumping for read more link.
 *
 * Clicking on "read more" or "continue reading" sends user to the top of the post, not to the place marked with "more".
 */

/*
function tinyforgechild_remove_more_jump_link($link) { 
	$offset = strpos($link, '#more-');
	if ($offset) {
	$end = strpos($link, '"',$offset);
	}
	if ($end) {
	$link = substr_replace($link, '', $offset, $end-$offset);
	}
	return $link;
}
add_filter('the_content_more_link', 'tinyforgechild_remove_more_jump_link');
*/

/**
 * 3.5 - Tip84 - Remove error message on the login page - better for security reasons,
 *
 * via: http://www.wpbeginner.com/wp-tutorials/11-vital-tips-and-hacks-to-protect-your-wordpress-admin-area/
 */
 
// add_filter('login_errors', create_function('$a', "return null;"));

// 3.6 - Tip34 - Display author info card at the bottom of posts on a single author website.

// add_filter( 'is_multi_author', '__return_true' );


/*

Nik gehitutako kodea

*/
/*
* Funtzio honek txantiloi gurasoaren izen bereko funtzioa berdefinitzen du. Honen bidez post bat erakustean
* ez da Data eta Egilerik agertuko
*/
function tinyforge_entry_meta_top() 
{
	
}