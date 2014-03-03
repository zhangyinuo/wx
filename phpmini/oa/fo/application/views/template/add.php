<?php
	require_javascript("og/ObjectPicker.js");
	require_javascript("og/modules/addTemplate.js");
	require_javascript("og/DateField.js");
	
	$genid = gen_id();
	$object = $cotemplate;
?>
<form id="templateForm" style='height:100%;background-color:white' class="internalForm" action="<?php echo $cotemplate->isNew() ? get_url('template', 'add') : $cotemplate->getEditUrl() ?>" method="post" enctype="multipart/form-data" onsubmit="return og.templateConfirmSubmit('<?php echo $genid ?>') && og.handleMemberChooserSubmit('<?php echo $genid; ?>', <?php echo $cotemplate->manager()->getObjectTypeId() ?>);">

<div id = "templateConteiner" class="template">
<div class="coInputHeader">
<div class="coInputHeaderUpperRow">
	<div class="coInputTitle"><table style="width:535px"><tr><td><?php echo $cotemplate->isNew() ? lang('new template') : lang('edit template') ?>
	</td><td style="text-align:right"><?php echo submit_button($cotemplate->isNew() ? lang('save template') : lang('save changes'),'s',array('style'=>'margin-top:0px;margin-left:10px')) ?></td></tr></table>
	</div>
</div>
	<div>
	<?php echo label_tag(lang('name'), $genid . 'templateFormName', true) ?>
	<?php echo text_field('template[name]', array_var($template_data, 'name'), 
		array('id' => $genid . 'templateFormName', 'class' => 'name long', 'tabindex' => '1')) ?>
	</div>
	
	<?php $categories = array(); Hook::fire('object_edit_categories', $object, $categories); ?>
	
	<div style="padding-top:5px">
		<?php foreach ($categories as $category) { ?>
			- <a href="#" class="option" <?php if ($category['visible']) echo 'style="font-weight: bold"'; ?> onclick="og.toggleAndBolden('<?php echo $genid . $category['name'] ?>', this)"><?php echo lang($category['name'])?></a>
		<?php } ?>
	</div>
</div>
<div class="coInputSeparator"></div>
<div class="coInputMainBlock">	

	<div>
		<fieldset>
		<legend><?php echo label_tag(lang('description'), 'templateFormDescription', false) ?></legend>
		
		<?php echo editor_widget('template[description]', array_var($template_data, 'description'), 
			array('id' => $genid . 'templateFormDescription', 'class' => 'long', 'tabindex' => '2')) ?>
		</fieldset>
	</div>
	
	<div id="<?php echo $genid ?>add_template_objects_div">
		<fieldset>
			<legend><?php echo lang('tasks')?></legend>
			<br/>
			<div id="<?php echo $genid ?>template_tasks_div">
				
			</div>
			<br/>
			<div class="db-ico ico-task" style="float: left;"></div>
			
			<a id="<?php echo $genid ?>add_template_task" class='internalLink dashboard-link' href="#" onmousedown="og.openLink(og.getUrl('task', 'add_task', {template_task:1, template_id:<?php echo $cotemplate->getId()? $cotemplate->getId():0 ?>}), {caller:'new_task_template'});" onclick="Ext.getCmp('tabs-panel').activate('new_task_template');">
		<?php echo lang('add a new task to this template') ?></a>
		 
		 <?php if (config_option('use_milestones')){ ?>	
			<br/>
		
		 		<div class="db-ico ico-milestone" style="float: left;"></div>
			
			<a id="<?php echo $genid ?>add_template_milestone" class='internalLink dashboard-link' href="#" onmousedown="og.openLink(og.getUrl('milestone', 'add', {template_milestone:1, template_id:<?php echo $cotemplate->getId()? $cotemplate->getId():0 ?>}}), {caller:'new_task_template'});" onclick="Ext.getCmp('tabs-panel').activate('new_task_template');">
		 	<?php echo lang('add a new milestone to this template') ?></a>
		 <?php }?>
		
		
			
		</fieldset>
	</div>
	
	<div id="<?php echo $genid ?>add_template_parameters_div">
		<fieldset><legend><?php echo lang("variables")?></legend>
			<a id="<?php echo $genid ?>params" href="#" onclick="og.promptAddParameter(this, 0)"><?php echo lang('add a variable to this template') ?></a>
		</fieldset>
	</div>
	<?php
		if (isset($add_to) && $add_to) {
			echo input_field("add_to", "true", array("type"=>"hidden"));
		}
	?>
	
	<?php foreach ($categories as $category) { ?>
	<div <?php if (!$category['visible']) echo 'style="display:none"' ?> id="<?php echo $genid . $category['name'] ?>">
	<fieldset>
		<legend><?php echo lang($category['name'])?><?php if ($category['required']) echo ' <span class="label_required">*</span>'; ?></legend>
		<?php echo $category['content'] ?>
	</fieldset>
	</div>
	<?php } ?>
	
	<?php echo submit_button($cotemplate->isNew() ? lang('add template') : lang('save changes'),'s',
		array('style'=>'margin-top:0px', 'tabindex' => '3')) ?>
</div>
</div>
</form>

<script>
		og.actual_template_id = <?php echo $cotemplate->getId()? $cotemplate->getId():'0' ?>;
		og.loadTemplateVars();
		Ext.get('<?php echo $genid ?>templateFormName').focus();
	<?php
	
	
	if (isset($parameters) && is_array($parameters)) {
		foreach ($parameters as $param) { ?>
		og.addParameterToTemplate(document.getElementById('<?php echo $genid ?>params'), '<?php echo str_replace("'","\'",$param->getName()) ?>','<?php echo $param->getType() ?>'); 
	<?php }
	}?>

	og.add_template_input_divs = [];
	var inputs = document.getElementById('<?php echo $genid ?>add_template_objects_div').getElementsByTagName('input');
	for (var i=0; i < inputs.length; i++) {
		if(inputs[i].className == 'objectID') {
			og.add_template_input_divs[inputs[i].value] = inputs[i].parentNode.parentNode.id;
		}
	}

	for (x=0; x<og.templateObjects.length; x++) {
		var tobj = og.templateObjects[x];
		if (tobj.type == 'task') og.drawTemplateObjectMilestonesCombo(Ext.get(og.add_template_input_divs[tobj.object_id]).dom, tobj);
	}

	og.redrawTemplateObjectsLists = function(data){
		if(data.type == "template_task"){
			og.redrawTemplateTaskList(data);
		}else if(data.type == "template_milestone"){
			og.redrawTemplateMilestoneList(data);
		}
		
	}

	og.redrawTemplateTaskList = function(data){
		if(data.action == "edit"){
			$('#objectDiv'+data.id).remove();
		}
		if(data.milestone_id){
			og.addObjectToTemplate(('subTasksDiv'+data.milestone_id), data, true);
			$('#subtasksExpander'+data.milestone_id).show();
		}else if(data.parent_id){
			og.addObjectToTemplate(('subTasksDiv'+data.parent_id), data, true);
			$('#subtasksExpander'+data.parent_id).show();
		}else{
			og.addObjectToTemplate(('<?php echo $genid ?>template_tasks_div'), data, true);
		}
	}

	og.redrawTemplateMilestoneList = function(data){
		og.addObjectToTemplate(('<?php echo $genid ?>template_tasks_div'), data, true);
	}

	

	<?php if (is_array($objects)) {	
		foreach ($objects as $o) {	?>			
			og.redrawTemplateObjectsLists(<?php echo json_encode($o)?>);			
			<?php 
					if(isset($object_properties) && is_array($object_properties)){
						$oid = $o["object_id"];
						if(isset($object_properties[$oid])){
							foreach($object_properties[$oid] as $objProp){  
								$property = $objProp->getProperty();
								$value =  str_replace("'","\'",$objProp->getValue());

							?>
							og.addTemplateObjectProperty(<?php echo $oid ?>, <?php echo $oid ?>, '<?php echo $property ?>', '<?php echo $value ?>');
					  <?php }
						}
					}	
					?>			
	<?php } }?>
			
	var p = og.getParentContentPanel(Ext.get('<?php echo $genid ?>templateFormName'));
	
	$( "#<?php echo $genid ?>templateFormName" ).change(function() {
		Ext.getCmp(p.id).setPreventClose(true);
	});
	$( "#<?php echo $genid ?>templateFormDescription" ).change(function() {
		Ext.getCmp(p.id).setPreventClose(true);
	});
	$('#<?php echo $genid ?>template_tasks_div').bind("DOMSubtreeModified",function(){
		  Ext.getCmp(p.id).setPreventClose(true);
		});
	$('#<?php echo $genid ?>add_template_parameters_div').bind("DOMSubtreeModified",function(){
		  Ext.getCmp(p.id).setPreventClose(true);
	});
	$("#templateForm" ).submit(function( event ) {
		Ext.getCmp(p.id).setPreventClose(false);
	});
			
	og.editTempObj = function(id, type){
		if(type == "template_task"){
			og.openLink(og.getUrl('task', 'edit_task', {id: id, template_task:1}), {caller:'new_task_template'});
		}else if(type == "template_milestone"){
			og.openLink(og.getUrl('milestone', 'edit', {id: id, template_milestone:1}), {caller:'new_task_template'});
		}
	}

	og.viewTempObj = function(id, type){
		if(type == "template_task"){
			og.openLink(og.getUrl('task', 'view', {id: id, template_task:1}), {caller:'new_task_template'});
		}else if(type == "template_milestone"){
			og.openLink(og.getUrl('milestone', 'edit', {id: id, template_milestone:1}), {caller:'new_task_template'});
		}
	}
	
	
</script>