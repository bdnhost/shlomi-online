<footer class="site-footer">
    <div class="footer-widgets">
        <?php if (is_active_sidebar('footer-1')): ?>
            <div class="footer-column">
                <?php dynamic_sidebar('footer-1'); ?>
            </div>
        <?php else: ?>
            <div class="footer-widget">
                <h3>אודות</h3>
                <ul>
                    <li><a href="<?php echo home_url('/about'); ?>">אודות שלומי אונליין</a></li>
                    <li><a href="<?php echo home_url('/team'); ?>">הצוות שלנו</a></li>
                    <li><a href="<?php echo home_url('/contact'); ?>">צור קשר</a></li>
                    <li><a href="<?php echo home_url('/advertise'); ?>">פרסם אצלנו</a></li>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (is_active_sidebar('footer-2')): ?>
            <div class="footer-column">
                <?php dynamic_sidebar('footer-2'); ?>
            </div>
        <?php else: ?>
            <div class="footer-widget">
                <h3>קטגוריות</h3>
                <ul>
                    <?php
                    wp_list_categories(array(
                        'title_li' => '',
                        'number' => 5,
                    ));
                    ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (is_active_sidebar('footer-3')): ?>
            <div class="footer-column">
                <?php dynamic_sidebar('footer-3'); ?>
            </div>
        <?php else: ?>
            <div class="footer-widget">
                <h3>שירותים</h3>
                <ul>
                    <li><a href="#">לוח מודעות</a></li>
                    <li><a href="#">מזג אוויר</a></li>
                    <li><a href="#">לוח שנה קהילתי</a></li>
                    <li><a href="#">מדריך עסקים</a></li>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (is_active_sidebar('footer-4')): ?>
            <div class="footer-column">
                <?php dynamic_sidebar('footer-4'); ?>
            </div>
        <?php else: ?>
            <div class="footer-widget">
                <h3>עקבו אחרינו</h3>
                <ul>
                    <li><a href="https://facebook.com/shlomionline" target="_blank">📘 פייסבוק</a></li>
                    <li><a href="https://instagram.com/shlomionline" target="_blank">📷 אינסטגרם</a></li>
                    <li><a href="https://twitter.com/shlomionline" target="_blank">🐦 טוויטר</a></li>
                    <li><a href="https://youtube.com/shlomionline" target="_blank">📺 יוטיוב</a></li>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <div class="site-info" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap;">
        <p>
            © <?php echo date('Y'); ?>
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
            - כל הזכויות שמורות |
            <a href="<?php echo home_url('/terms'); ?>">תנאי שימוש</a> |
            <a href="<?php echo home_url('/privacy'); ?>">מדיניות פרטיות</a>
        </p>

        <!-- קרדיט ל-BDNHOST -->
        <p style="margin:0; display:flex; align-items:center; font-size:0.9em;">
            תבנית נבנתה על ידי 
            <a href="https://bdnhost.net" target="_blank" style="display:flex; align-items:center; margin-left:5px;">
                <img src="https://bdnhost.net/wp-content/uploads/2024/04/cropped-bdnhost-1-1.png" alt="BDNHOST" style="height:20px; margin-left:5px;">
                BDNHOST
            </a>
        </p>
    </div>
</footer>
</div> <!-- סגירת #page -->

<?php wp_footer(); ?>
</body>
</html>