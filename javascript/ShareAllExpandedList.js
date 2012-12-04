
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

	mainLinkSelector: ".shareAllExpandedListHeader h5 a",

	openSpeed: "slow",

	closeSpeed: "slow",

	listShownClass: "listShown",

	listClosedClass: "listHidden",

	init: function() {
		jQuery(ShareAllExpandedList.shareAllExpandedListUL).css("display", "none");
		jQuery(ShareAllExpandedList.mainLinkSelector).click(
			function() {
				if(jQuery(this).parent().parent().siblings(ShareAllExpandedList.shareAllExpandedListUL).is(":hidden")) {
					jQuery(this).addClass(ShareAllExpandedList.listShownClass);
					jQuery(this).removeClass(ShareAllExpandedList.listClosedClass);
					jQuery(this).parent().parent().siblings(ShareAllExpandedList.shareAllExpandedListUL).slideDown(ShareAllExpandedList.openSpeed);
				}
				else {
					jQuery(this).addClass(ShareAllExpandedList.listClosedClass);
					jQuery(this).removeClass(ShareAllExpandedList.listShownClass);
					jQuery(this).parent().parent().siblings(ShareAllExpandedList.shareAllExpandedListUL).slideUp(ShareAllExpandedList.closeSpeed);
				}
				return false;
			}

		)
		.addClass(ShareAllExpandedList.listClosedClass);
		jQuery(ShareAllExpandedList.shareAllExpandedListULCloseSelector).click(
			function(){
				jQuery(this).parent().parent().parent().find(ShareAllExpandedList.mainLinkSelector).click();
				return false;
			}
		);
	}




}
