=== שלומי אונליין ===

Contributors: BDNHOST
Theme URI: https://shlomi.online
Author: BDNHOST
Author URI: https://bdnhost.net
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.1
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: news, rtl-language-support, custom-colors, custom-menu, featured-images, threaded-comments, translation-ready, responsive-layout, accessibility-ready

תבנית WordPress מקצועית לפורטל חדשות מקומי בעברית עם עיצוב מודרני ורספונסיבי.

== Description ==

תבנית "שלומי אונליין" היא תבנית WordPress מקצועית ומלאה המיועדת לאתרי חדשות מקומיים, פורטלים קהילתיים ובלוגים בעברית. התבנית בנויה עם דגש על ביצועים, נגישות ועיצוב מודרני.

**תכונות עיקריות:**

* עיצוב מודרני ומקצועי לאתרי חדשות
* תמיכה מלאה בעברית (RTL)
* רספונסיבית לחלוטין - תצוגה מושלמת בכל המכשירים
* תפריט המבורגר מותאם למובייל
* אזור חדשות אחרונות עם אנימציה
* כתבה ראשית מודגשת
* רשת כתבות מעוצבת (Grid Layout)
* Sidebar עם widgets מותאמים אישית
* תמיכה בתגובות מתורגמות
* אופטימיזציה מלאה ל-SEO
* Lazy Loading לתמונות (שיפור ביצועים)
* Skip to Content לנגישות
* Breadcrumbs לניווט
* כפתור חזרה למעלה
* עמוד 404 מעוצב ומותאם
* Widget כתבות פופולריות
* תיבת מחבר עם אווטר
* כפתורי שיתוף לרשתות חברתיות
* תמיכה בתמונות ראשיות
* לוגו מותאם אישית
* צבעים מותאמים אישית (CSS Variables)
* קוד נקי ומאובטח

**טכנולוגיות:**

* HTML5 Semantic
* CSS3 עם Flexbox ו-Grid
* JavaScript (jQuery)
* WordPress Hooks & Filters
* WCAG 2.1 Accessibility Standards
* Mobile-First Responsive Design

**תמיכה ב-RTL:**

התבנית מגיעה עם קובץ rtl.css מלא לתמיכה מושלמת בעברית ושפות RTL אחרות.

**אופטימיזציה:**

* Lazy Loading אוטומטי לכל התמונות
* Hardware Acceleration לאנימציות
* CSS Minification Ready
* Responsive Images Support
* Fast Page Load Times

**נגישות:**

* ARIA Labels על כל הכפתורים
* Skip to Content Link
* Keyboard Navigation Support
* Screen Reader Friendly
* Focus Visible Indicators
* Semantic HTML5

== Installation ==

1. העלה את תיקיית התבנית `shlomi-online` לתיקייה `/wp-content/themes/`
2. הפעל את התבנית דרך תפריט 'תבניות' ב-WordPress
3. התאם אישית את התבנית דרך Customizer (מראה > התאמה אישית)
4. הגדר תפריט ראשי ב-Appearance > Menus וקשר אותו ל"תפריט ראשי"
5. הוסף Widgets לאזור Sidebar ולאזורי Footer

== Frequently Asked Questions ==

= איך מוסיפים לוגו לאתר? =

עבור אל מראה > התאמה אישית > זהות האתר > לוגו, והעלה את הלוגו שלך. הגודל המומלץ: 180x60 פיקסלים.

= איך משנים את הצבעים? =

ניתן לשנות את הצבעים בקובץ style.css תחת המשתנים ב-:root:
* --primary-blue: צבע כחול ראשי
* --primary-red: צבע אדום ראשי
* --light-gray: רקע אפור בהיר
* --dark-gray: צבע טקסט כהה
* --border-gray: צבע גבולות

= איך מוסיפים תפריט? =

1. עבור אל מראה > תפריטים
2. צור תפריט חדש
3. הוסף פריטים לתפריט
4. בחר "תפריט ראשי" במיקומי תפריט
5. שמור תפריט

= התבנית תומכת ב-WooCommerce? =

כרגע התבנית אינה כוללת תמיכה מלאה ב-WooCommerce, אך ניתן להוסיף תמיכה בעתיד.

= איך מוסיפים Widgets? =

עבור אל מראה > Widgets ו גרור widgets לאזורים הבאים:
* Sidebar ראשי
* Footer 1-4

= התבנית תומכת בתוספים? =

כן! התבנית תומכת ברוב תוספי WordPress הפופולריים כולל:
* Yoast SEO
* Contact Form 7
* Akismet
* Jetpack
* Classic Editor / Gutenberg

= יש בעיות תצוגה במובייל? =

התבנית נבדקה במכשירים רבים ואמורה לעבוד מצוין. אם נתקלת בבעיה, אנא דווח עליה.

== Screenshots ==

1. דף הבית - תצוגה מלאה
2. כתבה בודדת עם תיבת מחבר
3. עמוד 404 מעוצב
4. תפריט מובייל
5. Sidebar עם Widgets
6. Footer עם 4 אזורים

הערה: Screenshots יש להוסיף בפורמט screenshot.png בגודל 1200x900px

== Changelog ==

= 1.1 - 2024-11-14 =
* תוקן: הוספת פונקציה חסרה shlomi_custom_comment לתגובות
* תוקן: sanitization של $_GET לשיפור אבטחה
* תוקן: בדיקת get_the_category() למניעת שגיאות
* תוקן: fallback לקטגוריית "עידכונים" ב-breaking news
* נוסף: Skip to Content Link לנגישות
* נוסף: Lazy Loading אוטומטי לכל התמונות
* נוסף: CSS מלא לכפתור Back to Top
* נוסף: CSS מלא לדף 404
* נוסף: CSS מלא ל-Breadcrumbs
* נוסף: CSS מלא ל-Comments
* נוסף: CSS מלא ל-Author Box
* נוסף: CSS מלא ל-Share Buttons
* נוסף: Performance Optimizations (Hardware Acceleration)
* נוסף: aria-hidden על אמוג'ים לשיפור נגישות
* נוסף: Focus-Visible Indicators
* נוסף: Screen Reader Text Support
* שופר: קוד PHP נקי יותר ובטוח יותר
* שופר: נגישות כללית של התבנית

= 1.0 - 2024-01-01 =
* שחרור ראשוני

== Upgrade Notice ==

= 1.1 =
גרסה זו כוללת תיקוני אבטחה חשובים ושיפורי נגישות. מומלץ לעדכן.

= 1.0 =
שחרור ראשוני

== Credits ==

* פותח על ידי: BDNHOST (https://bdnhost.net)
* גופנים: Google Fonts - Heebo (https://fonts.google.com/specimen/Heebo)
* אייקונים: Unicode Emoji (זמני - מומלץ להחליף ב-Font Awesome)

== Theme Support ==

לתמיכה טכנית או שאלות:
* אתר: https://bdnhost.net
* אימייל: support@bdnhost.net

== Copyright ==

שלומי אונליין WordPress Theme, Copyright 2024 BDNHOST
שלומי אונליין מופץ תחת GNU GPL v2 ומעלה.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

תבנית זו משתמשת במשאבים הבאים:

* Google Fonts (Heebo) - SIL Open Font License 1.1
  https://fonts.google.com/specimen/Heebo

* Normalize.css - MIT License
  https://necolas.github.io/normalize.css/

כל הקוד האחר והעיצוב הם רכוש בלעדי של BDNHOST ומופצים תחת GPL v2.
