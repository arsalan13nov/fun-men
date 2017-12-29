
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
	$s3url 	= $atts['s3url'];
	$s3icon = $atts['s3icon'];
  $s3bucket = $atts['s3bucket'];
	$browser = '';
	$s3title = isset( $atts['s3title'] ) ? $atts['s3title'] : 'Download Now';

// switch ($s3icon) {
//     case "audio":
//         $icon = 'fa-volume-up'; 
//         break;
//     case "video":
//         $icon = 'fa-play-circle';
//         break;
//     case "pdf":
//         $icon = 'fa-file-pdf-o';
//         break;
//     case "doc":
//         $icon = 'fa-file-o';
//         break;
//     case "sheet":
//         $icon = 'fa-file-excel-o';
//         break;    
//     default:
//         $icon = 'fa-download';
// }

	// remove_s3file_shortcodes();
	// my_add_shortcodes();

	if ( isset($_SERVER['HTTP_USER_AGENT']) ) {
    	$agent = $_SERVER['HTTP_USER_AGENT'];    	
	}
	if ( strlen( strstr( $agent, 'Firefox')) > 0) {
    		$browser = 'firefox';
	}

	$file_url = do_shortcode('[s3file s3url='.$s3url.' s3bucket="'.$s3bucket.'"]');
  
	if( empty( $browser ) )
		$download_att = 'download="'.$s3url.'"';
	else
		$download_att = '';
	

  $dom = new DOMDocument();
  $dom->loadHTML($file_url);

  $node = $dom->getElementsByTagName('a')->item(0);
  $node->setAttribute( 'download', $s3url );
  $node->setAttribute( 'class', 's3file-link' );

  $title = '<span>'.$s3title.'</span>';
  

  $node2 = $dom->createElement("span", $s3title);
  $node->appendChild($node2);

  // <a target="_blank" class="s3file-link" href="'.$file_url.'" '.$download_att.'>
	return $content = '<div class="s3file-class">
						<i class="fa '.$s3icon.'" ></i>
            '.$dom->saveHTML($node).'
					</div>';
}
add_shortcode( 'download_s3file', 'download_s3file_shortcode' );

function my_add_shortcodes() {
    add_shortcode( 's3file', 'custom_S3MM_shortcode_showFile' );
}
function custom_S3MM_shortcode_showFile( $atts, $content ){
	// Process the attributes
	extract(shortcode_atts(array(
		's3bucket'		=> '',
                's3bucketregion' => '',
		's3accesskey'	=> '',
		's3secretkey'	=> '',
		's3expiry'		=> false,
		's3url'			=>  '',
		'newtab'		=> false,
		'attributes'	=> false,
		'cloudfront'		=> false,
		'distribution'		=> ''
	), $atts));

	// Basic check of a URL
	$s3url = trim($s3url);
	if (!$s3url) {
		return sprintf('<p>%s</p>', __('No URL specified.', TX_S3MM));
	}

	// Ensure we have something to click
	if (!$content) {
		$content = $s3url;
	}

	if ('yes' == $newtab) {
		$newtab = 'target="_blank"';
	}

	if(!$cloudfront)
	{
		// Work out the URL for the media file
		$s3auth = S3MM_S3_getFieldsUsingDefaults($s3accesskey, $s3secretkey, $s3bucket, $s3bucketregion, $s3expiry);

		// Using Amazon S3 with an expiring link
		$url = S3MM_S3_s3_getTemporaryLink($s3auth['accesskey'], $s3auth['secretkey'], $s3auth['bucket'],  $s3auth['bucketregion'], $s3url, $s3auth['expiry']);
	}else
	{
		// Use settings to fill in blank defaults
		$cfauth = S3MM_CloudFront_getFieldsUsingDefaults($cfkeyPairID, $cfprivateKey, $distribution, $s3expiry);

		// Using Amazon S3 with an expiring link
		$url = S3MM_CloudFront_getTemporaryLink($cfauth['keyPairID'], $cfauth['privateKey'], $cfauth['distribution'], $s3url, $cfauth['expiry']);
	}

	return $url;
}
function remove_s3file_shortcodes() {
    remove_shortcode( 's3file' );
}
?>
