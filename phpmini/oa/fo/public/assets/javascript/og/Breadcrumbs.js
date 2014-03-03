
og.Breadcrumbs = {
	
	//cmp: null,
	
	items: 0, 
	
	/*status: 0,*/
	
	/*mainDimension: null ,	
	
	collapse: function () {
		this.cmp.collapse(false);
	},
	
	expand: function () {
		this.cmp.expand(false);
	},
	
	resize: function() {
		var cmp = Ext.getCmp("breadcrumbs-panel") ;
		if (this.items == 0) {
			cmp.setHeight(0);
		
		}else if(this.items == 0) {
			cmp.setHeight(30);
		}else{
			cmp.setHeight(45);
		}
		cmp.doLayout();
		
	},*/
	
	init: function (text) {
		//this.status = 1 ;
	    $('#breadcrumbs').html('<div><div class="primary-breadcrumb" >'
		    	+text
		    	+'</div><ul class="secondary-breadcrumb"></ul></div>'
		 );
	},

	refresh: function (node) {
		if (typeof(node) == 'undefined') return;
		
		// Clean Previews state
		var itemclass = '';
	    var dimensionName = node.attributes.loader.ownerTree.initialConfig.dimensionCode;
	    var mainDimensionId = node.attributes.loader.ownerTree.initialConfig.dimensionId;  
	    var parent = node.parentNode;
	    if (parent) {
	    	parent = node.attributes.loader.ownerTree.getNodeById(parent.id);
	    }
	    var allInfo = true;
	    var defineMainTitle = false;
	    
	    mainText = node.text ;
	    if(node.getDepth() == '0') {
	    	defineMainTitle = true;
	    }	    
	    if (parent && !parent.isRoot ){
	    	mainText += " ("+parent.text+")";
	    }
	    
	    if(defineMainTitle){			
			$('#breadcrumbs').html('<div><div class="primary-breadcrumb" >'
			    	+lang("viewing all information")
			    	+'</div><ul class="secondary-breadcrumb"></ul></div>'
			    );
		}else{		
			$('#breadcrumbs').html('<div><div class="primary-breadcrumb" >'
			    	+mainText
			    	+'</div><ul class="secondary-breadcrumb"></ul></div>'
			    );
		}
	    
	    for (i in og.contextManager.dimensionMembers) {
			var dimId = i ;
			if (dimId != mainDimensionId ){
				var members = og.contextManager.dimensionMembers[i];
				if (members.length ) {
					for(var j in members) {
						var member = members[j];
						if (member > 0 ) {
							
							memberTitle = og.contextManager.getMemberName(dimId, member);
							dimensionTitle = og.contextManager.getDimensionName(dimId);
							var path = og.contextManager.getMemberPath(dimId, member, " / ");
							if (memberTitle) {
								if(!defineMainTitle){
									if ($('#breadcrumbs ul.secondary-breadcrumb li').length == 0 ){
										itemClass = "first";
									}else{
										itemClass = "";
									}
									$("#breadcrumbs ul.secondary-breadcrumb").append("<li class='"+itemClass+"'><strong>"+dimensionTitle+"</strong>: "+path+memberTitle+"</li>");
								}else{
									$("#breadcrumbs div.primary-breadcrumb").text(memberTitle);
									defineMainTitle = false;
									
								}
							}
							allInfo = false;
						}
					}
				}
			}
		}
		
	}
}