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