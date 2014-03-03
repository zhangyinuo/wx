<?php header ("Content-Type: text/html; charset=utf-8", true); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title><?php echo clean(CompanyWebsite::instance()->getCompany()->getFirstName()) . ' - ' . PRODUCT_NAME ?></title>
	<?php echo link_tag(with_slash(ROOT_URL)."favicon.ico", "rel", "shortcut icon") ?>
	<?php echo add_javascript_to_page("og/app.js") // loaded first because it's needed for translating?>
	<?php echo add_javascript_to_page(get_url("access", "get_javascript_translation")); ?>
	<?php echo add_javascript_to_page(get_url("access", "get_javascript_translation_default")); ?>
	<!--[if IE 7]>
	<?php echo stylesheet_tag("og/ie7.css"); ?>
	<![endif]-->
	<!--[if IE 8]>
	<?php echo stylesheet_tag("og/ie8.css"); ?>
	<![endif]-->
	
	<?php echo meta_tag('content-type', 'text/html; charset=utf-8', true) ?>
<?php

	$version = product_version();
	if (defined('COMPRESSED_CSS') && COMPRESSED_CSS) {
		echo stylesheet_tag("ogmin.css");
	} else {
		echo stylesheet_tag('website.css');
	}
	
	// Include plguin specif stylesheets
	foreach (Plugins::instance()->getActive() as $p) {
		/* @var $p Plugin */
		$css_file =	PLUGIN_PATH ."/".$p->getSystemName()."/public/assets/css/".$p->getSystemName().".css" ;
		if (is_file($css_file)) {
			echo stylesheet_tag(ROOT_URL."/plugins/".$p->getSystemName()."/public/assets/css/".$p->getSystemName().".css" );
			echo "\n";// exit;
		}
	}
	
	
	
	$theme = config_option('theme', DEFAULT_THEME);
	if (is_file(PUBLIC_FOLDER . "/assets/themes/$theme/stylesheets/custom.css")) {
		echo stylesheet_tag('custom.css');
	}
	$css = array();
	Hook::fire('autoload_stylesheets', null, $css);
	foreach ($css as $c) {
		echo stylesheet_tag($c);
	}

	if (defined('COMPRESSED_JS') && COMPRESSED_JS) {
		$jss = array("ogmin.js");
	} else {
		$jss = include "javascripts.php";
	}
	Hook::fire('autoload_javascripts', null, $jss);
	if (defined('USE_JS_CACHE') && USE_JS_CACHE) {
		echo add_javascript_to_page(with_slash(ROOT_URL)."public/tools/combine.php?version=$version&type=javascript&files=".implode(',', $jss));
	} else {
		foreach ($jss as $onejs) {
			echo add_javascript_to_page($onejs);
		}
	}
	$ext_lang_file = get_ext_language_file(get_locale());
	if ($ext_lang_file)	{
		echo add_javascript_to_page("extjs/locale/$ext_lang_file");
	}
	echo add_javascript_to_page("ckeditor/ckeditor.js");
	
	// Include plguin specif js
	foreach (Plugins::instance()->getActive() as $p) {
		/* @var $p Plugin */
		$js_file =	PLUGIN_PATH ."/".$p->getSystemName()."/public/assets/javascript/".$p->getSystemName().".js" ;
		if (is_file($js_file)) {
			add_javascript_to_page(get_public_url("assets/javascript/".$p->getSystemName().".js", $p->getSystemName()));
			echo "\n";
		}
	}
	
	?>
	<?php echo add_javascript_to_page(get_url('dimension', 'dimensions_js')); // loaded first because it's needed for translating?>
	<style>
		#loading {
		    font-size: 20px;
		    left: 45%;
		    position: absolute;
		    top: 45%;
			color: #333333;
			font-family: verdana,arial,helvetica,sans-serif;
    		line-height: 150%;
		}
	</style>
</head>
<body id="body" <?php echo render_body_events() ?>>

<iframe name="_download" style="display:none"></iframe>

<div id="loading">
	<img src="<?php echo get_image_url("layout/loading.gif") ?>" width="32" height="32" style="margin-right:8px;vertical-align: middle;"/><?php echo lang("loading") ?>...
</div>

<div id="subWsExpander" onmouseover="clearTimeout(og.eventTimeouts['swst']);" onmouseout="og.eventTimeouts['swst'] = setTimeout('og.HideSubWsTooltip()', 2000);" style="display:none;top:10px;"></div>

<?php 
echo render_page_javascript();
echo render_page_inline_js();
$use_owner_company_logo = owner_company()->hasLogo();
?>
<!-- header -->
<div id="header">
	<div id="headerContent">
            <div style="float: left; cursor:pointer;" onclick="og.clearDimensionSelection();">
                <div id="logodiv">
                    <div style="" id="logo_company_margin_top">
                        <img src="<?php echo ($use_owner_company_logo) ? owner_company()->getLogoUrl() : 's.gif' ?>" name="img_company_margin" id="img_company_margin" style="display: none;"/>
                        <script>
                            $('#img_company_margin').load(function() {
                                var margin = (Ext.isIE) ? 25 : Math.round(parseInt(document.img_company_margin.height) / 2);
                                var magin_top = "-" + margin + "px";
                                $("#logo_company_margin_top").css({'margin-top':magin_top, 'position': 'relative', 'top': '50%'});
                                $("#img_company_margin").show();
                            });
                        </script>
                    </div>
                    <div style="float: left;">
                        <?php if(!$use_owner_company_logo){?>
                        <a id="change-logo-link" style="color: #fff; font-size: 10px; padding-top:15px;" href="index.php?c=contact&a=edit_logo&id=<?php echo owner_company()->getObjectId(); ?>"><?php echo lang('change logo')?></a>
                        <?php } ?>
                        <h1 style="padding-top:15px;"><?php echo clean(owner_company()->getObjectName()) ?></h1>
                    </div>
                </div>
                
            </div>
                        
            </table>
		<div class="header-content-right">
			<div id="searchbox">
				<form name='search_form' class="internalForm" action="<?php echo ROOT_URL . '/index.php' ?>" method="get" id="form_search">
					<table>
						<tr>
							<td>
								<input name="search_for_in" placeholder="<?php echo lang('search') . "..."?>" id="search_for_in"/>
								<input type="hidden" name="c" value="search" />
								<input type="hidden" name="a" value="search" />
								<input type="hidden" name="current" value="search" />
								<input type="hidden" id="hfVars" name="vars" value="dashboard" />
								<input style="display:none" id="searchButtonReal" type="submit" />
								<input style="display:none" name="search_for" id="search_for"/>
							</td>
							<td>
								<div class="btn-group">
									<button class="btn" style="height: 21px;" type="button" id="searchButton"><span style="margin-top: -3px; display: block;"><?php echo lang('search')?></span></button>
									<a class="btn dropdown-toggle" style="height: 11px;" data-toggle="dropdown" href="#"><span class="caret"></span></a>
									<ul class="dropdown-menu">
										<li><a href="<?php echo get_url('search', 'search', array('advanced' => true))?>"><?php echo lang('advanced search')?></a></li>
									</ul>
								</div>
							</td>
							<td style="padding-left:10px">
								<div id="quickAdd" style="display: none"></div>
							</td>
						</tr>
					</table>
				</form>
			</div>			
			<div id="userboxWrapper">
				<h2>
				<a href="#" onclick="showUserOptionsPanel()"><?php echo clean(logged_user()->getObjectName()); ?></a></h2>
				<a href="#" class="account" onclick="showUserOptionsPanel()">&nbsp;</a>								
			</div>
			<div class="clear"></div>
			<?php echo render_user_box(logged_user())?>
		</div>
		<?php Hook::fire('render_page_header', null, $ret); 
			  Hook::fire('on_page_load', 'mail', $ret);
		?>
        <script>
            
            /*** User Settings Panel ***/ 
            
            /**
            * Slide to show/hide user setting panel
            */
            function showUserOptionsPanel()
            {
                $('div.user-box-actions').slideToggle();  
            }
            
            /**
            * Save selected colors to Organization settings
            */            
            function saveBrandColors (element)
            {
                element.disabled = true;
                var parameters = {};
                $('div.theme-color-picker form input[type=text]').each(function(inx, obj){
                    parameters[obj['name']] = obj.value;
                });
                $.ajax({
    				data: parameters,	
    				url: og.makeAjaxUrl(og.getUrl('administration','scolors')),
    				dataType: "json",
    				type: "POST",
    				complete: function(data){
	                	$('li.theme-color-picker-wrapper').slideUp();
	                    element.disabled = false;
                	}
                });
            }
                        
            /*** Brand color-picker ***/
			var brand_colors = {
				head_back: '<?php echo config_option('brand_colors_head_back')?>',
				head_font: '<?php echo config_option('brand_colors_head_font')?>',
				tabs_back: '<?php echo config_option('brand_colors_tabs_back')?>',
				tabs_font: '<?php echo config_option('brand_colors_tabs_font')?>'
            };
            
            /**
            * Create style sheet for current colors
            */
            function createBrandColorsSheet ()
            {
                var header_back = brand_colors['head_back'];
                var tabs_back = brand_colors['tabs_back'];
                var tabs_font = brand_colors['tabs_font'];
                var header_font = brand_colors['head_font'];

                var cssRules = '.x-accordion-hd, ul.x-tab-strip li {background-color: #' + tabs_back + '}';
                cssRules += 'ul.x-tab-strip li {border-color: #' + tabs_back + '}';
                cssRules += '#header, #userboxWrapper h2 a {background-color: #' + header_back + '}';
                cssRules += '.x-accordion-hd, .x-tab-strip span.x-tab-strip-text {color: #' + tabs_font + '}';
                cssRules += 'ul.x-tab-strip li.x-tab-strip-active {background-color: #' + tabs_font + ' !important}';
                cssRules += 'ul.x-tab-strip li.x-tab-strip-active span.x-tab-strip-text {color: #' + tabs_back + ' !important}';
                cssRules += '#logodiv h1, #userboxWrapper h2 a, div.og-loading {color: #' + header_font + '}';
				// dimension title
                cssRules += '.x-accordion-hd {background-color: #' + tabs_back + '}';
				// selected member
				var node_selected_back = color_utils.make_transparent_color('#'+tabs_back);
				if (node_selected_back) cssRules += '.x-tree-node .x-tree-selected {background-color: '+node_selected_back+'; border-color: '+color_utils.darker_html_color(node_selected_back)+'}';
				// ckeditor back color
                cssRules += '.cke_wrapper {background-color: #'+tabs_back+' !important;}';

                var styleElement = document.createElement("style");
                styleElement.type = "text/css";
                if (styleElement.styleSheet) {
                    styleElement.styleSheet.cssText = cssRules;
                } else {
                    styleElement.appendChild(document.createTextNode(cssRules));
                }
                document.getElementsByTagName("head")[0].appendChild(styleElement);
            }
            
            /**
            * OnReady events
            */ 
            $(document).ready(
                function() {
                    createBrandColorsSheet();
                    $('.back-color-value').val('#'+brand_colors['head_back']);
                    $('.front-color-value').val('#'+brand_colors['tabs_back']);
                    $('.face-font-color-value').val('#'+brand_colors['tabs_font']);
                    $('.title-font-color-value').val('#'+brand_colors['head_font']);

                    $('.back-color-value, .front-color-value, .face-font-color-value, .title-font-color-value').modcoder_excolor({
                       shadow : false,
                       background_color : '#eeeeee',
                       backlight : false,
                       callback_on_ok : function() {
                            brand_colors['head_back'] = $('.back-color-value').val().substring(1,7);
                            brand_colors['tabs_back'] = $('.front-color-value').val().substring(1,7);
                            brand_colors['tabs_font'] = $('.face-font-color-value').val().substring(1,7);
                            brand_colors['head_font'] = $('.title-font-color-value').val().substring(1,7);
                            createBrandColorsSheet();
                       }
                    });
                    
                    $("#searchButton").click(function () {
						if($("#search_for_in").val() != ""){
                        	$("#searchButtonReal").click();                            
                        }                  
                    });
                    
                    $("#advancedSearch").click(function() {
                        $("#searchButtonReal").click();
                    });

                    $("#form_search").submit(function (e){
						$("#search_for").val($("#search_for_in").val());
                    	$("#searchButton").prop("disabled",true);
                    	$("#search_for_in").prop("disabled",true);
                    });

					//if enter key press on chrome submit the form
                    if (navigator.userAgent.toLowerCase().indexOf('chrome')>-1){
                    	$('#form_search').keypress(function(e){
	                        if(e.which == 13){
	                            if($("#search_for_in").val() != ""){
	                            	$("#searchButtonReal").click();                            
	                            }        
	                        }
	                    });
                    }
                }
            );
        </script>
	</div>
</div>
<!-- /header -->

<!-- footer -->
<div id="footer">
	<div id="copy">
		<?php if(0 && is_valid_url($owner_company_homepage = owner_company()->getHomepage())) { 
		//FIXME Pepe getHomepage not defined
			?>
			<?php echo lang('footer copy with homepage', date('Y'), $owner_company_homepage, clean(owner_company()->getObjectName())) ?>
		<?php } else { ?>
			<?php echo lang('footer copy without homepage', date('Y'), clean(owner_company()->getObjectName())) ?>
		<?php } // if ?>
	</div>
	<?php Hook::fire('render_page_footer', null, $ret) ?>
	<div id="productSignature"><?php echo product_signature() ?></div>
</div>
<!-- /footer -->

<script>
		
	
// OG config options
og.hostName = '<?php echo ROOT_URL ?>';
og.sandboxName = <?php echo defined('SANDBOX_URL') ? "'".SANDBOX_URL."'" : 'false'; ?>;
og.maxUploadSize = '<?php echo get_max_upload_size() ?>';
<?php //FIXME initialWS for initialMembers
$initialWS = user_config_option('initialWorkspace');
if ($initialWS === "remember") {
	$initialWS = user_config_option('lastAccessedWorkspace', 0);
}
?>
og.initialWorkspace = '<?php echo $initialWS ?>';
<?php $qs = (trim($_SERVER['QUERY_STRING'])) ? "&" . $_SERVER['QUERY_STRING'] : "";  ?>
og.queryString = '<?php echo $_SERVER['QUERY_STRING'] ?>';
og.initialURL = '<?php echo ROOT_URL ."/?".$_SERVER['QUERY_STRING'] ?>';
<?php if (user_config_option("rememberGUIState")) { ?>
og.initialGUIState = <?php echo json_encode(GUIController::getState()) ?>;
<?php }
 
if (user_config_option("autodetect_time_zone", null)) {
	$now = DateTimeValueLib::now();
?>
	og.usertimezone = og.calculate_time_zone(new Date(<?php echo $now->getYear() ?>,<?php echo $now->getMonth() - 1 ?>,<?php echo $now->getDay() ?>,<?php echo $now->getHour() ?>,<?php echo $now->getMinute() ?>,<?php echo $now->getSecond() ?>));
	og.openLink(og.getUrl('account', 'set_timezone', {'tz': og.usertimezone}), {'hideLoading': true});
<?php 
} ?>
og.CurrentPagingToolbar = <?php echo defined('INFINITE_PAGING') && INFINITE_PAGING ? 'og.InfinitePagingToolbar' : 'og.PagingToolbar' ?>;
og.ownerCompany = {
	id: '<?php echo owner_company()->getId()?>',
	name: '<?php clean(owner_company()->getObjectName())?>'
};
og.loggedUser = {
	id: <?php echo logged_user()->getId() ?>,
	username: <?php echo json_encode(logged_user()->getUsername()) ?>,
	displayName: <?php echo json_encode(logged_user()->getObjectName()) ?>,
	isAdmin: <?php echo logged_user()->isAdministrator() ? 'true' : 'false' ?>,
	isGuest: <?php echo logged_user()->isGuest() ? 'true' : 'false' ?>,
	tz: <?php echo logged_user()->getTimezone() ?>,
	type: <?php echo logged_user()->getUserType() ?>
};
og.zipSupported = <?php echo zip_supported() ? 1 : 0 ?>;
og.hasNewVersions = <?php
	if (config_option('upgrade_last_check_new_version', false) && logged_user()->isAdministrator()) {
		echo json_encode(lang('new Feng Office version available', "#", "og.openLink(og.getUrl('administration', 'upgrade'))"));
	} else {
		echo "false";
	}
?>;
og.config = {
	'mails_per_page': <?php echo json_encode(user_config_option('mails_per_page',50)) ?>,
	'contacts_per_page': <?php echo json_encode(user_config_option('contacts_per_page',50)) ?>,
	'files_per_page': <?php echo json_encode(config_option('files_per_page', 50)) ?>,
	'days_on_trash': <?php echo json_encode(config_option("days_on_trash", 0)) ?>,
	'checkout_notification_dialog': <?php echo json_encode(config_option('checkout_notification_dialog', 0)) ?>,
	'use_time_in_task_dates': <?php echo json_encode(config_option('use_time_in_task_dates')) ?>,
	'can_assign_tasks_to_companies': <?php echo json_encode(config_option('can_assign_tasks_to_companies')) ?>,
	'enable_notes_module': <?php echo json_encode(module_enabled("messages")) ?>,
	'enable_email_module': <?php echo json_encode(module_enabled("mails")) ?>,
	'enable_contacts_module': <?php echo json_encode(module_enabled("contacts")) ?>,
	'enable_calendar_module': <?php echo json_encode(module_enabled("calendar")) ?>,
	'enable_documents_module': <?php echo json_encode(module_enabled("documents")) ?>,
	'enable_tasks_module': <?php echo json_encode(module_enabled("tasks")) ?>,
	'enable_weblinks_module': <?php echo json_encode(module_enabled('weblinks')) ?>,
	'enable_time_module': <?php echo json_encode(module_enabled("time") && can_manage_time(logged_user())) ?>,
	'enable_reporting_module': <?php echo json_encode(module_enabled("reporting")) ?>
};
og.preferences = {
	'viewContactsChecked': <?php echo json_encode(user_config_option('viewContactsChecked')) ?>,
	'viewUsersChecked': <?php echo json_encode(user_config_option('viewUsersChecked')) ?>,
	'viewCompaniesChecked': <?php echo json_encode(user_config_option('viewCompaniesChecked')) ?>,
	'rememberGUIState': <?php echo user_config_option('rememberGUIState') ? '1' : '0' ?>,
	'time_format_use_24': <?php echo json_encode(user_config_option('time_format_use_24')) ?>,
	'show_unread_on_title': <?php echo user_config_option('show_unread_on_title') ? '1' : '0' ?>,
	'email_polling': <?php echo json_encode(user_config_option('email_polling')) ?> ,
	'email_check_acc_errors': <?php echo json_encode(user_config_option('mail_account_err_check_interval')) ?> ,
	'date_format': <?php echo json_encode(user_config_option('date_format')) ?>,
	'date_format_tip': <?php echo json_encode(date_format_tip(user_config_option('date_format'))) ?>,
	'start_monday': <?php echo user_config_option('start_monday') ? '1' : '0' ?>,
	'draft_autosave_timeout': <?php echo json_encode(user_config_option('draft_autosave_timeout')) ?>,
	'drag_drop_prompt': <?php echo json_encode(user_config_option('drag_drop_prompt')) ?>,
	'mail_drag_drop_prompt': <?php echo json_encode(user_config_option('mail_drag_drop_prompt')) ?>,
	'access_member_after_add': <?php echo user_config_option('access_member_after_add') ? '1' : '0' ?>,
	'access_member_after_add_remember': <?php echo user_config_option('access_member_after_add_remember') ? '1' : '0' ?>,
	'listing_preferences': [],
	'breadcrumb_member_count': <?php echo user_config_option('breadcrumb_member_count') ?>
};
<?php
	$listing_preferences = ContactConfigOptions::getOptionsByCategoryName('listing preferences');
	foreach ($listing_preferences as $lp) {
		if (str_starts_with($lp->getName(), 'lp_dim_')) {
			$dcode = str_replace('lp_dim_', '', str_replace('_show_as_column', '', $lp->getName()));
			$dim = Dimensions::findByCode($dcode);
			?>og.preferences['listing_preferences']['<?php echo 'lp_dim_'.$dim->getId().'_show_as_column' ?>'] = <?php echo user_config_option($lp->getName()) ? '1' : '0'?>;<?php
		}
	} 
?>
og.breadcrumbs_skipped_dimensions = [];

Ext.Ajax.timeout = <?php echo get_max_execution_time()*1100 // give a 10% margin to PHP's timeout ?>;
og.musicSound = new Sound();
og.systemSound = new Sound();

var quickAdd = new og.QuickAdd({renderTo:'quickAdd'});

<?php if (!defined('DISABLE_JS_POLLING') || !DISABLE_JS_POLLING) { ?>
var isActiveBrowserTab = true;
if (Ext.isIE) {
	document.onfocusin = function () {
	  isActiveBrowserTab = true;
	};
	document.onfocusout = function () {
	  isActiveBrowserTab = false;
	};
} else {
	window.onfocus = function () {
	  isActiveBrowserTab = true;
	};
	window.onblur = function () {
	  isActiveBrowserTab = false;
	};
}

og.dimensions_check_date = new Date();

setInterval(function() {
	if (window.isActiveBrowserTab) {
		og.openLink(og.getUrl('object', 'popup_reminders'), {
			hideLoading: true,
			hideErrors: true,
			preventPanelLoad: true,
			post: {
				dims_check_date: Math.floor(og.dimensions_check_date.getTime()/1000)
			},
			callback: function(success, data) {
				
				if (data.reload_dims) {
					og.openLink(og.getUrl('dimension', 'reload_dimensions_js'), {
						hideLoading: true,
						hideErrors: true,
						preventPanelLoad: true,
						callback: function(s, d) {
							if (d.dims) {
								og.dimensions = d.dims;
								og.dimensions_check_date = new Date();
								if (d.perms) og.member_permissions = d.perms;
							}
						}
					});
				}
			}
		});
	}
}, 60000);
<?php } ?>

og.openLink(og.getUrl('dimension', 'reload_dimensions_js'), {
	hideLoading: true,
	hideErrors: true,
	preventPanelLoad: true,
	callback: function(s, d) {
		if (d.dims) {
			og.dimensions = d.dims;
			og.dimensions_check_date = new Date();
			if (d.perms) og.member_permissions = d.perms;
		}
	}
});

<?php if (Plugins::instance()->isActivePlugin('mail')) { ?>
	og.loadEmailAccounts('view');
	og.loadEmailAccounts('edit');
	og.loggedUserHasEmailAccounts = <?php echo logged_user()->hasEmailAccounts() ? 'true' : 'false' ?>;
	og.emailFilters = {};
	og.emailFilters.classif = '<?php echo user_config_option('mails classification filter') ?>';
	og.emailFilters.read = '<?php echo user_config_option('mails read filter') ?>';
	og.emailFilters.account = '<?php echo user_config_option('mails account filter') ?>';
	if (og.emailFilters.account != 0 && og.emailFilters.account != '') {
		og.emailFilters.accountName = '<?php
			$acc_id = user_config_option('mails account filter');
			$acc = $acc_id > 0 ? MailAccounts::findById($acc_id) : null; 
			echo ($acc instanceof MailAccount ? mysql_real_escape_string($acc->getName()) : ''); 
		?>';
	} else og.emailFilters.accountName = '';
<?php } ?>
og.lastSelectedRow = {messages:0, mails:0, contacts:0, documents:0, weblinks:0, overview:0, linkedobjs:0, archived:0};

og.menuPanelCollapsed = false;

og.dimensionPanels = [
	<?php
	$dimensionController = new DimensionController() ;
	$first = true ; 
	$dimensions = $dimensionController->get_context() ;
	foreach ( $dimensions['dimensions'] AS $dimension ):
	 	if ( $dimension->getOptions(1) && isset($dimension->getOptions(1)->hidden) && $dimension->getOptions(1)->hidden ) {
	 		continue ;
	 	}
	 		
		/* @var $dimension Dimension */
		$title = ( $dimension->getOptions() && isset ($dimension->getOptions(1)->useLangs) && ($dimension->getOptions(1)->useLangs) )   ? lang($dimension->getCode()) : $dimension->getName(); 
		if (!$first): ?>,<?php endif; $first = false ;?>                      
		{	
			reloadDimensions: <?php echo json_encode( DimensionMemberAssociations::instance()->getDimensionsToReload($dimension->getId()) ) ; ?>,
			xtype: 'member-tree',
			id: 'dimension-panel-<?php echo $dimension->getId() ; ?>',
			dimensionId: <?php echo $dimension->getId() ; ?>,
			dimensionCode: '<?php echo $dimension->getCode() ; ?>',
			dimensionOptions: <?php echo ( $dimension->getOptions() ) ?  $dimension->getOptions() : '""' ; ?>,
			isDefault: '<?php echo (int) $dimension->isDefault() ; ?>',
			title: "<?php echo $title ?>",
			multipleSelection: <?php echo (int)$dimension->getAllowsMultipleSelection() ?>,
			isRoot: <?php echo (int) $dimension->getIsRoot(); ?>,
			requiredObjectTypes: <?php echo json_encode($dimension->getRequiredObjectTypes()) ?>,
			hidden: <?php echo (int) ! $dimension->getIsRoot(); ?>,
			isManageable: <?php echo (int) $dimension->getIsManageable() ?>,
			quickAdd: <?php echo ( $dimension->getOptions(1) && isset($dimension->getOptions(1)->quickAdd) && $dimension->getOptions(1)->quickAdd ) ? 'true' : 'false'  ?>,
					
			minHeight: 10
			//animate: false,
			//animCollapse: false
		}	
	<?php endforeach; ?>
];


og.contextManager.construct();
og.objPickerTypeFilters = [];
<?php
	$obj_picker_type_filters = ObjectTypes::findAll(array("conditions" => "`type` = 'content_object'
		AND (plugin_id IS NULL OR plugin_id IN (SELECT distinct(id) FROM ".TABLE_PREFIX."plugins WHERE is_installed = 1 AND is_activated = 1 ))
		AND `name` <> 'file revision' AND name <> 'template_task' AND name <> 'template_milestone' AND `id` NOT IN (
			SELECT `object_type_id` FROM ".TabPanels::instance()->getTableName(true)." WHERE `enabled` = 0
		)  OR `type` = 'comment' OR `name` = 'milestone'"));
	
	$pg_ids = logged_user()->getPermissionGroupIds();
	if (!is_array($pg_ids) || count($pg_ids) == 0) $pg_ids = array(0);
	
	foreach ($obj_picker_type_filters as $type) {
		if (! $type instanceof  ObjectType ) continue ;
		/* @var $type ObjectType */
		$linkable = $type->getIsLinkableObjectType();
		if ($linkable) {
			$tab_ids = DB::executeAll("SELECT id FROM ".TABLE_PREFIX."tab_panels WHERE object_type_id = ".$type->getId());
			if (count($tab_ids) > 0) {
				$tab_id = $tab_ids[0]['id'];
				if (!TabPanelPermissions::isModuleEnabled($tab_id, implode(',', $pg_ids))) {
					continue;
				}
			}
?>
			og.objPickerTypeFilters.push({
				id: '<?php echo $type->getName() ?>',
				name: '<?php echo lang($type->getName()) ?>',
				type: '<?php echo $type->getName() ?>',
				filter: 'type',
				iconCls: 'ico-<?php echo $type->getIcon() ?>'
			});
<?php
		}
	}
?>
	var searchForm = document.getElementById("searchbox").getElementsByTagName("form")[0] ;
	H5F.setup(searchForm);


	og.additional_on_dimension_object_click = [];
	og.dimension_object_types = [];
<?php
	$dimension_object_types = ObjectTypes::findAll(array('conditions' => "`type` IN ('dimension_object', 'dimension_group')"));
	foreach ($dimension_object_types as $dot) { ?>
		og.dimension_object_types[<?php echo $dot->getId()?>] = '<?php echo $dot->getName()?>';
<?php
	}
	foreach (Plugins::instance()->getActive() as $p) {
		$js_code = 'if (og.'.$p->getName().' && og.'.$p->getName().'.init) og.'.$p->getName().'.init();'."\n";
		echo $js_code;
	} 
?>

og.dimension_object_type_contents = [];
<?php 
	$dotcs = DimensionObjectTypeContents::findAll();
	foreach ($dotcs as $dotc) { /* @var $dotc DimensionObjectTypeContent */?>
		var dim = <?php echo $dotc->getDimensionId() ?>;
		var dot = <?php echo $dotc->getDimensionObjectTypeId() ?>;
		var cot = <?php echo $dotc->getContentObjectTypeId() ?>;
		if (!og.dimension_object_type_contents[dim]) og.dimension_object_type_contents[dim] = [];
		if (!og.dimension_object_type_contents[dim][dot]) og.dimension_object_type_contents[dim][dot] = [];
		og.dimension_object_type_contents[dim][dot][cot] = {required:<?php echo $dotc->getIsRequired()?"1":"0"?>, multiple:<?php echo $dotc->getIsMultiple()?"1":"0"?>};
<?php
	} 
?>

$(document).ready(function() {
	var logo_link = document.getElementById("change-logo-link");
	if (logo_link) {
		logo_link.onclick = function(e){
			if(e && e.stopPropagation) {
				e.stopPropagation();
			} else {
				e = window.event;
				e.cancelBubble = true;
			}
		}
	}

	og.custom_properties_by_type = [];
	og.openLink(og.getUrl('object', 'get_cusotm_property_columns'), {
		callback: function(success, data){
			og.custom_properties_by_type = data.properties;
		}
	});
});

</script>
<?php include_once(Env::getLayoutPath("listeners"));?>

	<div id="quick-form" > 
            <div style="float: right; cursor: pointer;height: 12px;position: absolute;right: 19px;top: 2px;"><a href="#" onclick="$('.close').click();">close</a></div>
            <div class="close" style="float: right;"></div>
            <div class="form-container"></div>
	</div>
</body>
</html>

<?php Hook::fire('page_rendered', null, $ret); ?>

