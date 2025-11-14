<?php
/**
 * The template for displaying 404 pages (not found)
 * תבנית דף 404 - שלומי אונליין
 */

get_header();
?>

<div class="error-404-header">
    <div class="container">
        <div class="error-404-content">
            <div class="error-404-icon">🔍</div>

            <div class="error-404-info">
                <h1 class="error-404-title">404 - הדף לא נמצא</h1>
                <p class="error-404-message">
                    מצטערים, הדף שחיפשת לא קיים או הועבר למקום אחר.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="site-content">
    <div class="content-area">
        <main class="main-content">

            <div class="error-404-section">

                <!-- חיפוש -->
                <div class="search-section">
                    <h2>🔍 חפש במאגר הכתבות</h2>
                    <form role="search" method="get" class="search-form-404"
                        action="<?php echo esc_url(home_url('/')); ?>">
                        <input type="search" name="s" placeholder="חפש כתבות, נושאים, תגיות..." required>
                        <button type="submit" class="btn btn-primary">
                            חפש
                        </button>
                    </form>
                </div>

                <!-- קטגוריות פופולריות -->
                <div class="popular-categories">
                    <h2>📂 קטגוריות פופולריות</h2>
                    <div class="category-grid">
                        <?php
                        $categories = get_categories(array(
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'number' => 6,
                            'hide_empty' => true,
                        ));

                        foreach ($categories as $category):
                            ?>
                            <a href="<?php echo get_category_link($category->term_id); ?>" class="category-card">
                                <h3><?php echo esc_html($category->name); ?></h3>
                                <span><?php echo $category->count; ?> כתבות</span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- כתבות אחרונות -->
                <div class="recent-posts-404">
                    <h2>📰 כתבות אחרונות</h2>
                    <div class="posts-grid">
                        <?php
                        $recent_posts = new WP_Query(array(
                            'posts_per_page' => 6,
                            'post_status' => 'publish'
                        ));

                        if ($recent_posts->have_posts()):
                            while ($recent_posts->have_posts()):
                                $recent_posts->the_post();
                                ?>
                                <article class="post-card">
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

                                        <div class="post-meta">
                                            <span>🕐 <?php echo get_the_date('j.n.Y'); ?></span>
                                        </div>
                                    </div>
                                </article>
                                <?php
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>
                </div>

                <!-- קישורים מהירים -->
                <div class="quick-links">
                    <h2>🔗 קישורים מהירים</h2>
                    <div class="links-grid">
                        <a href="<?php echo home_url('/'); ?>" class="quick-link">
                            🏠 עמוד ראשי
                        </a>
                        <a href="<?php echo home_url('/about'); ?>" class="quick-link">
                            ℹ️ אודות
                        </a>
                        <a href="<?php echo home_url('/contact'); ?>" class="quick-link">
                            📞 צור קשר
                        </a>
                        <a href="<?php echo home_url('/sitemap'); ?>" class="quick-link">
                            🗺️ מפת האתר
                        </a>
                    </div>
                </div>

            </div>

        </main>

        <?php get_sidebar(); ?>
    </div>
</div>

<?php get_footer(); ?>