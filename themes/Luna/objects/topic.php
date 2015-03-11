<div class="list-group-item <?php echo $item_status ?><?php if ($cur_topic['soft'] == true) echo ' soft'; ?>">
	<span class="middot">&middot;</span>
	<span class="hidden-xs hidden-sm hidden-md hidden-lg">
		<?php echo forum_number_format($topic_count + $start_from) ?>
	</span>
	<a href="<?php echo $url ?>"><?php echo $subject ?></a> <?php echo $subject_new_posts ?> <?php echo $subject_multipage ?> <?php echo $by ?>
	<?php if ($cur_topic['moved_to'] == 0) { ?>
		&middot; <span class="text-muted">
			<?php echo $last_post_date ?>
			<span class="hidden-xs">
				<?php echo $last_poster ?>
			</span>
		</span>
		<span class="pull-right label label-default"><?php echo forum_number_format($cur_topic['num_replies']) ?></span>
	<?php } ?>
</div>