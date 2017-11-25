<?php
/**
 * Template Name: Authors Library Page
 */
get_header();
?>
<?php if ( is_active_sidebar('sidebar') ) : ?>
	<div class="page-right-sidebar">
<?php else : ?>
	<div class="page-full-width">
<?php endif; ?>
<div id="primary" class="site-content">
	<div id="content" role="main">
			<article id="post-<?php the_id()?>" class="post-<?php the_id()?> page type-page status-publish hentry user-has-not-earned">
				<header class="entry-header ">
					<h1 class="entry-title ">All <?php echo get_the_title()?></h1>
				</header>

				<div class="entry-content">
					<div id="course-list-wrap">

				<?php
					$category_id = ( get_field( 'category' ) != 0 ) ? get_field( 'category' ) : '-'.get_field( 'category' );

					global $post;
					$args = array (
					    'cat' => array( $category_id ),
					    'posts_per_page' => -1, //showposts is deprecated
					    'fields' => 'ids'
					);					

					// The Query
					$query = new WP_Query( $args );


					if ( $query->have_posts() ):
						while ( $query->have_posts() ):
							$query->the_post();
								/* Include the post format-specific template for the content. If you want to
								 * this in a child theme then include a file called called content-___.php
								 * (where ___ is the post format) and that will be used instead.
								 */
								get_template_part( 'content', 'grid' );

							endwhile;

							buddyboss_pagination();
							?>

						<?php else : ?>
							<?php get_template_part( 'content', 'none' ); ?>
						<?php endif; ?>
					<?php wp_reset_query();?>
					</div><!-- .course-list-wrap -->
			</div><!-- .entry-content -->
		 </article>
	</div>
</div> <!-- #primary -->
</div>
<?php get_footer(); ?>