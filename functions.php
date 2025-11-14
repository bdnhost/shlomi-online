<?php
/**
 * שלומי אונליין - Functions and definitions
 * גרסה מתוקנת ובטוחה
 */

// מניעת גישה ישירה
if (!defined('ABSPATH')) {
    exit;
}

// הגדרת תמיכה בתכונות וורדפרס
function shlomi_setup()
{
    // תמיכה בכותרת דינמית
    add_theme_support('title-tag');

    // תמיכה בתמונות ראשיות
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(800, 600, true);
    add_image_size('featured-large', 1200, 500, true);

    // תמיכה ב-HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // תמיכה בלוגו מותאם אישית
    add_theme_support('custom-logo', array(
        'height' => 60,
        'width' => 180,
        'flex-height' => true,
        'flex-width' => true,
    ));

    // רישום תפריטי ניווט
    register_nav_menus(array(
        'primary' => 'תפריט ראשי (Menu 1)',
        'footer' => 'תפריט תחתון',
    ));

    // תמיכה ב-RSS
    add_theme_support('automatic-feed-links');
}
add_action('after_setup_theme', 'shlomi_setup');

// רישום Sidebar
function shlomi_widgets_init()
{
    register_sidebar(array(
        'name' => 'Sidebar ראשי',
        'id' => 'sidebar-1',
        'description' => 'מופיע בצד העמוד',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    // Footer widgets
    for ($i = 1; $i <= 4; $i++) {
        register_sidebar(array(
            'name' => 'Footer ' . $i,
            'id' => 'footer-' . $i,
            'description' => 'אזור ' . $i . ' בתחתית העמוד',
            'before_widget' => '<div class="footer-widget">',
            'after_widget' => '</div>',
            'before_title' => '<h3>',
            'after_title' => '</h3>',
        ));
    }
}
add_action('widgets_init', 'shlomi_widgets_init');

// טעינת סקריפטים וסגנונות
function shlomi_scripts()
{
    // CSS ראשי
    wp_enqueue_style('shlomi-style', get_stylesheet_uri(), array(), '1.1');

    // גופן עברי - רק אם לא באדמין
    if (!is_admin()) {
        wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Heebo:wght@300;400;500;700&display=swap', array(), null);
    }

    // jQuery
    wp_enqueue_script('jquery');

    // JavaScript ראשי - רק אם הקובץ קיים
    $js_file = get_template_directory() . '/js/scripts.js';
    if (file_exists($js_file)) {
        wp_enqueue_script('shlomi-scripts', get_template_directory_uri() . '/js/scripts.js', array('jquery'), '1.2', true);

        // העברת משתנים ל-JavaScript
        wp_localize_script('shlomi-scripts', 'shlomiData', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('shlomi-nonce'),
            'homeUrl' => home_url('/'),
        ));
    }

    // תגובות
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'shlomi_scripts');

// הוספת תמיכה ב-RTL
function shlomi_rtl_support()
{
    $rtl_file = get_template_directory() . '/rtl.css';
    if (is_rtl() && file_exists($rtl_file)) {
        wp_enqueue_style('shlomi-rtl', get_template_directory_uri() . '/rtl.css', array('shlomi-style'), '1.0');
    }
}
add_action('wp_enqueue_scripts', 'shlomi_rtl_support');

// הוספת מחלקות לגוף העמוד
function shlomi_body_classes($classes)
{
    if (!is_singular()) {
        $classes[] = 'hfeed';
    }

    if (!has_post_thumbnail()) {
        $classes[] = 'no-featured-image';
    }

    return $classes;
}
add_filter('body_class', 'shlomi_body_classes');

// קטעי תוכן מותאמים אישית
function shlomi_excerpt_length($length)
{
    return 25;
}
add_filter('excerpt_length', 'shlomi_excerpt_length');

function shlomi_excerpt_more($more)
{
    return '...';
}
add_filter('excerpt_more', 'shlomi_excerpt_more');

// Widget כתבות פופולריות
class Shlomi_Popular_Posts_Widget extends WP_Widget
{
    function __construct()
    {
        parent::__construct(
            'shlomi_popular_posts',
            'כתבות פופולריות',
            array('description' => 'מציג את הכתבות הנצפות ביותר')
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        $number = (!empty($instance['number'])) ? absint($instance['number']) : 5;

        $popular_posts = new WP_Query(array(
            'posts_per_page' => $number,
            'orderby' => 'comment_count',
            'order' => 'DESC'
        ));

        if ($popular_posts->have_posts()) {
            echo '<ul class="popular-list">';
            $counter = 1;
            while ($popular_posts->have_posts()) {
                $popular_posts->the_post();
                echo '<li>';
                echo '<div class="popular-number">' . $counter . '</div>';
                echo '<div class="popular-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></div>';
                echo '</li>';
                $counter++;
            }
            echo '</ul>';
            wp_reset_postdata();
        }

        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : 'הכתבות הנצפות ביותר';
        $number = !empty($instance['number']) ? $instance['number'] : 5;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">כותרת:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>">מספר כתבות:</label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>"
                name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1"
                value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 5;
        return $instance;
    }
}

function shlomi_register_widgets()
{
    register_widget('Shlomi_Popular_Posts_Widget');
}
add_action('widgets_init', 'shlomi_register_widgets');

// SEO בסיסי ובטוח
function shlomi_basic_seo()
{
    ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">

    <?php if (is_single()): ?>
        <?php
        $excerpt = get_the_excerpt();
        if (empty($excerpt)) {
            $excerpt = wp_trim_words(get_the_content(), 25);
        }
        ?>
        <meta name="description" content="<?php echo esc_attr($excerpt); ?>">
        <meta property="og:type" content="article">
        <meta property="og:title" content="<?php the_title(); ?> | שלומי אונליין">
        <meta property="og:description" content="<?php echo esc_attr($excerpt); ?>">
        <meta property="og:url" content="<?php the_permalink(); ?>">
        <meta property="og:site_name" content="שלומי אונליין">

        <?php if (has_post_thumbnail()): ?>
            <meta property="og:image" content="<?php echo esc_url(get_the_post_thumbnail_url(null, 'large')); ?>">
        <?php endif; ?>

    <?php elseif (is_home() || is_front_page()): ?>
        <meta name="description"
            content="שלומי אונליין - פורטל החדשות המקומי המוביל. חדשות מקומיות, עדכונים ועוד מהעיר שלומי והסביבה">
        <meta property="og:type" content="website">
        <meta property="og:title" content="שלומי אונליין - חדשות מקומיות">
        <meta property="og:description" content="פורטל החדשות המקומי המוביל של שלומי והסביבה">
        <meta property="og:url" content="<?php echo esc_url(home_url()); ?>">
    <?php endif; ?>
<?php
}
add_action('wp_head', 'shlomi_basic_seo');

// הסרת גרסת וורדפרס מה-HEAD (אבטחה)
remove_action('wp_head', 'wp_generator');

// אופטימיזציה - הסרת emoji scripts
function shlomi_disable_emojis()
{
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
}
add_action('init', 'shlomi_disable_emojis');

// הסרת המילה "ארכיון" מכותרות
function shlomi_remove_archive_title_prefix($title)
{
    if (is_category()) {
        $title = single_cat_title('', false);
    } elseif (is_tag()) {
        $title = single_tag_title('', false);
    } elseif (is_author()) {
        $title = get_the_author();
    }
    return $title;
}
add_filter('get_the_archive_title', 'shlomi_remove_archive_title_prefix');

// אופטימיזציה לכותרות SEO
function shlomi_optimize_title($title)
{
    if (is_home() || is_front_page()) {
        return 'שלומי אונליין - חדשות מקומיות | עדכונים יומיים מהעיר שלומי';
    }

    if (is_single()) {
        $categories = get_the_category();
        $cat_name = $categories ? $categories[0]->name : '';
        return get_the_title() . ($cat_name ? ' | ' . $cat_name : '') . ' | שלומי אונליין';
    }

    if (is_category()) {
        return single_cat_title('', false) . ' | ארכיון | שלומי אונליין';
    }

    return $title;
}
add_filter('pre_get_document_title', 'shlomi_optimize_title');

// פונקציה לטיפול בשגיאות PHP
function shlomi_error_handler($errno, $errstr, $errfile, $errline)
{
    // לא להציג שגיאות קלות בפרודקשן
    if (!(error_reporting() & $errno)) {
        return false;
    }

    // רישום השגיאה לקובץ לוג
    error_log("PHP Error: [$errno] $errstr in $errfile on line $errline");

    return true;
}

// הפעלת טיפול בשגיאות רק אם לא במצב debug
if (!defined('WP_DEBUG') || !WP_DEBUG) {
    set_error_handler('shlomi_error_handler');
}

// פונקציה לבדיקת תקינות תמונות
function shlomi_check_image_exists($attachment_id)
{
    if (!$attachment_id) {
        return false;
    }

    $file_path = get_attached_file($attachment_id);
    return $file_path && file_exists($file_path);
}

// פונקציה בטוחה לקבלת תמונה ראשית
function shlomi_get_safe_thumbnail($post_id = null, $size = 'medium')
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    if (has_post_thumbnail($post_id)) {
        $thumbnail_id = get_post_thumbnail_id($post_id);
        if (shlomi_check_image_exists($thumbnail_id)) {
            return get_the_post_thumbnail($post_id, $size);
        }
    }

    return '<div class="mock-img"></div>';
}

// אפשרות להסתיר תמונה ראשית בפוסט בודד
function shlomi_hide_featured_image_option() {
    add_meta_box(
        'shlomi_featured_image_options',
        'הגדרות תמונה ראשית',
        'shlomi_featured_image_meta_box',
        'post',
        'side',
        'default'
    );
}

function shlomi_featured_image_meta_box($post) {
    wp_nonce_field('shlomi_featured_image_nonce', 'shlomi_featured_image_nonce');
    $hide_featured = get_post_meta($post->ID, '_hide_featured_image', true);
    ?>
    <label>
        <input type="checkbox" name="hide_featured_image" value="1" <?php checked($hide_featured, '1'); ?>>
        הסתר תמונה ראשית בדף הפוסט
    </label>
    <p><small>התמונה עדיין תופיע ברשימות ובקטגוריות</small></p>
    <?php
}

function shlomi_save_featured_image_option($post_id) {
    if (!isset($_POST['shlomi_featured_image_nonce']) || 
        !wp_verify_nonce($_POST['shlomi_featured_image_nonce'], 'shlomi_featured_image_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $hide_featured = isset($_POST['hide_featured_image']) ? '1' : '0';
    update_post_meta($post_id, '_hide_featured_image', $hide_featured);
}

// הפעלת המטא בוקס רק אם לא במצב עריכה מהירה
$current_action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
if ($current_action !== 'edit') {
    add_action('add_meta_boxes', 'shlomi_hide_featured_image_option');
    add_action('save_post', 'shlomi_save_featured_image_option');
}

// פונקציה מותאמת אישית להצגת תגובות
function shlomi_custom_comment($comment, $args, $depth)
{
    $GLOBALS['comment'] = $comment;
    ?>
    <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
        <article class="comment-body">
            <div class="comment-author-avatar">
                <?php echo get_avatar($comment, 60); ?>
            </div>
            <div class="comment-content-wrapper">
                <div class="comment-meta">
                    <span class="comment-author-name">
                        <?php echo get_comment_author_link(); ?>
                    </span>
                    <span class="comment-metadata">
                        <a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>">
                            <?php printf('%s בשעה %s', get_comment_date('j F Y'), get_comment_time('H:i')); ?>
                        </a>
                    </span>
                </div>

                <?php if ($comment->comment_approved == '0'): ?>
                    <p class="comment-awaiting-moderation">התגובה שלך ממתינה לאישור.</p>
                <?php endif; ?>

                <div class="comment-content">
                    <?php comment_text(); ?>
                </div>

                <div class="comment-reply">
                    <?php
                    comment_reply_link(array_merge($args, array(
                        'depth' => $depth,
                        'max_depth' => $args['max_depth'],
                        'reply_text' => 'השב',
                    )));
                    ?>
                </div>
            </div>
        </article>
    <?php
}

// הוספת lazy loading אוטומטי לכל התמונות
function shlomi_add_lazy_loading($attr, $attachment, $size)
{
    // לא להוסיף lazy loading לתמונה הראשית (featured) בדף בודד
    if (!is_singular() || !has_post_thumbnail(get_the_ID()) || $attachment->ID !== get_post_thumbnail_id(get_the_ID())) {
        $attr['loading'] = 'lazy';
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'shlomi_add_lazy_loading', 10, 3);

// הוספת srcset ו-sizes לתמונות לאופטימיזציה
function shlomi_responsive_images()
{
    add_theme_support('responsive-embeds');
}
add_action('after_setup_theme', 'shlomi_responsive_images');

/* ========================================
   מערכת עסקים מקומיים - Local Business Directory
   ======================================== */

// רישום Custom Post Type - עסק מקומי
function shlomi_register_business_post_type()
{
    $labels = array(
        'name' => 'עסקים מקומיים',
        'singular_name' => 'עסק',
        'menu_name' => 'עסקים מקומיים',
        'add_new' => 'הוסף עסק',
        'add_new_item' => 'הוסף עסק חדש',
        'edit_item' => 'ערוך עסק',
        'new_item' => 'עסק חדש',
        'view_item' => 'צפה בעסק',
        'search_items' => 'חפש עסקים',
        'not_found' => 'לא נמצאו עסקים',
        'not_found_in_trash' => 'לא נמצאו עסקים בפח',
        'all_items' => 'כל העסקים',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'business'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-store',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest' => true,
    );

    register_post_type('business', $args);
}
add_action('init', 'shlomi_register_business_post_type');

// Flush rewrite rules on theme activation
function shlomi_business_rewrite_flush()
{
    shlomi_register_business_post_type();
    shlomi_register_business_taxonomy();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'shlomi_business_rewrite_flush');

// יצירת קטגוריות ברירת מחדל עם אייקונים
function shlomi_create_default_business_categories()
{
    $default_categories = array(
        'restaurants' => array(
            'name' => 'מסעדות ובתי קפה',
            'slug' => 'restaurants',
            'icon' => '🍽️',
            'color' => '#e74c3c'
        ),
        'shopping' => array(
            'name' => 'קניות ושירותים',
            'slug' => 'shopping',
            'icon' => '🛒',
            'color' => '#3498db'
        ),
        'health' => array(
            'name' => 'בריאות ויופי',
            'slug' => 'health',
            'icon' => '💪',
            'color' => '#2ecc71'
        ),
        'education' => array(
            'name' => 'חינוך ולימודים',
            'slug' => 'education',
            'icon' => '📚',
            'color' => '#9b59b6'
        ),
        'services' => array(
            'name' => 'שירותים מקצועיים',
            'slug' => 'services',
            'icon' => '🔧',
            'color' => '#f39c12'
        ),
        'entertainment' => array(
            'name' => 'בילוי ופנאי',
            'slug' => 'entertainment',
            'icon' => '🎭',
            'color' => '#e91e63'
        ),
        'automotive' => array(
            'name' => 'רכב ותחבורה',
            'slug' => 'automotive',
            'icon' => '🚗',
            'color' => '#34495e'
        ),
        'realestate' => array(
            'name' => 'נדל"ן ובניין',
            'slug' => 'realestate',
            'icon' => '🏠',
            'color' => '#16a085'
        ),
    );

    foreach ($default_categories as $cat_data) {
        if (!term_exists($cat_data['slug'], 'business_category')) {
            $term = wp_insert_term(
                $cat_data['name'],
                'business_category',
                array('slug' => $cat_data['slug'])
            );

            if (!is_wp_error($term)) {
                update_term_meta($term['term_id'], 'category_icon', $cat_data['icon']);
                update_term_meta($term['term_id'], 'category_color', $cat_data['color']);
            }
        }
    }
}
add_action('init', 'shlomi_create_default_business_categories');

// רישום Taxonomy - קטגוריות עסקים
function shlomi_register_business_taxonomy()
{
    $labels = array(
        'name' => 'קטגוריות עסקים',
        'singular_name' => 'קטגוריית עסק',
        'search_items' => 'חפש קטגוריות',
        'all_items' => 'כל הקטגוריות',
        'parent_item' => 'קטגוריה אב',
        'parent_item_colon' => 'קטגוריה אב:',
        'edit_item' => 'ערוך קטגוריה',
        'update_item' => 'עדכן קטגוריה',
        'add_new_item' => 'הוסף קטגוריה חדשה',
        'new_item_name' => 'שם קטגוריה חדשה',
        'menu_name' => 'קטגוריות',
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'business-category'),
        'show_in_rest' => true,
    );

    register_taxonomy('business_category', array('business'), $args);
}
add_action('init', 'shlomi_register_business_taxonomy');

// הוספת Meta Boxes לעסקים
function shlomi_add_business_meta_boxes()
{
    add_meta_box(
        'business_contact_info',
        '📞 פרטי יצירת קשר',
        'shlomi_business_contact_meta_box',
        'business',
        'normal',
        'high'
    );

    add_meta_box(
        'business_location_info',
        '📍 מיקום וכתובת',
        'shlomi_business_location_meta_box',
        'business',
        'normal',
        'high'
    );

    add_meta_box(
        'business_hours_info',
        '⏰ שעות פעילות',
        'shlomi_business_hours_meta_box',
        'business',
        'normal',
        'default'
    );

    add_meta_box(
        'business_social_info',
        '📱 רשתות חברתיות',
        'shlomi_business_social_meta_box',
        'business',
        'side',
        'default'
    );

    add_meta_box(
        'business_extra_info',
        '⭐ מידע נוסף',
        'shlomi_business_extra_meta_box',
        'business',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'shlomi_add_business_meta_boxes');

// Meta Box - פרטי יצירת קשר
function shlomi_business_contact_meta_box($post)
{
    wp_nonce_field('shlomi_business_meta_nonce', 'shlomi_business_nonce');

    $phone = get_post_meta($post->ID, '_business_phone', true);
    $mobile = get_post_meta($post->ID, '_business_mobile', true);
    $email = get_post_meta($post->ID, '_business_email', true);
    $website = get_post_meta($post->ID, '_business_website', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="business_phone">טלפון קווי</label></th>
            <td><input type="tel" id="business_phone" name="business_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" placeholder="04-1234567"></td>
        </tr>
        <tr>
            <th><label for="business_mobile">נייד</label></th>
            <td><input type="tel" id="business_mobile" name="business_mobile" value="<?php echo esc_attr($mobile); ?>" class="regular-text" placeholder="054-1234567"></td>
        </tr>
        <tr>
            <th><label for="business_email">אימייל</label></th>
            <td><input type="email" id="business_email" name="business_email" value="<?php echo esc_attr($email); ?>" class="regular-text" placeholder="info@business.com"></td>
        </tr>
        <tr>
            <th><label for="business_website">אתר אינטרנט</label></th>
            <td><input type="url" id="business_website" name="business_website" value="<?php echo esc_attr($website); ?>" class="regular-text" placeholder="https://example.com"></td>
        </tr>
    </table>
    <?php
}

// Meta Box - מיקום וכתובת
function shlomi_business_location_meta_box($post)
{
    $address = get_post_meta($post->ID, '_business_address', true);
    $city = get_post_meta($post->ID, '_business_city', true);
    $zip = get_post_meta($post->ID, '_business_zip', true);
    $lat = get_post_meta($post->ID, '_business_lat', true);
    $lng = get_post_meta($post->ID, '_business_lng', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="business_address">כתובת רחוב</label></th>
            <td><input type="text" id="business_address" name="business_address" value="<?php echo esc_attr($address); ?>" class="regular-text" placeholder="רח' הראשונים 5"></td>
        </tr>
        <tr>
            <th><label for="business_city">עיר</label></th>
            <td><input type="text" id="business_city" name="business_city" value="<?php echo esc_attr($city); ?>" class="regular-text" placeholder="שלומי"></td>
        </tr>
        <tr>
            <th><label for="business_zip">מיקוד</label></th>
            <td><input type="text" id="business_zip" name="business_zip" value="<?php echo esc_attr($zip); ?>" class="regular-text" placeholder="22832"></td>
        </tr>
        <tr>
            <th colspan="2"><strong>קואורדינטות GPS (למפה)</strong></th>
        </tr>
        <tr>
            <th><label for="business_lat">Latitude (רוחב)</label></th>
            <td><input type="text" id="business_lat" name="business_lat" value="<?php echo esc_attr($lat); ?>" class="regular-text" placeholder="33.0716"></td>
        </tr>
        <tr>
            <th><label for="business_lng">Longitude (אורך)</label></th>
            <td><input type="text" id="business_lng" name="business_lng" value="<?php echo esc_attr($lng); ?>" class="regular-text" placeholder="35.1547"></td>
        </tr>
        <tr>
            <td colspan="2">
                <p class="description">
                    💡 <strong>טיפ:</strong> כדי למצוא קואורדינטות, פתח <a href="https://www.openstreetmap.org/" target="_blank">OpenStreetMap</a>,
                    חפש את הכתובת, לחץ ימני על המיקום ובחר "Show address" - הקואורדינטות יופיעו בכתובת URL.
                </p>
            </td>
        </tr>
    </table>
    <?php
}

// Meta Box - שעות פעילות
function shlomi_business_hours_meta_box($post)
{
    $days = array(
        'sunday' => 'ראשון',
        'monday' => 'שני',
        'tuesday' => 'שלישי',
        'wednesday' => 'רביעי',
        'thursday' => 'חמישי',
        'friday' => 'שישי',
        'saturday' => 'שבת'
    );
    ?>
    <table class="form-table">
        <?php foreach ($days as $day_key => $day_name):
            $hours = get_post_meta($post->ID, '_business_hours_' . $day_key, true);
        ?>
        <tr>
            <th><label for="business_hours_<?php echo $day_key; ?>"><?php echo $day_name; ?></label></th>
            <td>
                <input type="text" id="business_hours_<?php echo $day_key; ?>"
                       name="business_hours_<?php echo $day_key; ?>"
                       value="<?php echo esc_attr($hours); ?>"
                       class="regular-text"
                       placeholder="09:00-18:00 או 'סגור'">
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p class="description">השאר ריק אם העסק פתוח 24/7, או כתוב "סגור" ליום סגור.</p>
    <?php
}

// Meta Box - רשתות חברתיות
function shlomi_business_social_meta_box($post)
{
    $facebook = get_post_meta($post->ID, '_business_facebook', true);
    $instagram = get_post_meta($post->ID, '_business_instagram', true);
    $whatsapp = get_post_meta($post->ID, '_business_whatsapp', true);
    ?>
    <p>
        <label for="business_facebook"><strong>📘 פייסבוק</strong></label>
        <input type="url" id="business_facebook" name="business_facebook" value="<?php echo esc_attr($facebook); ?>" class="widefat" placeholder="https://facebook.com/...">
    </p>
    <p>
        <label for="business_instagram"><strong>📷 אינסטגרם</strong></label>
        <input type="url" id="business_instagram" name="business_instagram" value="<?php echo esc_attr($instagram); ?>" class="widefat" placeholder="https://instagram.com/...">
    </p>
    <p>
        <label for="business_whatsapp"><strong>💬 WhatsApp</strong></label>
        <input type="tel" id="business_whatsapp" name="business_whatsapp" value="<?php echo esc_attr($whatsapp); ?>" class="widefat" placeholder="972541234567">
        <span class="description">מספר בפורמט בינלאומי (ללא +)</span>
    </p>
    <?php
}

// Meta Box - מידע נוסף
function shlomi_business_extra_meta_box($post)
{
    $rating = get_post_meta($post->ID, '_business_rating', true);
    $badge = get_post_meta($post->ID, '_business_badge', true);
    $featured = get_post_meta($post->ID, '_business_featured', true);
    ?>
    <p>
        <label for="business_rating"><strong>⭐ דירוג</strong></label>
        <select id="business_rating" name="business_rating" class="widefat">
            <option value="">ללא דירוג</option>
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <option value="<?php echo $i; ?>" <?php selected($rating, $i); ?>>
                <?php echo str_repeat('⭐', $i); ?>
            </option>
            <?php endfor; ?>
        </select>
    </p>
    <p>
        <label for="business_badge"><strong>🏷️ תווית מיוחדת</strong></label>
        <select id="business_badge" name="business_badge" class="widefat">
            <option value="">ללא תווית</option>
            <option value="VIP" <?php selected($badge, 'VIP'); ?>>VIP</option>
            <option value="NEW" <?php selected($badge, 'NEW'); ?>>חדש</option>
            <option value="RECOMMENDED" <?php selected($badge, 'RECOMMENDED'); ?>>מומלץ</option>
            <option value="POPULAR" <?php selected($badge, 'POPULAR'); ?>>פופולרי</option>
        </select>
    </p>
    <p>
        <label>
            <input type="checkbox" name="business_featured" value="1" <?php checked($featured, '1'); ?>>
            <strong>📌 עסק מודגש (יופיע למעלה)</strong>
        </label>
    </p>
    <?php
}

// שמירת Meta Data
function shlomi_save_business_meta($post_id)
{
    // בדיקות אבטחה
    if (!isset($_POST['shlomi_business_nonce']) ||
        !wp_verify_nonce($_POST['shlomi_business_nonce'], 'shlomi_business_meta_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // שדות ליצירת קשר
    $contact_fields = array('phone', 'mobile', 'email', 'website');
    foreach ($contact_fields as $field) {
        if (isset($_POST['business_' . $field])) {
            update_post_meta($post_id, '_business_' . $field, sanitize_text_field($_POST['business_' . $field]));
        }
    }

    // שדות מיקום
    $location_fields = array('address', 'city', 'zip', 'lat', 'lng');
    foreach ($location_fields as $field) {
        if (isset($_POST['business_' . $field])) {
            update_post_meta($post_id, '_business_' . $field, sanitize_text_field($_POST['business_' . $field]));
        }
    }

    // שעות פעילות
    $days = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
    foreach ($days as $day) {
        if (isset($_POST['business_hours_' . $day])) {
            update_post_meta($post_id, '_business_hours_' . $day, sanitize_text_field($_POST['business_hours_' . $day]));
        }
    }

    // רשתות חברתיות
    $social_fields = array('facebook', 'instagram', 'whatsapp');
    foreach ($social_fields as $field) {
        if (isset($_POST['business_' . $field])) {
            update_post_meta($post_id, '_business_' . $field, sanitize_text_field($_POST['business_' . $field]));
        }
    }

    // מידע נוסף
    if (isset($_POST['business_rating'])) {
        update_post_meta($post_id, '_business_rating', sanitize_text_field($_POST['business_rating']));
    }
    if (isset($_POST['business_badge'])) {
        update_post_meta($post_id, '_business_badge', sanitize_text_field($_POST['business_badge']));
    }

    $featured = isset($_POST['business_featured']) ? '1' : '0';
    update_post_meta($post_id, '_business_featured', $featured);
}
add_action('save_post_business', 'shlomi_save_business_meta');

// טעינת Leaflet.js (OpenStreetMap) ו-CSS לעסקים
function shlomi_business_scripts()
{
    // טעינה רק בדפי עסקים או בדפים שמכילים shortcode
    if (is_singular('business') || is_post_type_archive('business') || is_tax('business_category') ||
        (is_page() && (has_shortcode(get_post()->post_content, 'business_list') ||
                       has_shortcode(get_post()->post_content, 'business_map')))) {

        // Leaflet CSS
        wp_enqueue_style(
            'leaflet-css',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
            array(),
            '1.9.4'
        );

        // Leaflet JS
        wp_enqueue_script(
            'leaflet-js',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
            array(),
            '1.9.4',
            true
        );

        // Business Map Script
        wp_enqueue_script(
            'shlomi-business-map',
            get_template_directory_uri() . '/js/business-map.js',
            array('jquery', 'leaflet-js'),
            '1.0',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'shlomi_business_scripts');

// ייבוא עסקים מ-JSON
function shlomi_import_businesses_from_json($json_file_path)
{
    if (!file_exists($json_file_path)) {
        return new WP_Error('file_not_found', 'קובץ JSON לא נמצא');
    }

    $json_content = file_get_contents($json_file_path);
    $data = json_decode($json_content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return new WP_Error('json_error', 'שגיאה בפענוח JSON: ' . json_last_error_msg());
    }

    if (!isset($data['businesses']) || !is_array($data['businesses'])) {
        return new WP_Error('invalid_format', 'פורמט JSON לא תקין - חסר מערך businesses');
    }

    $imported = 0;
    $errors = array();

    foreach ($data['businesses'] as $business_data) {
        // ולידציה בסיסית
        if (empty($business_data['title'])) {
            $errors[] = 'עסק ללא כותרת דולג';
            continue;
        }

        // יצירת פוסט חדש
        $post_data = array(
            'post_title' => sanitize_text_field($business_data['title']),
            'post_content' => isset($business_data['description']) ? wp_kses_post($business_data['description']) : '',
            'post_status' => 'publish',
            'post_type' => 'business',
        );

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            $errors[] = 'שגיאה ביצירת עסק: ' . $business_data['title'];
            continue;
        }

        // הוספת קטגוריה
        if (!empty($business_data['category'])) {
            $term = term_exists($business_data['category'], 'business_category');
            if (!$term) {
                $term = wp_insert_term($business_data['category'], 'business_category');
            }
            if (!is_wp_error($term)) {
                wp_set_post_terms($post_id, array($term['term_id']), 'business_category');
            }
        }

        // פרטי קשר
        if (!empty($business_data['phone'])) {
            update_post_meta($post_id, '_business_phone', sanitize_text_field($business_data['phone']));
        }
        if (!empty($business_data['mobile'])) {
            update_post_meta($post_id, '_business_mobile', sanitize_text_field($business_data['mobile']));
        }
        if (!empty($business_data['email'])) {
            update_post_meta($post_id, '_business_email', sanitize_email($business_data['email']));
        }
        if (!empty($business_data['website'])) {
            update_post_meta($post_id, '_business_website', esc_url_raw($business_data['website']));
        }

        // כתובת
        if (!empty($business_data['address'])) {
            if (is_array($business_data['address'])) {
                update_post_meta($post_id, '_business_address', sanitize_text_field($business_data['address']['street'] ?? ''));
                update_post_meta($post_id, '_business_city', sanitize_text_field($business_data['address']['city'] ?? ''));
                update_post_meta($post_id, '_business_zip', sanitize_text_field($business_data['address']['zip'] ?? ''));
            }
        }

        // קואורדינטות
        if (!empty($business_data['coordinates'])) {
            update_post_meta($post_id, '_business_lat', sanitize_text_field($business_data['coordinates']['lat'] ?? ''));
            update_post_meta($post_id, '_business_lng', sanitize_text_field($business_data['coordinates']['lng'] ?? ''));
        }

        // שעות פעילות
        if (!empty($business_data['hours']) && is_array($business_data['hours'])) {
            foreach ($business_data['hours'] as $day => $hours) {
                update_post_meta($post_id, '_business_hours_' . $day, sanitize_text_field($hours));
            }
        }

        // רשתות חברתיות
        if (!empty($business_data['social'])) {
            if (!empty($business_data['social']['facebook'])) {
                update_post_meta($post_id, '_business_facebook', esc_url_raw($business_data['social']['facebook']));
            }
            if (!empty($business_data['social']['instagram'])) {
                update_post_meta($post_id, '_business_instagram', esc_url_raw($business_data['social']['instagram']));
            }
            if (!empty($business_data['social']['whatsapp'])) {
                update_post_meta($post_id, '_business_whatsapp', sanitize_text_field($business_data['social']['whatsapp']));
            }
        }

        // מידע נוסף
        if (!empty($business_data['rating'])) {
            update_post_meta($post_id, '_business_rating', intval($business_data['rating']));
        }
        if (!empty($business_data['badge'])) {
            update_post_meta($post_id, '_business_badge', sanitize_text_field($business_data['badge']));
        }

        // תמונת לוגו
        if (!empty($business_data['logo'])) {
            shlomi_set_business_thumbnail_from_url($post_id, $business_data['logo']);
        }

        $imported++;
    }

    return array(
        'success' => true,
        'imported' => $imported,
        'errors' => $errors
    );
}

// פונקציה עוזרת להוספת תמונה מ-URL
function shlomi_set_business_thumbnail_from_url($post_id, $image_url)
{
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $tmp = download_url($image_url);
    if (is_wp_error($tmp)) {
        return false;
    }

    $file_array = array(
        'name' => basename($image_url),
        'tmp_name' => $tmp
    );

    $id = media_handle_sideload($file_array, $post_id);

    if (is_wp_error($id)) {
        @unlink($file_array['tmp_name']);
        return false;
    }

    set_post_thumbnail($post_id, $id);
    return true;
}

// Admin Page לייבוא JSON
function shlomi_business_import_menu()
{
    add_submenu_page(
        'edit.php?post_type=business',
        'ייבוא עסקים מ-JSON',
        'ייבוא JSON',
        'manage_options',
        'business-import-json',
        'shlomi_business_import_page'
    );
}
add_action('admin_menu', 'shlomi_business_import_menu');

// עמוד הייבוא
function shlomi_business_import_page()
{
    ?>
    <div class="wrap">
        <h1>ייבוא עסקים מקומיים מ-JSON</h1>

        <?php
        if (isset($_POST['import_json']) && check_admin_referer('import_business_json')) {
            if (!empty($_FILES['json_file']['tmp_name'])) {
                $result = shlomi_import_businesses_from_json($_FILES['json_file']['tmp_name']);

                if (is_wp_error($result)) {
                    echo '<div class="notice notice-error"><p>' . $result->get_error_message() . '</p></div>';
                } else {
                    echo '<div class="notice notice-success"><p>';
                    echo 'ייובאו בהצלחה ' . $result['imported'] . ' עסקים!';
                    if (!empty($result['errors'])) {
                        echo '<br>שגיאות: <ul>';
                        foreach ($result['errors'] as $error) {
                            echo '<li>' . esc_html($error) . '</li>';
                        }
                        echo '</ul>';
                    }
                    echo '</p></div>';
                }
            }
        }
        ?>

        <div class="card">
            <h2>העלה קובץ JSON</h2>
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('import_business_json'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="json_file">בחר קובץ JSON</label></th>
                        <td>
                            <input type="file" name="json_file" id="json_file" accept=".json" required>
                            <p class="description">העלה קובץ JSON בפורמט המתאים</p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="import_json" class="button button-primary" value="ייבא עסקים">
                </p>
            </form>
        </div>

        <div class="card" style="margin-top: 20px;">
            <h2>📋 פורמט JSON נדרש</h2>
            <p>הקובץ צריך להיות בפורמט הבא:</p>
            <pre style="background: #f5f5f5; padding: 15px; direction: ltr; text-align: left; overflow-x: auto;">{
  "businesses": [
    {
      "title": "שם העסק",
      "category": "קטגוריה",
      "description": "תיאור העסק",
      "phone": "04-1234567",
      "mobile": "054-1234567",
      "email": "info@business.com",
      "website": "https://example.com",
      "address": {
        "street": "רח' הראשונים 15",
        "city": "שלומי",
        "zip": "22832"
      },
      "coordinates": {
        "lat": 33.0716,
        "lng": 35.1547
      },
      "hours": {
        "sunday": "09:00-18:00",
        "monday": "09:00-18:00",
        "tuesday": "09:00-18:00",
        "wednesday": "09:00-18:00",
        "thursday": "09:00-18:00",
        "friday": "09:00-15:00",
        "saturday": "סגור"
      },
      "social": {
        "facebook": "https://facebook.com/business",
        "instagram": "https://instagram.com/business",
        "whatsapp": "972541234567"
      },
      "rating": 5,
      "badge": "VIP",
      "logo": "https://example.com/logo.jpg"
    }
  ]
}</pre>
            <p><a href="<?php echo get_template_directory_uri(); ?>/sample-businesses.json" class="button" download>💾 הורד קובץ דוגמה</a></p>
        </div>
    </div>
    <?php
}

// Shortcode - רשימת עסקים
function shlomi_business_list_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'category' => '',
        'limit' => 12,
        'view' => 'grid', // grid or list
        'featured' => false
    ), $atts);

    $args = array(
        'post_type' => 'business',
        'posts_per_page' => intval($atts['limit']),
        'orderby' => 'date',
        'order' => 'DESC'
    );

    if (!empty($atts['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'business_category',
                'field' => 'slug',
                'terms' => $atts['category']
            )
        );
    }

    if ($atts['featured']) {
        $args['meta_query'] = array(
            array(
                'key' => '_business_featured',
                'value' => '1'
            )
        );
    }

    $businesses = new WP_Query($args);

    ob_start();
    ?>
    <div class="business-directory business-<?php echo esc_attr($atts['view']); ?>-view">
        <?php if ($businesses->have_posts()): ?>
            <div class="business-grid">
                <?php while ($businesses->have_posts()): $businesses->the_post(); ?>
                    <?php get_template_part('template-parts/content', 'business-card'); ?>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="no-businesses">לא נמצאו עסקים.</p>
        <?php endif; ?>
    </div>
    <?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('business_list', 'shlomi_business_list_shortcode');

// Shortcode - מפת עסקים
function shlomi_business_map_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'height' => '500px',
        'zoom' => 13,
        'center_lat' => 33.0716,
        'center_lng' => 35.1547
    ), $atts);

    $businesses = new WP_Query(array(
        'post_type' => 'business',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_business_lat',
                'compare' => 'EXISTS'
            ),
            array(
                'key' => '_business_lng',
                'compare' => 'EXISTS'
            )
        )
    ));

    $markers = array();
    if ($businesses->have_posts()) {
        while ($businesses->have_posts()) {
            $businesses->the_post();
            $lat = get_post_meta(get_the_ID(), '_business_lat', true);
            $lng = get_post_meta(get_the_ID(), '_business_lng', true);

            if ($lat && $lng) {
                $markers[] = array(
                    'lat' => floatval($lat),
                    'lng' => floatval($lng),
                    'title' => get_the_title(),
                    'url' => get_permalink()
                );
            }
        }
        wp_reset_postdata();
    }

    $map_id = 'business-map-' . uniqid();

    ob_start();
    ?>
    <div id="<?php echo esc_attr($map_id); ?>" class="business-map" style="height: <?php echo esc_attr($atts['height']); ?>; width: 100%; border-radius: 10px;"></div>
    <script>
    jQuery(document).ready(function($) {
        if (typeof L !== 'undefined') {
            var map = L.map('<?php echo $map_id; ?>').setView([<?php echo $atts['center_lat']; ?>, <?php echo $atts['center_lng']; ?>], <?php echo $atts['zoom']; ?>);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            var markers = <?php echo json_encode($markers); ?>;
            markers.forEach(function(markerData) {
                var marker = L.marker([markerData.lat, markerData.lng]).addTo(map);
                marker.bindPopup('<strong><a href="' + markerData.url + '">' + markerData.title + '</a></strong>');
            });
        }
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('business_map', 'shlomi_business_map_shortcode');

/* ========================================
   מערכת ביקורות ודירוגים - Reviews System (תוכנית C)
   ======================================== */

// רישום Custom Post Type - ביקורות
function shlomi_register_review_post_type()
{
    $labels = array(
        'name' => 'ביקורות',
        'singular_name' => 'ביקורת',
        'add_new' => 'הוסף ביקורת',
        'add_new_item' => 'הוסף ביקורת חדשה',
        'edit_item' => 'ערוך ביקורת',
        'new_item' => 'ביקורת חדשה',
        'view_item' => 'צפה בביקורת',
        'search_items' => 'חפש ביקורות',
        'not_found' => 'לא נמצאו ביקורות',
        'all_items' => 'כל הביקורות',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => 'edit.php?post_type=business',
        'query_var' => true,
        'rewrite' => array('slug' => 'review'),
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'supports' => array('title', 'editor', 'author'),
        'show_in_rest' => true,
    );

    register_post_type('review', $args);
}
add_action('init', 'shlomi_register_review_post_type');

// Meta Box - פרטי ביקורת
function shlomi_add_review_meta_boxes()
{
    add_meta_box(
        'review_details',
        'פרטי הביקורת',
        'shlomi_review_details_callback',
        'review',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'shlomi_add_review_meta_boxes');

function shlomi_review_details_callback($post)
{
    wp_nonce_field('shlomi_save_review_meta', 'shlomi_review_meta_nonce');

    $business_id = get_post_meta($post->ID, '_review_business_id', true);
    $rating = get_post_meta($post->ID, '_review_rating', true);
    $reviewer_name = get_post_meta($post->ID, '_review_name', true);
    $reviewer_email = get_post_meta($post->ID, '_review_email', true);
    $verified = get_post_meta($post->ID, '_review_verified', true);

    $businesses = get_posts(array('post_type' => 'business', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC'));
    ?>
    <table class="form-table">
        <tr>
            <th><label for="review_business_id">עסק:</label></th>
            <td>
                <select name="review_business_id" id="review_business_id" style="width: 300px;">
                    <option value="">בחר עסק...</option>
                    <?php foreach ($businesses as $business): ?>
                        <option value="<?php echo $business->ID; ?>" <?php selected($business_id, $business->ID); ?>>
                            <?php echo esc_html($business->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="review_rating">דירוג (1-5 כוכבים):</label></th>
            <td>
                <select name="review_rating" id="review_rating">
                    <option value="">בחר דירוג...</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php selected($rating, $i); ?>>
                            <?php echo str_repeat('⭐', $i) . ' - ' . $i . ' כוכבים'; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="review_name">שם המבקר:</label></th>
            <td><input type="text" name="review_name" id="review_name" value="<?php echo esc_attr($reviewer_name); ?>" style="width: 300px;"></td>
        </tr>
        <tr>
            <th><label for="review_email">אימייל המבקר:</label></th>
            <td><input type="email" name="review_email" id="review_email" value="<?php echo esc_attr($reviewer_email); ?>" style="width: 300px;"></td>
        </tr>
        <tr>
            <th><label for="review_verified">ביקורת מאומתת:</label></th>
            <td>
                <input type="checkbox" name="review_verified" id="review_verified" value="1" <?php checked($verified, '1'); ?>>
                <span class="description">סמן אם זו ביקורת מאומתת (לקוח אמיתי)</span>
            </td>
        </tr>
    </table>
    <?php
}

function shlomi_save_review_meta($post_id)
{
    if (!isset($_POST['shlomi_review_meta_nonce']) || !wp_verify_nonce($_POST['shlomi_review_meta_nonce'], 'shlomi_save_review_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = array('review_business_id', 'review_rating', 'review_name', 'review_email', 'review_verified');

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }

    // עדכון דירוג ממוצע של העסק
    if (isset($_POST['review_business_id']) && !empty($_POST['review_business_id'])) {
        shlomi_update_business_average_rating($_POST['review_business_id']);
    }
}
add_action('save_post_review', 'shlomi_save_review_meta');

// עדכון דירוג ממוצע של עסק
function shlomi_update_business_average_rating($business_id)
{
    $reviews = new WP_Query(array(
        'post_type' => 'review',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_review_business_id',
                'value' => $business_id,
            )
        )
    ));

    $total_rating = 0;
    $count = 0;

    if ($reviews->have_posts()) {
        while ($reviews->have_posts()) {
            $reviews->the_post();
            $rating = get_post_meta(get_the_ID(), '_review_rating', true);
            if ($rating) {
                $total_rating += intval($rating);
                $count++;
            }
        }
        wp_reset_postdata();
    }

    if ($count > 0) {
        $average = round($total_rating / $count, 1);
        update_post_meta($business_id, '_business_rating', $average);
        update_post_meta($business_id, '_business_review_count', $count);
    }
}

// פונקציה להצגת ביקורות של עסק
function shlomi_get_business_reviews($business_id, $limit = -1)
{
    $reviews = new WP_Query(array(
        'post_type' => 'review',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
        'meta_query' => array(
            array(
                'key' => '_review_business_id',
                'value' => $business_id,
            )
        )
    ));

    return $reviews;
}

/* ========================================
   גלריית תמונות - Image Gallery
   ======================================== */

// Meta Box - גלריית תמונות
function shlomi_add_gallery_meta_box()
{
    add_meta_box(
        'business_gallery',
        '📸 גלריית תמונות העסק',
        'shlomi_gallery_meta_box_callback',
        'business',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'shlomi_add_gallery_meta_box');

function shlomi_gallery_meta_box_callback($post)
{
    wp_nonce_field('shlomi_save_gallery_meta', 'shlomi_gallery_meta_nonce');
    $gallery_images = get_post_meta($post->ID, '_business_gallery', true);
    ?>
    <div class="business-gallery-container">
        <div id="business-gallery-images" class="business-gallery-images">
            <?php if (!empty($gallery_images)): ?>
                <?php foreach ($gallery_images as $image_id): ?>
                    <div class="gallery-image-item" data-id="<?php echo $image_id; ?>">
                        <?php echo wp_get_attachment_image($image_id, 'thumbnail'); ?>
                        <button type="button" class="remove-gallery-image">×</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <input type="hidden" name="business_gallery" id="business_gallery" value="<?php echo esc_attr(implode(',', (array) $gallery_images)); ?>">
        <button type="button" class="button" id="add-gallery-images">➕ הוסף תמונות לגלריה</button>
        <p class="description">העלה תמונות של העסק - מוצרים, שירותים, אווירה ועוד</p>
    </div>
    <style>
        .business-gallery-images {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            margin-bottom: 15px;
        }
        .gallery-image-item {
            position: relative;
            border: 2px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
        .gallery-image-item img {
            display: block;
            width: 100%;
            height: 100px;
            object-fit: cover;
        }
        .remove-gallery-image {
            position: absolute;
            top: 0;
            right: 0;
            background: #e74c3c;
            color: white;
            border: none;
            width: 25px;
            height: 25px;
            cursor: pointer;
            font-size: 20px;
            line-height: 1;
        }
    </style>
    <script>
    jQuery(document).ready(function($) {
        var galleryFrame;

        $('#add-gallery-images').on('click', function(e) {
            e.preventDefault();

            if (galleryFrame) {
                galleryFrame.open();
                return;
            }

            galleryFrame = wp.media({
                title: 'בחר תמונות לגלריה',
                button: {
                    text: 'הוסף לגלריה'
                },
                multiple: true
            });

            galleryFrame.on('select', function() {
                var selection = galleryFrame.state().get('selection');
                var currentIds = $('#business_gallery').val().split(',').filter(Boolean);

                selection.each(function(attachment) {
                    var id = attachment.id;
                    var thumbnail = attachment.attributes.sizes.thumbnail || attachment.attributes.sizes.full;

                    if (currentIds.indexOf(id.toString()) === -1) {
                        currentIds.push(id);

                        var html = '<div class="gallery-image-item" data-id="' + id + '">' +
                                  '<img src="' + thumbnail.url + '">' +
                                  '<button type="button" class="remove-gallery-image">×</button>' +
                                  '</div>';

                        $('#business-gallery-images').append(html);
                    }
                });

                $('#business_gallery').val(currentIds.join(','));
            });

            galleryFrame.open();
        });

        $(document).on('click', '.remove-gallery-image', function() {
            var item = $(this).closest('.gallery-image-item');
            var id = item.data('id');
            var currentIds = $('#business_gallery').val().split(',').filter(Boolean);
            var index = currentIds.indexOf(id.toString());

            if (index > -1) {
                currentIds.splice(index, 1);
            }

            $('#business_gallery').val(currentIds.join(','));
            item.remove();
        });
    });
    </script>
    <?php
}

function shlomi_save_gallery_meta($post_id)
{
    if (!isset($_POST['shlomi_gallery_meta_nonce']) || !wp_verify_nonce($_POST['shlomi_gallery_meta_nonce'], 'shlomi_save_gallery_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['business_gallery'])) {
        $gallery = array_filter(array_map('intval', explode(',', $_POST['business_gallery'])));
        update_post_meta($post_id, '_business_gallery', $gallery);
    } else {
        delete_post_meta($post_id, '_business_gallery');
    }
}
add_action('save_post_business', 'shlomi_save_gallery_meta');

/* ========================================
   מערכת קופונים והנחות - Coupons System
   ======================================== */

// רישום Custom Post Type - קופונים
function shlomi_register_coupon_post_type()
{
    $labels = array(
        'name' => 'קופונים והנחות',
        'singular_name' => 'קופון',
        'add_new' => 'הוסף קופון',
        'add_new_item' => 'הוסף קופון חדש',
        'edit_item' => 'ערוך קופון',
        'new_item' => 'קופון חדש',
        'view_item' => 'צפה בקופון',
        'search_items' => 'חפש קופונים',
        'not_found' => 'לא נמצאו קופונים',
        'all_items' => 'כל הקופונים',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => 'edit.php?post_type=business',
        'query_var' => true,
        'rewrite' => array('slug' => 'coupon'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_icon' => 'dashicons-tickets-alt',
        'supports' => array('title', 'editor', 'thumbnail'),
        'show_in_rest' => true,
    );

    register_post_type('coupon', $args);
}
add_action('init', 'shlomi_register_coupon_post_type');

// Meta Box - פרטי קופון
function shlomi_add_coupon_meta_boxes()
{
    add_meta_box(
        'coupon_details',
        '🎟️ פרטי הקופון',
        'shlomi_coupon_details_callback',
        'coupon',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'shlomi_add_coupon_meta_boxes');

function shlomi_coupon_details_callback($post)
{
    wp_nonce_field('shlomi_save_coupon_meta', 'shlomi_coupon_meta_nonce');

    $business_id = get_post_meta($post->ID, '_coupon_business_id', true);
    $discount = get_post_meta($post->ID, '_coupon_discount', true);
    $code = get_post_meta($post->ID, '_coupon_code', true);
    $expiry = get_post_meta($post->ID, '_coupon_expiry', true);
    $terms = get_post_meta($post->ID, '_coupon_terms', true);

    $businesses = get_posts(array('post_type' => 'business', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC'));
    ?>
    <table class="form-table">
        <tr>
            <th><label for="coupon_business_id">עסק:</label></th>
            <td>
                <select name="coupon_business_id" id="coupon_business_id" style="width: 300px;">
                    <option value="">בחר עסק...</option>
                    <?php foreach ($businesses as $business): ?>
                        <option value="<?php echo $business->ID; ?>" <?php selected($business_id, $business->ID); ?>>
                            <?php echo esc_html($business->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="coupon_discount">גובה ההנחה:</label></th>
            <td>
                <input type="text" name="coupon_discount" id="coupon_discount" value="<?php echo esc_attr($discount); ?>" placeholder="לדוגמה: 20% או 50 ₪" style="width: 200px;">
                <p class="description">לדוגמה: "20%", "50 ₪", "קנה 1 קבל 2"</p>
            </td>
        </tr>
        <tr>
            <th><label for="coupon_code">קוד קופון:</label></th>
            <td>
                <input type="text" name="coupon_code" id="coupon_code" value="<?php echo esc_attr($code); ?>" placeholder="SUMMER2024" style="width: 200px; text-transform: uppercase;">
                <p class="description">קוד ייחודי להזנה בעסק (אופציונלי)</p>
            </td>
        </tr>
        <tr>
            <th><label for="coupon_expiry">תוקף עד:</label></th>
            <td>
                <input type="date" name="coupon_expiry" id="coupon_expiry" value="<?php echo esc_attr($expiry); ?>" style="width: 200px;">
            </td>
        </tr>
        <tr>
            <th><label for="coupon_terms">תנאי השימוש:</label></th>
            <td>
                <textarea name="coupon_terms" id="coupon_terms" rows="4" style="width: 100%;"><?php echo esc_textarea($terms); ?></textarea>
                <p class="description">לדוגמה: "בתוקף בימים א'-ה' בלבד", "לא כולל משקאות"</p>
            </td>
        </tr>
    </table>
    <?php
}

function shlomi_save_coupon_meta($post_id)
{
    if (!isset($_POST['shlomi_coupon_meta_nonce']) || !wp_verify_nonce($_POST['shlomi_coupon_meta_nonce'], 'shlomi_save_coupon_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = array('coupon_business_id', 'coupon_discount', 'coupon_code', 'coupon_expiry', 'coupon_terms');

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post_coupon', 'shlomi_save_coupon_meta');

// פונקציה להצגת קופונים של עסק
function shlomi_get_business_coupons($business_id, $active_only = true)
{
    $args = array(
        'post_type' => 'coupon',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_coupon_business_id',
                'value' => $business_id,
            )
        )
    );

    if ($active_only) {
        $args['meta_query'][] = array(
            'relation' => 'OR',
            array(
                'key' => '_coupon_expiry',
                'value' => date('Y-m-d'),
                'compare' => '>=',
                'type' => 'DATE'
            ),
            array(
                'key' => '_coupon_expiry',
                'compare' => 'NOT EXISTS'
            )
        );
    }

    return new WP_Query($args);
}

/* ========================================
   מערכת חיפוש מתקדם - Advanced Search
   ======================================== */

// Shortcode - חיפוש עסקים מתקדם
function shlomi_business_search_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'show_filters' => 'yes',
        'results_per_page' => 12
    ), $atts);

    ob_start();
    ?>
    <div class="business-advanced-search">
        <?php if ($atts['show_filters'] === 'yes'): ?>
        <div class="search-filters">
            <h3>🔍 חיפוש עסקים</h3>
            <form id="business-search-form" method="get">
                <div class="search-row">
                    <div class="search-field">
                        <label for="search_keyword">מילות חיפוש:</label>
                        <input type="text" id="search_keyword" name="keyword" placeholder="שם עסק, תיאור...">
                    </div>

                    <div class="search-field">
                        <label for="search_category">קטגוריה:</label>
                        <select id="search_category" name="category">
                            <option value="">כל הקטגוריות</option>
                            <?php
                            $categories = get_terms(array('taxonomy' => 'business_category', 'hide_empty' => false));
                            foreach ($categories as $category):
                                $icon = get_term_meta($category->term_id, 'category_icon', true);
                            ?>
                                <option value="<?php echo $category->slug; ?>">
                                    <?php echo $icon ? $icon . ' ' : ''; ?><?php echo esc_html($category->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="search-row">
                    <div class="search-field">
                        <label for="search_city">עיר:</label>
                        <input type="text" id="search_city" name="city" placeholder="שם העיר">
                    </div>

                    <div class="search-field">
                        <label for="search_rating">דירוג מינימלי:</label>
                        <select id="search_rating" name="min_rating">
                            <option value="">כל הדירוגים</option>
                            <option value="5">⭐⭐⭐⭐⭐ 5 כוכבים</option>
                            <option value="4">⭐⭐⭐⭐ 4+ כוכבים</option>
                            <option value="3">⭐⭐⭐ 3+ כוכבים</option>
                        </select>
                    </div>
                </div>

                <div class="search-row">
                    <div class="search-field">
                        <label>
                            <input type="checkbox" name="open_now" value="1"> רק עסקים פתוחים כעת
                        </label>
                    </div>

                    <div class="search-field">
                        <label>
                            <input type="checkbox" name="has_coupons" value="1"> רק עם קופונים פעילים
                        </label>
                    </div>
                </div>

                <div class="search-actions">
                    <button type="submit" class="btn btn-search">חפש עסקים</button>
                    <button type="reset" class="btn btn-reset">נקה סינון</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <div id="search-results" class="search-results">
            <div class="business-grid">
                <?php
                // תוצאות ברירת מחדל - כל העסקים
                $default_query = new WP_Query(array(
                    'post_type' => 'business',
                    'posts_per_page' => $atts['results_per_page'],
                    'post_status' => 'publish'
                ));

                if ($default_query->have_posts()):
                    while ($default_query->have_posts()):
                        $default_query->the_post();
                        get_template_part('template-parts/content', 'business-card');
                    endwhile;
                    wp_reset_postdata();
                else:
                    echo '<p class="no-results">לא נמצאו עסקים.</p>';
                endif;
                ?>
            </div>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('#business-search-form').on('submit', function(e) {
            e.preventDefault();

            var formData = $(this).serialize();

            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: formData + '&action=business_search',
                beforeSend: function() {
                    $('#search-results').html('<div class="loading">טוען תוצאות...</div>');
                },
                success: function(response) {
                    $('#search-results').html(response);
                },
                error: function() {
                    $('#search-results').html('<p class="error">אירעה שגיאה בחיפוש. אנא נסה שנית.</p>');
                }
            });
        });

        $('button[type="reset"]').on('click', function() {
            $('#business-search-form')[0].reset();
            $('#business-search-form').submit();
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('business_search', 'shlomi_business_search_shortcode');

// Ajax handler - חיפוש עסקים
function shlomi_business_search_ajax()
{
    $args = array(
        'post_type' => 'business',
        'posts_per_page' => 12,
        'post_status' => 'publish'
    );

    // חיפוש טקסט
    if (!empty($_POST['keyword'])) {
        $args['s'] = sanitize_text_field($_POST['keyword']);
    }

    // סינון לפי קטגוריה
    if (!empty($_POST['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'business_category',
                'field' => 'slug',
                'terms' => sanitize_text_field($_POST['category'])
            )
        );
    }

    $meta_query = array('relation' => 'AND');

    // סינון לפי עיר
    if (!empty($_POST['city'])) {
        $meta_query[] = array(
            'key' => '_business_city',
            'value' => sanitize_text_field($_POST['city']),
            'compare' => 'LIKE'
        );
    }

    // סינון לפי דירוג
    if (!empty($_POST['min_rating'])) {
        $meta_query[] = array(
            'key' => '_business_rating',
            'value' => floatval($_POST['min_rating']),
            'compare' => '>=',
            'type' => 'DECIMAL'
        );
    }

    if (!empty($meta_query) && count($meta_query) > 1) {
        $args['meta_query'] = $meta_query;
    }

    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) {
        echo '<div class="business-grid">';
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/content', 'business-card');
        }
        echo '</div>';

        if ($query->found_posts > 0) {
            echo '<p class="search-count">נמצאו ' . $query->found_posts . ' עסקים</p>';
        }
    } else {
        echo '<p class="no-results">לא נמצאו עסקים התואמים את החיפוש.</p>';
    }

    wp_reset_postdata();
    echo ob_get_clean();
    wp_die();
}
add_action('wp_ajax_business_search', 'shlomi_business_search_ajax');
add_action('wp_ajax_nopriv_business_search', 'shlomi_business_search_ajax');

/* ========================================
   דוחות וסטטיסטיקות - Statistics & Reports
   ======================================== */

// דף ניהול - סטטיסטיקות עסקים
function shlomi_add_statistics_page()
{
    add_submenu_page(
        'edit.php?post_type=business',
        'סטטיסטיקות ודוחות',
        '📊 סטטיסטיקות',
        'manage_options',
        'business-statistics',
        'shlomi_statistics_page_callback'
    );
}
add_action('admin_menu', 'shlomi_add_statistics_page');

function shlomi_statistics_page_callback()
{
    // ספירת עסקים
    $total_businesses = wp_count_posts('business')->publish;
    $total_reviews = wp_count_posts('review')->publish;
    $total_coupons = wp_count_posts('coupon')->publish;

    // עסקים לפי קטגוריה
    $categories = get_terms(array('taxonomy' => 'business_category', 'hide_empty' => false));

    // דירוג ממוצע
    $businesses = get_posts(array('post_type' => 'business', 'posts_per_page' => -1, 'post_status' => 'publish'));
    $total_rating = 0;
    $rated_businesses = 0;

    foreach ($businesses as $business) {
        $rating = get_post_meta($business->ID, '_business_rating', true);
        if ($rating) {
            $total_rating += floatval($rating);
            $rated_businesses++;
        }
    }

    $average_rating = $rated_businesses > 0 ? round($total_rating / $rated_businesses, 2) : 0;

    // עסקים עם הכי הרבה ביקורות
    $top_reviewed = get_posts(array(
        'post_type' => 'business',
        'posts_per_page' => 5,
        'meta_key' => '_business_review_count',
        'orderby' => 'meta_value_num',
        'order' => 'DESC'
    ));

    ?>
    <div class="wrap">
        <h1>📊 סטטיסטיקות ודוחות - מערכת עסקים מקומיים</h1>

        <div class="statistics-dashboard">
            <!-- סיכום כללי -->
            <div class="stats-overview">
                <div class="stat-card">
                    <div class="stat-icon">🏢</div>
                    <div class="stat-content">
                        <h3><?php echo number_format($total_businesses); ?></h3>
                        <p>סה"כ עסקים</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">⭐</div>
                    <div class="stat-content">
                        <h3><?php echo $average_rating; ?></h3>
                        <p>דירוג ממוצע</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">💬</div>
                    <div class="stat-content">
                        <h3><?php echo number_format($total_reviews); ?></h3>
                        <p>סה"כ ביקורות</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">🎟️</div>
                    <div class="stat-content">
                        <h3><?php echo number_format($total_coupons); ?></h3>
                        <p>קופונים פעילים</p>
                    </div>
                </div>
            </div>

            <!-- עסקים לפי קטגוריה -->
            <div class="stats-section">
                <h2>עסקים לפי קטגוריה</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>קטגוריה</th>
                            <th>מספר עסקים</th>
                            <th>אחוז</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category):
                            $count = $category->count;
                            $percentage = $total_businesses > 0 ? round(($count / $total_businesses) * 100, 1) : 0;
                            $icon = get_term_meta($category->term_id, 'category_icon', true);
                            $color = get_term_meta($category->term_id, 'category_color', true);
                        ?>
                            <tr>
                                <td>
                                    <span style="color: <?php echo esc_attr($color); ?>;">
                                        <?php echo $icon ? $icon . ' ' : ''; ?><?php echo esc_html($category->name); ?>
                                    </span>
                                </td>
                                <td><?php echo $count; ?></td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo $percentage; ?>%; background: <?php echo esc_attr($color); ?>;"></div>
                                        <span><?php echo $percentage; ?>%</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- עסקים מובילים -->
            <div class="stats-section">
                <h2>עסקים עם הכי הרבה ביקורות</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>עסק</th>
                            <th>דירוג</th>
                            <th>מספר ביקורות</th>
                            <th>קטגוריה</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_reviewed as $business):
                            $rating = get_post_meta($business->ID, '_business_rating', true);
                            $review_count = get_post_meta($business->ID, '_business_review_count', true);
                            $terms = get_the_terms($business->ID, 'business_category');
                            $category_name = !empty($terms) ? $terms[0]->name : '-';
                        ?>
                            <tr>
                                <td>
                                    <strong>
                                        <a href="<?php echo get_edit_post_link($business->ID); ?>">
                                            <?php echo esc_html($business->post_title); ?>
                                        </a>
                                    </strong>
                                </td>
                                <td><?php echo str_repeat('⭐', round($rating)); ?> (<?php echo $rating; ?>)</td>
                                <td><?php echo $review_count; ?></td>
                                <td><?php echo esc_html($category_name); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <style>
            .statistics-dashboard {
                margin-top: 20px;
            }
            .stats-overview {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin-bottom: 30px;
            }
            .stat-card {
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                display: flex;
                align-items: center;
                gap: 15px;
            }
            .stat-icon {
                font-size: 40px;
            }
            .stat-content h3 {
                margin: 0;
                font-size: 32px;
                color: #2271b1;
            }
            .stat-content p {
                margin: 5px 0 0 0;
                color: #666;
            }
            .stats-section {
                background: white;
                padding: 20px;
                margin-bottom: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .stats-section h2 {
                margin-top: 0;
                border-bottom: 2px solid #2271b1;
                padding-bottom: 10px;
            }
            .progress-bar {
                width: 200px;
                height: 25px;
                background: #f0f0f0;
                border-radius: 5px;
                position: relative;
                overflow: hidden;
            }
            .progress-fill {
                height: 100%;
                transition: width 0.3s;
            }
            .progress-bar span {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                font-weight: bold;
                color: #333;
                font-size: 12px;
            }
        </style>
    </div>
    <?php
}

/* ========================================
   Widget - מפת עסקים צדדית
   ======================================== */

class Shlomi_Business_Map_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'shlomi_business_map',
            '🗺️ מפת עסקים מקומיים',
            array('description' => 'מציג מפה אינטראקטיבית של עסקים מקומיים')
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        $height = !empty($instance['height']) ? $instance['height'] : '300px';
        $zoom = !empty($instance['zoom']) ? $instance['zoom'] : 13;
        $category = !empty($instance['category']) ? $instance['category'] : '';

        echo do_shortcode('[business_map height="' . $height . '" zoom="' . $zoom . '"]');

        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : 'עסקים מקומיים';
        $height = !empty($instance['height']) ? $instance['height'] : '300px';
        $zoom = !empty($instance['zoom']) ? $instance['zoom'] : 13;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">כותרת:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('height'); ?>">גובה המפה:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('height'); ?>"
                   name="<?php echo $this->get_field_name('height'); ?>" type="text"
                   value="<?php echo esc_attr($height); ?>" placeholder="300px">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('zoom'); ?>">רמת זום:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('zoom'); ?>"
                   name="<?php echo $this->get_field_name('zoom'); ?>" type="number"
                   value="<?php echo esc_attr($zoom); ?>" min="1" max="20">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['height'] = (!empty($new_instance['height'])) ? sanitize_text_field($new_instance['height']) : '300px';
        $instance['zoom'] = (!empty($new_instance['zoom'])) ? absint($new_instance['zoom']) : 13;
        return $instance;
    }
}

function shlomi_register_business_widgets()
{
    register_widget('Shlomi_Business_Map_Widget');
}
add_action('widgets_init', 'shlomi_register_business_widgets');

/* ========================================
   SEO אוטומטי - Auto SEO & Schema.org
   ======================================== */

// הוספת Meta Tags אוטומטית לעסקים
function shlomi_business_seo_meta_tags()
{
    if (is_singular('business')) {
        global $post;

        $title = get_the_title();
        $description = wp_trim_words(get_the_excerpt() ?: strip_tags(get_the_content()), 30);
        $image = get_the_post_thumbnail_url($post->ID, 'large') ?: get_bloginfo('url') . '/wp-content/themes/shlomi-online/screenshot.png';
        $url = get_permalink();

        $phone = get_post_meta($post->ID, '_business_phone', true);
        $address = get_post_meta($post->ID, '_business_address', true);
        $city = get_post_meta($post->ID, '_business_city', true);
        $rating = get_post_meta($post->ID, '_business_rating', true);
        $review_count = get_post_meta($post->ID, '_business_review_count', true);

        // Open Graph Meta Tags
        echo '<meta property="og:type" content="business.business">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
        echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";

        if ($phone) {
            echo '<meta property="business:contact_data:phone_number" content="' . esc_attr($phone) . '">' . "\n";
        }

        // Twitter Card
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
        echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
    }
}
add_action('wp_head', 'shlomi_business_seo_meta_tags');

// Schema.org Structured Data
function shlomi_business_schema_org()
{
    if (is_singular('business')) {
        global $post;

        $phone = get_post_meta($post->ID, '_business_phone', true);
        $email = get_post_meta($post->ID, '_business_email', true);
        $website = get_post_meta($post->ID, '_business_website', true);
        $address = get_post_meta($post->ID, '_business_address', true);
        $city = get_post_meta($post->ID, '_business_city', true);
        $zip = get_post_meta($post->ID, '_business_zip', true);
        $lat = get_post_meta($post->ID, '_business_lat', true);
        $lng = get_post_meta($post->ID, '_business_lng', true);
        $rating = get_post_meta($post->ID, '_business_rating', true);
        $review_count = get_post_meta($post->ID, '_business_review_count', true);
        $image = get_the_post_thumbnail_url($post->ID, 'large');

        $categories = get_the_terms($post->ID, 'business_category');
        $category_name = !empty($categories) ? $categories[0]->name : 'עסק מקומי';

        $hours = array();
        $days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        $days_keys = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');

        foreach ($days_keys as $index => $day) {
            $hours_value = get_post_meta($post->ID, '_business_hours_' . $day, true);
            if ($hours_value && $hours_value !== 'סגור') {
                $hours[] = '"' . $days[$index] . ' ' . esc_js($hours_value) . '"';
            }
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => get_the_title(),
            'description' => wp_trim_words(strip_tags(get_the_content()), 50),
            'url' => get_permalink(),
        );

        if ($image) {
            $schema['image'] = $image;
        }

        if ($phone) {
            $schema['telephone'] = $phone;
        }

        if ($email) {
            $schema['email'] = $email;
        }

        if ($address || $city) {
            $schema['address'] = array(
                '@type' => 'PostalAddress',
                'streetAddress' => $address,
                'addressLocality' => $city,
                'postalCode' => $zip,
                'addressCountry' => 'IL'
            );
        }

        if ($lat && $lng) {
            $schema['geo'] = array(
                '@type' => 'GeoCoordinates',
                'latitude' => $lat,
                'longitude' => $lng
            );
        }

        if ($rating && $review_count) {
            $schema['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => $rating,
                'reviewCount' => $review_count,
                'bestRating' => '5',
                'worstRating' => '1'
            );
        }

        if (!empty($hours)) {
            $schema['openingHours'] = $hours;
        }

        echo '<script type="application/ld+json">' . "\n";
        echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo "\n</script>\n";
    }
}
add_action('wp_head', 'shlomi_business_schema_org');

/* ========================================
   Shortcode תצוגה ראשית - Homepage Business Dashboard
   ======================================== */

// Shortcode - תצוגת אינדקס עסקים מלאה עם וידג'טים
function shlomi_business_homepage_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'featured' => 3,
        'latest' => 6,
        'categories_display' => 'yes',
        'search' => 'yes',
        'map' => 'yes',
        'stats' => 'yes'
    ), $atts);

    ob_start();
    ?>
    <div class="business-homepage-dashboard">

        <!-- Hero Section עם סטטיסטיקות -->
        <?php if ($atts['stats'] === 'yes'): ?>
            <div class="business-stats-hero">
                <h2>🏢 קטלוג עסקים מקומיים</h2>
                <div class="stats-quick-view">
                    <?php
                    $total = wp_count_posts('business')->publish;
                    $categories = get_terms(array('taxonomy' => 'business_category', 'hide_empty' => true));
                    $reviews = wp_count_posts('review')->publish;
                    ?>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $total; ?></span>
                        <span class="stat-label">עסקים</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo count($categories); ?></span>
                        <span class="stat-label">קטגוריות</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $reviews; ?></span>
                        <span class="stat-label">ביקורות</span>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- חיפוש מתקדם -->
        <?php if ($atts['search'] === 'yes'): ?>
            <div class="homepage-search-widget">
                <?php echo do_shortcode('[business_search]'); ?>
            </div>
        <?php endif; ?>

        <!-- קטגוריות -->
        <?php if ($atts['categories_display'] === 'yes'): ?>
            <div class="business-categories-showcase">
                <h2>📂 עיין לפי קטגוריה</h2>
                <div class="categories-grid">
                    <?php
                    $categories = get_terms(array('taxonomy' => 'business_category', 'hide_empty' => false));
                    foreach ($categories as $category):
                        $icon = get_term_meta($category->term_id, 'category_icon', true);
                        $color = get_term_meta($category->term_id, 'category_color', true);
                        $count = $category->count;
                    ?>
                        <a href="<?php echo get_term_link($category); ?>" class="category-card" style="border-color: <?php echo esc_attr($color); ?>;">
                            <div class="category-icon" style="background: <?php echo esc_attr($color); ?>;">
                                <?php echo $icon; ?>
                            </div>
                            <div class="category-info">
                                <h3><?php echo esc_html($category->name); ?></h3>
                                <span class="category-count"><?php echo $count; ?> עסקים</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- עסקים מומלצים -->
        <?php if ($atts['featured'] > 0):
            $featured_businesses = new WP_Query(array(
                'post_type' => 'business',
                'posts_per_page' => $atts['featured'],
                'meta_key' => '_business_badge',
                'meta_value' => array('VIP', 'מומלץ', 'RECOMMENDED'),
                'meta_compare' => 'IN'
            ));

            if ($featured_businesses->have_posts()):
        ?>
            <div class="featured-businesses-section">
                <h2>⭐ עסקים מומלצים</h2>
                <div class="business-grid">
                    <?php while ($featured_businesses->have_posts()): $featured_businesses->the_post(); ?>
                        <?php get_template_part('template-parts/content', 'business-card'); ?>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
        <?php
            endif;
        endif;
        ?>

        <!-- מפת עסקים -->
        <?php if ($atts['map'] === 'yes'): ?>
            <div class="homepage-map-widget">
                <h2>🗺️ מפת עסקים</h2>
                <?php echo do_shortcode('[business_map height="500px"]'); ?>
            </div>
        <?php endif; ?>

        <!-- עסקים אחרונים -->
        <?php if ($atts['latest'] > 0):
            $latest_businesses = new WP_Query(array(
                'post_type' => 'business',
                'posts_per_page' => $atts['latest'],
                'orderby' => 'date',
                'order' => 'DESC'
            ));

            if ($latest_businesses->have_posts()):
        ?>
            <div class="latest-businesses-section">
                <h2>🆕 התווספו לאחרונה</h2>
                <div class="business-grid">
                    <?php while ($latest_businesses->have_posts()): $latest_businesses->the_post(); ?>
                        <?php get_template_part('template-parts/content', 'business-card'); ?>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
        <?php
            endif;
        endif;
        ?>

        <!-- קופונים פעילים -->
        <?php
        $active_coupons = new WP_Query(array(
            'post_type' => 'coupon',
            'posts_per_page' => 4,
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => '_coupon_expiry',
                    'value' => date('Y-m-d'),
                    'compare' => '>=',
                    'type' => 'DATE'
                ),
                array(
                    'key' => '_coupon_expiry',
                    'compare' => 'NOT EXISTS'
                )
            )
        ));

        if ($active_coupons->have_posts()):
        ?>
            <div class="homepage-coupons-section">
                <h2>🎟️ קופונים והנחות חמים</h2>
                <div class="coupons-slider">
                    <?php while ($active_coupons->have_posts()): $active_coupons->the_post();
                        $discount = get_post_meta(get_the_ID(), '_coupon_discount', true);
                        $business_id = get_post_meta(get_the_ID(), '_coupon_business_id', true);
                        $business_name = $business_id ? get_the_title($business_id) : '';
                    ?>
                        <div class="coupon-mini-card">
                            <div class="coupon-discount-large"><?php echo esc_html($discount); ?></div>
                            <h4><?php the_title(); ?></h4>
                            <?php if ($business_name): ?>
                                <p class="coupon-business">ב-<?php echo esc_html($business_name); ?></p>
                            <?php endif; ?>
                            <?php if ($business_id): ?>
                                <a href="<?php echo get_permalink($business_id); ?>" class="btn-coupon-use">לפרטים</a>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Call to Action -->
        <div class="business-cta-section">
            <h2>רוצים להוסיף את העסק שלכם?</h2>
            <p>הצטרפו לקטלוג העסקים המקומיים שלנו והגיעו ללקוחות חדשים!</p>
            <a href="<?php echo home_url('/contact'); ?>" class="btn btn-cta">פנו אלינו עכשיו</a>
        </div>

    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('business_homepage', 'shlomi_business_homepage_shortcode');