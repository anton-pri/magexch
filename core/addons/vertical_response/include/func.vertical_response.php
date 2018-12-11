<?php
/**
 * subscribe/unsubscribe user with selected news
 *
 * @param $customer_id
 * @param $profile
 * @return bool|null
 */
function cw_vertical_response_on_profile_modify($customer_id, $profile) {
	if (!isset($profile['mailing_list'])) return true;
	if ($customer_id) {
		$userinfo = cw_call('cw_user_get_info', array($customer_id, 1));
	} else {
		return null;
	}

	global $config;

	$vertical_response_data = cw_session_register('vertical_response_data');
	$vertical_response_email = trim($config[vertical_response_addon_name]['vertical_response_email']);
	$vertical_response_password = trim($config[vertical_response_addon_name]['vertical_response_password']);

	try {
		$clear_data = false;
		$delete_list_id = $vertical_response_data['user_lists'];

		foreach ($profile['mailing_list'] as $lid => $v) {
			if (strpos($lid, 'vr_') !== false) {
				$list_id = str_replace('vr_', '', $lid);
				// if select new email list, then add user to list
				if (!in_array($list_id, $vertical_response_data['user_lists'])) {
					if (empty($sid)) {
						$vr = new SoapClient(vertical_response_wsdl,
							array (
								'connection_timeout' => 5,
							)
						);
						$sid = $vr->login(
							array(
								'username' => "$vertical_response_email",
								'password' => "$vertical_response_password",
								'session_duration_minutes' => vertical_response_ses_time
							)
						);
					}
					// add user to list
					$vr->addListMember(
						array(
							'session_id'  => $sid,
							'list_member' => array(
								'list_id'     => $list_id,
								'member_data' => array(
									array('name' => 'email_address', 'value' => $userinfo['email']),
									array('name' => 'first_name', 'value' => $userinfo['main_address']['firstname']),
									array('name' => 'last_name', 'value' => $userinfo['main_address']['lastname']),
								),
							),
						)
					);
					$clear_data = true;
				} else {// else list still select and don't need to delete them
					$delete_list_id = array_diff($delete_list_id, array($list_id));
				}
			}
		}

		if (count($delete_list_id)) {
			foreach ($delete_list_id as $list_id) {
				if (empty($sid)) {
					$vr = new SoapClient(vertical_response_wsdl,
						array (
							'connection_timeout' => 5,
						)
					);
					$sid = $vr->login(
						array(
							'username' => "$vertical_response_email",
							'password' => "$vertical_response_password",
							'session_duration_minutes' => vertical_response_ses_time
						)
					);
				}
				// delete user from list
				$vr->deleteListMember(
					array(
						'session_id' => $sid,
						'list_member' => array(
							'list_id' => $list_id,
							'member_data' => array(
								array(
									'name' => 'hash',
									'value' => $vertical_response_data['hash'],
								),
							),
						),
					)
				);
			}
			$clear_data = true;
		}

		if ($clear_data) {
			cw_session_unregister('vertical_response_data');
		}
	} catch (SoapFault $exception) {
		//exit ('fault: "' . $exception->faultcode . '" - ' . $exception->faultstring . "\n");
	}
}

/**
 * get mail lists by customer
 *
 * @param $customer_id
 * @return null
 */
function cw_vertical_response_get_newslists_by_customer($customer_id) {
	global $config;

	$return = cw_get_return();

	if ($customer_id) {
		$userinfo = cw_call('cw_user_get_info', array($customer_id, 0));
	} else {
		return $return;
	}

	$vertical_response_data = &cw_session_register('vertical_response_data', array());
	// clear data after day
	if (isset($vertical_response_data['time']) && $vertical_response_data['time'] + SECONDS_PER_DAY < cw_core_get_time()) {
		$vertical_response_data = array();
	}

	if (isset($vertical_response_data['lists']) && !empty($vertical_response_data['lists'])) {
		foreach ($vertical_response_data['lists'] as $list) {
			if ($list->status == 'active') {
				$return['vr_' . $list->id] = array(
					'list_id' 	=> 'vr_' . $list->id,
					'name' 		=> $list->name,
					'descr' 	=> $list->name,
					'direct' 	=> in_array($list->id, is_array($vertical_response_data['user_lists'])?$vertical_response_data['user_lists']:array()) ? 1 : 0,
				);
			}
		}
	} else {
		$vertical_response_email = trim($config[vertical_response_addon_name]['vertical_response_email']);
		$vertical_response_password = trim($config[vertical_response_addon_name]['vertical_response_password']);

		try {
			$vertical_response_data['time'] = cw_core_get_time();	// lifetime

			$vr = new SoapClient(vertical_response_wsdl,
				array (
					'connection_timeout' => 5,
				)
			);

			$sid = $vr->login(
				array(
					'username' => "$vertical_response_email",
					'password' => "$vertical_response_password",
					'session_duration_minutes' => vertical_response_ses_time
				)
			);

			// get all lists
			$lists = $vr->enumerateLists( array(
				'session_id'         => $sid,
				'type'               => 'email',
				'include_field_info' => false,
				'limit'              => 20,
			));

			$vertical_response_data['lists'] = $lists;

			if (!empty($lists) && count($lists)) {
				foreach ($lists as $list) {
					if ($list->status == 'active') {
						// find lists with user with email $userinfo['email']
						$list_members = $vr->searchListMembers(array(
							'session_id'  => $sid,
							'field_name'  => 'email_address',
							'field_value' => $userinfo['email'],
							'list_id'     => $list->id,
							'max_records' => 1
						));

						if (!empty($list_members) && count($list_members)) {
							$vertical_response_data['user_lists'][] = $list_members[0]->list_id;
							$vertical_response_data['user_lists'] = array_unique($vertical_response_data['user_lists']);

							if (empty($vertical_response_data['hash'])) {
								foreach ($list_members[0]->member_data as $data) {
									if ($data->name == 'hash') {
										$vertical_response_data['hash'] = $data->value;
										break;
									}
								}
							}
						}

						$return['vr_' . $list->id] = array(
							'list_id' 	=> 'vr_' . $list->id,
							'name' 		=> $list->name,
							'descr' 	=> $list->name,
							'direct' 	=> !empty($list_members) ? 1 : 0,
						);
					}
				}
			}
		} catch (SoapFault $exception) {
			//exit ('fault: "' . $exception->faultcode . '" - ' . $exception->faultstring . "\n");
			return $return;
		}
	}

	return $return;
}

/**
 * get mail lists
 *
 * @return array|null
 */
function cw_vertical_response_get_newslists() {
	global $config;

	$return = cw_get_return();
	$vertical_response_data = cw_session_register('vertical_response_data');

	if (isset($vertical_response_data['lists']) && !empty($vertical_response_data['lists'])) {
		foreach ($vertical_response_data['lists'] as $list) {
			if ($list->status == 'active') {
				$return[] = array(
					'id' 		=> $list->id,
					'list_id' 	=> 'vr_' . $list->id,
					'name' 		=> $list->name,
					'descr' 	=> $list->name
				);
			}
		}
	} else {
		$vertical_response_email = trim($config[vertical_response_addon_name]['vertical_response_email']);
		$vertical_response_password = trim($config[vertical_response_addon_name]['vertical_response_password']);

		try {
			$vr = new SoapClient(vertical_response_wsdl,
				array (
					'connection_timeout' => 5,
				)
			);

			$sid = $vr->login(
				array(
					'username' => "$vertical_response_email",
					'password' => "$vertical_response_password",
					'session_duration_minutes' => vertical_response_ses_time
				)
			);

			// get all lists
			$lists = $vr->enumerateLists( array(
				'session_id'         => $sid,
				'type'               => 'email',
				'include_field_info' => false,
				'limit'              => 20,
			));

			if (!empty($lists) && count($lists)) {
				foreach ($lists as $list) {
					if ($list->status == 'active') {
						$return[] = array(
							'id' 		=> $list->id,
							'list_id' 	=> 'vr_' . $list->id,
							'name' 		=> $list->name,
							'descr' 	=> $list->name
						);
					}
				}
			}
		} catch (SoapFault $exception) {
			//exit ('fault: "' . $exception->faultcode . '" - ' . $exception->faultstring . "\n");
		}
	}

	return $return;
}

/**
 * get mail lists
 *
 * @return array|null
 */
function cw_vertical_response_get_lists() {
	global $config;

	$vertical_response_data = &cw_session_register('vertical_response_data');
	// clear data after day
	if (isset($vertical_response_data['time']) && $vertical_response_data['time'] + SECONDS_PER_DAY < cw_core_get_time()) {
		$vertical_response_data = array();
	}
	$result = array();

	if (isset($vertical_response_data['lists']) && !empty($vertical_response_data['lists'])) {
		foreach ($vertical_response_data['lists'] as $list) {
			if ($list->status == 'active') {
				$result[] = array(
					'list_id' 	=> $list->id,
					'name' 		=> $list->name,
					'descr' 	=> $list->name
				);
			}
		}
	} else {
		$vertical_response_email = trim($config[vertical_response_addon_name]['vertical_response_email']);
		$vertical_response_password = trim($config[vertical_response_addon_name]['vertical_response_password']);

		try {
			$vertical_response_data['time'] = cw_core_get_time();	// lifetime

			$vr = new SoapClient(vertical_response_wsdl,
				array (
					'connection_timeout' => 5,
				)
			);

			$sid = $vr->login(
				array(
					'username' => "$vertical_response_email",
					'password' => "$vertical_response_password",
					'session_duration_minutes' => vertical_response_ses_time
				)
			);

			// get all lists
			$lists = $vr->enumerateLists( array(
				'session_id'         => $sid,
				'type'               => 'email',
				'include_field_info' => false,
				'limit'              => 20,
			));

			$vertical_response_data['lists'] = $lists;

			if (!empty($lists) && count($lists)) {
				foreach ($lists as $list) {
					if ($list->status == 'active') {
						$result[] = array(
							'list_id' 	=> $list->id,
							'name' 		=> $list->name,
							'descr' 	=> $list->name
						);
					}
				}
			}
		} catch (SoapFault $exception) {
			//exit ('fault: "' . $exception->faultcode . '" - ' . $exception->faultstring . "\n");
		}
	}

	return $result;
}

/**
 * delete profile
 *
 * @param $profile_id
 */
function cw_vertical_response_profile_delete($profile_id) {
	global $tables;

	if (!empty($profile_id) && is_numeric($profile_id)) {
		db_query("DELETE FROM $tables[recurring_list_update] WHERE id = '$profile_id'");
	}
}

/**
 * get saved search
 *
 * @return array
 */
function cw_vertical_response_get_saved_search() {
	global $tables;

	$result = cw_query("
		SELECT ss_id, name FROM $tables[saved_search] WHERE type = 'C' ORDER BY name
	");

	$return = array();
	if (!empty($result)) {
		foreach ($result as $r) {
			$return[] = array(
				'id' => $r['ss_id'],
				'name' => $r['name']
			);
		}
	}

	return $return;
}

/**
 * get recurring list profiles
 *
 * @return array
 */
function cw_vertical_response_get_recurring_list_profiles() {
	global $tables;

	$result = cw_query("
		SELECT r.*, s.name as saved_search
		FROM $tables[recurring_list_update] r
		LEFT JOIN $tables[saved_search] s ON r.saved_search_id = s.ss_id
		WHERE s.type = 'C'
	");

	return $result;
}

/**
 * update list by cron daily
 *
 * @param $time
 */
function cw_vertical_response_daily_list_update($time) {
	global $tables, $config;

	// find active profiles
	$result = cw_query("
		SELECT r.list_id, s.sql_query
		FROM $tables[recurring_list_update] r
		LEFT JOIN $tables[saved_search] s ON r.saved_search_id = s.ss_id
		WHERE r.active = 1 AND s.type = 'C'
	");

	if (!empty($result)) {
		foreach ($result as $value) {
			if (!empty($value['sql_query'])) {
				// find customers by saved query
				$customers = cw_query($value['sql_query']);

				$customer_emails = array();
				if ($customers) {
					foreach ($customers as $customer) {
						// if user is active
						if ($customer['status'] == 'Y') {
							$customer_emails[] = $customer['email'];
						}
					}

					// if emails is not empty
					if (!empty($customer_emails) && !empty($value['list_id'])) {
						$vertical_response_email = trim($config[vertical_response_addon_name]['vertical_response_email']);
						$vertical_response_password = trim($config[vertical_response_addon_name]['vertical_response_password']);

						try {
							$vr = new SoapClient(vertical_response_wsdl,
								array (
									'connection_timeout' => 5,
								)
							);

							$sid = $vr->login(
								array(
									'username' => "$vertical_response_email",
									'password' => "$vertical_response_password",
									'session_duration_minutes' => vertical_response_ses_time
								)
							);

							// get all users from list
							$members_list = $vr->downloadList(array(
								'session_id' => $sid,
								'list_id' => $value['list_id'],
								'delimiter' => 'csv',
								'fields_to_include' => array('email_address')
							));

							if (
								isset($members_list->contents->location)
								&& ($handle = fopen($members_list->contents->location, "r")) !== FALSE
							) {
								$row = 1;
								$members_list_emails = array();
								while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
									// if it's not header line
									if ($row != 1) {
										$members_list_emails[] = $data[0];
									}
									$row++;
								}
								fclose($handle);

								// if need add some users
								if (array_diff($customer_emails, $members_list_emails)) {
									foreach ($customers as $customer) {
										// if user is active
										if (
											$customer['status'] == 'Y'
											&& in_array($customer['email'], $customer_emails)
										) {
											// add user to list
											$vr->addListMember(
												array(
													'session_id'  => $sid,
													'list_member' => array(
														'list_id'     => $value['list_id'],
														'member_data' => array(
															array('name' => 'email_address', 'value' => $customer['email']),
															array('name' => 'first_name', 'value' => $customer['firstname']),
															array('name' => 'last_name', 'value' => $customer['lastname']),
														),
													),
												)
											);
										}
									}
								}
							}
						} catch (SoapFault $exception) {
							//exit ('fault: "' . $exception->faultcode . '" - ' . $exception->faultstring . "\n");
						}
					}
				}
			}
		}
	}
}

/**
 * edit list
 *
 * @param $list_id
 * @param $name
 * @return bool
 */
function cw_vertical_response_edit_list($list_id, $name) {
	global $config;

	$vertical_response_email = trim($config[vertical_response_addon_name]['vertical_response_email']);
	$vertical_response_password = trim($config[vertical_response_addon_name]['vertical_response_password']);
	$return = $list_id;

	try {
		$vr = new SoapClient(vertical_response_wsdl,
			array (
				'connection_timeout' => 5,
			)
		);

		$sid = $vr->login(
			array(
				'username' => "$vertical_response_email",
				'password' => "$vertical_response_password",
				'session_duration_minutes' => vertical_response_ses_time
			)
		);

		if (empty($list_id)) {
			$return = $vr->createList(array(
				'session_id' => $sid,
				'name' 		 => $name,
				'type'		 => 'email',
			));
		} else {
			$vr->editListAttribute(array(
				'session_id'      => $sid,
				'list_id'         => $list_id,
				'attribute_name'  => 'name',
				'attribute_value' => $name,
			));
		}
	} catch (SoapFault $exception) {
		//exit ('fault: "' . $exception->faultcode . '" - ' . $exception->faultstring . "\n");
		return false;
	}

	return $return;
}

/**
 * delete list
 *
 * @param $list_id
 * @return bool
 */
function cw_vertical_response_delete_list($list_id) {
	global $config;

	$vertical_response_email = trim($config[vertical_response_addon_name]['vertical_response_email']);
	$vertical_response_password = trim($config[vertical_response_addon_name]['vertical_response_password']);

	try {
		$vr = new SoapClient(vertical_response_wsdl,
			array (
				'connection_timeout' => 5,
			)
		);

		$sid = $vr->login(
			array(
				'username' => "$vertical_response_email",
				'password' => "$vertical_response_password",
				'session_duration_minutes' => vertical_response_ses_time
			)
		);

		$vr->deleteList(array(
			'session_id' => $sid,
			'list_id'    => $list_id,
		));
	} catch (SoapFault $exception) {
		//exit ('fault: "' . $exception->faultcode . '" - ' . $exception->faultstring . "\n");
		return false;
	}

	return true;
}

/**
 * add subcriber
 *
 * @param $list_id
 * @param $email
 * @return bool
 */
function cw_vertical_response_add_subcriber($list_id, $email) {
	global $config;

	$vertical_response_email = trim($config[vertical_response_addon_name]['vertical_response_email']);
	$vertical_response_password = trim($config[vertical_response_addon_name]['vertical_response_password']);

	try {
		$vr = new SoapClient(vertical_response_wsdl,
			array (
				'connection_timeout' => 5,
			)
		);

		$sid = $vr->login(
			array(
				'username' => "$vertical_response_email",
				'password' => "$vertical_response_password",
				'session_duration_minutes' => vertical_response_ses_time
			)
		);

		$r=$vr->addListMember(
			array(
				'session_id'  => $sid,
				'list_member' => array(
					'list_id'     => $list_id,
					'member_data' => array(
						array('name' => 'email_address', 'value' => $email),
					),
				),
			)
		);
	} catch (SoapFault $exception) {
		//exit ('fault: "' . $exception->faultcode . '" - ' . $exception->faultstring . "\n");
		return false;
	}

	return true;
}

/**
 * delete subcriber
 *
 * @param $list_id
 * @param $email
 * @return bool
 */
function cw_vertical_response_delete_subcriber($list_id, $email) {
	global $config;

	$vertical_response_email = trim($config[vertical_response_addon_name]['vertical_response_email']);
	$vertical_response_password = trim($config[vertical_response_addon_name]['vertical_response_password']);

	try {
		$vr = new SoapClient(vertical_response_wsdl,
			array (
				'connection_timeout' => 5,
			)
		);

		$sid = $vr->login(
			array(
				'username' => "$vertical_response_email",
				'password' => "$vertical_response_password",
				'session_duration_minutes' => vertical_response_ses_time
			)
		);

		$list_members = $vr->searchListMembers(array(
			'session_id'  => $sid,
			'field_name'  => 'email_address',
			'field_value' => $email,
			'list_id'     => $list_id,
			'max_records' => 1
		));

		if (!empty($list_members) && count($list_members)) {
			$hash = "";
			foreach ($list_members[0]->member_data as $data) {
				if ($data->name == 'hash') {
					$hash = $data->value;
					break;
				}
			}
			$vr->deleteListMember(
				array(
					'session_id' => $sid,
					'list_member' => array(
						'list_id' => $list_id,
						'member_data' => array(
							array(
								'name' => 'hash',
								'value' => $hash,
							),
						),
					),
				)
			);
		}
	} catch (SoapFault $exception) {
		//exit ('fault: "' . $exception->faultcode . '" - ' . $exception->faultstring . "\n");
		return false;
	}

	return true;
}

/**
 * get subscribers
 *
 * @param $list_id
 * @return array
 */
function cw_vertical_response_get_subscribers($list_id) {
	global $config;

	$vertical_response_email = trim($config[vertical_response_addon_name]['vertical_response_email']);
	$vertical_response_password = trim($config[vertical_response_addon_name]['vertical_response_password']);

	$subscribers = array();

	try {
		$vr = new SoapClient(vertical_response_wsdl,
			array (
				'connection_timeout' => 5,
			)
		);

		$sid = $vr->login(
			array(
				'username' => "$vertical_response_email",
				'password' => "$vertical_response_password",
				'session_duration_minutes' => vertical_response_ses_time
			)
		);

		$lists = $vr->downloadList(array(
			'session_id' => $sid,
			'list_id' => $list_id,
			'delimiter' => 'csv',
			'fields_to_include' => array('email_address', 'create_date'),
		));

		if (!empty($lists)) {
			$content = file_get_contents($lists->contents->location);
			$rows = str_getcsv($content, "\n");
			if ($rows) {
				unset($rows[0]);
				foreach ($rows as $row) {
					$list = str_getcsv($row, ",");
					$datetime = date_create_from_format('Y-m-d H:i:s', $list[1]);
					$timestamp = date_timestamp_get($datetime);
					$subscribers[] = array(
						'email' => $list[0],
						'create_date' => $timestamp,
						'optin_status' => $list[2]
					);
				}
			}
		}
	} catch (SoapFault $exception) {
		//exit ('fault: "' . $exception->faultcode . '" - ' . $exception->faultstring . "\n");
	}

	return $subscribers;
}

/**
 * get message
 *
 * @param $list_id
 * @param $message_id
 * @return array
 */
function cw_vertical_response_get_message($list_id, $message_id) {
	global $config;

	$vertical_response_email = trim($config[vertical_response_addon_name]['vertical_response_email']);
	$vertical_response_password = trim($config[vertical_response_addon_name]['vertical_response_password']);

	try {
		$vr = new SoapClient(vertical_response_wsdl,
			array (
				'connection_timeout' => 5,
			)
		);

		$sid = $vr->login(
			array(
				'username' => "$vertical_response_email",
				'password' => "$vertical_response_password",
				'session_duration_minutes' => vertical_response_ses_time
			)
		);

		$params = array(
			'session_id'      => $sid,
			'statuses'        => array('active'),
			'limit'           => 20,
			'include_content' => true,
			'include_lists'   => true
		);
		if ($message_id) {
			$params['campaign_ids'] = array($message_id);
		}
		$messages = $vr->enumerateEmailCampaigns($params);

		if (!empty($messages)) {
			$data = array();
			foreach ($messages as $message) {
				if ($message->lists && is_array($message->lists)) {
					foreach ($message->lists as $list) {
						if ($list->id == $list_id && $message->contents) {
							$data['news_id'] = $message->id;
							foreach ($message->contents as $content) {
								if ($content->type == "subject") {
									$data['subject'] = $content->copy;
								}
								if ($content->type == "freeform_html") {
									$data['body'] = $content->copy;
								}
							}
							return $data;
						}
					}
				}
			}
		}
	} catch (SoapFault $exception) {
		//exit ('fault: "' . $exception->faultcode . '" - ' . $exception->faultstring . "\n");
		return array();
	}

	return array();
}

/**
 * edit message
 *
 * @param $list_id
 * @param $message_id
 * @param $content
 * @param $subject
 * @return bool
 */
function cw_vertical_response_edit_message($list_id, $message_id, $content, $subject) {
	global $config;

	$vertical_response_email = trim($config[vertical_response_addon_name]['vertical_response_email']);
	$vertical_response_password = trim($config[vertical_response_addon_name]['vertical_response_password']);
	$mid = $message_id;

	try {
		$vr = new SoapClient(vertical_response_wsdl,
			array (
				'connection_timeout' => 5,
			)
		);

		$sid = $vr->login(
			array(
				'username' => "$vertical_response_email",
				'password' => "$vertical_response_password",
				'session_duration_minutes' => vertical_response_ses_time
			)
		);

		// check if email exist on VR
		$data = cw_vertical_response_get_message($list_id, $message_id);
		if (!isset($data['news_id'])) {
			$mid = 0;
		}

		if ($mid) {
			// edit message
			$vr->updateEmailContents(array(
				'session_id'    => $sid,
				'email_id'      => $mid,
				'freeform_html' => $content,
				'freeform_text' => trim($config['Company']['company_name']) . " message"
			));
		} else {
			// create a message
			$message = array(
				'name'          	=> "Email #" . cw_core_get_time(),
				'email_type'    	=> "canvas",
				'from_label'    	=> $config['Company']['company_name'],
				'reply_to_email'    => $config['Company']['site_administrator'],
				'freeform_html'		=> $content,
				'freeform_text' 	=> trim($config['Company']['company_name']) . " message",
				'subject' 			=> $subject . " (#" . cw_core_get_time() . ")",
				'hosted_email'		=> true,
			);
			$mid = $vr->createEmail(
				array(
					'session_id' => $sid,
					'email' => $message
				)
			);
			// attaches the list you made above to the campaign you just created
			$vr->setCampaignLists(
				array(
					'session_id' => $sid,
					'campaign_id' => $mid,
					'list_ids' => array($list_id)
				)
			);
		}
	} catch (SoapFault $exception) {
		// exit ('fault: "' . $exception->faultcode . '" - ' . $exception->faultstring . "\n");
		return false;
	}

	return $mid;
}

/**
 * Update emails for saved_search list by cron
 */
function cw_vertical_response_emails_update() {
	global $tables;

	$log = array();

	$lists = cw_query("
		SELECT list_id, salesman_customer_id FROM $tables[newslists]
		WHERE avail = 1 AND show_as_news = 2 AND salesman_customer_id <> 0
	");
	if ($lists) {
		foreach ($lists as $list) {
			$list_id = $list['list_id'];

                        $log[$list_id]['list_id']=$list_id;

			db_query("DELETE FROM $tables[newslist_subscription] WHERE list_id = '$list_id'");
			// search subscribers
			$saved_search = $list['salesman_customer_id'];	// salesman_customer_id is saved search id
			$sql_query = cw_query_first_cell("
				SELECT sql_query FROM $tables[saved_search] WHERE ss_id = '$saved_search'
			");
			$exist_cw = array();	// emails from saved_search
			if ($sql_query) {
				$results = cw_query($sql_query);
				if ($results) {
					$data = array();
					foreach ($results as $result) {
						$count = cw_query_first_cell("
							SELECT COUNT(*) FROM $tables[newslist_subscription]
							WHERE list_id='$list_id' AND email='" . $result['email'] . "'
						");
						if ($count < 1 && !cw_is_anonymous($result['email']) && filter_var($result['email'], FILTER_VALIDATE_EMAIL)) {
							$exist_cw[] = $result['email'];

							$data['list_id'] 	= $list_id;
							$data['email'] 		= addslashes($result['email']);
							$data['since_date'] = cw_core_get_time();
							cw_array2insert('newslist_subscription', $data);
						}
					}
				}
			}

			// synchronize emails
			// get emails from vr
			$exist_vr = array();
			$subscribers = cw_vertical_response_get_subscribers($list_id);
			if ($subscribers) {
				foreach ($subscribers as $subscriber) {
					$exist_vr[] = $subscriber['email'];
				}
			}
			$exist_cw = array_map('strtolower',$exist_cw);
			$exist_vr = array_map('strtolower',$exist_vr);
			// need add
			$need_add = array_diff($exist_cw, $exist_vr);
			$ii=1;
			if ($need_add) {
				foreach ($need_add as $email) {
//					echo ($ii++).': + '.$email."\n";
                                        $log[$list_id]['add'][] = $email;
					cw_vertical_response_add_subcriber($list_id, $email);
					if ($ii>20) break;
				}
			}
			// need delete
			$need_delete = array_diff($exist_vr, $exist_cw);
			$ii=1;
			if ($need_delete) {
				foreach ($need_delete as $email) {
//                                        echo ($ii++).': - '.$email."\n";
					$log[$list_id]['delete'][] = $email;
					cw_vertical_response_delete_subcriber($list_id, $email);
                                        if ($ii>20) break;
				}
			}
		}
	}
	return $log;
}
