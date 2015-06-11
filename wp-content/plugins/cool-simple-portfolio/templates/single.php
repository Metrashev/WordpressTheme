<?php
/**
 * The template for displaying single item
 *
 */
?>

<?php get_header(); ?>

<?php /* The loop */ ?>
<?php while ( have_posts() ) : the_post(); ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <?php the_post_thumbnail( 'csportfolio-medium' ); ?>
        <?php if ( get_post_meta( $post->ID, 'otw_head_title_setting_pfl', true ) != 1 ) { ?>
		<div class="wrapper-content">
		   <div class="entry-header portfolio-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
            </div>
        <?php } ?>

			<div class="entry-content portfolio-content">
				<?php the_content(); ?>
				<!-- Portfolio Meta Content -->
				<?php if ( get_post_meta( $post->ID, 'custom_cool-simple-portfolio-url', true ) ) { ?>
					<div class="visit-site"><a href="http://<?php echo get_post_meta( $post->ID, 'custom_cool-simple-portfolio-url', true ); ?>"><?php _e( 'Visit site', 'hq_csp' ); ?></a></div>
				<?php } ?>

				<?php if ( get_post_meta( $post->ID, 'custom_cool-simple-portfolio-quote', true ) ) { ?>
					<blockquote class="otw-sc-quote bordered">
						<p><?php echo get_post_meta( $post->ID, 'custom_cool-simple-portfolio-quote', true ); ?></p>
					</blockquote>
				<?php } ?>
				<!-- END Portfolio Meta Content -->
			</div>
		</div>
		
		<div class="categories"><?php the_taxonomies(); ?> <?php edit_post_link( __( 'Edit Post', 'hq_csp' ), '<span class="edit-link">', '</span><br /><br />' ); ?></div>

        <nav class="nav-single">
            <h3 class="assistive-text"><?php _e( 'Post navigation', 'hq_csp' ); ?></h3>
            <span class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'hq_csp' ) . '</span> %title' ); ?></span>
            <span class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'hq_csp' ) . '</span>' ); ?></span>
        </nav><!-- .nav-single -->

        <?php // comments_template( '', true );  ?>
    </article><!-- #post -->

    <div class="relateditems">
        <h3>Related posts</h3>
        <?php
        $orig_post = $post;
        global $post;
        $tags = wp_get_post_tags( $post->ID );

        if ( !$tags ) {
            $tag_ids = array();
            foreach ( $tags as $individual_tag )
                $tag_ids[] = $individual_tag->term_id;
            $args = array(
                'tag__in' => $tag_ids,
                'post__not_in' => array( $post->ID ),
                'posts_per_page' => 4, // Number of related posts to display.
                'post_type' => 'csportfolio'
                    //'caller_get_posts' => 1
            );

            $my_query = new wp_query( $args );

            while ( $my_query->have_posts() ) {
                $my_query->the_post();
                ?>
                <div class="">
					<div class="wrapper-image-grid-portfolio effect-lily">
					<?php the_post_thumbnail( 'csportfolio-medium' ); ?>
						<div class="overlay-hover">
							<h2><?php the_title(); ?></h2>
							<a rel="external" href="<?php the_permalink() ?>"></a>
						</div>			
					</div>
                </div>
                <?php
            }
        }
        $post = $orig_post;
        wp_reset_query();
        ?>
    </div>

    <?php comments_template(); ?>
<?php endwhile; ?>

<?php get_footer(); ?>