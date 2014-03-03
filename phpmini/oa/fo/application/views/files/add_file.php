<?php
require_javascript("og/modules/addFileForm.js");
if ($file->isNew()) {
	$submit_url = get_url('files', 'add_file');
} else if (isset($checkin) && $checkin) {
	$submit_url = $file->getCheckinUrl();
} else {
	$submit_url = $file->getEditUrl();
}

$enableUpload = $file->isNew()
|| (isset($checkin) && $checkin) || ($file->getCheckedOutById() == 0) || ($file->getCheckedOutById() != 0 && logged_user()->isAdministrator())
|| ($file->getCheckedOutById() == logged_user()->getId());
$genid = gen_id();
$object = $file;
$comments_required = config_option('file_revision_comments_required');

$visible_cps = CustomProperties::countVisibleCustomPropertiesByObjectType($object->getObjectTypeId()); 

?>
<form 
	onsubmit=" return og.fileCheckSubmit('<?php echo $genid ?>');"
	class="internalForm" style="height: 100%; background-color: white" id="<?php echo $genid ?>addfile" name="<?php echo $genid ?>addfile" action="<?php echo $submit_url ?>"  method="post"
>
	<input id="<?php echo $genid ?>hfFileIsNew" type="hidden" value="<?php echo $file->isNew()?>">
	<input id="<?php echo $genid ?>hfAddFileAddType" name='file[add_type]' type="hidden" value="regular">
	<input id="<?php echo $genid ?>hfFileId" name='file[file_id]' type="hidden" value="<?php echo array_var($file_data, 'file_id') ?>">
	<input id="<?php echo $genid ?>hfEditFileName" name='file[edit_name]' type="hidden" value="<?php echo clean(array_var($file_data, 'edit_name')) ?>">
	<input id="<?php echo $genid ?>hfType" name='file[type]' type="hidden" value="<?php echo $file->isNew() ? "" : $file->getType() ?>">
	<input name="file[upload_id]" type="hidden" value="<?php echo $genid ?>" />

<div class="file">

<div class="coInputHeader">

<div class="coInputHeaderUpperRow">
<div class="coInputTitle">
<table style="width: 535px">
	<tr>
		<td><?php echo $file->isNew() ? lang('upload file') : (isset($checkin) ? lang('checkin file') : lang('edit file properties')) ?>
		</td>
		<td style="text-align: right">
			<?php echo submit_button($file->isNew() ? lang('add file') : (isset($checkin) ? lang('checkin file') : lang('save changes')),'s',array('style'=>'margin-top:0px;margin-left:10px','id' => $genid.'add_file_submit1', 'tabindex' => '210')) ?>
		</td>
	</tr>
</table>
</div>
</div>

<?php if ($enableUpload) {
	if ($file->isNew()) {?>
		<div id="<?php echo $genid ?>selectFileControlDiv">
			<label class="checkbox" style="display:none;">
	    	<?php echo radio_field($genid.'_rg', true, array('id' => $genid.'fileRadio', 'onchange' => 'og.addDocumentTypeChanged(0, "'.$genid.'")', 'value' => '0'))?>
	    	<?php echo lang('file') ?>
	    	</label>
	    	<label class="checkbox" style="display:none;">
	    	<?php echo radio_field($genid.'_rg', false, array('id' => $genid.'weblinkRadio', 'onchange' => 'og.addDocumentTypeChanged(1, "'.$genid.'")', 'value' => '1'))?>
	    	<?php echo lang('weblink') ?>
	    	</label>
	        <div id="<?php echo $genid ?>fileUploadDiv">
			<?php echo label_tag(lang('file'), $genid . 'fileFormFile', true) ?>
			<?php 
				Hook::fire('render_upload_control', array(
					"genid" => $genid,
					"attributes" => array(
						"id" => $genid . "fileFormFile",
						"class" => "title",
						"size" => "88",
						"style" => 'width:530px',
						"tabindex" => "10"
							//"onchange" => "javascript:og.updateFileName('" . $genid .  "', this.value);"
					)
				), $ret);
			?>
			<p><?php echo lang('upload file desc', format_filesize(get_max_upload_size())) ?></p>
			</div>
	    	<div id="<?php echo $genid ?>weblinkDiv" style="display:none;">
	        <?php echo label_tag(lang('weblink'), 'file[url]', true, array('id' => $genid.'weblinkLbl', 'type' => 'text')) ?>
	    	<?php echo text_field('file[url]', '', array('id' => $genid.'url', 'style' => 'width:500px;', "onchange" => "javascript:og.updateFileName('" . $genid .  "', this.value);")) ?>
	    	</div>
		</div>
	<?php } ?>
<?php } // if ?>

	<div id="<?php echo $genid ?>addFileFilename" style="<?php echo $file->isNew()? 'display:none' : '' ?>">
      	<?php echo label_tag(lang('new filename'), $genid .'fileFormFilename') ?>    
        <?php echo text_field('file[name]',$file->getFilename(), array("id" => $genid .'fileFormFilename', 'tabindex' => '20', 'class' => 'title', 
        	'onchange' => ($file->getType() == ProjectFiles::TYPE_DOCUMENT? 'javascript:og.checkFileName(\'' . $genid .  '\')' : ''))) ?>
        
    	<?php if ($file->getType() == ProjectFiles::TYPE_WEBLINK){?>
        <?php echo label_tag(lang('new weblink'), $genid .'fileFormFilename') ?>
        <?php echo text_field('file[url]',$file->getUrl(), array("id" => $genid .'fileFormUrl', 'class' => 'title', 'tabindex' => '21')) ?>
        <?php } //else ?>
    </div>

	<?php $categories = array(); Hook::fire('object_edit_categories', $object, $categories); ?>

	<div style="padding-top: 5px">
	<a href="#" class="option <?php echo ($file->isNew() ? 'bold':'');?>" onclick="og.toggleAndBolden('<?php echo $genid ?>add_file_select_context_div',this)"><?php echo lang('context') ?></a> 
	- <a href="#" class="option" onclick="og.toggleAndBolden('<?php echo $genid ?>add_file_description_div',this)"><?php echo lang('description') ?></a>
	- <a href="#" class="option <?php echo $visible_cps>0 ? 'bold' : ''?>" onclick="og.toggleAndBolden('<?php echo $genid ?>add_custom_properties_div',this)"><?php echo lang('custom properties') ?></a>
	- <a href="#" class="option" onclick="og.toggleAndBolden('<?php echo $genid ?>add_subscribers_div',this)"><?php echo lang('object subscribers') ?></a>
	<?php if($object->isNew() || $object->canLinkObject(logged_user())) { ?>
		- <a href="#" class="option" onclick="og.toggleAndBolden('<?php echo $genid ?>add_linked_objects_div',this)"><?php echo lang('linked objects') ?></a>
	<?php } ?>
	<?php foreach ($categories as $category) { ?>
		- <a href="#" class="option" <?php if ($category['visible']) echo 'style="font-weight: bold"'; ?> onclick="og.toggleAndBolden('<?php echo $genid . $category['name'] ?>', this)"><?php echo lang($category['name'])?></a>
	<?php } ?>
	</div>
</div>

<div class="coInputSeparator"></div>

<div class="coInputMainBlock">
		<fieldset id="<?php echo $genid ?>multipleFile" style="display: none;">
			<legend><?php echo lang("files") ?></legend>
			<div id="<?php echo $genid ?>multipleFileNames" ></div>
		</fieldset>
<?php if ($enableUpload) { ?>

	<?php if($file->isNew()) { //----------------------------------------------------ADD   ?>

		<div class="content">
			<div id="<?php echo $genid ?>addFileFilenameCheck" style="display: none">
				<h2><?php echo lang("checking filename") ?></h2>
			</div>
			<div id="<?php echo $genid ?>addFileUploadingFile" style="display: none">
				<h2><?php echo lang("uploading file") ?></h2>
			</div>

			<div id="<?php echo $genid ?>addFileFilenameExists" style="display: none">
				<h2><?php echo lang("duplicate filename")?></h2>
				<p><?php echo lang("filename exists") ?></p>
				<div style="padding-top: 10px">
				<table>
					<tr>
						<td style="height: 20px; padding-right: 4px">
							<?php echo radio_field('file[upload_option]',true, array("id" => $genid . 'radioAddFileUploadAnyway', "value" => -1, 'tabindex' => '30')) ?>
						</td>
						<td>
							<?php echo lang('upload anyway')?>
						</td>
					</tr>
				</table>
				<table id="<?php echo $genid ?>upload-table">
				</table>
				</div>
			</div>
		</div>
		<?php if ($comments_required) { ?>
			<?php echo label_tag(lang('revision comment'), $genid.'fileFormRevisionComment', $comments_required) ?>
			<?php echo textarea_field('file[revision_comment]', array_var($file_data, 'revision_comment', lang('initial versions')), array('id' => $genid.'fileFormRevisionComment', 'class' => 'long')) ?>
		<?php } else { ?>
			<?php echo input_field('file[revision_comment]', array_var($file_data, 'revision_comment', lang('initial versions')), array('type' => 'hidden', 'id' => $genid.'fileFormRevisionComment')) ?>
		<?php } ?>
		<input type='hidden' id ="<?php echo $genid ?>RevisionCommentsRequired" value="<?php echo $comments_required? '1':'0'?>"/>

	<?php }  else {//----------------------------------------------------------------EDIT?>

		<div class="content">
			<?php 
			if($file->getType() == ProjectFiles::TYPE_DOCUMENT){
				if (!isset($checkin)) {?>
				<div class="header">
					<?php echo checkbox_field('file[update_file]', array_var($file_data, 'update_file'), array('class' => 'checkbox', 'id' => $genid . 'fileFormUpdateFile', 'tabindex' => '60', 'onclick' => 'App.modules.addFileForm.updateFileClick(\'' . $genid .'\')')) ?>
					<?php echo label_tag(lang('update file'), $genid .'fileFormUpdateFile', false, array('class' => 'checkbox'), '') ?>
				</div>
				<div id="<?php echo $genid ?>updateFileDescription">
					<p><?php echo lang('replace file description') ?></p>
				</div>
			<?php } // if ?>
			<div id="<?php echo $genid ?>updateFileForm"  style="<?php echo isset($checkin) ? '': 'display:none' ?>">
				<p>
					<strong><?php echo lang('existing file') ?>:</strong>
					<a target="_blank" href="<?php echo $file->getDownloadUrl() ?>" id="extension_old"><?php echo clean($file->getFilename()) ?></a>
					| <?php echo format_filesize($file->getFilesize()) ?>
				</p>
				<p id="warning_extension_file"></p>
				<div id="<?php echo $genid ?>selectFileControlDiv">
					<?php echo label_tag(lang('new file'), $genid.'fileFormFile', true) ?>
					<?php
						Hook::fire('render_upload_control', array(
							"genid" => $genid,
							"attributes" => array(
								"id" => $genid . "fileFormFile",
								"tabindex" => "65",
								"size" => 88,
								"style" => 'width:530px',
							)
						), $ret);
					?>
				</div>
				<div id="<?php echo $genid ?>revisionControls">
					<div>
						<?php echo checkbox_field('file[version_file_change]', array_var($file_data, 'version_file_change', true), array('id' => $genid.'fileFormVersionChange', 'class' => 'checkbox', 'tabindex' => '70')) ?>
						<?php echo label_tag(lang('version file change'), $genid.'fileFormVersionChange', false, array('class' => 'checkbox'), '') ?>
					</div>
					<div id="<?php echo $genid ?>fileFormRevisionCommentBlock">
						<?php echo label_tag(lang('revision comment'), $genid.'fileFormRevisionComment', $comments_required) ?>
						<?php echo textarea_field('file[revision_comment]', array_var($file_data, 'revision_comment'), array('class' => 'long', 'tabindex' => '75', 'id' => $genid.'fileFormRevisionComment')) ?>
						<input type='hidden' id ="<?php echo $genid ?>RevisionCommentsRequired" value="<?php echo $comments_required? '1':'0'?>"/>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if (!isset($checkin) && $file->getType() == ProjectFiles::TYPE_DOCUMENT) {?>
				<script>
					App.modules.addFileForm.updateFileClick('<?php echo $genid ?>');
					App.modules.addFileForm.versionFileChangeClick('<?php echo $genid ?>');
				</script>
			<?php } // if ?>
		</div>
	<?php } // if type add / edit ?>
<?php } // if enableupload ?>



	<div id="<?php echo $genid ?>add_file_select_context_div" <?php echo ($file->isNew() ? '' : 'style="display:none"');?>>
		<fieldset>
			<legend><?php echo lang('context') ?></legend>
			<?php
			$listeners = array('on_selection_change' => 'og.reload_subscribers("'.$genid.'",'.$object->manager()->getObjectTypeId().')'); 
			if ($file->isNew()) {
				render_member_selectors($file->manager()->getObjectTypeId(), $genid, null, array('select_current_context' => true, 'listeners' => $listeners)); 
			} else {
				render_member_selectors($file->manager()->getObjectTypeId(), $genid, $file->getMemberIds(), array('listeners' => $listeners)); 
			} ?>
			<?php if (!$file->isNew()) {?>
				<div id="<?php echo $genid ?>addFileFilenameCheck" style="display: none">
					<h2><?php echo lang("checking filename") ?></h2>
				</div>
				<div id="<?php echo $genid ?>addFileUploadingFile" style="display: none">
					<h2><?php echo lang("uploading file") ?></h2>
				</div>
				<div id="<?php echo $genid ?>addFileFilenameExists" style="display: none">
					<h2><?php echo lang("duplicate filename")?></h2>
					<?php echo lang("filename exists edit") ?>
				</div>
			<?php } // if ?>
		</fieldset>
	</div>

	<div id="<?php echo $genid ?>add_file_description_div" style="display: none">
		<fieldset>
		<legend><?php echo lang('description') ?></legend>
		<?php echo textarea_field('file[description]', array_var($file_data, 'description'), array('class' => '', 'id' => $genid.'fileFormDescription', 'tabindex' => '90')) ?>
		</fieldset>
	</div>

	<div id="<?php echo $genid ?>add_custom_properties_div" style="<?php echo ($visible_cps > 0 ? "" : "display:none") ?>">
		<fieldset>
			<legend><?php echo lang('custom properties') ?></legend>
			<?php echo render_object_custom_properties($object, false) ?>
      		<?php echo render_add_custom_properties($object); ?>
		</fieldset>
	</div>

	<div id="<?php echo $genid ?>add_subscribers_div" style="display: none">
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
			
			<p>&nbsp;</p>    
			<p><?php echo checkbox_field('file[attach_to_notification]', array_var($file_data, 'attach_to_notification', user_config_option('attach_to_notification')), array('id' => $genid . 'eventAttachNotification', 'tabindex' => '111')) ?>
			<label for="<?php echo $genid ?>eventAttachNotification" class="checkbox"><?php echo lang('attach to notification') ?></label></p>
			<?php if (ConfigOptions::getByName('notify_myself_too') instanceof ConfigOption) : ?>
			<br />
			<p><?php echo checkbox_field('file[notify_myself_too]', config_option('notify_myself_too'), array('id' => $genid . 'notifyMyselfToo', 'tabindex' => '120')) ?>
			<label for="<?php echo $genid ?>notifyMyselfToo" class="checkbox"><?php echo lang('notify myself too') ?></label></p>
			<?php endif; ?>
			<br/>
			<div style="width: 400px; align: center; text-align: left;">
				<table>
					<tr>
						<td colspan="2" style="vertical-align:middle;" >
							<strong><?php echo lang('subject for email notification')?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="vertical-align:middle; height: 22px;">
							<label><?php echo radio_field('file[default_subject_sel]',true,array('value' => 'default')) ."&nbsp;". lang('use default subject')?></label>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="vertical-align:middle; height: 22px;"><?php 
							$sel = false;
							if(array_var($file_data, 'default_subject') != ""){
							    $sel = true;
							}										
							echo radio_field('file[default_subject_sel]',$sel,array('value' => 'subject'));
							echo "&nbsp;" . text_field('file[default_subject_text]',array_var($file_data, 'default_subject'), array('style'=>'width:300px', 'placeholder' => lang('enter a custom subject')));
						?></td>
					</tr>
				</table>
			</div>
		</fieldset>
	</div>

	<?php if($object->isNew() || $object->canLinkObject(logged_user())) { ?>
		<div style="display: none" id="<?php echo $genid ?>add_linked_objects_div">
		<fieldset>
			<legend><?php echo lang('linked objects') ?></legend>
			<?php echo render_object_link_form($object) ?>
		</fieldset>
		</div>
	<?php } // if ?>

	<?php foreach ($categories as $category) { ?>
		<div <?php if (!$category['visible']) echo 'style="display:none"' ?> id="<?php echo $genid . $category['name'] ?>">
		<fieldset>
			<legend><?php echo lang($category['name'])?><?php if ($category['required']) echo ' <span class="label_required">*</span>'; ?></legend>
			<?php echo $category['content'] ?>
		</fieldset>
		</div>
	<?php } ?>

	
	<?php if ($file->getType() == ProjectFiles::TYPE_WEBLINK) { ?>
	<div>
		<?php echo checkbox_field('file[version_file_change]', array_var($file_data, 'version_file_change', false), array('id' => $genid.'fileFormVersionChange', 'class' => 'checkbox', 'tabindex' => '70')) ?>
		<?php echo label_tag(lang('version file change'), $genid.'fileFormVersionChange', false, array('class' => 'checkbox'), '') ?>
	</div>
	<div id="<?php echo $genid ?>fileFormRevisionCommentBlock">
		<?php echo label_tag(lang('revision comment'), $genid.'fileFormRevisionComment', $comments_required) ?>
		<?php echo textarea_field('file[revision_comment]', array_var($file_data, 'revision_comment'), array('class' => 'long', 'tabindex' => '75', 'id' => $genid.'fileFormRevisionComment')) ?>
		<input type='hidden' id ="<?php echo $genid ?>RevisionCommentsRequired" value="<?php echo $comments_required? '1':'0'?>"/>
	</div>
	<?php } ?>

	<div id="<?php echo $genid ?>fileSubmitButton" style="display: inline">
		<input type="hidden" name="upload_id" value="<?php echo $genid ?>" />
		<?php
			if (!$file->isNew()) { //Edit file
				if (isset($checkin) && $checkin) {
					echo submit_button(lang('checkin file'),'s',array("id" => $genid.'add_file_submit2', 'tabindex' => '200'));
				} else {
					echo submit_button(lang('save changes'),'s',array("id" => $genid.'add_file_submit2', 'tabindex' => '200'));
				}
			} else { //New file
				echo submit_button(lang('add file'),'s',array("id" => $genid.'add_file_submit2', 'tabindex' => '200'));
			}
		?>
	</div>

</div>
</div>
</form>

<script>
	
	var ctl = Ext.get('<?php echo $genid ?>fileFormFile');
	if (ctl) ctl.focus();
        
        $(document).ready(function() {
            $('#<?php echo $genid ?>fileFormFile').change(function () {
                var extension = this.value.split('.');
                var ext_old_html = $('#extension_old').html();
                var extension_old = ext_old_html ? ext_old_html.split('.') : "";
                if(extension_old[1] != extension[1]){
                    var html = "<strong style='color:#FF0000'><?php echo lang('warning file extension type') ?></strong>";                
                    $('#warning_extension_file').html(html);
                }else{
                    $('#warning_extension_file').html("");
                }
            })
			
			//check if support input type=file/multiple
         	function supportMultiple() {
				var el = document.createElement("input");
				return ("multiple" in el);
			}

			var is_new = <?php echo $file->isNew() ? '1' : '0'; ?>;

			if (is_new) {
				if(supportMultiple()) {
	            	$('#<?php echo $genid ?>addfile').attr("action", "<?php echo get_url('files', 'add_multiple_files') ?>");
	            	$('#<?php echo $genid ?>fileFormFile').attr("name", "file_file[]");
	            }else{
					og.msg(lang("upload multiple files"), lang("for upload multiple files upgrade your browser"), 6, "err");
				}
			} 

			$('#<?php echo $genid ?>fileFormFile').change(function (){
				if(is_new && supportMultiple()) {
			        // if select more than one file upload all files as new files
				    if(this.files.length > 1){
				    	$('#<?php echo $genid ?>multipleFileNames').empty();
				    	$('#<?php echo $genid ?>multipleFile').show();
				    	$('#<?php echo $genid ?>addFileFilenameExists').hide();
				    	$('#<?php echo $genid ?>addFileFilename').hide();
				    	var $ul = $("<ul>");
				    	for (var i = 0; i < this.files.length; ++i) {
					          var name = this.files.item(i).name;
					          var $li = $("<li>");
					          $li.text(name);
					          $ul.append($li);
					          
					        }
				    	$('#<?php echo $genid ?>multipleFileNames').append($ul);
					}else{
						// if select one file check the file name
						$('#<?php echo $genid ?>multipleFile').hide();
						og.updateFileName("<?php echo $genid ?>", this.files.item(0).name);
					}
			    }else{
				   og.updateFileName("<?php echo $genid ?>", this.value);
				}
	        });
        });
        
        
</script>