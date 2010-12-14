
/**
 * AsyncFileUpload class
 * @class 
 */
AsyncFileUpload = function(elt, options){

	var self = this;
	var elt = elt;
	var root_url = options.rootUrl;
	var base_www = options.basePath;
	
	this.settings = {
			"script"    	: root_url + "/tao/File/upload",
			"popupUrl"		: root_url + "/tao/File/htmlUpload",
			"uploader"  	: base_www + "lib/jquery.uploadify/uploadify.swf",
			"cancelImg" 	: base_www + "img/cancel.png",
			"buttonImg"		: base_www + "img/browse_btn.png",
			"scriptAccess"	: 'sameDomain',
			"width"			: 140,
			"height"		: 40,
			"auto"      	: true,
			"multiple"		: false,
			"buttonText"	: 'Browse',
			"folder"    	: "/"
	};
	
	this.settings = $.extend(true, this.settings, options);
	
	var target = false;
	if(options.target){
		var target = $(options.target);
	}
	if(target){
		this.settings.onComplete = function(event, queueID, fileObj, response, data){
			response = $.parseJSON(response);
			if(response.uploaded){
				target.val(response.uploaded_file);
			}
			return false;
		};
	}
	
	if(isFlashPluginEnabled() && typeof(jQuery.fn.uploadify) != 'undefined'){
	
		$(elt).uploadify(this.settings);
	}
	else{
		//fallback if no flash or if uploadify is not loaded
		var params = {
			target  : options.target,
			format  : 'file'	
		};
		if(this.settings.fileExt){
			params.fileExt = this.settings.fileExt;
		}
		if(this.settings.fileExt){
			params.sizeLimit = this.settings.sizeLimit;
		}
		
		var opener = $("<span><a href='#'>Upload File</a></span>");
		opener.click(function(e){
			
			$(this).attr('disabled', true);
			
			var url = self.settings.popupUrl + '?' + $.param(params);
			var popupOpts = "width=350px,height=100px,menubar=no,resizable=yes,status=no,toolbar=no,dependent=yes,left="+e.pageX+",top="+e.pageY;
			
			self.window = window.open(url, 'fileuploader', popupOpts);
			self.window.focus();
			
			
			return false;
		});
		$(elt).parent().append(opener);
		
		$(elt).hide();
	}
};


if(typeof(isFlashPluginEnabled) != 'function'){
	/**
	 * Check if a flahs player is found in the plugins list
	 * @return {boolean}
	 */
	function isFlashPluginEnabled(){
		if(navigator.plugins != null && navigator.plugins.length > 0){
			for(i in navigator.plugins){
				if(/(Shockwave|Flash)/i.test(navigator.plugins[i]['name'])){
					return true;
				}
			}
		}
		return false;
	}
}