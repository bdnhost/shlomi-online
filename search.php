<?php
/**
 * The template for displaying search results
 * תבנית חיפוש - שלומי אונליין
 */

get_header();
?>

<div class="search-header">
    <div class="container">
        <div class="search-header-content">
            <div class="search-icon">🔍</div>
            
            <div class="search-info">
                <h1 class="search-title">
                    תוצאות חיפוש עבור: <span class="search-query">"<?php echo get_search_query(); ?>"</span>
                </h1>
                
                <?php if (have_posts()) : ?>
                    <p class="search-results-count">
                        נמצאו <?php echo $wp_query->found_posts; ?> תוצאות
                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- טופס חיפוש מורחב -->
        <div class="search-form-wrapper">
            <form role="search" method="get" class="search-form-extended" action="<?php echo esc_url(home_url('/')); ?>">
                <input type="search" 
                       name="s" 
                       placeholder="חפש כתבות, נושאים, תגיות..." 
                       value="<?php echo get_search_query(); ?>"
                       required>
                <button type="submit" class="btn btn-primary">
                    🔍 חפש
                </button>
            </form>
        </div>
    </div>
</div>

<div class="site-content">
    <div class="content-area">
        <main class="main-content">
            
            <?php if (have_posts()) : ?>
                
                <!-- רשת תוצאות -->
                <div class="posts-grid search-results-grid">
                    <?php
                    while (have_posts()) : the_post();
                    ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-card search-result-card'); ?>>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium'); ?>
                                    <?php else : ?>
                                        <div class="mock-img" style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 48px;">
                                            🔍
                                        </div>
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
                                    <?php 
                                    // הדגשת מילות החיפוש בתקציר
                                    $excerpt = get_the_excerpt();
                                    $search_query = get_search_query();
                                    if ($search_query) {
                                        $excerpt = preg_replace(
                                            '/(' . preg_quote($search_query, '/') . ')/iu',
                                            '<mark>$1</mark>',
                                            $excerpt
                                        );
                                    }
                                    echo wp_trim_words($excerpt, 25, '...');
                                    ?>
                                </div>
                                
                                <div class="post-meta">
                                    <span>🕐 <?php echo get_the_date('j.n.Y'); ?></span>
                                    <span>💬 <?php comments_number('0', '1', '%'); ?></span>
                                    <?php if (get_the_author()) : ?>
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
                    ));
                    ?>
                </div>

            <?php else : ?>
                
                <div class="no-posts-message no-search-results">
                    <div class="no-posts-icon">🔍</div>
                    <h2>לא נמצאו תוצאות</h2>
                    <p>לא נמצאו כתבות התואמות את החיפוש "<strong><?php echo get_search_query(); ?></strong>".</p>
                    
                    <div class="search-suggestions">
                        <h3>💡 טיפים לחיפוש:</h3>
                        <ul>
                            <li>בדוק שהמילים מאויתות נכון</li>
                            <li>נסה מילות חיפוש שונות או כלליות יותר</li>
                            <li>נסה פחות מילות חיפוש</li>
                        </ul>
                    </div>
                    
                    <a href="<?php echo home_url('/'); ?>" class="btn btn-primary">חזרה לעמוד הראשי</a>
                    
                    <!-- הצעות קטגוריות -->
                    <div class="suggested-categories">
                        <h3>או עיין בקטגוריות:</h3>
                        <div class="category-badges">
                            <?php
                            $categories = get_categories(array(
                                'orderby' => 'count',
                                'order' => 'DESC',
                                'number' => 6,
                                'hide_empty' => true,
                            ));
                            
                            foreach ($categories as $category) :
                            ?>
                                <a href="<?php echo get_category_link($category->term_id); ?>" class="category-badge">
                                    <?php echo esc_html($category->name); ?> (<?php echo $category->count; ?>)
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            <?php endif; ?>

        </main>

        <?php get_sidebar(); ?>
    </div>
</div>

<?php get_footer(); ?>