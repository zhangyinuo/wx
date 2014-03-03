<?php
	require_javascript('og/modules/addMessageForm.js');
	set_page_title($file->isNew() ? lang('add webpage') : lang('edit webpage'));
	$genid = gen_id();
	
	$visible_cps = CustomProperties::countVisibleCustomPropertiesByObjectType($file->getObjectTypeId());
?>
<form id="<?php echo $genid ?>submit-edit-form" style='height: 100%; background-color: white' class="internalForm"
	action="<?php echo $file->isNew() ? get_url('files', 'add_weblink') : $file->getEditUrl() ?>" method="post">


<div class="webpage">
<div class="coInputHeader">
<div class="coInputHeaderUpperRow">
<div class="coInputTitle">
<table style="width: 535px">
	<tr>
		<td><?php echo $file->isNew() ? lang('new webpage') : lang('edit webpage') ?>
		</td>
		<td style="text-align: right"><?php echo submit_button($file->isNew() ? lang('add webpage') : lang('save changes'),'s',array('style'=>'margin-top:0px;margin-left:10px', 'tabindex' => '20')) ?></td>
	</tr>
</table>
</div>

</div>
<div><?php echo label_tag(lang('title'), 'webpageFormTitle', true) ?> <?php echo text_field('webpage[name]', array_var($file_data, 'name'), array('class' => 'title', 'tabindex' => '1', 'id' => $genid.'webpageFormTitle')) ?>
</div>

<?php $categories = array(); Hook::fire('object_edit_categories', $file, $categories); ?>

	<div style="padding-top: 5px">
		<a href="#" class="option" onclick="og.toggleAndBolden('<?php echo $genid ?>add_webpage_select_context_div',this)"><?php echo lang('context') ?></a>  
		- <a href="#" class="option" tabindex=0 onclick="og.toggleAndBolden('<?php echo $genid?>add_webpage_description_div', this)"><?php echo lang('description') ?></a>
		- <a href="#" class="option <?php echo $visible_cps > 0 ? 'bold' : '' ?>" onclick="og.toggleAndBolden('<?php echo $genid ?>add_custom_properties_div',this)"><?php echo lang('custom properties') ?></a>
		- <a href="#" class="option" onclick="og.toggleAndBolden('<?php echo $genid ?>add_subscribers_div',this)"><?php echo lang('object subscribers') ?></a>
		<?php if($file->isNew() || $file->canLinkObject(logged_user())) { ?>
			- <a href="#" class="option" onclick="og.toggleAndBolden('<?php echo $genid ?>add_linked_objects_div',this)"><?php echo lang('linked objects') ?></a>
		<?php } ?>
		<?php foreach ($categories as $category) { ?>
			- <a href="#" class="option" <?php if ($category['visible']) echo 'style="font-weight: bold"'; ?> onclick="og.toggleAndBolden('<?php echo $genid . $category['name'] ?>', this)"><?php echo lang($category['name'])?></a>
		<?php } ?>
	</div>
</div>
<div class="coInputSeparator"></div>
<div class="coInputMainBlock">

	<input id="<?php echo $genid?>updated-on-hidden" type="hidden" name="updatedon" value="<?php echo $file->isNew()? '' : $file->getUpdatedOn()->getTimestamp() ?>">
	<input id="<?php echo $genid?>merge-changes-hidden" type="hidden" name="merge-changes" value="" >
	<input id="<?php echo $genid?>genid" type="hidden" name="genid" value="<?php echo $genid ?>" >


	<div id="<?php echo $genid ?>add_webpage_select_context_div" style="display:none">
		
		<?php 
		$listeners = array('on_selection_change' => 'og.reload_subscribers("'.$genid.'",'.$file->manager()->getObjectTypeId().')');
		if ($file->isNew()) {
			render_member_selectors($file->manager()->getObjectTypeId(), $genid, null, array('select_current_context' => true, 'listeners' => $listeners)); 
		} else {
			render_member_selectors($file->manager()->getObjectTypeId(), $genid, $file->getMemberIds(), array('listeners' => $listeners)); 
		} ?>
	
	</div>



	<div id="<?php echo $genid?>add_webpage_description_div" style="display: none">
		<fieldset><legend><?php echo label_tag(lang('description'), 'webpageFormDesc') ?></legend>
			<?php echo textarea_field('webpage[description]', array_var($file_data, 'description'), array('class' => 'long', 'id' => 'webpageFormDesc', 'tabindex' => '40')) ?>
		</fieldset>
	</div>
        
	<div id="<?php echo $genid ?>add_custom_properties_div" style="<?php echo ($visible_cps > 0 ? "" : "display:none") ?>">
            <fieldset>	
                <legend><?php echo lang('custom properties') ?></legend>
                <?php echo render_object_custom_properties($file, false) ?>
                <?php echo render_add_custom_properties($file); ?>
            </fieldset>
        </div>
        
	<div id="<?php echo $genid ?>add_subscribers_div" style="display: none">
		<fieldset><legend><?php echo lang('object subscribers') ?></legend>
			<?php $subscriber_ids = array();
				if (!$file->isNew()) {
					$subscriber_ids = $file->getSubscriberIds();
				} else {
					$subscriber_ids[] = logged_user()->getId();
				}
			?><input type="hidden" id="<?php echo $genid ?>subscribers_ids_hidden" value="<?php echo implode(',',$subscriber_ids)?>"/>
			<div id="<?php echo $genid ?>add_subscribers_content">
				<?php //echo render_add_subscribers($webpage, $genid); ?>
			</div>
		</fieldset>
	</div>
	
	<?php if($file->isNew() || $file->canLinkObject(logged_user())) { ?>
	<div style="display: none" id="<?php echo $genid ?>add_linked_objects_div">
		<fieldset><legend><?php echo lang('linked objects') ?></legend>
			<?php echo render_object_link_form($file) ?>
		</fieldset>
	</div>
	<?php } ?>
	
	<div>
		<?php echo label_tag(lang('url'), 'webpageFormURL', true) ?>
		<?php echo text_field('webpage[url]', array_var($file_data, 'url'), array('class' => 'title', 'tabindex' => '50', 'id' => 'webpageFormURL')) ?>
	</div>
	
	<?php foreach ($categories as $category) { ?>
	<div <?php if (!$category['visible']) echo 'style="display:none"' ?> id="<?php echo $genid . $category['name'] ?>">
		<fieldset><legend><?php echo lang($category['name'])?><?php if ($category['required']) echo ' <span class="label_required">*</span>'; ?></legend>
			<?php echo $category['content'] ?>
		</fieldset>
	</div>
	<?php } ?>
	
	<?php echo submit_button($file->isNew() ? lang('add webpage') : lang('save changes'), 's', array('tabindex' => '20000')) ?>

</div>

</div>
</form>

<script>
	Ext.get('<?php echo $genid ?>webpageFormTitle').focus();
</script>
