<?php
/**
 * The template for displaying single business
 * תבנית עסק בודד - שלומי אונליין
 */

get_header();
?>

<div class="site-content">
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <a href="<?php echo home_url(); ?>">בית</a>
        <span> > </span>
        <a href="<?php echo get_post_type_archive_link('business'); ?>">עסקים מקומיים</a>
        <?php
        $categories = get_the_terms(get_the_ID(), 'business_category');
        if (!empty($categories) && !is_wp_error($categories)):
            $category = $categories[0];
        ?>
            <span> > </span>
            <a href="<?php echo get_term_link($category); ?>"><?php echo esc_html($category->name); ?></a>
        <?php endif; ?>
        <span> > </span>
        <span><?php the_title(); ?></span>
    </div>

    <div class="content-area">
        <main id="main-content" class="main-content">
            <?php
            while (have_posts()):
                the_post();

                // שליפת כל המטא-דאטה
                $phone = get_post_meta(get_the_ID(), '_business_phone', true);
                $mobile = get_post_meta(get_the_ID(), '_business_mobile', true);
                $email = get_post_meta(get_the_ID(), '_business_email', true);
                $website = get_post_meta(get_the_ID(), '_business_website', true);
                $address = get_post_meta(get_the_ID(), '_business_address', true);
                $city = get_post_meta(get_the_ID(), '_business_city', true);
                $zip = get_post_meta(get_the_ID(), '_business_zip', true);
                $lat = get_post_meta(get_the_ID(), '_business_lat', true);
                $lng = get_post_meta(get_the_ID(), '_business_lng', true);
                $rating = get_post_meta(get_the_ID(), '_business_rating', true);
                $badge = get_post_meta(get_the_ID(), '_business_badge', true);
                $facebook = get_post_meta(get_the_ID(), '_business_facebook', true);
                $instagram = get_post_meta(get_the_ID(), '_business_instagram', true);
                $whatsapp = get_post_meta(get_the_ID(), '_business_whatsapp', true);

                $categories = get_the_terms(get_the_ID(), 'business_category');
                ?>
                <article id="business-<?php the_ID(); ?>" <?php post_class('single-business'); ?>>

                    <header class="business-entry-header">
                        <?php if ($badge): ?>
                            <div class="business-badge business-badge-<?php echo esc_attr(strtolower($badge)); ?>">
                                <?php
                                $badge_labels = array(
                                    'VIP' => '👑 VIP',
                                    'NEW' => '🆕 חדש',
                                    'RECOMMENDED' => '⭐ מומלץ',
                                    'POPULAR' => '🔥 פופולרי'
                                );
                                echo isset($badge_labels[$badge]) ? $badge_labels[$badge] : $badge;
                                ?>
                            </div>
                        <?php endif; ?>

                        <div class="business-title-section">
                            <h1 class="entry-title"><?php the_title(); ?></h1>

                            <?php if ($rating): ?>
                                <div class="business-rating-large">
                                    <?php echo str_repeat('⭐', intval($rating)); ?>
                                    <span class="rating-text">(דירוג: <?php echo $rating; ?>/5)</span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($categories) && !is_wp_error($categories)): ?>
                                <div class="business-categories">
                                    <?php foreach ($categories as $category): ?>
                                        <a href="<?php echo get_term_link($category); ?>" class="business-category-tag">
                                            <?php echo esc_html($category->name); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </header>

                    <?php if (has_post_thumbnail()): ?>
                        <div class="business-thumbnail">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>

                    <!-- פרטי קשר -->
                    <div class="business-contact-box">
                        <h2>📞 פרטי יצירת קשר</h2>
                        <div class="contact-grid">
                            <?php if ($mobile): ?>
                                <div class="contact-item">
                                    <span class="contact-icon">📱</span>
                                    <div>
                                        <strong>נייד:</strong>
                                        <a href="tel:<?php echo esc_attr($mobile); ?>"><?php echo esc_html($mobile); ?></a>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($phone): ?>
                                <div class="contact-item">
                                    <span class="contact-icon">📞</span>
                                    <div>
                                        <strong>טלפון:</strong>
                                        <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($email): ?>
                                <div class="contact-item">
                                    <span class="contact-icon">📧</span>
                                    <div>
                                        <strong>אימייל:</strong>
                                        <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($website): ?>
                                <div class="contact-item">
                                    <span class="contact-icon">🌐</span>
                                    <div>
                                        <strong>אתר:</strong>
                                        <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener"><?php echo esc_html(parse_url($website, PHP_URL_HOST)); ?></a>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($address): ?>
                                <div class="contact-item">
                                    <span class="contact-icon">📍</span>
                                    <div>
                                        <strong>כתובת:</strong>
                                        <span><?php echo esc_html($address); ?><?php echo $city ? ', ' . esc_html($city) : ''; ?><?php echo $zip ? ' ' . esc_html($zip) : ''; ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- תיאור -->
                    <div class="business-entry-content">
                        <?php the_content(); ?>
                    </div>

                    <!-- שעות פעילות -->
                    <?php
                    $days = array(
                        'sunday' => 'ראשון',
                        'monday' => 'שני',
                        'tuesday' => 'שלישי',
                        'wednesday' => 'רביעי',
                        'thursday' => 'חמישי',
                        'friday' => 'שישי',
                        'saturday' => 'שבת'
                    );
                    $has_hours = false;
                    foreach ($days as $day_key => $day_name) {
                        if (get_post_meta(get_the_ID(), '_business_hours_' . $day_key, true)) {
                            $has_hours = true;
                            break;
                        }
                    }
                    ?>

                    <?php if ($has_hours): ?>
                        <div class="business-hours-box">
                            <h2>⏰ שעות פעילות</h2>
                            <table class="hours-table">
                                <?php foreach ($days as $day_key => $day_name):
                                    $hours = get_post_meta(get_the_ID(), '_business_hours_' . $day_key, true);
                                    if ($hours):
                                ?>
                                    <tr>
                                        <td class="day-name"><?php echo $day_name; ?></td>
                                        <td class="day-hours"><?php echo esc_html($hours); ?></td>
                                    </tr>
                                <?php endif; endforeach; ?>
                            </table>
                        </div>
                    <?php endif; ?>

                    <!-- מפה -->
                    <?php if ($lat && $lng): ?>
                        <div class="business-map-box">
                            <h2>🗺️ מיקום על המפה</h2>
                            <div id="single-business-map" style="height: 400px; width: 100%; border-radius: 10px;"></div>
                            <script>
                            jQuery(document).ready(function($) {
                                if (typeof L !== 'undefined') {
                                    var map = L.map('single-business-map').setView([<?php echo $lat; ?>, <?php echo $lng; ?>], 16);

                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        attribution: '© OpenStreetMap contributors',
                                        maxZoom: 19
                                    }).addTo(map);

                                    var marker = L.marker([<?php echo $lat; ?>, <?php echo $lng; ?>]).addTo(map);
                                    marker.bindPopup('<strong><?php echo esc_js(get_the_title()); ?></strong><br><?php echo esc_js($address); ?>').openPopup();
                                }
                            });
                            </script>
                            <p class="map-link">
                                <a href="https://www.openstreetmap.org/?mlat=<?php echo $lat; ?>&mlon=<?php echo $lng; ?>#map=17/<?php echo $lat; ?>/<?php echo $lng; ?>"
                                   target="_blank" class="btn btn-primary">פתח ב-OpenStreetMap</a>
                                <a href="https://www.google.com/maps?q=<?php echo $lat; ?>,<?php echo $lng; ?>"
                                   target="_blank" class="btn btn-secondary">פתח ב-Google Maps</a>
                                <a href="https://waze.com/ul?ll=<?php echo $lat; ?>,<?php echo $lng; ?>&navigate=yes"
                                   target="_blank" class="btn btn-secondary">פתח ב-Waze</a>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- רשתות חברתיות -->
                    <?php if ($facebook || $instagram || $whatsapp): ?>
                        <div class="business-social-box">
                            <h2>📱 עקבו אחרינו</h2>
                            <div class="social-links">
                                <?php if ($facebook): ?>
                                    <a href="<?php echo esc_url($facebook); ?>" target="_blank" class="social-link facebook">
                                        📘 פייסבוק
                                    </a>
                                <?php endif; ?>

                                <?php if ($instagram): ?>
                                    <a href="<?php echo esc_url($instagram); ?>" target="_blank" class="social-link instagram">
                                        📷 אינסטגרם
                                    </a>
                                <?php endif; ?>

                                <?php if ($whatsapp): ?>
                                    <a href="https://wa.me/<?php echo esc_attr($whatsapp); ?>" target="_blank" class="social-link whatsapp">
                                        💬 WhatsApp
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </article>

                <!-- עסקים קשורים -->
                <?php
                if (!empty($categories) && !is_wp_error($categories)):
                    $related = new WP_Query(array(
                        'post_type' => 'business',
                        'posts_per_page' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'business_category',
                                'field' => 'term_id',
                                'terms' => $categories[0]->term_id
                            )
                        )
                    ));

                    if ($related->have_posts()):
                        ?>
                        <div class="related-businesses">
                            <h2 class="section-title">עסקים דומים</h2>
                            <div class="business-grid">
                                <?php while ($related->have_posts()): $related->the_post(); ?>
                                    <?php get_template_part('template-parts/content', 'business-card'); ?>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        <?php
                        wp_reset_postdata();
                    endif;
                endif;
                ?>

            <?php endwhile; ?>
        </main>

        <?php get_sidebar(); ?>
    </div>
</div>

<?php get_footer(); ?>
