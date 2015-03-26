<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

$jumbo_style = 'style="background:'.$cur_post['color'].';"';

?>
</div>
<div class="jumbotron<?php echo $item_status ?>"<?php echo $jumbo_style ?>>
	<div class="container">
		<h2>Edit "<?php echo luna_htmlspecialchars($cur_post['subject']) ?>"</h2><span class="pull-right"><a class="btn btn-danger" href="viewtopic.php?id=<?php echo $cur_post['tid'] ?>"><span class="fa fa-fw fa-chevron-left"></span> Cancel</a></span>
	</div>
</div>
<div class="container">
<?php draw_error_panel($errors); ?>
<?php draw_preview_panel($message); ?>

<form id="edit" method="post" action="edit.php?id=<?php echo $id ?>&amp;action=edit" onsubmit="return process_form(this)">
<?php if ($can_edit_subject): ?>
	<input class="info-textfield form-control" type="text" name="req_subject" maxlength="70" value="<?php echo luna_htmlspecialchars(isset($_POST['req_subject']) ? $_POST['req_subject'] : $cur_post['subject']) ?>" />
<?php endif; ?>
<?php draw_editor('20'); ?>
</form>