<?php
/**
 * The template for displaying business archive
 * תבנית ארכיון עסקים - שלומי אונליין
 */

get_header();
?>

<div class="site-content">
    <div class="content-area">
        <main id="main-content" class="main-content">

            <header class="page-header business-archive-header">
                <h1 class="page-title">
                    <?php
                    if (is_tax('business_category')):
                        echo '🏷️ ' . single_term_title('', false);
                    else:
                        echo '🏢 עסקים מקומיים';
                    endif;
                    ?>
                </h1>

                <?php if (is_tax() && term_description()): ?>
                    <div class="taxonomy-description">
                        <?php echo term_description(); ?>
                    </div>
                <?php endif; ?>
            </header>

            <!-- פילטר קטגוריות -->
            <div class="business-filters">
                <h3>סינון לפי קטגוריה:</h3>
                <div class="filter-buttons">
                    <a href="<?php echo get_post_type_archive_link('business'); ?>"
                       class="filter-btn <?php echo !is_tax() ? 'active' : ''; ?>">
                        הכל
                    </a>
                    <?php
                    $categories = get_terms(array(
                        'taxonomy' => 'business_category',
                        'hide_empty' => true
                    ));
                    if (!empty($categories) && !is_wp_error($categories)):
                        foreach ($categories as $category):
                            $is_active = is_tax('business_category', $category->slug);
                            ?>
                            <a href="<?php echo get_term_link($category); ?>"
                               class="filter-btn <?php echo $is_active ? 'active' : ''; ?>">
                                <?php echo esc_html($category->name); ?>
                                <span class="count">(<?php echo $category->count; ?>)</span>
                            </a>
                        <?php endforeach;
                    endif;
                    ?>
                </div>
            </div>

            <!-- רשת עסקים -->
            <?php if (have_posts()): ?>
                <div class="business-directory">
                    <div class="business-grid">
                        <?php while (have_posts()): the_post(); ?>
                            <?php get_template_part('template-parts/content', 'business-card'); ?>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Pagination -->
                <?php
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => '← קודם',
                    'next_text' => 'הבא →',
                ));
                ?>

            <?php else: ?>
                <div class="no-businesses-found">
                    <p>לא נמצאו עסקים בקטגוריה זו.</p>
                    <a href="<?php echo get_post_type_archive_link('business'); ?>" class="btn btn-primary">
                        חזרה לכל העסקים
                    </a>
                </div>
            <?php endif; ?>

        </main>

        <?php get_sidebar(); ?>
    </div>
</div>

<?php get_footer(); ?>
