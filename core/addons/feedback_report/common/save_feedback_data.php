<?php
global $APP_SESSION_VARS;

if (!empty($_POST['data'])) {
	$json = $_POST['data'];
	$json_string = stripslashes($json);
	$data = json_decode($json_string, true);

	$comment = $data[0]['Issue'];
	$image = $data[1];

	if ($image) {
        $decoded = base64_decode(str_replace('data:image/' . feedback_image_type . ';base64,', '', $image));

        if (cw_fbr_check_image_is_correct_type($decoded)) {
			$path = cw_fbr_create_new_folder();

			if ($path) {
				$files = array(
					'message.txt' => $comment,
                    'image.' . feedback_image_type => $decoded,
					'session_dump.txt' => print_r($APP_SESSION_VARS, TRUE),
					'navigation_history.txt' => print_r($APP_SESSION_VARS['navigation_history_list'], TRUE)
				);
				cw_fbr_put_files_to_folder($path, $files);
				
				exit('0');
			}
			exit('4');  // Saving error
		}
		exit('3');  // Wrong image
	}
	exit('2');  // Empty image
}

exit('1');  // Empty data
