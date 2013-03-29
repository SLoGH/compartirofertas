<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die(_e('Please do not load this page directly. Thanks!', 'churchope'));

if (post_password_required())
{
	?>
	<p class="nocomments clearfix"><?php _e('This post is password protected. Enter the password to view comments.', 'churchope'); ?></p>
	<?php
	return;
}
?>




<?php if (have_comments()) : ?>
<div class="comments clearfix">
		<h3 id="comments"><?php printf(_n('One Response to &quot;%2$s&quot;', '%1$s Responses to &quot;%2$s&quot;', get_comments_number(), 'churchope'), number_format_i18n(get_comments_number()), get_the_title()); ?></h3>
		<ol class="commentlist">
	<?php wp_list_comments('callback=list_comments'); ?>
		</ol>




			<?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : // Are there comments to navigate through?  ?>
			<div class="pagination clearfix">
				<?php
				paginate_comments_links(array(
					'type' => 'list'
				))
				?>
			</div>
		<?php endif; // check for comment navigation  ?>

</div>

<?php else : // this is displayed if there are no comments so far  ?>

		<?php if ('open' == $post->comment_status) : ?>
			<!-- If comments are open, but there are no comments. -->

		<?php else : // comments are closed ?>
			<!-- If comments are closed. -->

	<?php endif; ?>
<?php endif; ?>

<?php if (comments_open()) : ?>
	<?php
	global $aria_req, $am_validate;
	$am_validate = true;
	$commenter = wp_get_current_commenter();
	$comment_args = array('fields' => apply_filters('comment_form_default_fields', array(
			'author' => '<p class="comment-form-author clearfix">' .
			'<input id="author" name="author" placeholder="'.__('Name','churchope').'" type="text" value="' .
			esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . ' class="required" />' .
			'',
			'email' => '' .
			'<input id="email" name="email" placeholder="'.__('E-mail','churchope').'" type="text" value="' . esc_attr($commenter['comment_author_email']) . '" size="30"' . $aria_req . ' class="required" />' .
			'</p>',
			'url' => '<p class="comment-form-url">' .
			'<input id="url" name="url" placeholder="'.__('Website','churchope').'" type="text" value="' . esc_attr($commenter['comment_author_url']) . '" size="30"' . $aria_req . ' />' .
			'</p>')),
		'comment_field' => '<p class="comment-form-comment">' .
		'<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" class="required"></textarea>' .
		'</p>',
		'comment_notes_after' => '',
		'comment_notes_before' => '',
		'sumbit' => 'test'
	);
	comment_form($comment_args);
	?>
<?php endif; ?>