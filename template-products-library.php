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
								<a href="<?php echo get_field( "sales_url", $p_id ); ?>">
									<div class="view-course-outline" >Price: <?php echo get_field( "post_price", $p_id );  ?></div>
								</a>	

								<a href="<?php echo $view_more_link ?>">
									<div class="view-course-outline <?php echo $user_credits_class; ?>" >Credit: <?php echo $required_credits;  ?></div>
								</a>		
							</div>										
						</div>					
					</div>
				</div>	
				<?php
					endforeach;
				?>	
			</div>
			<?php endif; ?>
		</article>		
	</div> <!-- main Content -->
</div> <!-- Primary Content -->

<style>
/**/
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

.price-credit-btns a {
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
