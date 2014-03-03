<?php 
	require_javascript('og/modules/addMessageForm.js');
	$genid = gen_id();
	$object = $company;
	if($company->isNew()) { 
		$form_action = get_url('contact', 'add_company'); 
	} else {
		$form_action = $company->getEditUrl();
	}
	$renderContext = has_context_to_render($company->manager()->getObjectTypeId());
	$visible_cps = CustomProperties::countVisibleCustomPropertiesByObjectType($object->getObjectTypeId());	
?>
<form style="height:100%;background-color:white" class="internalForm" action="<?php echo $form_action ?>" method="post">


<div class="adminAddCompany">
  <div class="adminHeader">
  	<div class="adminHeaderUpperRow">
  		<div class="adminTitle"><table style="width:535px"><tr><td>
  			<?php echo $company->isNew() ? lang('new company') : lang('edit company') ?>
  		</td><td style="text-align:right">
  			<?php echo submit_button($company->isNew() ? lang('add company') : lang('save changes'), 's', array('style'=>'margin-top:0px;margin-left:10px')) ?>
  		</td></tr></table></div>
  	</div>
  	
  <div>
    <?php echo label_tag(lang('name'), $genid.'clientFormName', true) ?>
    <?php echo text_field('company[first_name]', array_var($company_data, 'first_name'), 
    	array('class' => 'title', 'tabindex' => '1', 'id' => $genid.'clientFormName')) ?>
  </div>
  
  	<?php $categories = array(); Hook::fire('object_edit_categories', $object, $categories); ?>
  	
  	<div style="padding-top:5px">
	  	<?php if ( $renderContext ) :?>
			<a href="#" class="option" onclick="og.toggleAndBolden('<?php echo $genid ?>add_company_select_context_div',this)"><?php echo lang('context') ?></a> -
		<?php endif; ?>
			<a href="#" class="option" tabindex=0 onclick="og.toggleAndBolden('add_company_timezone',this)"><?php echo lang('timezone') ?></a> -
			<a href="#" class="option <?php echo $visible_cps>0 ? 'bold' : ''?>" onclick="og.toggleAndBolden('<?php echo $genid ?>add_custom_properties_div',this)"><?php echo lang('custom properties') ?></a> -
			<a href="#" class="option" onclick="og.toggleAndBolden('<?php echo $genid ?>add_contact_notes', this)"><?php echo lang('notes') ?></a> -
			<a href="#" class="option" onclick="og.toggleAndBolden('<?php echo $genid ?>add_subscribers_div',this)"><?php echo lang('object subscribers') ?></a>
		<?php if($object->isNew() || $object->canLinkObject(logged_user())) { ?> - 
			<a href="#" class="option" onclick="og.toggleAndBolden('<?php echo $genid ?>add_linked_objects_div',this)"><?php echo lang('linked objects') ?></a>
		<?php } ?>
		<?php foreach ($categories as $category) { ?>
			- <a href="#" class="option" <?php if ($category['visible']) echo 'style="font-weight: bold"'; ?> onclick="og.toggleAndBolden('<?php echo $genid . $category['name'] ?>', this)"><?php echo lang($category['name'])?></a>
		<?php } ?>
	</div>
  </div>
  <div class="adminSeparator"></div>
  <div class="adminMainBlock">
	
	<?php if ( $renderContext ) :?>
		<div id="<?php echo $genid ?>add_company_select_context_div" style="display:none">
			<fieldset>
				<legend><?php echo lang('context')?></legend>
				<?php 
				$listeners = array('on_selection_change' => 'og.reload_subscribers("'.$genid.'",'.$object->manager()->getObjectTypeId().')');
				if ($company->isNew()) {
					render_member_selectors($company->manager()->getObjectTypeId(), $genid, null, array('select_current_context' => true, 'listeners' => $listeners));
				} else {
					render_member_selectors($company->manager()->getObjectTypeId(), $genid, $company->getMemberIds(), array('listeners' => $listeners)); 
				} ?>
			</fieldset>
		</div>
	<?php endif ;?>	
	
	<div id='<?php echo $genid ?>add_custom_properties_div' style="<?php echo ($visible_cps > 0 ? "" : "display:none") ?>">
		<fieldset>
			<legend><?php echo lang('custom properties') ?></legend>
			<?php echo render_object_custom_properties($object, false) ?>
			<?php echo render_add_custom_properties($object); ?>
		</fieldset>
	</div>
	
	<div style="display:none" id="<?php echo $genid ?>add_contact_notes">
		<fieldset><legend><?php echo lang('notes') ?></legend>
		    <div>
		      <?php echo label_tag(lang('notes'), $genid.'profileFormNotes') ?>
		      <?php echo textarea_field('company[comments]', array_var($company_data, 'comments'), array('id' => $genid.'profileFormNotes', 'tabindex' => '275')) ?>
		    </div>
		</fieldset>
	</div>
	
	<div id="<?php echo $genid ?>add_subscribers_div" style="display:none">
		<fieldset>
		<legend><?php echo lang('object subscribers') ?></legend>
		<?php $subscriber_ids = array();
			if (!$object->isNew()) {
				$subscriber_ids = $object->getSubscriberIds();
			} else {
				$subscriber_ids[] = logged_user()->getId();
			} 
		?><input type="hidden" id="<?php echo $genid ?>subscribers_ids_hidden" value="<?php echo implode(',',$subscriber_ids)?>"/>
		<div id="<?php echo $genid ?>add_subscribers_content">
		<?php //echo render_add_subscribers($object, $genid); ?>
		</div>
		</fieldset>
	</div>
	
	

	<?php if($object->isNew() || $object->canLinkObject(logged_user())) { ?>
	<div style="display:none" id="<?php echo $genid ?>add_linked_objects_div">
	<fieldset>
		<legend><?php echo lang('linked objects') ?></legend>
		<?php echo render_object_link_form($object) ?>
	</fieldset>	
	</div>
	<?php } // if ?>
		
	
  <div id="add_company_timezone" style="display:none">
  <fieldset>
    <legend><?php echo lang('timezone') ?></legend>
    <?php echo label_tag(lang('timezone'), 'clientFormTimezone', false)?>
    <?php echo select_timezone_widget('company[timezone]', array_var($company_data, 'timezone'), array('id' => 'clientFormTimezone', 'class' => 'long', 'tabindex' => '190')) ?>
  </fieldset>
  </div>
  
  <?php foreach ($categories as $category) { ?>
	<div <?php if (!$category['visible']) echo 'style="display:none"' ?> id="<?php echo $genid . $category['name'] ?>">
	<fieldset>
		<legend><?php echo lang($category['name'])?><?php if ($category['required']) echo ' <span class="label_required">*</span>'; ?></legend>
		<?php echo $category['content'] ?>
	</fieldset>
	</div>
	<?php } ?>
	
	  <table style="margin-left:12px;margin-right:12px; margin-top:12px">
		<tr>
			<td style="padding-right:30px">
			<table style="width:100%">
			<tr>
				<td class="td-pr"><?php echo label_tag(lang('address'), $genid.'profileFormWAddress') ?></td>
				<td><?php echo textarea_field('company[address]', array_var($company_data, 'address'), array('id' => $genid.'clientFormAddress', 'tabindex' => '10', 'class' => 'short textarea')) ?></td>
			</tr><tr>
				<td class="td-pr"><?php echo label_tag(lang('city'), $genid.'clientFormCity') ?></td>
				<td><?php echo text_field('company[city]', array_var($company_data, 'city'), array('id' => $genid.'clientFormCity', 'tabindex' => '30', 'maxlength' => 50)) ?></td>
			</tr><tr>
				<td class="td-pr"><?php echo label_tag(lang('state'), $genid.'clientFormState') ?></td>
				<td><?php echo text_field('company[state]', array_var($company_data, 'state'), array('id' => $genid.'clientFormState', 'tabindex' => '40', 'maxlength' => 50)) ?></td>
			</tr><tr>
				<td class="td-pr"><?php echo label_tag(lang('zipcode'), $genid.'clientFormZipcode') ?></td>
				<td><?php echo text_field('company[zipcode]', array_var($company_data, 'zipcode'), array('id' => $genid.'clientFormZipcode', 'tabindex' => '50', 'maxlength' => 30)) ?></td>
			</tr><tr>
				<td class="td-pr"><?php echo label_tag(lang('country'), $genid.'clientFormCountry') ?></td>
				<td><?php echo select_country_widget('company[country]', array_var($company_data, 'country'), array('id' => $genid.'clientFormCountry', 'tabindex' => '60')) ?></td>
			</tr>
			</table>
			</td><td>
			<table style="width:100%">
			<tr>
				<td class="td-pr"><?php echo label_tag(lang('phone'), $genid.'clientFormPhoneNumber') ?> </td>
				<td><?php echo text_field('company[phone_number]', array_var($company_data, 'phone_number'), array('id' => $genid.'clientFormPhoneNumber', 'tabindex' => '70', 'maxlength' => 50)) ?></td>
			</tr><tr>
				<td class="td-pr"><?php echo label_tag(lang('fax'), $genid.'clientFormFaxNumber') ?> </td>
				<td><?php echo text_field('company[fax_number]', array_var($company_data, 'fax_number'), array('id' => $genid.'clientFormFaxNumber', 'tabindex' => '80', 'maxlength' => 50)) ?></td>
			</tr><tr height=10><td></td><td></td></tr><tr>
				<td class="td-pr"><?php echo label_tag(lang('email address'), $genid.'clientFormEmail') ?> </td>
				<td><?php echo text_field('company[email]', array_var($company_data, 'email'), array('id' => $genid.'clientFormAssistantNumber', 'tabindex' => '90')) ?></td>
			</tr><tr height=10><td></td><td></td></tr><tr>
				<td class="td-pr"><?php echo label_tag(lang('homepage'), $genid.'clientFormHomepage') ?></td>
				<td><?php echo text_field('company[homepage]', array_var($company_data, 'homepage'), array('id' => $genid.'clientFormCallbackNumber', 'tabindex' => '100')) ?></td>
			</tr>
			</table>
			</td>
		</tr>
	</table>
	

  
<?php if(!$company->isNew() && $company->isOwnerCompany()) { ?>
  <?php echo submit_button(lang('save changes'), 's', array('tabindex' => '20000')) ?>
<?php } else { ?>
  <?php echo submit_button($company->isNew() ? lang('add company') : lang('save changes'), 's', array('tabindex' => '20000')) ?>
<?php } // if ?>
</div>
</div>
</form>

<script>
	Ext.get('<?php echo $genid ?>clientFormName').focus();
</script>