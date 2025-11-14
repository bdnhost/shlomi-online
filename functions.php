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