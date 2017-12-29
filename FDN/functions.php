
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
  wp_enqueue_style( 'boss-child-custom', get_stylesheet_directory_uri().'/css/custom.css', array(), time() );
}
add_action( 'wp_enqueue_scripts', 'boss_child_theme_scripts_styles', 9999 );

/**
 * Custom JS script to fix the player width across mobile resolutions.
 */
function video_player_mobile_width_fix() {
  ?>
    <script>
      var s3_video_player       = jQuery('div.s3mm_player_video');
      var s3_video_player_layer = jQuery('div.mejs-layers .mejs-overlay');
      var max_interval = 20;
      var interval_counter = 0;

      // Run script only if page contains a video player.
      if( s3_video_player.length && s3_video_player_layer.length ) {

        var player_width = jQuery('.entry-content').width();

        // Event triggered on window resize.
        jQuery( window ).resize(function() {
          var screen_width = jQuery(window).width();
          if ( screen_width >= 320 && screen_width <= 1366 ) {
            // set the player widht same as main content div width.
            player_width = jQuery('.entry-content').width();
          }
          set_player_width( player_width );
          
        });        

        // Since the player's width changes again when clicked paly.
        jQuery('div.s3mm_player_video, div.mejs-playpause-button').on('click', function(){
            var player_interval = setInterval( function() {
                interval_counter++;
                // Check if player widht is bigger then the content width
                if( s3_video_player.width() !=  player_width ) {
                  set_player_width( player_width );
                  clearInterval(player_interval);
                } 

                if( interval_counter >= max_interval) {
                  clearInterval(player_interval);
                }                   
                console.log( 'interval_counter = ' + interval_counter );
             }, 1000);
        });

        // Added a delay to override width inserted by the player JS script.
        setTimeout(function(){   
            set_player_width( player_width );
          }, 1200);
      }
      
      // Utility function to set player width
      function set_player_width( player_width ) {      
        jQuery('div.s3mm_player_video').css('width', player_width);
        jQuery('div.mejs-layers .mejs-overlay').css('width', player_width); 
        jQuery('div.s3mm_player_video video').css('width', player_width); 
      }

    </script>
  <?php
}
add_action( 'wp_footer', 'video_player_mobile_width_fix' );


/****************************** CUSTOM FUNCTIONS ******************************/

// Add your own custom functions here


function download_s3file_shortcode( $atts ) {
	$s3url 	= str_replace("+"," ",$atts['s3url']);
	$s3icon = $atts['s3icon'];
  $s3bucket = $atts['s3bucket'];
  $s3region = $atts['s3region'];
	$browser = '';
	$s3title = isset( $atts['s3title'] ) ? $atts['s3title'] : 'Download Now';
  $user_id = get_current_user_id(); 
  // if( $user_id == 4 ){
  //     echo '[s3mm type="file" files="'.$s3url.'" '.$s3bucket_code.' '.$s3region_code.' /]';
  //     exit;
  // }
 
	if ( isset($_SERVER['HTTP_USER_AGENT']) ) {
    	$agent = $_SERVER['HTTP_USER_AGENT'];    	
	}
	if ( strlen( strstr( $agent, 'Firefox')) > 0) {
    		$browser = 'firefox';
	}

  if( !empty($s3region) )
    $s3region_code = 's3region ="'.$s3region.'"';
  else
    $s3region_code = '';

  if( !empty($s3bucket) )
     $s3bucket_code = 's3bucket ="'.$s3bucket.'"';
  else
    $s3bucket_code = '';

  // echo '[s3mm type="file" files="'.$s3url.'" s3bucket="'.$s3bucket.'" '.$s3region_code.' /]';
  // exit;

	$file_url = do_shortcode('[s3mm type="file" files="'.$s3url.'" '.$s3bucket_code.' '.$s3region_code.' /]');
  
	if( empty( $browser ) )
		$download_att = 'download="'.$s3url.'"';
	else
		$download_att = '';
	
  // To add attribute and title in the download file link.
  $dom = new DOMDocument();
  $dom->loadHTML($file_url);

  $download_link = $dom->getElementsByTagName('a')->item(0);
  $download_link->setAttribute( 'download', $s3url );
  $download_link->setAttribute( 'class', 's3file-link' );

  $title = '<span>'.$s3title.'</span>';
  

  $download_title = $dom->createElement("span", $s3title);
  $download_link->appendChild($download_title);

  // <a target="_blank" class="s3file-link" href="'.$file_url.'" '.$download_att.'>
	return $content = '<div class="s3file-class">
						<i class="fa '.$s3icon.'" ></i>
            '.$dom->saveHTML($download_link).'
					</div>';
}
add_shortcode( 'download_s3file', 'download_s3file_shortcode' );

function s3mm_custom_html( $html, $atts, $content ) {
  $link = explode( "?", $html );

  $dom = new DOMDocument();
  $dom->loadHTML( $html );

  $download_link = $dom->getElementsByTagName('a')->item(0);
  $href_link = $download_link->getAttribute('href');

  if($href_link != '')
     $href_link_updated = str_replace("%2B", "%20", $href_link );
  
  $download_link->setAttribute( 'href', $href_link_updated );

  return $dom->saveHTML($download_link);
  // echo '<pre>';
  // print_r($link);
  // print_r($content);
  // exit;
  // $html_updated = str_replace("%2B", "%20", $link[0]); 
  // $html = $html_updated . '?'.$link[1];
  // return $html; 

}
//add_filter( 's3mm_shortcode_html', 's3mm_custom_html', 10, 3 );
?>
