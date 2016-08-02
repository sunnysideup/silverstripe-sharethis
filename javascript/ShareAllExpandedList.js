
/*
 *@author: nicolaas[at] sunnysideup.co.nz
 *@description: share this expanded list toggle functions
 **/

;(function($) {
	$(document).ready(function() {
		ShareAllExpandedList.init();
	});
})(jQuery);



var ShareAllExpandedList = {

	shareAllExpandedListULSelector: ".shareAllExpandedListUL",

	shareAllExpandedListULCloseSelector: ".shareAllExpandedListUL li.shareAllExpandedListULClose a",

	mainLinkSelector: ".shareAllExpandedListHeader h5 > a",

	listShownClass: "listShown",

	listClosedClass: "listHidden",

	init: function() {
		jQuery("body").on(
			"click",
			ShareAllExpandedList.mainLinkSelector+ ", " + shareAllExpandedListULCloseSelector
			function() {
				jQuery(ShareAllExpandedList.shareAllExpandedListULSelector)
					.toggleSlide()
					.toggleClass(ShareAllExpandedList.listShownClass)
					.toggleClass(ShareAllExpandedList.listClosedClass);
				return false;
			}
		)
	}

}


//immediately
jQuery(ShareAllExpandedList.shareAllExpandedListULSelector)
	.css("display", "none")
	.addClass(ShareAllExpandedList.listClosedClass);
