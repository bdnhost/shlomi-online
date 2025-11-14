<?php
/**
 * The template for displaying archive pages
 * תבנית ארכיון - שלומי אונליין
 */

get_header();
?>

<div class="archive-header">
    <div class="container">
        <div class="archive-header-content">
            <div class="archive-icon">
                <?php
                if (is_day()) {
                    echo '📅';
                } elseif (is_month()) {
                    echo '📆';
                } elseif (is_year()) {
                    echo '🗓️';
                } elseif (is_tag()) {
                    echo '🏷️';
                } elseif (is_author()) {
                    echo '✍️';
                } else {
                    echo '📂';
                }
                ?>
            </div>
            
            <div class="archive-info">
                <h1 class="archive-title">
                    <?php
                    if (is_day()) {
                        echo get_the_date('j F Y');
                    } elseif (is_month()) {
                        echo get_the_date('F Y');
                    } elseif (is_year()) {
                        echo get_the_date('Y');
                    } elseif (is_tag()) {
                        echo single_tag_title('', false);
                    } elseif (is_author()) {
                        echo 'כתבות של: ' . get_the_author();
                    } else {
                        echo get_the_archive_title();
                    }
                    ?>
                </h1>
                
                <?php if (is_tag() && tag_description()) : ?>
                    <div class="archive-description">
                        <?php echo tag_description(); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (is_author() && get_the_author_meta('description')) : ?>
                    <div class="archive-description">
                        <?php echo get_the_author_meta('description'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="site-content">
    <div class="content-area">
        <main class="main-content">
            
            <?php if (have_posts()) : ?>
                
                <!-- רשת כתבות -->
                <div class="posts-grid archive-posts-grid">
                    <?php
                    while (have_posts()) : the_post();
                    ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium'); ?>
                                    <?php else : ?>
                                        <div class="mock-img" style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                                    <?php endif; ?>
                                </a>
                            </div>
                            
                            <div class="post-content">
                                <?php
                                $categories = get_the_category();
                                if (!empty($categories)) :
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

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    <?php
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => '← קודם',
                        'next_text' => 'הבא →',
                    ));
                    ?>
                </div>

            <?php else : ?>
                
                <div class="no-posts-message">
                    <div class="no-posts-icon">📭</div>
                    <h2>לא נמצאו כתבות</h2>
                    <p>לא נמצאו כתבות בארכיון זה.</p>
                    <a href="<?php echo home_url('/'); ?>" class="btn btn-primary">חזרה לעמוד הראשי</a>
                </div>

            <?php endif; ?>

        </main>

        <?php get_sidebar(); ?>
    </div>
</div>

<?php get_footer(); ?>