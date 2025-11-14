<?php
/**
 * The template for displaying category pages
 * תבנית קטגוריה - שלומי אונליין
 */

get_header();

$category = get_queried_object();
?>

<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <a href="<?php echo home_url(); ?>">בית</a>
    <span> > </span>
    <span><?php single_cat_title(); ?></span>
</div>

<div class="category-header">
    <div class="container">
        <div class="category-header-content">
            <?php
            // אייקון לפי שם הקטגוריה
            $category_icons = array(
                'חדשות מקומיות' => '📰',
                'ביטחון' => '🛡️',
                'חינוך' => '🎓',
                'עידכונים' => '📢',
                'שלומי' => '🏘️',
                'עסקים מקומיים' => '💼',
                'שירות לתושב' => '🏛️',
                'תרבות ופנאי' => '🎭',
                'קהילה' => '🤝',
                'דיור' => '🏠',
                'כלכלה' => '💰',
                'גליל מערבי' => '🌄',
                'יומן מלחמה' => '📖',
            );

            $icon = isset($category_icons[$category->name]) ? $category_icons[$category->name] : '📂';
            ?>

            <div class="category-icon"><?php echo $icon; ?></div>

            <div class="category-info">
                <h1 class="category-title">
                    <?php single_cat_title(); ?>
                </h1>

                <?php if ($category->description): ?>
                    <div class="category-description">
                        <?php echo category_description(); ?>
                    </div>
                <?php endif; ?>

                <div class="category-meta">
                    <span class="category-count">
                        📊 <?php echo $category->count; ?> כתבות
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="site-content">
    <div class="content-area">
        <main class="main-content">

            <?php if (have_posts()): ?>

                <!-- רשת כתבות -->
                <div class="posts-grid category-posts-grid">
                    <?php
                    while (have_posts()):
                        the_post();
                        ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()): ?>
                                        <?php the_post_thumbnail('medium'); ?>
                                    <?php else: ?>
                                        <div class="mock-img"
                                            style="width: 100%; height: 200px; background: linear-gradient(135deg, #1a2c5b 0%, #c41e3a 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 48px;">
                                            <?php echo $icon; ?>
                                        </div>
                                    <?php endif; ?>
                                </a>

                                <?php
                                // תג "חדש" לפוסטים מהיום האחרון
                                $post_date = get_the_time('U');
                                $current_time = current_time('timestamp');
                                if (($current_time - $post_date) < 86400):  // 24 שעות
                                    ?>
                                    <span class="new-badge">🔥 חדש</span>
                                <?php endif; ?>
                            </div>

                            <div class="post-content">
                                <?php
                                $categories = get_the_category();
                                if (!empty($categories)):
                                    foreach ($categories as $cat):
                                        if ($cat->term_id !== $category->term_id):  // לא להציג את הקטגוריה הנוכחית
                                            ?>
                                            <div class="post-category">
                                                <a href="<?php echo get_category_link($cat->term_id); ?>">
                                                    <?php echo esc_html($cat->name); ?>
                                                </a>
                                            </div>
                                            <?php
                                            break; // רק קטגוריה אחת נוספת
                                        endif;
                                    endforeach;
                                endif;
                                ?>

                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

                                <div class="post-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                </div>

                                <div class="post-meta">
                                    <span>🕐
                                        <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' לפני'; ?></span>
                                    <span>💬 <?php comments_number('0', '1', '%'); ?></span>
                                    <?php if (get_the_author()): ?>
                                        <span>✍️ <?php the_author(); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                        <?php
                    endwhile;
                    ?>
                </div>

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    <?php
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => '← קודם',
                        'next_text' => 'הבא →',
                        'before_page_number' => '<span class="screen-reader-text">עמוד </span>',
                    ));
                    ?>
                </div>

            <?php else: ?>

                <div class="no-posts-message">
                    <div class="no-posts-icon"><?php echo $icon; ?></div>
                    <h2>אין כתבות בקטגוריה זו כרגע</h2>
                    <p>נראה שעדיין לא פורסמו כתבות בקטגוריה "<?php single_cat_title(); ?>".</p>
                    <a href="<?php echo home_url('/'); ?>" class="btn btn-primary">חזרה לעמוד הראשי</a>
                </div>

            <?php endif; ?>

        </main>

        <?php get_sidebar(); ?>
    </div>
</div>

<?php get_footer(); ?>