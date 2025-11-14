<?php
/**
 * The template for displaying single posts
 */

get_header();
?>

<div class="site-content">
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <a href="<?php echo home_url(); ?>">בית</a>
        <?php if (get_the_category()): ?>
            <?php $category = get_the_category()[0]; ?>
            <span> > </span>
            <a href="<?php echo get_category_link($category->term_id); ?>"><?php echo $category->name; ?></a>
        <?php endif; ?>
        <span> > </span>
        <span><?php the_title(); ?></span>
    </div>
    <div class="content-area">
        <main class="main-content">
            <?php
            while (have_posts()):
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>

                    <header class="entry-header">
                        <?php
                        $categories = get_the_category();
                        if (!empty($categories)):
                            ?>
                            <div class="post-category"><?php echo esc_html($categories[0]->name); ?></div>
                        <?php endif; ?>

                        <h1 class="entry-title"><?php the_title(); ?></h1>

                        <div class="entry-meta">
                            <span class="author">✍️ מאת: <a
                                    href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php the_author(); ?></a></span>
                            <span class="separator"> | </span>
                            <span class="date">🕐 <?php echo get_the_date('j F Y'); ?> בשעה
                                <?php echo get_the_time('H:i'); ?></span>
                            <span class="separator"> | </span>
                            <span class="comments">💬
                                <?php comments_number('אין תגובות', 'תגובה אחת', '% תגובות'); ?></span>
                            <?php if (get_the_tags()): ?>
                                <span class="separator"> | </span>
                                <span class="tags">🏷️ <?php the_tags('', ', ', ''); ?></span>
                            <?php endif; ?>
                        </div>
                    </header>

                    <?php 
                    $hide_featured = get_post_meta(get_the_ID(), '_hide_featured_image', true);
                    if (has_post_thumbnail() && !$hide_featured): 
                    ?>
                        <div class="post-thumbnail-single">
                            <?php the_post_thumbnail('medium'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="entry-content">
                        <?php
                        the_content();

                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . 'עמודים:',
                            'after' => '</div>',
                        ));
                        ?>
                    </div>

                    <footer class="entry-footer">
                        <?php if (get_the_tags()): ?>
                            <div class="post-tags">
                                <strong>תגיות:</strong> <?php the_tags('', ', ', ''); ?>
                            </div>
                        <?php endif; ?>

                        <!-- שיתוף -->
                        <div class="share-buttons">
                            <strong>שתף את הכתבה:</strong>
                            <div class="share-links">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>"
                                    target="_blank">
                                    📘 פייסבוק
                                </a>
                                <a href="https://twitter.com/intent/tweet?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>"
                                    target="_blank">
                                    🐦 טוויטר
                                </a>
                                <a href="https://api.whatsapp.com/send?text=<?php the_title(); ?> <?php the_permalink(); ?>"
                                    target="_blank">
                                    💬 וואטסאפ
                                </a>
                            </div>
                        </div>
                    </footer>

                </article>

                <!-- Author Box -->
                <div class="author-box">
                    <div class="author-content">
                        <div class="author-avatar">
                            <?php echo get_avatar(get_the_author_meta('ID'), 80); ?>
                        </div>
                        <div class="author-info">
                            <h3><?php the_author(); ?></h3>
                            <p><?php echo get_the_author_meta('description'); ?></p>
                            <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"
                                class="btn btn-primary">
                                כל הכתבות של <?php the_author(); ?>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Related Posts -->
                <?php
                $related = new WP_Query(array(
                    'category__in' => wp_get_post_categories($post->ID),
                    'post__not_in' => array($post->ID),
                    'posts_per_page' => 3,
                ));

                if ($related->have_posts()):
                    ?>
                    <div class="related-posts">
                        <h2 class="section-title">כתבות קשורות</h2>
                        <div class="posts-grid">
                            <?php while ($related->have_posts()):
                                $related->the_post(); ?>
                                <div class="post-card">
                                    <div class="post-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php if (has_post_thumbnail()): ?>
                                                <?php the_post_thumbnail('medium'); ?>
                                            <?php else: ?>
                                                <div class="mock-img"></div>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="post-content">
                                        <?php
                                        $cats = get_the_category();
                                        if (!empty($cats)):
                                            ?>
                                            <div class="post-category"><?php echo esc_html($cats[0]->name); ?></div>
                                        <?php endif; ?>
                                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                        <div class="post-meta">
                                            <span>🕐
                                                <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' לפני'; ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <?php
                    wp_reset_postdata();
                endif;
                ?>

                <!-- Comments -->
                <?php
                if (comments_open() || get_comments_number()):
                    comments_template();
                endif;
                ?>

            <?php endwhile; ?>
        </main>

        <?php get_sidebar(); ?>
    </div>
</div>

<?php get_footer(); ?>