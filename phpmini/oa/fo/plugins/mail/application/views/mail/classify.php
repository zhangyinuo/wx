<?php $genid = gen_id(); ?>

<form id='formClassify' name='formClassify' style='height:100%;background-color:white'  class="internalForm" action="<?php echo get_url('mail','classify', array('id'=>$email->getId())) ?>" method="post">
	<div class="classify">
		<?php render_member_selectors(MailContents::instance()->getObjectTypeId(), $genid, $email->getMemberIds()); ?>
	</div>
	<input type="hidden" name="id" value="<?php echo $email->getId() ?>" />
	<input type="hidden" name="submit" value="1" />
	<?php echo submit_button(lang('classify'), 's', array('tabindex' => '50')) ?>
</form>
  