<?php
/**
 * @package Boss Child Theme
 * The parent theme functions are located at /boss/buddyboss-inc/theme-functions.php
 * Add your own functions in this file.
 */

/**
 * Sets up theme defaults
 *
 * @since Boss Child Theme 1.0.0
 */
function boss_child_theme_setup()
{
  /**
   * Makes child theme available for translation.
   * Translations can be added into the /languages/ directory.
   * Read more at: http://www.buddyboss.com/tutorials/language-translations/
   */

  // Translate text from the PARENT theme.
  load_theme_textdomain( 'boss', get_stylesheet_directory() . '/languages' );

  // Translate text from the CHILD theme only.
  // Change 'boss' instances in all child theme files to 'boss_child_theme'.
  // load_theme_textdomain( 'boss_child_theme', get_stylesheet_directory() . '/languages' );

}
add_action( 'after_setup_theme', 'boss_child_theme_setup' );

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Boss Child Theme  1.0.0
 */
function boss_child_theme_scripts_styles()
{
  /**
   * Scripts and Styles loaded by the parent theme can be unloaded if needed
   * using wp_deregister_script or wp_deregister_style.
   *
   * See the WordPress Codex for more information about those functions:
   * http://codex.wordpress.org/Function_Reference/wp_deregister_script
   * http://codex.wordpress.org/Function_Reference/wp_deregister_style
   **/

  /*
   * Styles
   */
  wp_enqueue_style( 'boss-child-custom', get_stylesheet_directory_uri().'/css/custom.css', array(), time() , false );
}
add_action( 'wp_enqueue_scripts', 'boss_child_theme_scripts_styles', 9999 );


/****************************** CUSTOM FUNCTIONS ******************************/

// Add your own custom functions here

//** ADD IN OUR RSE FUNCTION CUSTOMIZATIONS **//

$dir = dirname(__FILE__);//can be replaced with your local path

wpe_include_dir( $dir . "/inc" );

function wpe_include_dir($dir = "" ) {

    if( ! empty( $dir ) ) {
        foreach (glob( $dir ."/*.php") as $filename) {
			
             if(file_exists($filename))
             {
				 //echo "<pre>";
				 //echo $filename;
				 //echo "</pre>";
                 require_once($filename);
             }
        };
    }
}
//** END RSE FUNCTION CUSTOMIZATIONS **//

/* Override Page Title on BuddyPress Courses" Page */
function bp_learndash_courses_page_title_override(){
    $title = sprintf( __( 'Registered %s', 'buddypress-learndash' ), LearnDash_Custom_Label::get_label( 'courses' ) );
//    $title = sprintf( __( 'Courses in Progress' ) );
        echo apply_filters( 'bp_learndash_courses_page_title_override',$title );
}
add_filter('bp_learndash_courses_page_title', 'bp_learndash_courses_page_title_override');

/* Turn off RSS feeds 
function bp_remove_feeds() {
        remove_action( 'wp', 'bp_activity_action_sitewide_feed', 3 );
        remove_action( 'wp', 'bp_activity_action_personal_feed', 3 );
        remove_action( 'wp', 'bp_activity_action_friends_feed', 3 );
        remove_action( 'wp', 'bp_activity_action_my_groups_feed', 3 );
        remove_action( 'wp', 'bp_activity_action_mentions_feed', 3 );
        remove_action( 'wp', 'bp_activity_action_favorites_feed', 3 );
        remove_action( 'wp', 'groups_action_group_feed', 3 );
}
add_action('init', 'bp_remove_feeds');
*/

/* Turn off RSS feeds */
add_filter( 'bp_activity_enable_feeds', '__return_false' );

/**
 * Filter the except length to 20 words.
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 */
function wpdocs_custom_excerpt_length( $length ) {
    return 15;
}
//add_filter( 'excerpt_length', 'wpdocs_custom_excerpt_length', 999 );

function acf_load_categories_product_lib_page( $field ) {
    
    // reset choices
    $field['choices'] = array();
    $field['choices'][0] = 'All';

    $all_categories = get_categories( array(
    'hide_empty' => 0
    ));

    foreach( $all_categories as $cats ) {
        $field['choices'][ $cats->term_id ] = $cats->name;
    }

    // return the field
    return $field;    
}

add_filter('acf/load_field/name=category', 'acf_load_categories_product_lib_page');

add_image_size( 'product_library_image_size', 624, 468, true );

?>
