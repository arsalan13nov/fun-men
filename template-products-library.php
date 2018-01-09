<?php
/**
 * Template Name: Library Page
 */
get_header();
$template_type = get_field( "page_template_style" );

	$category_id = ( get_field( 'category' ) != 0 ) ? get_field( 'category' ) : '-'.get_field( 'category' );

	global $post;
	$args = array (
	    'cat' => array( $category_id ),
	    'posts_per_page' => -1, //showposts is deprecated
	    'fields' => 'ids'
	);
	$all_posts = get_posts( $args );
?>
<?php if ( is_active_sidebar('webinar-sidebar') ) : ?>
	<div class="page-right-sidebar">
<?php else : ?>
	<div class="page-full-width">
<?php endif; ?>
<div id="primary" class="site-content">	
	<div id="content" role="main">
		<article>

		<?php if( !isset( $template_type ) || $template_type == 1 ) : ?>

		<div class="pl-grid-wrapper">
	<?php 
		/**** Memeberium  Accessible Posts *****/
		foreach( $all_posts as $p_id => $post_id ) :	

			if ( memb_hasPostAccess( $post_id ) ) :
				$class = '';
			else:
				$class="no-access grayscale";
			endif;	

			$product_posts['link'] = get_the_permalink( $post_id ); 
			$custom_size_attachment_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'product_library_image_size', true ); 
			$product_posts['img'] = $custom_size_attachment_image[0];
			$product_posts['title'] = get_the_title( $post_id ); 
			$post_excerpt = apply_filters( 'the_excerpt', get_post_field('post_excerpt', $post_id) );
			$product_posts['excerpt'] = ( !empty( $post_excerpt ) ) 
			? $post_excerpt
			: '';	
	?>
				<div class="grid-course pl-col-14 pl-3-col">
					<div class="pl-border">
						<div class="pl-box">
							<?php if( $product_posts['img'] != '' ) : ?>
							<div class="featured-image">
								<a href="<?php echo $product_posts['link']; ?>">						
									<img src="<?php echo $product_posts['img']; ?>" class="uo-grid-featured-image <?php echo $class; ?>">						
								</a>
							</div>
							<?php endif; ?>
							<div class="course-info-holder">
								<div class="course-title"><?php echo $product_posts['title']; ?></div>
										<div class="content_desc"><?php echo wpautop( $product_posts['excerpt']); ?></div>
							</div>
							<a href="<?php echo $product_posts['link']; ?>"><div class="view-course-outline">View More</div></a>					
							<!-- <div class="price-credit-btns">
								<a href="<?php echo get_field( "sales_url", $p_id ); ?>">
									<div class="view-course-outline" >Price: <?php echo get_field( "post_price", $p_id );  ?></div>
								</a>	
								<a href="<?php echo home_url('/credit-sale/'); ?>">
									<div class="view-course-outline" >Credit: <?php echo get_field( "post_credit", $p_id );  ?></div>
								</a>		
							</div>		 -->				
						</div>					
					</div>
				</div>	
			<?php
				endforeach;
				wp_reset_postdata();
			?>	
			</div>
	<?php elseif( $template_type == 2 ): ?>
	
		<div class="pl-grid-wrapper">
	<?php 
		/**** Memeberium  Accessible Posts *****/
		foreach( $all_posts as $p_id => $post_id ) :	

			if ( memb_hasPostAccess( $post_id ) ) :

			$product_posts['link'] = get_the_permalink( $post_id ); 
			$custom_size_attachment_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'product_library_image_size', true ); 
			$product_posts['img'] = $custom_size_attachment_image[0];
			$product_posts['title'] = get_the_title( $post_id ); 
			$post_excerpt = apply_filters( 'the_excerpt', get_post_field('post_excerpt', $post_id) );
			$product_posts['excerpt'] = ( !empty( $post_excerpt ) ) 
			? $post_excerpt
			: '';	
	?>
				<div class="grid-course pl-col-14 pl-3-col">
					<div class="pl-border">
						<div class="pl-box">
							<?php if( $product_posts['img'] != '' ) : ?>
							<div class="featured-image">
								<a href="<?php echo $product_posts['link']; ?>">						
									<img src="<?php echo $product_posts['img']; ?>" class="uo-grid-featured-image">						
								</a>
							</div>
							<?php endif; ?>
							<div class="course-info-holder">
								<div class="course-title"><?php echo $product_posts['title']; ?></div>
										<div class="content_desc"><?php echo wpautop( $product_posts['excerpt']); ?></div>
							</div>
							<a href="<?php echo $product_posts['link']; ?>"><div class="view-course-outline">View More</div></a>					
							<!-- <div class="price-credit-btns">
								<a href="<?php echo get_field( "sales_url", $p_id ); ?>">
									<div class="view-course-outline" >Price: <?php echo get_field( "post_price", $p_id );  ?></div>
								</a>	
								<a href="<?php echo home_url('/credit-sale/'); ?>">
									<div class="view-course-outline" >Credit: <?php echo get_field( "post_credit", $p_id );  ?></div>
								</a>		
							</div>		 -->				
						</div>					
					</div>
				</div>	
			<?php
				endif;
				endforeach;
				wp_reset_postdata();
			?>	
			</div>

	<?php elseif( $template_type == 3 ): ?>

		
			<p style="line-height: 50px; margin-bottom: 0px !important;" data-unit="px"><span class="tve_custom_font_size  rft" style="font-size: 30px;">
				<font color="#ff6700">
					<?php echo get_field( "title_top" ); ?>
				</font>	
				<span class="bold_text"></span>
			</p>
			<div class="thrv_wrapper" style="margin-top: -30px !important; margin-bottom: 0px !important;">
				<hr class="tve_sep tve_sep1">
			</div>
	<?php

	$accessible_posts = array();
	$non_accessible_posts = array();

		foreach( $all_posts as $post_id ) :				
			
			if ( memb_hasPostAccess( $post_id ) ) {
			
				$accessible_posts[$post_id]['link'] = get_the_permalink( $post_id ); 
				$custom_size_attachment_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'product_library_image_size', true ); 
				$accessible_posts[$post_id]['img'] = $custom_size_attachment_image[0];
				$accessible_posts[$post_id]['title'] = get_the_title( $post_id ); 
				$post_excerpt = apply_filters( 'the_excerpt', get_post_field('post_excerpt', $post_id) );
				$accessible_posts[$post_id]['excerpt'] = ( !empty( $post_excerpt ) ) 
				? $post_excerpt
				: '';
			
			}  elseif ( !memb_hasPostAccess( $post_id ) ) {
				$non_accessible_posts[$post_id]['link'] = get_the_permalink( $post_id ); 
				$custom_size_attachment_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'product_library_image_size', true ); 
				$non_accessible_posts[$post_id]['img'] = $custom_size_attachment_image[0];
				$non_accessible_posts[$post_id]['title'] = get_the_title( $post_id ); 
				$post_excerpt = apply_filters( 'the_excerpt', get_post_field('post_excerpt', $post_id) );
				$non_accessible_posts[$post_id]['excerpt'] = ( !empty( $post_excerpt ) ) 
				? $post_excerpt 
				: '';
			}
			// echo $post_id;
			// var_dump(memb_hasPostAccess( $post_id ));
		endforeach; 
		wp_reset_postdata();
	?>			
	<div class="pl-grid-wrapper">
	<?php 
		/**** Memeberium  Accessible Posts *****/
		foreach( $accessible_posts as $p_id => $accessible_post ) :		
	?>
				<div class="grid-course pl-col-14 pl-3-col">
					<div class="pl-border">
						<div class="pl-box">
							<?php if( $accessible_post['img'] != '' ) : ?>
							<div class="featured-image">
								<a href="<?php echo $accessible_post['link']; ?>">						
									<img src="<?php echo $accessible_post['img']; ?>" class="uo-grid-featured-image">						
								</a>
							</div>
							<?php endif; ?>
							<div class="course-info-holder">
								<div class="course-title"><?php echo $accessible_post['title']; ?></div>
										<div class="content_desc"><?php echo wpautop( $accessible_post['excerpt']); ?></div>
							</div>
							<a href="<?php echo $accessible_post['link']; ?>"><div class="view-course-outline">View More</div></a>					
							<!-- <div class="price-credit-btns">
								<a href="<?php echo get_field( "sales_url", $p_id ); ?>">
									<div class="view-course-outline" >Price: <?php echo get_field( "post_price", $p_id );  ?></div>
								</a>	
								<a href="<?php echo home_url('/credit-sale/'); ?>">
									<div class="view-course-outline" >Credit: <?php echo get_field( "post_credit", $p_id );  ?></div>
								</a>		
							</div>		 -->				
						</div>					
					</div>
				</div>	
			<?php
				endforeach;
			?>	
			</div>

			<div class="bottom-title">	
				<p style="line-height: 50px; margin-bottom: 0px !important;" data-unit="px"><span class="tve_custom_font_size  rft" style="font-size: 30px;">
					<font color="#ff6700">
						<?php echo get_field( "title_bottom" ); ?>
					</font>	
				<span class="bold_text"></span>
				</p>
				<div class="thrv_wrapper" style="margin-top: -30px !important; margin-bottom: 0px !important;">
					<hr class="tve_sep tve_sep1">
					<?php if( empty( $non_accessible_posts ) ) : ?>
						<span><?php echo get_field( "title_bottom_description" ); ?></span>
					<?php endif; ?>
				</div>
			</div>

			<div class="pl-grid-wrapper">		
			<?php 
				/**** Memeberium Non accessible Posts *****/
				foreach( $non_accessible_posts as $p_id => $non_accessible_post ) :		
			?>
				<div class="grid-course pl-col-14 pl-3-col">
					<div class="pl-border">
						<div class="pl-box">
							<?php 
								// $view_more_link = $non_accessible_post['link'];
								$required_credits = get_field( "post_credit", $p_id ); 

								$user_credits = do_shortcode('[memb_contact fields=CLP_CREDITS]');
								$user_credits = !empty( $user_credits ) ? $user_credits : 0 ;
								$user_credits_class = ( $user_credits >= $required_credits ) ? 'show-credit-btn' : 'hide-credit-btn' ;

								$access_tag_id = get_field( "memberium_tags", $p_id ); 

								if( !empty( $required_credits) && $user_credits >= $required_credits ) :
									$view_more_link = home_url( '/library-content-sales-page/?tag_id='.$access_tag_id.'&re_cr='.$required_credits ); 
								else :
									$view_more_link = 'javascript:void(null)'; 
							?>
							<!-- <div id="ribbon"><?php echo $credit_price; ?></div> -->
							<?php endif; ?>
							<?php if( $non_accessible_post['img'] != '' ) : ?>
							<div class="featured-image">
								<a href="<?php echo $non_accessible_post['link']; ?>">
									<img src="<?php echo $non_accessible_post['img']; ?>" class="uo-grid-featured-image">
								</a>
							</div>
							<?php endif; ?>
							<div class="course-info-holder">
								<div class="course-title"><?php echo $non_accessible_post['title']; ?></div>
										<div class="content_desc"><?php echo wpautop( $non_accessible_post['excerpt']); ?></div>							
							</div>
							<div class="price-credit-btns">
								<p><?php  _e( 'Buy Now or Use Credits to gain access to this product.', 'textdomain' ); ?></p>
								<p class="available-credits"><?php  _e( 'Available Credits = ' . $user_credits, 'textdomain' ); ?></p>
								<a href="<?php echo get_field( "sales_url", $p_id ); ?>" class="price-credit-btns-links"> 
									<div class="view-course-outline" >Buy Now: <?php echo get_field( "post_price", $p_id );  ?></div>
								</a>	

								<a href="javascript:void(0);" class="price-credit-btns-links" >
									<div class="view-course-outline <?php echo $user_credits_class; ?>" onClick="confirm_clp_credit_use('<?php echo $p_id; ?>'); ">Use Credit: <?php echo $required_credits;  ?></div>
								</a>										
							</div>										
						</div>					
					</div>
				</div>
				<div id="<?php echo $p_id; ?>" class="cd-popup" role="alert">
									<div class="cd-popup-container">
										<p class="confirm-message">Do you want to use <?php echo $required_credits; ?> of your <?php echo $user_credits; ?> available credits to gain access to this product?</p>
											<ul class="cd-buttons">
												<li><a href="<?php echo $view_more_link ?>">Yes</a></li>
												<li><a href="javascript:void(0);">No</a></li>
											</ul>
									</div> <!-- cd-popup-container -->
								</div> <!-- cd-popup -->	
				<?php
					endforeach;
				?>	
			</div>
			<?php endif; ?>
		</article>		
	</div> <!-- main Content -->
</div> <!-- Primary Content -->
<script>

	function confirm_clp_credit_use( p_id ) {
		jQuery( '#' + p_id ).addClass('is-visible');
	}

	jQuery(document).ready(function($){
	//open popup
	// $('.show-credit-btn').on('click', function(event){
	// 	event.preventDefault();
	// 	$('.cd-popup').addClass('is-visible');
	// });
	
	//close popup
	// $('.cd-popup').on('click', function(event){
	// 	if( $(event.target).is('.cd-popup-close') || $(event.target).is('.cd-popup') ) {
	// 		event.preventDefault();
	// 		$(this).removeClass('is-visible');
	// 	}
	// });

	// No Button
	$('.cd-popup-container .cd-buttons li:last-child a').on('click', function(event){
		$('.cd-popup').removeClass('is-visible');
	});

	//close popup when clicking the esc keyboard button
	$(document).keyup(function(event){
    	if(event.which=='27'){
    		$('.cd-popup').removeClass('is-visible');
	    }
    });
});
</script>
<style>

/* -------------------------------- 

Modules - reusable parts of our design

-------------------------------- */
.img-replace {
  /* replace text with an image */
  display: inline-block;
  overflow: hidden;
  text-indent: 100%;
  color: transparent;
  white-space: nowrap;
}

/* -------------------------------- 

xnugget info 

-------------------------------- */
.cd-nugget-info {
  text-align: center;
  position: absolute;
  width: 100%;
  height: 50px;
  line-height: 50px;
  bottom: 0;
  left: 0;
}
.cd-nugget-info a {
  position: relative;
  font-size: 14px;
  color: #5e6e8d;
  -webkit-transition: all 0.2s;
  -moz-transition: all 0.2s;
  transition: all 0.2s;
}
.no-touch .cd-nugget-info a:hover {
  opacity: .8;
}
.cd-nugget-info span {
  vertical-align: middle;
  display: inline-block;
}
.cd-nugget-info span svg {
  display: block;
}
.cd-nugget-info .cd-nugget-info-arrow {
  fill: #5e6e8d;
}

.cd-popup-trigger {
  display: block;
  width: 170px;
  height: 50px;
  line-height: 50px;
  margin: 3em auto;
  text-align: center;
  color: #FFF;
  font-size: 14px;
  font-size: 0.875rem;
  font-weight: bold;
  text-transform: uppercase;
  border-radius: 50em;
  background: #35a785;
  box-shadow: 0 3px 0 rgba(0, 0, 0, 0.07);
}
@media only screen and (min-width: 1170px) {
  .cd-popup-trigger {
    margin: 6em auto;
  }
}

/* -------------------------------- 

xpopup 

-------------------------------- */
.cd-popup {
  position: fixed;
  left: 0;
  top: 0;
  height: 100%;
  width: 100%;
  background-color: rgba(94, 110, 141, 0.9);
  opacity: 0;
  visibility: hidden;
  -webkit-transition: opacity 0.3s 0s, visibility 0s 0.3s;
  -moz-transition: opacity 0.3s 0s, visibility 0s 0.3s;
  transition: opacity 0.3s 0s, visibility 0s 0.3s;
}
.cd-popup.is-visible {
  opacity: 1;
  visibility: visible;
   z-index: 10;
  -webkit-transition: opacity 0.3s 0s, visibility 0s 0s;
  -moz-transition: opacity 0.3s 0s, visibility 0s 0s;
  transition: opacity 0.3s 0s, visibility 0s 0s;
}

.cd-popup-container {
  position: relative;
  width: 90%;
  max-width: 400px;
  margin: 4em auto;
  background: #FFF;
  border-radius: .25em .25em .4em .4em;
  text-align: center;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
  -webkit-transform: translateY(-40px);
  -moz-transform: translateY(-40px);
  -ms-transform: translateY(-40px);
  -o-transform: translateY(-40px);
  transform: translateY(-40px);
  /* Force Hardware Acceleration in WebKit */
  -webkit-backface-visibility: hidden;
  -webkit-transition-property: -webkit-transform;
  -moz-transition-property: -moz-transform;
  transition-property: transform;
  -webkit-transition-duration: 0.3s;
  -moz-transition-duration: 0.3s;
  transition-duration: 0.3s;
}
.cd-popup-container p.confirm-message {
	font-size: 18px;
    color: black;
    margin-left: 10px;
  	padding: 3em 1em;
}
.cd-popup-container .cd-buttons:after {
  content: "";
  display: table;
  clear: both;
}
.cd-popup-container .cd-buttons li {
  float: left;
  width: 50%;
  list-style: none;
}
.cd-popup-container .cd-buttons a {
  display: block;
  height: 60px;
  width: 100%;
  line-height: 60px;
  text-transform: uppercase;
  color: #FFF;
  -webkit-transition: background-color 0.2s;
  -moz-transition: background-color 0.2s;
  transition: background-color 0.2s;
}
.cd-popup-container .cd-buttons li:first-child a {
  background: #0e8dc7;
  border-radius: 0 0 0 .25em;
}
.no-touch .cd-popup-container .cd-buttons li:first-child a:hover {
  background-color: #fc8982;
}
.cd-popup-container .cd-buttons li:last-child a {
  background: #ff6700;
  border-radius: 0 0 .25em 0;
}
.no-touch .cd-popup-container .cd-buttons li:last-child a:hover {
  background-color: #c5ccd8;
}
.cd-popup-container .cd-popup-close {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 30px;
  height: 30px;
}
.cd-popup-container .cd-popup-close::before, .cd-popup-container .cd-popup-close::after {
  content: '';
  position: absolute;
  top: 12px;
  width: 14px;
  height: 3px;
  background-color: #8f9cb5;
}
.cd-popup-container .cd-popup-close::before {
  -webkit-transform: rotate(45deg);
  -moz-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  -o-transform: rotate(45deg);
  transform: rotate(45deg);
  left: 8px;
}
.cd-popup-container .cd-popup-close::after {
  -webkit-transform: rotate(-45deg);
  -moz-transform: rotate(-45deg);
  -ms-transform: rotate(-45deg);
  -o-transform: rotate(-45deg);
  transform: rotate(-45deg);
  right: 8px;
}
.is-visible .cd-popup-container {
  -webkit-transform: translateY(0);
  -moz-transform: translateY(0);
  -ms-transform: translateY(0);
  -o-transform: translateY(0);
  transform: translateY(0);
}
@media only screen and (min-width: 1170px) {
  .cd-popup-container {
    margin: 8em auto;
  }
}


/**/
.price-credit-btns p {
	font-size: 12px;
    color: red;
    margin-left: 10px;
}
.available-credits {
	font-weight: bold;
}
.content_desc{
	display: block;

	margin-top: 10px;
}

div.featured-image img.no-access {
	 -webkit-filter: grayscale(100%);
  -moz-filter: grayscale(100%);
  -ms-filter: grayscale(100%);
  -o-filter: grayscale(100%);
  filter: grayscale(100%);
  filter: gray; /* IE 6-9 */
}

div.featured-image img.grayscale { 
	filter: url("data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\'><filter id=\'grayscale\'><feColorMatrix type=\'matrix\' values=\'0.3333 0.3333 0.3333 0 0 0.3333 0.3333 0.3333 0 0 0.3333 0.3333 0.3333 0 0 0 0 0 1 0\'/></filter></svg>#grayscale");
	filter: gray;
	-webkit-filter: grayscale(100%);
}

div.site-content article {
	    padding: 20px 25px !important;
}
div.hide-credit-btn {
	background: #c5c5c5 !important;
    border: 1px solid #b90d25 !important;
}
div.hide-credit-btn:hover {
	cursor: default;
}

.price-credit-btns .price-credit-btns-links {
    width: 49%;
    display: inline-block;
}
.price-credit-btns a:last-child {
     float: right;
}
.price-credit-btns a:first-child {
     float: left;
}
.bottom-title {
	display: block;
	clear: left;
}
#ribbon {
    background-color: #088ab1;
    box-shadow: 0px 2px 4px #088ab1;
    color: #fff;
    font-size: 12px;
    font-weight: bold;
    margin: 5px 0 0 -9px;
    min-width: 40px;
    padding: 2px 10px 2px 10px;
    position: absolute;
    Text-transform: uppercase;
}
#ribbon:before, #ribbon:after {
    content: ' ';
    height: 0;
    position: absolute;
    width: 0;
}
#ribbon:after {
    border-color: #07627d #07627d transparent transparent;
    border-style: solid;
    border-width: 4.5px 4px;
    left: 0px;
    top: 100%;
}
.pl-grid-wrapper {
	clear: both;
	display: flex;
	flex-wrap: wrap;
	/*float: left;*/
	margin-bottom: 30px !important;
	margin-left: -10px;
	margin-right: -10px;
	/*margin-top: 30px;*/
}

.pl-col-14 {
	margin-bottom: 20px;
	min-height: 1px;
	padding-left: 7px;
	padding-right: 7px;
	position: relative;
}

/* 4 columns layout, 4 courses + 1 view More */
.pl-col-14 {
	float: left;
	width: 20%;
}

@media (max-width: 500px) {
	
	.pl-col-14 {
		margin-left: 5px;
		padding-right: 0;
		width: 100%;
	}

	.pl-3-col {
		width: 100%;
	}
}

@media (max-width: 1330px) and (min-width: 501px) {
	
	.pl-col-14:nth-child(2n+0) {
		clear: right;
	}
	
	.pl-col-14:nth-child(2n+1),
	.pl-col-14:first-child {
		clear: left;
	}
}

	.pl-col-14 {
		width: 20%;
	}

	.pl-3-col {
		width: 31%;
	}

	.pl-3-col:nth-child(3n+0) {
		clear: right;
	}

	.pl-3-col:nth-child(3n+1) {
		clear: left;
	}

.pl-grid-wrapper .grid-course .featured-image {
	overflow: hidden;
}

.pl-grid-wrapper .grid-course .course-title {
	font-size: 14px;
}

.pl-grid-wrapper .grid-course h4 {
	color: #088ab1;
	font-size: 14px;
	text-align: center;
	text-transform: uppercase;

}

.pl-grid-wrapper .grid-course .course-info-holder {
	background: #f5f5f5;
	display: block;
	line-height: 1.2;
	/*min-height: 60px;*/
	padding: 10px;
	min-height: 51px;
	padding-bottom: 40px;
	transition: all 0.75s ease;
	-moz-transition: all 0.75s ease;
	-ms-transition: all 0.75s ease;
	-o-transition: all 0.75s ease;
	-webkit-transition: all 0.75s ease;
}

.pl-grid-wrapper .grid-course h4.view-course-outline {
	background: #088ab1;
	border: 1px solid #088ab1;
	/*border-radius: 4px;*/
	color: #fff;
	/*display: table;*/
	font-size: 11px;
	margin: 0 auto;
	/*white-space: nowrap;*/
	padding: 5px;
	text-transform: uppercase;
	width: 100%;
	box-sizing: border-box;
}

.pl-grid-wrapper .grid-course h4.view-course-outline:hover {
	background: #fff;
	color: #088ab1;
	text-decoration: none;
}

.pl-grid-wrapper .uo-view-more-holder.hidden {
	display: none;
}

.pl-grid-wrapper .grid-course .course-info-holder .list-tag-container {
	font-size: 12px;
	text-transform: uppercase;
}

.pl-grid-wrapper .grid-course .course-info-holder.completed .list-tag-container {
	color: #36ac2d;
}

.pl-grid-wrapper .grid-course .course-info-holder.completed .list-tag-container i {
	margin-left: 7px;
}

.pl-grid-wrapper .grid-course .course-info-holder.completed dd.uo-course-progress div.course_progress {
	background: #36ac2d;
}

/*Hovers*/

.pl-grid-wrapper .grid-course img {
	opacity: 1;
	transition: all 0.75s ease;
	-moz-transition: all 0.75s ease;
	-ms-transition: all 0.75s ease;
	-o-transition: all 0.75s ease;
	-webkit-transition: all 0.75s ease;
	vertical-align: bottom;
	width: 100%;
	max-width: 100%
}

.pl-grid-wrapper .grid-course:hover img {
	opacity: 1;
	text-decoration: none;
}

.pl-grid-wrapper .grid-course:hover .course-info-holder {
	background: #e5e5e5;
	cursor: pointer;
	text-decoration: none;
}

.pl-grid-wrapper .grid-course img:hover .course-info-holder.completed {
	background: rgba(54, 172, 45, 0.15);
	cursor: pointer;
	text-decoration: none;
}

.pl-grid-wrapper .grid-course .pl-border {
	border: 3px solid #fff;
	transition: all 0.75s ease;
	-moz-transition: all 0.75s ease;
	-ms-transition: all 0.75s ease;
	-o-transition: all 0.75s ease;
	-webkit-transition: all 0.75s ease;
}

.pl-grid-wrapper .grid-course:hover .pl-border {
	/*border: 3px solid #088ab1;*/
	text-decoration: none;
}

.pl-grid-wrapper .grid-course:hover .pl-border.completed {
	border: 3px solid #36ac2d;
}

.pl-grid-wrapper .grid-course,
.pl-grid-wrapper .grid-course .pl-border {
	display: flex;
}

.pl-grid-wrapper .grid-course .featured-image {
	overflow: hidden;
}

.pl-grid-wrapper .grid-course .course-info-holder:not(.bottom) {
	flex: 1 0 auto;
	position: relative;
}

.pl-grid-wrapper .grid-course .course-info-holder.bottom {
	position: relative;
}

.pl-grid-wrapper .grid-course h4 {
	background-color: #ddd;
	bottom: 0;
	/*color: #088ab1;*/
	font-size: 16px;
	font-weight: 600;
	left: 0;
	margin: 0;
	padding: 10px;
	position: absolute;
	width: 100%;
}

.pl-grid-wrapper .course-info-holder p {
	/*margin-top: 10px;*/
	font-size: 12px;
	padding-right: 15px;
	color: slategray;
}
@media (max-width: 500px) {
	.pl-col-14 {
		margin-left: 5px;
		padding-right: 0;
		width: 100%;
		margin-bottom: 30px;
	}
	.pl-grid-wrapper {
		margin-right: 0px; 
	}
	.pl-3-col {
		width: 100%;
	}
}
@media (max-width: 667px) and (min-width: 501px) {
	.pl-col-14 {
		margin-left: 5px;
		padding-right: 0;
		width: 100%;
		margin-bottom: 30px;
	}
	.pl-3-col {
		width: 100%;
	}
}
@media (max-width: 1024px) and (min-width: 668px) {
	.pl-col-14 {
		margin-left: 5px;
		padding-right: 0;
		width: 100%;
		margin-bottom: 30px;
	}
	.pl-3-col {
		width: 47%;
	}
	.pl-grid-wrapper {
		margin-right: -30px;
	}
	.pl-grid-wrapper .grid-course .course-info-holder{
		min-height: 74px;
	}
}

@media (min-width: 1330px) {
	
	.pl-3-col {
		width: 32%;
	}
}

div.view-course-outline {

	text-align: center;
	background: #088ab1;
    border: 1px solid #088ab1;
    /* border-radius: 4px; */
    color: #fff;
    /* display: table; */
    font-size: 11px;
    margin: 0 auto;
    /* white-space: nowrap; */
    padding: 5px 0px;
    text-transform: uppercase;
    position: relative;

}

div.view-course-outline:hover {
	background: #fff;
    color: #ff6700;
    text-decoration: none;
}

/*}*/
</style>
<script>
	// Grayscale Images fix for IE10-IE11
var GrayScaleFix = (function() {
	var needToFix = /(MSIE 10)|(Trident.*rv:11\.0)|( Edge\/[\d\.]+$)/.test(navigator.userAgent);

	function replaceImage(image) {
		var tmpImage = new Image();
		tmpImage.onload = function() {
			var imgWrapper = document.createElement('span'),
				svgTemplate = 
				'<svg xmlns="http://www.w3.org/2000/svg" id="svgroot" viewBox="0 0 '+tmpImage.width+' '+tmpImage.height+'" width="100%" height="100%">' +
					'<defs>' +
					'<filter id="gray">' +
						'<feColorMatrix type="matrix" values="0.3333 0.3333 0.3333 0 0 0.3333 0.3333 0.3333 0 0 0.3333 0.3333 0.3333 0 0 0 0 0 1 0" />' +
					'</filter>' +
					'</defs>' +
					'<image filter="url(&quot;#gray&quot;)" x="0" y="0" width="'+tmpImage.width+'" height="'+tmpImage.height+'" preserveAspectRatio="none" xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="'+tmpImage.src+'" />' +
				'</svg>';
			
			imgWrapper.innerHTML = svgTemplate;
			imgWrapper.className = 'grayscale-fix';
			image.parentNode.insertBefore(imgWrapper, image);

			image.style.cssText += 'visibility:hidden;display:block';
			imgWrapper.querySelector('svg').style.position = 'absolute';
			imgWrapper.style.cssText = 'display:inline-block;position:relative;';
			imgWrapper.appendChild(image);
		};
		tmpImage.src = image.src;
	}

	function replaceAll() {
		var images = document.querySelectorAll('img.grayscale');
		for(var i = 0; i < images.length; i++) {
			replaceImage(images[i]);
		}
	}

	if(needToFix) {
		document.addEventListener('DOMContentLoaded', replaceAll);
	}

	return {
		replace: replaceImage,
		refresh: replaceAll
	};
}());
	// jQuery(window).resize(function(){
	// 	jQuery('.view-course-outline').width( parseInt( jQuery('div.pl-box').width() - 2 ) );
	// });
	// jQuery(document).ready(function(){
	// 	jQuery('.view-course-outline').width( parseInt( jQuery('.grid-course').width() - 2 ) );
	// });
</script>
<?php get_footer(); ?>
