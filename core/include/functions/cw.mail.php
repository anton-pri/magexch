<?php
cw_load('files');

function cw_mail_quote($string, $charset) {
	return "=?".$charset."?B?".base64_encode($string)."?=";
}

function cw_send_mail($from, $to, $subject_template, $body_template, $language=null, $crypted=false, $is_pdf=false, $files=array(), $send_immediately=false) {
    global $smarty;
    global $current_language;

	if (empty($to)) return;

    $language = $language ? $language : $current_language;

    $mail_subject = chop(cw_display($subject_template, $smarty, false, $language));
    $mail_message = cw_display($body_template, $smarty, false, $language);

    $_files = implode(",", $files);

    $mail_id = cw_array2insert(
        'mail_spool', 
        cw_addslashes(
            array(
                'mail_from' => $from, 
                'mail_to' => $to, 
                'subject' => $mail_subject, 
                'body' => $mail_message, 
                'crypted' => $crypted, 
                'pdf_copy' => intval($is_pdf),
                'created' => constant('CURRENT_TIME'),
                'send' => constant('CURRENT_TIME'),
                'files' => $_files
            )
        )
    );
    
    if ($send_immediately) {
        return cw_call('cw_spool_send_mail', array($mail_id));
    }
    
    return true;
}

function cw_send_simple_mail($from, $to, $subject, $body, $extra_headers=array(), $files=array(), $send_immediately=false) {
    global $current_language;

    if (empty($to)) return;

    $language = $language ? $language : $current_language;

    $_files = implode(",", $files);

    $mail_id = cw_array2insert(
        'mail_spool', 
        cw_addslashes(
            array(
                'mail_from' => $from, 
                'mail_to' => $to, 
                'subject' => $subject, 
                'body' => $body, 
                'crypted' => false, 
                'created' => constant('CURRENT_TIME'),
                'send' => constant('CURRENT_TIME'),
                'files' => $_files
            )
        )
    );
    
    if ($send_immediately) {
        return cw_call('cw_spool_send_mail', array($mail_id));
    }
    
    return true;
}

function cw_parse_mail($msgs, $level = 0) {

	if (empty($msgs))
		return false;

	$lend = (CW_IS_OS_WINDOWS?"\r\n":"\n");
	$head = "";
	$msg = "";

	# Subarray
	if (is_array($msgs['content'])) {
		# Subarray is full
		if(count($msgs['content']) > 1) {
			$boundary = substr(uniqid(time()+rand()."_"), 0, 16);
			$msgs['header']['Content-Type'] .= ";$lend\t boundary=\"$boundary\"";
			foreach($msgs['header'] as $k => $v)
				$head .= $k.": ".$v.$lend;

			if($level > 0)
				$msg = $head.$lend;

			for($x = 0; $x < count($msgs['content']); $x++) {
				$res = cw_parse_mail($msgs['content'][$x], $level+1);
				$msg .= "--".$boundary.$lend.$res[1].$lend;
			}

			$msg .= "--".$boundary."--".$lend;
		} else {
			# Subarray have only one element
			list($msgs['header'], $msgs['content']) = cw_parse_mail($msgs['content'][0], $level);
		}
	}

	# Current array - atom
	if (!is_array($msgs['content'])) {
		if (is_array($msgs['header']))
			foreach ($msgs['header'] as $k => $v)
				$head .= $k.": ".$v.$lend;

		if ($level > 0)
			$msg = $head.$lend;

		$msg .= $msgs['content'].$lend;
	}

	# Header substitute
	if (empty($head)) {
		if (is_array($msgs['header'])) {
			foreach ($msgs['header'] as $k => $v)
				$head .= $k.": ".$v.$lend;
		} else {
			$head = $msgs['header'];
		}
	}

	return array($head, $msg);
}

function cw_pgp_encrypt($message) {
	global $config;

	if (!$config['Security']['crypt_method']) {
		return $message;
	}

	$fn = cw_temp_store($message);
	$gfile = cw_temp_store("");
	if ($config['Security']['crypt_method'] == 'G') {
		if (empty($config['Security']['gpg_key']))
			return $message;

		putenv("GNUPGHOME=".$config['Security']['gpg_home_dir']);

		$gpg_prog = cw_shellquote($config['Security']['gpg_prog']);
		$gpg_key = $config['Security']['gpg_key'];

		@exec($gpg_prog.' --always-trust -a --batch --yes --recipient "'.$gpg_key.'" --encrypt '.cw_shellquote($fn)." 2>".cw_shellquote($gfile));
	}
	else {
		if (empty($config['Security']['pgp_key']))
			return $message;

		putenv("PGPPATH=".$config['Security']['pgp_home_dir']);
		putenv("PGPHOME=".$config['Security']['pgp_home_dir']);

		$pgp_prog = cw_shellquote($config['Security']['pgp_prog']);
		$pgp_key = $config['Security']['pgp_key'];

		if ($config['Security']['use_pgp6'] == "Y") {
			@exec($pgp_prog." +batchmode +force -ea ".cw_shellquote($fn)." \"$pgp_key\" 2>".cw_shellquote($gfile));
		}
		else {
			@exec($pgp_prog.' +batchmode +force -fea "'.$pgp_key.'" < '.cw_shellquote($fn).' > '.cw_shellquote($fn).".asc 2>".cw_shellquote($gfile));
		}
	}

	$af = preg_replace('!\.[^\\\/]+$!S', '', $fn).".asc";
	$message = cw_temp_read($af, true);
	$config['PGP_output'] = cw_temp_read($gfile, true);
	@unlink($fn);

	return $message;
}

function cw_pgp_remove_key() {
	global $config;

	if (!$config['Security']['crypt_method']) {
		return false;
	}

	if ($config['Security']['crypt_method'] == 'G') {
		putenv("GNUPGHOME=".$config['Security']['gpg_home_dir']);

		$gpg_prog = cw_shellquote($config['Security']['gpg_prog']);
		$gpg_key = $config['Security']['gpg_key'];

		@exec($gpg_prog." --batch --yes --delete-key '$gpg_key'");
	}
	else {
		putenv("PGPPATH=".$config['Security']['pgp_home_dir']);
		putenv("PGPHOME=".$config['Security']['pgp_home_dir']);

		$pgp_prog = cw_shellquote($config['Security']['pgp_prog']);
		$pgp_key = $config['Security']['pgp_key'];

		if ($config['Security']['use_pgp6'] == "Y") {
			@exec($pgp_prog." -kr +force +batchmode '$pgp_key'");
		}
		else {
			@exec($pgp_prog." -kr +force '$pgp_key'");
		}
	}
}

function cw_pgp_add_key() {
	global $config;

	if (!$config['Security']['crypt_method']) {
		return false;
	}

	if ($config['Security']['crypt_method'] == 'G') {
		putenv("GNUPGHOME=".$config['Security']['gpg_home_dir']);

		$gpg_prog = cw_shellquote($config['Security']['gpg_prog']);
		$gpg_key = $config['Security']['gpg_key'];

		$fn = cw_temp_store($config['Security']['gpg_public_key']);
		chmod($fn, 0666);

		@exec($gpg_prog.' --batch --yes --import '.cw_shellquote($fn));
	}
	else {
		putenv("PGPPATH=".$config['Security']['pgp_home_dir']);
		putenv("PGPHOME=".$config['Security']['pgp_home_dir']);

		$fn = cw_temp_store( $config['Security']['pgp_public_key']);

		$pgp_prog = cw_shellquote($config['Security']['pgp_prog']);
		$pgp_key = $config['Security']['pgp_key'];

		$ftmp = cw_temp_store('');
		if ($config['Security']['use_pgp6'] == "Y") {
			@exec($pgp_prog.' +batchmode -ka '.cw_shellquote($fn).' 2> '.cw_shellquote($ftmp));
			@exec($pgp_prog.' +batchmode -ks "'.$pgp_key.'"');
		}
		else {
			@exec($pgp_prog.' -ka +force +batchmode '.cw_shellquote($fn).' 2> '.cw_shellquote($ftmp));
			@exec($pgp_prog.' +batchmode -ks "'.$pgp_key.'"');
		}

		unlink($ftmp);
	}

	unlink($fn);
}

#
# This function checks if email is valid
#
function cw_check_email($email) {
	#
	# Simplified checking
	#
	$email_regular_expression = "^([-\d\w][-.\d\w]*)?[-\d\w]@([-!#\$%&*+\\/=?\w\d^_`{|}~]+\.)+[a-zA-Z]{2,6}$";

	#
	# Full checking according to RFC 822
	# Uncomment the line below to use it (change also check_email_script.tpl)
	#	$email_regular_expression = "^[^.]{1}([-!#\$%&'*+.\\/0-9=?A-Z^_`a-z{|}~])+[^.]{1}@([-!#\$%&'*+\\/0-9=?A-Z^_`a-z{|}~]+\\.)+[a-zA-Z]{2,6}$";

	return preg_match("/".$email_regular_expression."/iS", stripslashes($email));
}

#
# Search images in  message body and return message body and images array
#
function cw_attach_images($message) {
	global $http_location, $app_web_dir, $app_main_dir, $current_location, $smarty, $app_http_host;

	# Get images location
	$hash = array();
	if (preg_match_all("/\ssrc=['\"]?([^\s'\">]+)['\">\s]/SsUi", $message, $preg))
		$hash = $preg[1];

	if (empty($hash))
		return array($message, array());

	# Get images data
	$names = array();
	$images = array();
	$app_web_skin_dir = str_replace($app_main_dir, $app_web_dir, $smarty->template_dir);
	foreach ($hash as $v) {
		$orig_name = $v;
		$parse = parse_url($v);
		$data = "";
		$file_path = "";
		if (empty($parse['scheme'])) {

			# Web-path without domain name
			$v = str_replace($app_web_skin_dir."/", "", $parse['path']);
			$file_path = $smarty->template_dir."/".str_replace("/", DIRECTORY_SEPARATOR, $v);
			$v = "http://".$app_http_host.$app_web_skin_dir."/".$v;
			if (!empty($parse['query']))
				$v .= "?".$parse['query'];

		} elseif (strpos($v, $current_location) === 0) {

			# Web-path with domain name
			$file_path = $app_main_dir.str_replace("/", DIRECTORY_SEPARATOR,substr($v, strlen($current_location)));
		}

		if (!empty($file_path) && strpos($file_path, ".php") === false && strpos($file_path, ".asp") === false) {

			# Get image content as local file
			if (file_exists($file_path) && is_readable($file_path)) {
				$fp = @fopen($file_path, "rb");
				if ($fp) {
					if (filesize($file_path) > 0)
						$data = fread($fp, filesize($file_path));
					fclose($fp);
				}

			} else {
				continue;
			}
		}

		if (!empty($images[$v])) {
			continue;
		}

		$tmp = array("name" => basename($v), "url" => $v, "data" => $data);
		if ($names[$tmp['name']]) {
			$cnt = 1;
			$name = $tmp['name'];
			while ($names[$tmp['name']]) {
				$tmp['name'] = $name.$cnt++;
			}
		}

		$names[$tmp['name']] = true;
		if (empty($tmp['data'])) {

			# Get image content as URL
			if ($fp = @fopen($tmp['url'], "rb")) {
				do {
					$tmpdata = fread($fp, 8192);
					if (strlen($tmpdata) == 0) {
						break;
					}
					$tmp['data'] .= $tmpdata;
				} while (true);

				fclose($fp);

			} else {
				continue;
			}
		}

		list($tmp1, $tmp2, $tmp3, $tmp['type']) = cw_get_image_size(empty($data) ? $tmp['url'] : $file_path);
		if (empty($tmp['type']))
			continue;

		$message = preg_replace("/(['\"\(])".preg_quote($orig_name, "/")."(['\"\)])/Ss", "\\1cid:".$tmp['name']."\\2", $message);
		$images[$tmp['url']] = $tmp;
	}

	return array($message, $images);
}

function cw_attach_get_content_type($ext) {
	$types = array (
		'gif' => 'image/gif',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'png' => 'image/png',
		'psd' => 'image/psd',
		'bmp' => 'image/bmp',
		'html' => 'text/html',
		'htm' => 'text/html',
		'txt' => 'text/plain',
		'rtf' => 'application/rtf',
		'zip' => 'application/zip',
		'wav' => 'application/x-wav',
		'mov' => 'video/quicktime',
	);
	
	if (array_key_exists($ext, $types)) {
		return $types[$ext];
	}
	
	return 'application/octet-stream';
}

/**
 * cw_smtp_send_mail sends email via SMTP PHPmailer library
 *
 * @param
 * mail_data = array(
 * from - 'sent from' email address, optional, default value: $config['Email']['smtp_mail_from'] 
 * from_name - 'Sent from' person name, optional
 * send_to - recepient email address, required
 * send_to_name - recepient person name, optional
 * subject - email subject, required
 * body - email body, required
 * alt_body - alternative body, optional
 *
 * @return boolean
 */
function cw_smtp_send_mail($mail_data, $dbg_level = 0) {

    global $config, $app_main_dir;

    include_once $app_main_dir.'/include/lib/PHPmailer/class.phpmailer.php';
    include_once $app_main_dir.'/include/lib/PHPmailer/class.smtp.php';

    $result = 0;

    $mail = new PHPMailer;

    $mail->isSMTP();

//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
    $mail->SMTPDebug = $dbg_level;

//Ask for HTML-friendly debug output
    if ($dbg_level)  
        $mail->Debugoutput = 'error_log';

//Set the hostname of the mail server
    $mail->Host = $config['Email']['smtp_server'];

//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
    $mail->Port = $config['Email']['smtp_port'];

//Set the encryption system to use - ssl (deprecated) or tls
    if ($config['Email']['smtp_use_tlc_connect'] == "Y")
        $mail->SMTPSecure = 'tls';

//Whether to use SMTP authentication
    $mail->SMTPAuth = true;

//Username to use for SMTP authentication - use full email address for gmail
    $mail->Username = $config['Email']['smtp_username'];

//Password to use for SMTP authentication
    $mail->Password = $config['Email']['smtp_password'];

//Set who the message is to be sent from
    if ($config['Email']['smtp_mail_from_force'] != 'Y')
        $mail_from = empty($mail_data['from'])?($config['Email']['smtp_mail_from']):($mail_data['from']);
    else
        $mail_from = $config['Email']['smtp_mail_from'];  

    $mail->setFrom($mail_from, $mail_data['from_name']);

    $mail->addAddress($mail_data['send_to'], $mail_data['send_to_name']);

//Set the subject line
    $mail->Subject = $mail_data['subject'];

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
    $mail->msgHTML($mail_data['body'], dirname(__FILE__));

//Replace the plain text body with one created manually
    if (!empty($mail_data['alt_body']))
        $mail->AltBody = $mail_data['alt_body'];

//send the message, check for errors
    if (!$mail->send()) {
        cw_log_add('email_smtp', $mail->ErrorInfo);
    } else {
        $result = 1;
        cw_log_add('email_smtp', $mail_data);
    }
    $mail->smtpClose();
    return $result;
}

/**
 * Send one email from spool
 */
function cw_spool_send_mail($mail_id) {
    global $config, $tables, $smarty;

    cw_log_add(__FUNCTION__, $mail_id); 

    if ($config['pause_email_sending']) return false;

    $mail_id = intval($mail_id);
    $mail = cw_query_first("select * from $tables[mail_spool] where mail_id=$mail_id");

    if (!$mail) return false;

    $from = preg_replace('![\x00-\x1f].*$!sm', '', $mail['mail_from']);
    $to = $mail['mail_to'];
    $encrypt_mail = $mail['crypted'] && $config['Security']['crypt_method'];
    $charset = 'utf-8';
    $lend = (CW_IS_OS_WINDOWS?"\r\n":"\n");

    $mail_subject = $mail['subject'];
    $mail_message = $mail['body'];

    $msgs = array(
        'header' => array (
            "Content-Type" => "multipart/related;$lend\ttype=\"multipart/alternative\""
        ),
        'content' => array()
    );

    if (CW_IS_OS_WINDOWS)
        $mail_message = preg_replace("/(?<!\r)\n/S", "\r\n", $mail_message);

    if ($encrypt_mail)
        $mail_message = cw_pgp_encrypt($mail_message);

    $orig_mail_message = $mail_message;
    $plain_mail_message = strip_tags(
                        preg_replace(
                                "/<style.*<\/style>/Uims",
                                '',
                                strtr(
                                        $orig_mail_message,
                                        array(
                                                "<br />\n" => "\n",
                                                '<br>' => "\n",
                                                '<br/>' => "\n",
                                                '<br />' => "\n",
                                                '<hr/>' => "---\n",
                                                '<hr />' => "---\n",
                                                '&gt;' => '>',
                                                '&lt;' => '<',
                                                '&quot;' => '"',
                                                '&amp;' => '&',
                                                "\n\n\n" => "\n",
                                                "\n\n" => "\n"
                                        )
                                )
                        )
                );



    $msgs['content'][] = array (
        'header' => array (
            "Content-Type" => "multipart/alternative"
        ),
        'content' => array (
            array (
                'header' => array (
                    "Content-Type" => "text/plain;$lend\tcharset=\"$charset\"",
                    "Content-Transfer-Encoding" => "8bit"
                ),
                'content' => $plain_mail_message
            )
        )
    );

    $smarty->assign('mail_message', $mail_message);
    $mail_message = cw_display("mail/html_message_template.tpl", $smarty, false, $mail['language']);
    list($mail_message, $files) = cw_attach_images($mail_message);

    $msgs['content'][0]['content'][] = array (
        "header" => array (
            "Content-Type" => "text/html;$lend\tcharset=\"$charset\"",
            "Content-Transfer-Encoding" => "8bit"
        ),
        "content" => $mail_message
    );

    if ($mail['files']) {
    	$paths = explode(",", $mail['files']);

    	if (is_array($paths)) {

    		foreach ($paths as $path) {

    			if ($path && file_exists($path)) {
    				$ext = end(explode('.', basename($path)));
    				$files[] = array(
    					'type' => cw_attach_get_content_type($ext),
    					'name' => basename($path),
    					'data' => file_get_contents($path)
    				);
    			}
    		}
    	}
    }

    if (!empty($files)) {
	    foreach ($files as $v) {
	        $msgs['content'][] = array (
	            "header" => array (
	                "Content-Type" => "$v[type];$lend\tname=\"$v[name]\"",
	                "Content-Transfer-Encoding" => "base64",
	                "Content-Disposition" => "attachment",
	                "Content-ID" => "<$v[name]>"
	            ),
	            "content" => chunk_split(base64_encode($v['data']))
	        );
	    }
    }

    list($message_header, $mail_message) = cw_parse_mail($msgs);

    $mail_from = $from;
    if ($config['Email']['use_base64_headers'] == "Y")
        $mail_subject = cw_mail_quote($mail_subject,$charset);

    $headers = "From: ".$mail_from.$lend."X-Mailer: PHP/".phpversion().$lend."MIME-Version: 1.0".$lend.$message_header;
    if (trim($mail_from) != "")
        $headers .= 'Reply-to: '.$mail_from.$lend;

    $mail_result = false;

    if ($config['Email']['use_smtp'] == "Y") {

        $mail_data = array(
            'from' => $mail_from,
            'send_to' => $to,
            //send_to_name
            'subject' => $mail_subject,
            'body' => $orig_mail_message,
            'alt_body' => $plain_mail_message
        );

        $mail_result = cw_smtp_send_mail($mail_data, ($config['Email']['smtp_debug_enabled']=='Y')?2:0); 
        cw_log_add('phpmailer_sent', $mail_data);
    } else {
        if (preg_match('/([^ @,;<>]+@[^ @,;<>]+)/S', $from, $m))
            $mail_result = mail($to, $mail_subject, $mail_message, $headers, "-f".$m[1]);
        else
            $mail_result = mail($to, $mail_subject, $mail_message, $headers);
    }
    
    if ($mail_result) {
        db_query("delete from $tables[mail_spool] where mail_id='$mail[mail_id]'");
    } else {
        $postpone = constant('CURRENT_TIME')+10*60; // Postpone to 10 min
        db_query("update $tables[mail_spool] set send=$postpone where mail_id='$mail[mail_id]'"); 
        cw_log_add('email_error', "Error: can't send email #$mail[mail_id]: $to - $mail_subject. Postponed.", false);   
    }

    return $mail_result;
}

/**
 * Emails spool cron handler
 */
function cw_spool_send_mails() {
    global $config, $tables;
    
    $start_time = $end_time = cw_core_get_time();

    if ($config['pause_email_sending']) return 'Warning: email sending is paused.';

    $log = array();
    
    while ($end_time - $start_time < constant('MAIL_SPOOL_TIMEOUT')) {

        $mail = cw_query_first("select * from $tables[mail_spool] where 
            send<=$start_time AND (send-created)<=".constant('MAIL_SPOOL_TTL')."
            limit 1");
        
        if (!$mail) break;
        
        $result = cw_call('cw_spool_send_mail', array($mail['mail_id']));
        if ($result) {
            $log[] = 'Ok: '.$mail['mail_to'].'  - '.$mail['subject'];
        } else {
            $log[] = 'Error: '.$mail['mail_to'].' - '.$mail['subject'];
        }
    
        $end_time = cw_core_get_time();
    }
    
    $obsolete =  cw_query_first_cell("select count(*) FROM $tables[mail_spool] where (send-created)>".constant('MAIL_SPOOL_TTL'));
    if ($obsolete > 0) {
        $log[] = 'Warning: '.$obsolete.' emails are obsolete and will not be sent anymore. Current TTL for email is '.constant('MAIL_SPOOL_TTL').' seconds';
    }
    
    
    return $log;
}
