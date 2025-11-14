<?php
/**
 * The sidebar containing the main widget area
 */

if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>

<aside class="sidebar" role="complementary">
    <?php dynamic_sidebar('sidebar-1'); ?>

    <?php if (!is_active_sidebar('sidebar-1')): ?>
        <!-- Widgets ברירת מחדל -->

        <!-- כתבות פופולריות -->
        <div class="widget">
            <h3 class="widget-title">הכתבות הנצפות ביותר</h3>
            <ul class="popular-list">
                <?php
                $popular = new WP_Query(array(
                    'posts_per_page' => 5,
                    'orderby' => 'comment_count',
                    'order' => 'DESC'
                ));

                $counter = 1;
                if ($popular->have_posts()):
                    while ($popular->have_posts()):
                        $popular->the_post();
                        ?>
                        <li>
                            <div class="popular-number"><?php echo $counter; ?></div>
                            <div class="popular-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </div>
                        </li>
                        <?php
                        $counter++;
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </ul>
        </div>

        <!-- קטגוריות -->
        <div class="widget">
            <h3 class="widget-title">קטגוריות</h3>
            <ul>
                <?php
                wp_list_categories(array(
                    'title_li' => '',
                    'orderby' => 'count',
                    'order' => 'DESC',
                    'number' => 8,
                    'show_count' => true,
                ));
                ?>
            </ul>
        </div>

        <!-- כתבות אחרונות -->
        <div class="widget">
            <h3 class="widget-title">כתבות אחרונות</h3>
            <ul>
                <?php
                $recent = new WP_Query(array(
                    'posts_per_page' => 5,
                    'post_status' => 'publish'
                ));

                if ($recent->have_posts()):
                    while ($recent->have_posts()):
                        $recent->the_post();
                        ?>
                        <li>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                            </a>
                            <span class="recent-post-time">
                                <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' לפני'; ?>
                            </span>
                        </li>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </ul>
        </div>

        <!-- תגיות -->
        <div class="widget">
            <h3 class="widget-title">תגיות פופולריות</h3>
            <div class="tagcloud">
                <?php
                wp_tag_cloud(array(
                    'smallest' => 12,
                    'largest' => 22,
                    'unit' => 'px',
                    'number' => 20,
                ));
                ?>
            </div>
        </div>

        <!-- מזג אוויר -->
        <div class="widget">
            <h3 class="widget-title">מזג אוויר</h3>
            <div class="weather-widget">
                <div class="weather-icon">🌤️</div>
                <div class="weather-temp">24°C</div>
                <div class="weather-desc">שמש וענן חלקי</div>
                <div class="weather-details">
                    <div>מינימום: 18° | מקסימום: 26°</div>
                </div>
            </div>
        </div>

    <?php endif; ?>
</aside>