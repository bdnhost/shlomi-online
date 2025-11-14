<?php
/**
 * Template for displaying single business
 * תבנית עסק בודד - שלומי אונליין - גרסה מתקדמת
 */

get_header();
?>

<div class="site-content">
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
        $review_count = get_post_meta(get_the_ID(), '_business_review_count', true);
        $badge = get_post_meta(get_the_ID(), '_business_badge', true);
        $facebook = get_post_meta(get_the_ID(), '_business_facebook', true);
        $instagram = get_post_meta(get_the_ID(), '_business_instagram', true);
        $whatsapp = get_post_meta(get_the_ID(), '_business_whatsapp', true);
        $tiktok = get_post_meta(get_the_ID(), '_business_tiktok', true);
        $gallery = get_post_meta(get_the_ID(), '_business_gallery', true);

        $categories = get_the_terms(get_the_ID(), 'business_category');
        $category_name = !empty($categories) && !is_wp_error($categories) ? $categories[0]->name : '';
        $category_icon = !empty($categories) ? get_term_meta($categories[0]->term_id, 'category_icon', true) : '';
        $category_color = !empty($categories) ? get_term_meta($categories[0]->term_id, 'category_color', true) : '#3498db';

        // שעות פעילות
        $hours = array();
        $days = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
        $days_hebrew = array('ראשון', 'שני', 'שלישי', 'רביעי', 'חמישי', 'שישי', 'שבת');
        foreach ($days as $day) {
            $hours[$day] = get_post_meta(get_the_ID(), '_business_hours_' . $day, true);
        }

        // ביקורות
        $reviews = shlomi_get_business_reviews(get_the_ID(), 10);

        // קופונים פעילים
        $coupons = shlomi_get_business_coupons(get_the_ID());
    ?>

    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <a href="<?php echo home_url(); ?>">🏠 בית</a>
        <span> › </span>
        <a href="<?php echo get_post_type_archive_link('business'); ?>">עסקים מקומיים</a>
        <?php if (!empty($categories)): ?>
            <span> › </span>
            <a href="<?php echo get_term_link($categories[0]); ?>">
                <?php echo $category_icon ? $category_icon . ' ' : ''; ?>
                <?php echo esc_html($category_name); ?>
            </a>
        <?php endif; ?>
        <span> › </span>
        <span><?php the_title(); ?></span>
    </div>

    <article id="business-<?php the_ID(); ?>" <?php post_class('single-business-full'); ?>>

        <!-- Header Section -->
        <div class="business-header">
            <div class="business-header-main">
                <div class="business-logo-large">
                    <?php if (has_post_thumbnail()): ?>
                        <?php the_post_thumbnail('large'); ?>
                    <?php else: ?>
                        <div class="business-placeholder-logo-large">
                            <span><?php echo mb_substr(get_the_title(), 0, 2); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="business-header-info">
                    <h1 class="business-title-main">
                        <?php the_title(); ?>
                        <?php if ($badge): ?>
                            <span class="business-badge-inline business-badge-<?php echo esc_attr(strtolower($badge)); ?>">
                                <?php echo esc_html($badge); ?>
                            </span>
                        <?php endif; ?>
                    </h1>

                    <?php if ($category_name): ?>
                        <div class="business-category-tag" style="background-color: <?php echo esc_attr($category_color); ?>;">
                            <?php echo $category_icon ? $category_icon . ' ' : ''; ?>
                            <?php echo esc_html($category_name); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($rating): ?>
                        <div class="business-rating-display">
                            <div class="stars-large">
                                <?php
                                $full_stars = floor($rating);
                                $half_star = ($rating - $full_stars) >= 0.5;
                                for ($i = 0; $i < $full_stars; $i++) {
                                    echo '⭐';
                                }
                                if ($half_star) {
                                    echo '⭐';
                                }
                                ?>
                            </div>
                            <span class="rating-number"><?php echo number_format($rating, 1); ?></span>
                            <?php if ($review_count): ?>
                                <span class="review-count">(<?php echo $review_count; ?> ביקורות)</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="business-actions-main">
                        <?php if ($whatsapp): ?>
                            <a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $whatsapp)); ?>"
                               class="btn btn-whatsapp-main" target="_blank">
                                💬 שלח הודעת WhatsApp
                            </a>
                        <?php endif; ?>

                        <?php if ($phone): ?>
                            <a href="tel:<?php echo esc_attr($phone); ?>" class="btn btn-call-main">
                                📞 התקשר עכשיו
                            </a>
                        <?php endif; ?>

                        <?php if ($lat && $lng): ?>
                            <a href="https://waze.com/ul?ll=<?php echo $lat; ?>,<?php echo $lng; ?>&navigate=yes"
                               class="btn btn-navigate-main" target="_blank">
                                🗺️ נווט ב-Waze
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- קופונים פעילים -->
        <?php if ($coupons->have_posts()): ?>
            <div class="business-coupons-section">
                <h2>🎟️ קופונים והנחות זמינים</h2>
                <div class="coupons-grid">
                    <?php while ($coupons->have_posts()): $coupons->the_post();
                        $discount = get_post_meta(get_the_ID(), '_coupon_discount', true);
                        $code = get_post_meta(get_the_ID(), '_coupon_code', true);
                        $expiry = get_post_meta(get_the_ID(), '_coupon_expiry', true);
                        $terms = get_post_meta(get_the_ID(), '_coupon_terms', true);
                    ?>
                        <div class="coupon-card">
                            <div class="coupon-discount"><?php echo esc_html($discount); ?></div>
                            <h3><?php the_title(); ?></h3>
                            <?php if ($code): ?>
                                <div class="coupon-code">קוד: <strong><?php echo esc_html($code); ?></strong></div>
                            <?php endif; ?>
                            <?php if ($expiry): ?>
                                <div class="coupon-expiry">בתוקף עד: <?php echo date_i18n('d/m/Y', strtotime($expiry)); ?></div>
                            <?php endif; ?>
                            <?php if ($terms): ?>
                                <div class="coupon-terms"><?php echo esc_html($terms); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- תיאור העסק -->
        <div class="business-content-section">
            <h2>📋 אודות העסק</h2>
            <div class="business-description">
                <?php the_content(); ?>
            </div>
        </div>

        <!-- גלריית תמונות -->
        <?php if (!empty($gallery) && is_array($gallery)): ?>
            <div class="business-gallery-section">
                <h2>📸 גלריית תמונות</h2>
                <div class="business-gallery-grid">
                    <?php foreach ($gallery as $image_id): ?>
                        <a href="<?php echo wp_get_attachment_url($image_id); ?>" class="gallery-item" data-lightbox="business-gallery">
                            <?php echo wp_get_attachment_image($image_id, 'medium'); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- פרטי קשר ומפה -->
        <div class="business-contact-map-section">
            <div class="contact-info-box">
                <h2>📞 פרטי התקשרות</h2>

                <?php if ($phone): ?>
                    <div class="contact-item">
                        <span class="contact-icon">☎️</span>
                        <strong>טלפון:</strong>
                        <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a>
                    </div>
                <?php endif; ?>

                <?php if ($mobile): ?>
                    <div class="contact-item">
                        <span class="contact-icon">📱</span>
                        <strong>נייד:</strong>
                        <a href="tel:<?php echo esc_attr($mobile); ?>"><?php echo esc_html($mobile); ?></a>
                    </div>
                <?php endif; ?>

                <?php if ($email): ?>
                    <div class="contact-item">
                        <span class="contact-icon">✉️</span>
                        <strong>אימייל:</strong>
                        <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                    </div>
                <?php endif; ?>

                <?php if ($website): ?>
                    <div class="contact-item">
                        <span class="contact-icon">🌐</span>
                        <strong>אתר:</strong>
                        <a href="<?php echo esc_url($website); ?>" target="_blank" rel="nofollow">
                            <?php echo esc_html(parse_url($website, PHP_URL_HOST)); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ($address): ?>
                    <div class="contact-item">
                        <span class="contact-icon">📍</span>
                        <strong>כתובת:</strong>
                        <?php echo esc_html($address); ?>
                        <?php if ($city): echo ', ' . esc_html($city); endif; ?>
                        <?php if ($zip): echo ' ' . esc_html($zip); endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Social Links -->
                <?php if ($facebook || $instagram || $whatsapp || $tiktok): ?>
                    <div class="social-links">
                        <h3>עקבו אחרינו:</h3>
                        <div class="social-icons">
                            <?php if ($facebook): ?>
                                <a href="<?php echo esc_url($facebook); ?>" target="_blank" class="social-icon facebook" title="Facebook">
                                    <span>👍</span>
                                </a>
                            <?php endif; ?>
                            <?php if ($instagram): ?>
                                <a href="<?php echo esc_url($instagram); ?>" target="_blank" class="social-icon instagram" title="Instagram">
                                    <span>📷</span>
                                </a>
                            <?php endif; ?>
                            <?php if ($whatsapp): ?>
                                <a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $whatsapp)); ?>" target="_blank" class="social-icon whatsapp" title="WhatsApp">
                                    <span>💬</span>
                                </a>
                            <?php endif; ?>
                            <?php if ($tiktok): ?>
                                <a href="<?php echo esc_url($tiktok); ?>" target="_blank" class="social-icon tiktok" title="TikTok">
                                    <span>🎵</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- שעות פעילות -->
            <?php if (array_filter($hours)): ?>
                <div class="business-hours-box">
                    <h2>🕒 שעות פעילות</h2>
                    <table class="hours-table">
                        <?php foreach ($days as $index => $day): ?>
                            <?php if (!empty($hours[$day])): ?>
                                <tr>
                                    <td class="day-name"><?php echo $days_hebrew[$index]; ?></td>
                                    <td class="day-hours"><?php echo esc_html($hours[$day]); ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- מפה אינטראקטיבית -->
        <?php if ($lat && $lng): ?>
            <div class="business-map-section">
                <h2>🗺️ מיקום על המפה</h2>
                <div id="single-business-map" class="single-business-map"></div>
                <script>
                jQuery(document).ready(function($) {
                    if (typeof L !== 'undefined') {
                        var map = L.map('single-business-map').setView([<?php echo $lat; ?>, <?php echo $lng; ?>], 16);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                            maxZoom: 19
                        }).addTo(map);

                        var marker = L.marker([<?php echo $lat; ?>, <?php echo $lng; ?>]).addTo(map);
                        marker.bindPopup('<strong><?php the_title(); ?></strong><br><?php echo esc_js($address); ?>').openPopup();
                    }
                });
                </script>
            </div>
        <?php endif; ?>

        <!-- ביקורות -->
        <?php if ($reviews->have_posts()): ?>
            <div class="business-reviews-section">
                <h2>⭐ ביקורות לקוחות (<?php echo $review_count; ?>)</h2>
                <div class="reviews-list">
                    <?php while ($reviews->have_posts()): $reviews->the_post();
                        $review_rating = get_post_meta(get_the_ID(), '_review_rating', true);
                        $reviewer_name = get_post_meta(get_the_ID(), '_review_name', true);
                        $verified = get_post_meta(get_the_ID(), '_review_verified', true);
                    ?>
                        <div class="review-item">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">
                                        <?php echo mb_substr($reviewer_name, 0, 1); ?>
                                    </div>
                                    <div>
                                        <strong><?php echo esc_html($reviewer_name); ?></strong>
                                        <?php if ($verified): ?>
                                            <span class="verified-badge">✓ מאומת</span>
                                        <?php endif; ?>
                                        <div class="review-date"><?php echo get_the_date(); ?></div>
                                    </div>
                                </div>
                                <div class="review-rating">
                                    <?php echo str_repeat('⭐', intval($review_rating)); ?>
                                </div>
                            </div>
                            <div class="review-content">
                                <?php the_content(); ?>
                            </div>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- עסקים דומים -->
        <?php if (!empty($categories)):
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
            <div class="related-businesses-section">
                <h2>עסקים דומים ב<?php echo esc_html($category_name); ?></h2>
                <div class="business-grid">
                    <?php while ($related->have_posts()): $related->the_post(); ?>
                        <?php get_template_part('template-parts/content', 'business-card'); ?>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
        <?php
            endif;
        endif;
        ?>

    </article>

    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
