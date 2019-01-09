/*
 *@author: nicolaas[at] sunnysideup.co.nz
 *@description: share this functions
 **/

;(function($) {
	jQuery(document).ready(function() {
		sharethis.init();
	});
})(jQuery);

var sharethis = {

	useBW: false,

	set_use_BW: function(value) {
		this.useBW = value;
	},

	ULSelector: ".ShareThisUL",

	ASelector: ".ShareThisUL li a",

	IMGMouseOutClass: "shareThisIMGMouseOut",

	IMGMouseOverClass: "shareThisIMGMouseOver",

	shareThisIconPathWithoutBW: "images/icons/",

	shareThisIconPathWithBW: "images/icons/BW/",

	init: function() {
		if(sharethis.useBW) {
			sharethis.addBWEffect();
		}
		sharethis.addPopUps();
	},

	bookmark: function (url, title) {
		if (window.sidebar) { // Mozilla Firefox Bookmark
			window.sidebar.addPanel(title, url,"");
		}
		else if( window.external ) { // IE Favorite
			window.external.AddFavorite( url, title);
		}
		else if(window.opera && window.print) { // Opera Hotlist
			return true;
		}
	},

	addPopUps: function() {
		jQuery(sharethis.ASelector).click(
			function(event) {
				event.preventDefault();
				var href = jQuery(this).attr("href");
				windowWidth = Math.round(jQuery(window).width() * 0.9);
				windowHeight = Math.round(jQuery(window).height() * 0.9);
				window.open(href,'share','resizable=1,status=0,menubar=0,toolbar=0,scrollbars=1,location=0,directories=0,width='+windowWidth+',height='+windowHeight+'');
			}
		)
	},

	addBWEffect: function() {
		jQuery(sharethis.ASelector).each(
			function(i, el){
				jQuery(el).mouseout(
					function() {
						jQuery(el).find("."+sharethis.IMGMouseOverClass).hide();
						jQuery(el).find("."+sharethis.IMGMouseOutClass).show();
					}
				);
				jQuery(el).mouseover(
					function(){
						jQuery(el).find("."+sharethis.IMGMouseOutClass).hide();
						jQuery(el).find("."+sharethis.IMGMouseOverClass).show();
					}
				);
				var src = jQuery(el).find("img").attr("src");
				var newSrc = sharethis.addBWPath(src);
				jQuery(el).find("img").attr("src", newSrc).addClass(sharethis.IMGMouseOutClass);
				jQuery(el).append('<img src="'+src+'" style="display: none;" class="'+sharethis.IMGMouseOverClass+'" />');
				jQuery(el).mouseout();
			}
		);
	},

	addBWPath: function(string) {
		string = string.replace(sharethis.shareThisIconPathWithoutBW, sharethis.shareThisIconPathWithBW);
		//string.replace("images/icons/BW/BW", "images/icons/BW/");
		return string;
	},

	removeBWPath: function(string) {
		string = string.replace(sharethis.shareThisIconPathWithBW, sharethis.shareThisIconPathWithoutBW);
		return string;
	}
}
