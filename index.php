<?php
/**
 * The main template file
 * תבנית ראשית - שלומי אונליין
 */

get_header(); ?>

<!-- Breaking News -->
<div class="breaking-news">
    <div class="container">
        <span class="breaking-badge">⚡ חדשות אחרונות</span>
        <div class="breaking-news-wrapper">
            <div class="breaking-text">
                <?php
                // הצגת 8 הכותרות האחרונות מקטגוריית "עידכונים"
                $breaking_query = new WP_Query(array(
                    'category_name' => 'עידכונים',
                    'posts_per_page' => 8,
                    'post_status' => 'publish'
                ));

                if ($breaking_query->have_posts()) {
                    while ($breaking_query->have_posts()) {
                        $breaking_query->the_post();
                        ?>
                        <a href="<?php the_permalink(); ?>" class="breaking-item"
                            title="<?php echo esc_attr(get_the_title()); ?>">
                            <span class="breaking-icon">🔴</span>
                            <?php the_title(); ?>
                        </a>
                        <span class="breaking-separator">•</span>
                        <?php
                    }
                    wp_reset_postdata();
                } else {
                    ?>
                    <a href="<?php echo home_url('/'); ?>" class="breaking-item">
                        <span class="breaking-icon">📰</span>
                        עקבו אחרי העדכונים האחרונים של שלומי אונליין
                    </a>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>

<div class="site-content">
    <div class="content-area">
        <main class="main-content">

            <?php
            // כתבה ראשית מודגשת
            $featured_query = new WP_Query(array(
                'posts_per_page' => 1,
                'post_status' => 'publish'
            ));

            if ($featured_query->have_posts()):
                while ($featured_query->have_posts()):
                    $featured_query->the_post();
                    ?>
                    <article class="featured-post">
                        <?php if (has_post_thumbnail()): ?>
                            <?php the_post_thumbnail('large'); ?>
                        <?php else: ?>
                            <div class="mock-img featured-mock"></div>
                        <?php endif; ?>

                        <div class="featured-overlay">
                            <?php
                            $categories = get_the_category();
                            if (!empty($categories)):
                                ?>
                                <span class="featured-category"><?php echo esc_html($categories[0]->name); ?></span>
                            <?php endif; ?>

                            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                            <div class="featured-meta">
                                <span>✍️ <?php the_author(); ?></span> •
                                <span>🕐
                                    <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' לפני'; ?></span>
                                •
                                <span>💬 <?php comments_number('0 תגובות', 'תגובה אחת', '% תגובות'); ?></span>
                            </div>
                        </div>
                    </article>
                    <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>

            <!-- רשת כתבות -->
            <h2 class="section-title">חדשות נוספות</h2>
            <div class="posts-grid">
                <?php
                // כתבות נוספות (לא כולל את הכתבה הראשית)
                $args = array(
                    'post_type' => 'post',
                    'posts_per_page' => 9,
                    'offset' => 1, // דלג על הכתבה הראשית
                    'post_status' => 'publish'
                );

                $main_query = new WP_Query($args);

                if ($main_query->have_posts()):
                    while ($main_query->have_posts()):
                        $main_query->the_post();
                        ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
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
                                $categories = get_the_category();
                                if (!empty($categories)):
                                    ?>
                                    <div class="post-category"><?php echo esc_html($categories[0]->name); ?></div>
                                <?php endif; ?>

                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

                                <div class="post-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                </div>

                                <div class="post-meta">
                                    <span>🕐
                                        <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' לפני'; ?></span>
                                    <span>💬 <?php comments_number('0', '1', '%'); ?></span>
                                </div>
                            </div>
                        </article>
                        <?php
                    endwhile;
                else:
                    ?>
                    <p>לא נמצאו כתבות.</p>
                    <?php
                endif;
                wp_reset_postdata();
                ?>
            </div>

            <!-- Pagination -->
            <?php
            the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => '← קודם',
                'next_text' => 'הבא →',
            ));
            ?>

        </main>

        <?php get_sidebar(); ?>
    </div>
</div>

<?php get_footer(); ?>