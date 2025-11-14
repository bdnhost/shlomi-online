<?php
/**
 * The template for displaying tag pages
 * תבנית תגית - שלומי אונליין
 */

get_header();

$tag = get_queried_object();
?>

<div class="tag-header">
    <div class="container">
        <div class="tag-header-content">
            <div class="tag-icon">🏷️</div>

            <div class="tag-info">
                <h1 class="tag-title">
                    <?php single_tag_title(); ?>
                </h1>

                <?php if (tag_description()): ?>
                    <div class="tag-description">
                        <?php echo tag_description(); ?>
                    </div>
                <?php endif; ?>

                <div class="tag-meta">
                    <span class="tag-count">
                        📊 <?php echo $tag->count; ?> כתבות
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

                <div class="posts-grid tag-posts-grid">
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
                                            style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 48px;">
                                            🏷️
                                        </div>
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
                                    <span>🕐 <?php echo get_the_date('j.n.Y'); ?></span>
                                    <span>💬 <?php comments_number('0', '1', '%'); ?></span>
                                </div>
                            </div>
                        </article>
                        <?php
                    endwhile;
                    ?>
                </div>

                <div class="pagination-wrapper">
                    <?php
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => '← קודם',
                        'next_text' => 'הבא →',
                    ));
                    ?>
                </div>

            <?php else: ?>

                <div class="no-posts-message">
                    <div class="no-posts-icon">🏷️</div>
                    <h2>אין כתבות בתגית זו</h2>
                    <p>לא נמצאו כתבות עם התגית "<?php single_tag_title(); ?>".</p>
                    <a href="<?php echo home_url('/'); ?>" class="btn btn-primary">חזרה לעמוד הראשי</a>
                </div>

            <?php endif; ?>

        </main>

        <?php get_sidebar(); ?>
    </div>
</div>

<?php get_footer(); ?>