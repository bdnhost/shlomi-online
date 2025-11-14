<?php
/**
 * The template for displaying comments
 * תבנית תגובות - שלומי אונליין
 */

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php
            $comments_number = get_comments_number();
            if ($comments_number === 1) {
                echo '💬 תגובה אחת';
            } else {
                printf('💬 %s תגובות', number_format_i18n($comments_number));
            }
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style' => 'ol',
                'short_ping' => true,
                'avatar_size' => 60,
                'callback' => 'shlomi_custom_comment',
            ));
            ?>
        </ol>

        <?php
        the_comments_navigation(array(
            'prev_text' => '← תגובות קודמות',
            'next_text' => 'תגובות חדשות יותר →',
        ));
        ?>

    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
        <p class="no-comments">התגובות סגורות.</p>
    <?php endif; ?>

    <?php
    // טופס תגובה מותאם אישית
    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $aria_req = ($req ? " aria-required='true'" : '');

    comment_form(array(
        'title_reply' => '✍️ השאר תגובה',
        'title_reply_to' => '✍️ השב ל-%s',
        'cancel_reply_link' => 'ביטול תשובה',
        'label_submit' => 'שלח תגובה',
        'class_submit' => 'btn btn-primary',
        'comment_field' => '<p class="comment-form-comment">
            <label for="comment">תגובה <span class="required">*</span></label>
            <textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required"></textarea>
        </p>',
        'fields' => array(
            'author' => '<p class="comment-form-author">
                <label for="author">שם <span class="required">*</span></label>
                <input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" maxlength="245"' . $aria_req . ' />
            </p>',
            'email' => '<p class="comment-form-email">
                <label for="email">אימייל <span class="required">*</span></label>
                <input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" maxlength="100" aria-describedby="email-notes"' . $aria_req . ' />
            </p>',
            'url' => '<p class="comment-form-url">
                <label for="url">אתר (אופציונלי)</label>
                <input id="url" name="url" type="url" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" maxlength="200" />
            </p>',
        ),
    ));
    ?>

</div>