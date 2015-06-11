<?php
/* Template Name: Portfolio Paginated */

get_header();
?>

<?php
$style_width = '';
if ( get_option( 'hq_csp_content_width' ) ) {
    $style_width = 'style="width:' . get_option( 'hq_csp_content_width' ) . 'px;"';
}
?>
<div <?php echo $style_width; ?>>
    <div class="otw-twentyfour otw-columns">

        <?php
        $taxo = get_object_taxonomies( 'cool-simple-portfolio' );
        foreach ( $taxo as $tax_name ) {
            $categories = get_categories( 'taxonomy=' . $tax_name );
            $i = 0;
            $len = count( $categories );
            foreach ( $categories as $category ) {
                if ( $i == 0 ) {
                    ?><ul class="cool-simple-portfolio-filter"><?php
                }
                if ( $i > 0 ) {
                    $sep = '<span class="separator">/</span>';
                }
                echo '<li class="' . $category->category_nicename . '"><a href="' . get_term_link( $category->slug, 'cool-simple-portfolio-category' ) . '">' . $sep . $category->cat_name . '</a></li>';
                if ( $i == $len - 1 ) {
                    echo '</ul>';
                }
                $i++;
            }
        }
        ?>

            <ul class="cool-simple-portfolio block-grid three-up mobile">
                                <?php if ( is_page() ) {
                                    $paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : 1;
                                    query_posts( 'post_type=cool-simple-portfolio&paged=' . $paged );
                                } ?>
<?php if ( have_posts() ): while ( have_posts() ) : the_post(); ?>
                        <li data-type="<?php foreach ( get_the_terms( $post->ID, 'cool-simple-portfolio-category' ) as $term )
            echo $term->slug . ' ' ?>" data-id="id-<?php echo($post->post_name) ?>">
                            <article id="post-<?php the_ID(); ?>" <?php post_class( 'cool-simple-portfolio-item' ); ?>>
                                <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="cool-simple-portfolio-item-link">
                                    <div class="image">
        <?php if ( has_post_thumbnail() ) { ?>
            <?php the_post_thumbnail( 'cool-simple-portfolio-medium' ); ?>
        <?php } else { ?>
                                            <div style="background:url(<?php echo plugins_url( '/cool-simple-portfolio-light/images/pattern-1.png' ) ?>);width:<?php echo get_option( 'hq_csp_thumb_size_w', '303' ); ?>px;height:<?php echo get_option( 'hq_csp_thumb_size_h', '210' ); ?>px" title="<?php _e( 'No Image', 'hq_csp' ); ?>"></div>
                        <?php } ?>
                                    </div>
                                    <div class="title">
                                        <h3><?php the_title(); ?></h3>
                                    </div>
                                    <div class="text entry-content">
        <?php the_excerpt(); ?>
                                    </div>
                                    <span class="shadow-overlay hide-for-small"></span></a>
                            </article>
                        </li>

                        <?php endwhile; ?>
                </ul>

<?php else: ?>

                <article id="post-0" class="post no-results not-found">
                    <header class="entry-header">
                        <h1 class="entry-title"><?php _e( 'Nothing Found', 'hq_csp' ); ?></h1>
                    </header>

                    <div class="entry-content">
                        <p><?php _e( 'Apologies, but no results were found. Perhaps searching will help find a related post.', 'hq_csp' ); ?></p>
    <?php get_search_form(); ?>
                    </div><!-- .entry-content -->
                </article><!-- #post-0 -->

<?php endif; ?>

    </div>
</div>

<?php get_footer(); ?>