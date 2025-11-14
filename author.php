<?php
/**
 * The template for displaying author pages
 * תבנית מחבר - שלומי אונליין
 */

get_header();

$author = get_queried_object();
?>

<div class="author-header">
    <div class="container">
        <div class="author-header-content">
            <div class="author-avatar-large">
                <?php echo get_avatar($author->ID, 120); ?>
            </div>
            
            <div class="author-info">
                <h1 class="author-name">
                    <?php echo get_the_author_meta('display_name', $author->ID); ?>
                </h1>
                
                <?php if (get_the_author_meta('description', $author->ID)) : ?>
                    <div class="author-bio">
                        <?php echo wpautop(get_the_author_meta('description', $author->ID)); ?>
                    </div>
                <?php endif; ?>
                
                <div class="author-stats">
                    <span class="author-stat">
                        📝 <?php echo count_user_posts($author->ID); ?> כתבות
                    </span>
                    
                    <?php if (get_the_author_meta('user_url', $author->ID)) : ?>
                        <a href="<?php echo esc_url(get_the_author_meta('user_url', $author->ID)); ?>" 
                           target="_blank" 
                           rel="noopener"
                           class="author-website">
                            🌐 אתר אישי
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="site-content">
    <div class="content-area">
        <main class="main-content">
            
            <h2 class="section-title">כתבות של <?php echo get_the_author_meta('display_name', $author->ID); ?></h2>
            
            <?php if (have_posts()) : ?>
                
                <div class="posts-grid author-posts-grid">
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
                    <div class="no-posts-icon">✍️</div>
                    <h2>אין כתבות עדיין</h2>
                    <p><?php echo get_the_author_meta('display_name', $author->ID); ?> עדיין לא פרסם/ה כתבות.</p>
                    <a href="<?php echo home_url('/'); ?>" class="btn btn-primary">חזרה לעמוד הראשי</a>
                </div>

            <?php endif; ?>

        </main>

        <?php get_sidebar(); ?>
    </div>
</div>

<?php get_footer(); ?>