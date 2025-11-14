/**
 * שלומי אונליין - JavaScript
 */

(function($) {
    'use strict';

    // המתנה לטעינת העמוד
    $(document).ready(function() {

        // ========================================
        // תפריט המבורגר למובייל
        // ========================================
        
        $('.mobile-menu-toggle').on('click', function() {
            $(this).toggleClass('active');
            $('.main-nav').toggleClass('active');
            
            // שינוי aria-expanded לנגישות
            var expanded = $(this).attr('aria-expanded') === 'true';
            $(this).attr('aria-expanded', !expanded);
        });

        // סגירת תפריט בלחיצה מחוץ לתפריט
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.main-navigation').length) {
                $('.main-nav').removeClass('active');
                $('.mobile-menu-toggle').removeClass('active');
                $('.mobile-menu-toggle').attr('aria-expanded', 'false');
            }
        });

        // סגירת תפריט בלחיצה על קישור
        $('.main-nav a').on('click', function() {
            if ($(window).width() <= 767) {
                $('.main-nav').removeClass('active');
                $('.mobile-menu-toggle').removeClass('active');
                $('.mobile-menu-toggle').attr('aria-expanded', 'false');
            }
        });

        // ========================================
        // Smooth Scroll לעוגן פנימי
        // ========================================
        
        $('a[href*="#"]:not([href="#"])').on('click', function() {
            if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && 
                location.hostname === this.hostname) {
                
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 800);
                    return false;
                }
            }
        });

        // ========================================
        // Sticky Header עם שינוי גודל
        // ========================================
        
        var lastScrollTop = 0;
        var header = $('.site-header');
        var headerHeight = header.outerHeight();
        
        $(window).on('scroll', function() {
            var scrollTop = $(this).scrollTop();
            
            if (scrollTop > headerHeight) {
                header.addClass('scrolled');
                
                // הסתרת/הצגת header בגלילה
                if (scrollTop > lastScrollTop && scrollTop > 300) {
                    // גלילה למטה - הסתר header
                    header.addClass('hidden');
                } else {
                    // גלילה למעלה - הצג header
                    header.removeClass('hidden');
                }
            } else {
                header.removeClass('scrolled hidden');
            }
            
            lastScrollTop = scrollTop;
        });

        // ========================================
        // כפתור חזרה למעלה
        // ========================================
        
        // יצירת כפתור חזרה למעלה
        $('body').append('<button id="back-to-top" aria-label="חזרה למעלה">⬆️</button>');
        
        var backToTop = $('#back-to-top');
        
        // הצגת/הסתרת כפתור
        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 300) {
                backToTop.addClass('show');
            } else {
                backToTop.removeClass('show');
            }
        });
        
        // פעולת כפתור
        backToTop.on('click', function() {
            $('html, body').animate({
                scrollTop: 0
            }, 600);
            return false;
        });

        // ========================================
        // Lazy Loading לתמונות
        // ========================================
        
        if ('IntersectionObserver' in window) {
            var imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var image = entry.target;
                        image.src = image.dataset.src;
                        image.classList.remove('lazy');
                        imageObserver.unobserve(image);
                    }
                });
            });

            $('.lazy').each(function() {
                imageObserver.observe(this);
            });
        }

        // ========================================
        // חיפוש - Focus בחיפוש
        // ========================================
        
        $('.search-box input').on('focus', function() {
            $(this).closest('.search-box').addClass('focused');
        }).on('blur', function() {
            $(this).closest('.search-box').removeClass('focused');
        });

        // ========================================
        // הוספת אנימציה לכרטיסי כתבות
        // ========================================
        
        if ('IntersectionObserver' in window) {
            var animateObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        animateObserver.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });

            $('.post-card').each(function() {
                animateObserver.observe(this);
            });
        }

        // ========================================
        // שיתוף ברשתות חברתיות
        // ========================================
        
        $('.share-buttons a').on('click', function(e) {
            var url = $(this).attr('href');
            
            // פתיחה בחלון קטן לפייסבוק וטוויטר
            if (url.indexOf('facebook.com') > -1 || url.indexOf('twitter.com') > -1) {
                e.preventDefault();
                window.open(url, 'shareWindow', 'width=600,height=400');
                return false;
            }
        });

        // ========================================
        // הדגשת תפריט פעיל
        // ========================================
        
        var currentUrl = window.location.href;
        $('.main-nav a').each(function() {
            if (this.href === currentUrl) {
                $(this).parent().addClass('current-menu-item');
            }
        });

        // ========================================
        // טיפול בטפסים
        // ========================================
        
        $('form').on('submit', function() {
            var btn = $(this).find('button[type="submit"], input[type="submit"]');
            btn.prop('disabled', true).addClass('loading');
            
            // השבת כפתור אחרי 3 שניות
            setTimeout(function() {
                btn.prop('disabled', false).removeClass('loading');
            }, 3000);
        });

        // ========================================
        // התראות
        // ========================================
        
        $('.alert .close').on('click', function() {
            $(this).closest('.alert').fadeOut(300);
        });

        // ========================================
        // Responsive Tables
        // ========================================
        
        $('.entry-content table').wrap('<div class="table-wrapper"></div>');

        // ========================================
        // Print Friendly
        // ========================================
        
        $('body').on('click', '.print-button', function(e) {
            e.preventDefault();
            window.print();
        });

    }); // End document ready

})(jQuery);