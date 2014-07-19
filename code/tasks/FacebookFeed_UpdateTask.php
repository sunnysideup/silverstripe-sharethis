<?php


class FacebookFeed_UpdateTask extends BuildTask {

	protected $title = "Update Facebook News";

	protected $description = "Checks for updates on Facebook";

	/**
	 *
	 */
	function run($request) {
		$facebookPages = DataObject::get('FacebookFeed_Page');
		if($facebookPages && $facebookPages->Count()) {
			foreach($facebookPages as $facebookPage) {
				DB::alteration_message("Facebook page #{$facebookPage->ID} '$facebookPage->Title' updated", 'changed');
				$facebookPage->Fetch();
			}
		}
	}
}
