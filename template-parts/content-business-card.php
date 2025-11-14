<?php
/**
 * Template part for displaying business card
 */

$phone = get_post_meta(get_the_ID(), '_business_phone', true);
$mobile = get_post_meta(get_the_ID(), '_business_mobile', true);
$address = get_post_meta(get_the_ID(), '_business_address', true);
$city = get_post_meta(get_the_ID(), '_business_city', true);
$rating = get_post_meta(get_the_ID(), '_business_rating', true);
$badge = get_post_meta(get_the_ID(), '_business_badge', true);
$whatsapp = get_post_meta(get_the_ID(), '_business_whatsapp', true);
$lat = get_post_meta(get_the_ID(), '_business_lat', true);
$lng = get_post_meta(get_the_ID(), '_business_lng', true);

$categories = get_the_terms(get_the_ID(), 'business_category');
$category_name = (!empty($categories) && !is_wp_error($categories)) ? $categories[0]->name : '';
?>

<article id="business-<?php the_ID(); ?>" <?php post_class('business-card'); ?>>
    <!-- תווית Badge -->
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

    <!-- לוגו -->
    <div class="business-logo">
        <a href="<?php the_permalink(); ?>">
            <?php if (has_post_thumbnail()): ?>
                <?php the_post_thumbnail('medium'); ?>
            <?php else: ?>
                <div class="business-placeholder-logo">
                    <span><?php echo mb_substr(get_the_title(), 0, 2); ?></span>
                </div>
            <?php endif; ?>
        </a>
    </div>

    <!-- תוכן הכרטיס -->
    <div class="business-card-content">
        <!-- כותרת ודירוג -->
        <div class="business-header">
            <h3 class="business-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>
            <?php if ($rating): ?>
                <div class="business-rating">
                    <?php echo str_repeat('⭐', intval($rating)); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- קטגוריה -->
        <?php if ($category_name): ?>
            <div class="business-category">
                <span class="category-icon">🏷️</span>
                <?php echo esc_html($category_name); ?>
            </div>
        <?php endif; ?>

        <div class="business-divider"></div>

        <!-- פרטים -->
        <div class="business-details">
            <?php if ($address): ?>
                <div class="business-detail">
                    <span class="detail-icon" aria-hidden="true">📍</span>
                    <span><?php echo esc_html($address); ?><?php echo $city ? ', ' . esc_html($city) : ''; ?></span>
                </div>
            <?php endif; ?>

            <?php if ($mobile): ?>
                <div class="business-detail">
                    <span class="detail-icon" aria-hidden="true">📱</span>
                    <a href="tel:<?php echo esc_attr($mobile); ?>"><?php echo esc_html($mobile); ?></a>
                </div>
            <?php elseif ($phone): ?>
                <div class="business-detail">
                    <span class="detail-icon" aria-hidden="true">📞</span>
                    <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a>
                </div>
            <?php endif; ?>
        </div>

        <!-- תיאור קצר -->
        <?php if (has_excerpt()): ?>
            <div class="business-excerpt">
                <?php the_excerpt(); ?>
            </div>
        <?php endif; ?>

        <div class="business-divider"></div>

        <!-- כפתורי פעולה -->
        <div class="business-actions">
            <a href="<?php the_permalink(); ?>" class="btn btn-business-view">
                צפה בעסק
            </a>

            <?php if ($whatsapp): ?>
                <a href="https://wa.me/<?php echo esc_attr($whatsapp); ?>" class="btn btn-whatsapp" target="_blank" rel="noopener">
                    <span aria-hidden="true">💬</span> WhatsApp
                </a>
            <?php endif; ?>

            <?php if ($lat && $lng): ?>
                <a href="https://www.openstreetmap.org/?mlat=<?php echo esc_attr($lat); ?>&mlon=<?php echo esc_attr($lng); ?>#map=17/<?php echo esc_attr($lat); ?>/<?php echo esc_attr($lng); ?>"
                   class="btn btn-navigate" target="_blank" rel="noopener">
                    <span aria-hidden="true">🗺️</span> ניווט
                </a>
            <?php endif; ?>
        </div>
    </div>
</article>
