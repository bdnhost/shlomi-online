<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <!-- Skip to content link for accessibility -->
    <a class="skip-link screen-reader-text" href="#main-content">דלג לתוכן הראשי</a>

    <div id="page" class="site-wrapper">

        <!-- Top Bar -->
        <div class="top-bar">
            <div class="container">
                <div class="date-weather">
                    <span>📅 <?php echo date_i18n('l, j F Y'); ?></span>
                    <span>🌤️ שלומי: 24°C</span>
                </div>
                <div class="social-icons">
                    <a href="https://facebook.com/shlomionline" target="_blank" aria-label="פייסבוק">📘</a>
                    <a href="https://instagram.com/shlomionline" target="_blank" aria-label="אינסטגרם">📷</a>
                    <a href="https://twitter.com/shlomionline" target="_blank" aria-label="טוויטר">🐦</a>
                    <a href="https://youtube.com/shlomionline" target="_blank" aria-label="יוטיוב">📺</a>
                </div>
            </div>
        </div>

        <!-- Header -->
        <header class="site-header">
            <div class="header-main">
                <div class="logo-area">
                    <?php
                    if (has_custom_logo()) {
                        the_custom_logo();
                    } else {
                        ?>
                        <h1 class="site-title">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                <?php bloginfo('name'); ?>
                            </a>
                        </h1>
                        <?php
                    }

                    $description = get_bloginfo('description', 'display');
                    if ($description || is_customize_preview()):
                        ?>
                        <p class="site-description"><?php echo $description; ?></p>
                    <?php endif; ?>
                </div>

                <div class="search-box">
                    <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                        <input type="search" name="s" placeholder="חיפוש חדשות..."
                            value="<?php echo get_search_query(); ?>" aria-label="חיפוש">
                        <button type="submit" aria-label="חפש">🔍</button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Navigation -->
        <nav class="main-navigation">
            <div class="nav-container">
                <!-- Mobile Menu Toggle - כפתור המבורגר -->
                <button class="mobile-menu-toggle" aria-label="תפריט ניווט" aria-expanded="false" type="button">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <?php
                // שימוש בתפריט המוגדר "Menu 1"
                if (has_nav_menu('primary')) {
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_class' => 'main-nav',
                        'container' => false,
                        'fallback_cb' => false,
                    ));
                } else {
                    // תפריט ברירת מחדל לפי הקטגוריות האמיתיות שלך
                    ?>
                    <ul class="main-nav">
                        <li class="current-menu-item"><a href="<?php echo home_url('/'); ?>">🏠 ראשי</a></li>
                        <li><a href="<?php echo get_category_link(get_cat_ID('חדשות מקומיות')); ?>">📰 חדשות מקומיות</a>
                        </li>
                        <li><a href="<?php echo get_category_link(get_cat_ID('ביטחון')); ?>">🛡️ ביטחון</a></li>
                        <li><a href="<?php echo get_category_link(get_cat_ID('עידכונים')); ?>">📢 עידכונים</a></li>
                        <li><a href="<?php echo get_category_link(get_cat_ID('חינוך')); ?>">🎓 חינוך</a></li>
                        <li><a href="<?php echo get_category_link(get_cat_ID('עסקים מקומיים')); ?>">💼 עסקים מקומיים</a>
                        </li>
                        <li><a href="<?php echo get_category_link(get_cat_ID('שירות לתושב')); ?>">🏛️ שירות לתושב</a></li>
                        <li><a href="<?php echo get_category_link(get_cat_ID('תרבות ופנאי')); ?>">🎭 תרבות ופנאי</a></li>
                    </ul>
                    <?php
                }
                ?>
            </div>
        </nav>