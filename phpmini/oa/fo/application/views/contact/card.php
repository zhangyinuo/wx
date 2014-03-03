<?php $genid = gen_id(); ?>

    <div class="layout-container contact" >
        <div class="left-column-wrapper">
            <div class="left-column view-container">
                <div class="person-view">
                	<?php $oncloseclick = $contact instanceof Contact && Plugins::instance()->isActivePlugin('core_dimensions') ? "og.onPersonClose()" : "og.closeView()" ?>
                	<div class="close-wrapper" onclick="<?php echo $oncloseclick; ?>"><?php echo lang("close");?><div class="close"></div></div>
                    <div class="person-information">
                        <div class="picture">
                            <img src="<?php echo $contact->getPictureUrl() ?>" alt="<?php echo clean($contact->getObjectName()) ?> picture" />
                            <?php if ($contact->canEdit(logged_user())):?>
                            	<a class="change-picture" href="<?php echo $contact->getUpdatePictureUrl() ?>">[<?php echo lang("edit picture")?>]</a>
                            <?php endif;?>
                        </div>
                        <div class="basic-info">
                        
                            <h2>
                                <?php echo clean($contact->getObjectName()) ?>
                            </h2>
                            <h3><?php
                            	$jt = clean($contact->getJobTitle());
                            	$cn = $company instanceof Contact && $company->getIsCompany() ? clean($company->getObjectName()) : '';
                            	$sep = ($jt != '' && $cn != '') ? '<span> | </span>' : '';
                            	echo $jt . $sep . $cn; 
                            ?></h3>
                            
                            <h4 class="editable"><?php echo lang ('contact info') ?>
                                <?php if ($contact->canEdit(logged_user())):?>
                            		<a class="edit-link coViewAction ico-edit" href="<?php echo $contact->getEditUrl()?>"><?php echo lang("edit")?></a>
                            	<?php endif;?>                        
                            </h4>
                            
                            <ul>
                                <li>
                                	
                                    <span class="mail">
                                    	<?php echo render_mailto($contact->getEmailAddress());?>
                                    </span>
                                    <?php echo ($contact->getPhoneNumber('work',true)) ? '- <strong>' . lang('work') . ' ' . lang('phone') . ':</strong> ' . $contact->getPhoneNumber('work',true) : ''; ?>
                                    <?php echo ($contact->getPhoneNumber('home',true)) ? '- <strong>' . lang('home') . ' ' . lang('phone') . ':</strong> ' . $contact->getPhoneNumber('home',true) : ''; ?>                                    
                                </li>
                            </ul>
                            
                            <?php if ($contact->isUser()) :?>
                            <h4 class="editable"><?php echo lang ('user info') ?>
                            	<?php if ($contact->canEdit(logged_user())):?>
                            		<a class="edit-link coViewAction ico-edit" href="<?php echo $contact->getEditProfileUrl()?>"><?php echo lang("edit")?></a>
                            	<?php endif;?>
                            </h4>
                            
                            <ul>
                                <li>
                                	<strong><?php echo lang("user type")?>: </strong><span class="username"><?php echo $contact->getUserTypeName()?></span>
                                </li>
                                <li>
                                    <strong><?php echo lang("username")?>: </strong><span class="username"><?php echo $contact->getUsername()?></span>
                                </li>
                            </ul>
                            <?php endif ;?>
                            
                            	<?php if (isset($internalDivs)){
				foreach ($internalDivs as $idiv)
					echo $idiv;
			}
			
			if (!isset($is_user) && user_config_option("show_object_direct_url") ) { ?>
			<div style="padding-bottom:15px" id="direct_url"><b><?php echo lang('direct url') ?>:</b>
				<a id="task_url" href="<?php echo($contact->getViewUrl()) ?>" target="_blank"><?php echo($contact->getViewUrl()) ?></a>
			</div>
			<?php } ?>
                            
                            <div class="all-info">                            
                                <h4><?php echo ucfirst(lang ('work')) ?></h4>
                                <ul> 
                                	<?php if (($contact->getAddress('work'))):?>                                   
                                    <li>
                                        <?php echo '<strong>' . lang('address') . ':</strong> ' . $contact->getStringAddress('work') . ' [<a class="map-link" href="http://maps.google.com/?q=' . $contact->getStringAddress('work') . '" target="_blank">Map</a>]' ?>
                                    </li>
                                    <?php endif;?>
                                    
                                    <?php if (($contact->getPhoneNumber('work',true))  ):?>
                                    <li>
                                        <?php echo '<strong>' . lang('phone') . ':</strong> ' . $contact->getPhoneNumber('work',true)  ?>
                                    </li>                                    
                                    <?php endif; ?>
                                    
                                    <?php if ($contact->getWebpageUrl('work')):?>
                                    <li>
                                        <?php echo  '<strong>' . lang('webpage') . ':</strong> ' . $contact->getWebpageUrl('work') ?>
                                    </li>   
                                    <?php endif;?>                                 
                                </ul>          
                                                      
                                <h4><?php echo ucfirst(lang ('home')) ?></h4>
                                <ul>
                                	<?php if(($contact->getAddress('home'))):?>
                                    <li>
                                        <?php echo  '<strong>' . lang('address') . ':</strong> ' . $contact->getStringAddress('home') . ' [<a href="http://maps.google.com/?q=' . $contact->getStringAddress('home') . '" target="_blank">Map</a>]'?>
                                    </li>      
                                    <?php endif;?>      
                                    
									<?php if(($contact->getPhoneNumber('home',true))):?>
                                    <li>
                                        <?php echo  '<strong>' . lang('phone') . ':</strong> ' .  $contact->getPhoneNumber('home',true) ; ?>                                    
                                    </li>
                                    <?php endif; ?>
                                    
                                    <?php if(($contact->getWebpageUrl('personal'))):?>
                                    <li>
                                        <?php echo  '<strong>' . lang('webpage') . ':</strong> ' . $contact->getWebpageUrl('personal');?>
                                    </li>
                                    <?php endif; ?>    
                              
                                </ul>                               
                                
                                <h4><?php echo ucfirst(lang ('other')) ?></h4>  
                                <ul>
                                	<?php if(($contact->getAddress('other'))):?>
                                    <li>
                                        <?php echo '<strong>' . lang('address') . ':</strong> ' . $contact->getStringAddress('other')  . ' [<a href="http://maps.google.com/?q=' . $contact->getStringAddress('other') . '" target="_blank">Map</a>]'?>
                                    </li>
                                    <?php endif; ?> 
                                    
                                    <?php if($contact->getPhoneNumber('other',true)) :?>                               
                                    <li>
                                        <?php echo '<strong>' . lang('phone') . ':</strong> ' .  $contact->getPhoneNumber('other',true); ?>                                    
                                    </li>
                                    <?php endif; ?>
                                    
                                    <?php if(($contact->getWebpageUrl('other'))):?>
                                    <li>
                                        <?php echo '<strong>' . lang('webpage') . ':</strong> ' . $contact->getWebpageUrl('other'); ?>
                                    </li>
                                    <?php endif; ?>                                   
                                </ul>  
                            </div>      
                            <a class="more-info" href="javascript:void();" style="display: none" ><?php echo lang ('more info') ?></a>
                                                  

                        </div>
                    </div>
                    <div class="clear"></div>
                    <div style="padding:10px;"><?php
                    
                    $back_color = '';
                    $bc = 'background-color:#EEEEF3;';
                    if($contact->isLinkableObject() && !$contact->isTrashed() && !$contact->isUser()) {
                    	echo '<div style="margin:20px 0; padding: 2px 0 5px;">';
						echo render_object_links_main($contact, $contact->canEdit(logged_user()));
						echo '</div>';
						$back_color = $bc;
                    }
                    
                    if ($contact instanceof ApplicationDataObject) {
                    	echo '<div style="margin:20px 0;'.$back_color.'">';
                    	echo render_custom_properties($contact);
                    	echo '</div>';
                    	$back_color = $back_color == '' ? $bc : '';
                    }
                    	
                    if (!$contact->isUser() && $contact->isCommentable()) {
                    	echo '<div style="margin:20px 0;">';
                    	echo render_object_comments($contact, $contact->getViewUrl());
                    	echo '</div>';
                    	$back_color = $back_color == '' ? $bc : '';
                    }
                    
                    if ($contact->getCommentsField()) {
                    	echo '<div class="commentsTitle">'.lang('notes').'</div>';
                    	echo escape_html_whitespace(convert_to_links(clean($contact->getCommentsField())));
                    }
                    
                    ?></div>
                    <div class="clear"></div>                    
	                <?php Hook::fire('after_contact_view', $contact, $null); ?>
                </div>
                <?php if ( isset($show_person_activity) ): ?>
                <div class="person-activity">
                    <h2><?php echo lang('related to') ?></h2>
                    <ul>
                    <?php foreach($feeds as $feed): ?>
	                    <?php if ( array_var($feed, 'object_id') != $contact->getId() ) :?>
	                        <li class="<?php echo array_var($feed, 'icon') ?>">
	                            <em class="feed-date"><?php echo ucfirst (array_var($feed, 'type')); ?> - <?php echo array_var($feed, 'dateUpdated');?></em>
	                            - <a href="Javascript:;" onclick="og.openLink('<?php echo array_var($feed, 'url') ?>');"><?php echo array_var($feed, 'name') ?></a>
	                            <?php if(array_var($feed, 'content') != '') { ?>
	                                <p><?php echo array_var($feed, 'content'); ?></p>
	                            <?php } ?>
	                        </li>
	                    <?php endif ;?> 
                    <?php endforeach ?>
                    </ul>
                </div>
                <?php endif ; ?>
            </div>
        </div>	
        <div class="right-column">
            <?php 
                //Add action and properties components to right sidebar.
                tpl_assign("object", $contact);
                tpl_assign("genid", $genid);
                $this->includeTemplate(get_template_path('actions', 'co'));
                $this->includeTemplate(get_template_path('properties', 'co'));                 
            ?>
        </div>
</div>
<div class="clear"></div>

<script>
	$(function(){
	
		$("a.more-info").click(function(){
			var link = this ;
			$('div.all-info').slideToggle('slow',function(){
				if ($(this).is(':visible')) {
					$(link).text(lang("less info"));
				}else{
					$(link).text(lang("more info"));
				}
					
			});
		});
		
		// Remove empty groups
		$(".all-info ul").each(  function() {
		    var elem = $(this);
		    if (elem.children().length == 0) {
			   	elem.prev("h4").remove();
		      	elem.remove();
		    }
		});
		if (!$(".all-info").children().length ){
			$(".more-info").remove();
		}

		$("a.more-info").show();
	});
</script>