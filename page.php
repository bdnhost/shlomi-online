<?php
/**
 * The template for displaying pages
 * תבנית דף רגיל - שלומי אונליין
 */

get_header();
?>

<div class="site-content">
    <div class="content-area">
        <main class="main-content">
            
            <?php
            while (have_posts()) : the_post();
            ?>
                <article id="page-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
                    
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="page-featured-image">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <header class="page-header">
                        <h1 class="page-title"><?php the_title(); ?></h1>
                        
                        <?php if (get_the_modified_date() !== get_the_date()) : ?>
                            <div class="page-meta">
                                <span>📅 עודכן לאחרונה: <?php echo get_the_modified_date('j F Y'); ?></span>
                            </div>
                        <?php endif; ?>
                    </header>

                    <div class="page-entry-content">
                        <?php
                        the_content();
                        
                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . 'עמודים:',
                            'after' => '</div>',
                        ));
                        ?>
                    </div>

                    <?php if (comments_open() || get_comments_number()) : ?>
                        <div class="page-comments">
                            <?php comments_template(); ?>
                        </div>
                    <?php endif; ?>

                </article>
            <?php endwhile; ?>

        </main>

        <?php
        // הצגת sidebar רק אם לא דף מלא רוחב
        if (!is_page_template('page-full-width.php')) {
            get_sidebar();
        }
        ?>
    </div>
</div>

<?php get_footer(); ?>