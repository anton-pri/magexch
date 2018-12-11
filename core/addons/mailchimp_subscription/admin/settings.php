<?php
if (
	AREA_TYPE == 'A' 
	&& $_POST['action'] == 'update'
	&& $_POST['cat'] == 'mailchimp_subscription'
) {

	if (empty($_POST['configuration']['mailchimp_apikey'])) {
		$top_message = array('content' => cw_get_langvar_by_name('txt_please_enter_all_required_info'), 'type' => 'E');
		cw_header_location('index.php?target=settings&cat=mailchimp_subscription');
	}
	else {
		$mailchimp_response = cw_mailchimp_list_history($_POST['configuration']['mailchimp_id'], $_POST['configuration']['mailchimp_apikey']);
		
		if (!empty($mailchimp_response['Error_message'])) {
			$top_message = array('content' => cw_get_langvar_by_name('txt_mailchimp_error_txt') . $mailchimp_response['Error_message'], 'type' => 'E');
			cw_header_location('index.php?target=settings&cat=mailchimp_subscription');
		}
	}
}
