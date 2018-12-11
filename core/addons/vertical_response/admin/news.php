<?php
$required_mode = array("update", "modify", "export", "message", "delete", "subscribers");

if (!in_array($mode, $required_mode)) $mode = '';

$vertical_response_data = &cw_session_register('vertical_response_data', array());
// clear data after day
if (isset($vertical_response_data['time']) && $vertical_response_data['time'] + 60*60*24 < cw_core_get_time()) {
	$vertical_response_data = array();
}

$conditions = " and show_as_news=2";

if ($mode == 'subscribers') {
    if ($action == "add" && !empty($new_email)) {
        $count = cw_query_first_cell("
			SELECT COUNT(*) FROM $tables[newslist_subscription]
			WHERE list_id='$list_id' AND email='$new_email'
        ");
		if ($count < 1) {
			$result = cw_vertical_response_add_subcriber($list_id, $new_email);
			if ($result) {
				db_query("
					INSERT INTO $tables[newslist_subscription] (list_id, email, since_date)
					VALUES ('$list_id', '$new_email', '" . cw_core_get_time() . "')
				");
			}
			$top_message['content'] = cw_get_langvar_by_name("msg_adm_news_subscriber_add");
        } else {
		    $top_message['content'] = cw_get_langvar_by_name("msg_adm_err_news_subscriber_add");
			$top_message['type'] = "E";
        }
    } elseif ($action == "update") {
		$is_search = cw_query_first_cell("SELECT avail FROM $tables[newslists] WHERE list_id = '$list_id'");
		// if list was manual, but now is search
		if (!$is_search && $type_list_control == 1) {
			// delete subscribers
			db_query("DELETE FROM $tables[newslist_subscription] WHERE list_id='$list_id'");
			$vertical_response_data['subscribers'][$list_id] = array();
		// if list was search, but now is manual
		} elseif ($is_search && $type_list_control == 0) {
			$vertical_response_data['subscribers'][$list_id] = array();
		}
		// change parameters
		cw_array2update('newslists',
			array('avail' => $type_list_control, 'salesman_customer_id' => $saved_search),
			"list_id = '$list_id'"
		);
    } elseif ($action == "delete" && is_array($to_delete)) {
	    foreach ($to_delete as $email => $flag) {
			$result = cw_vertical_response_delete_subcriber($list_id, $email);
			if ($result) {
				db_query("DELETE FROM $tables[newslist_subscription] WHERE list_id='$list_id' AND email='$email'");
			}
		}
        $top_message['content'] = cw_get_langvar_by_name("msg_adm_news_subscribers_del");
    } elseif ($action == "export" && !empty($to_delete)) {
	    header("Content-type: application/force-download");
	    header("Content-disposition: attachment; filename=subscribers.txt");

        $subscribers = cw_query("
			SELECT * FROM $tables[newslist_subscription]
			WHERE list_id='$list_id' AND email IN ('".implode("','", array_keys($to_delete))."')
        ");
		if (is_array($subscribers))
		    foreach ($subscribers as $value)
			    echo $value['email'] . "\n";
        exit;
    }

    cw_header_location("index.php?target=$target&js_tab=subscriptions&list_id=".$list_id);
}

if ($mode == 'message') {
    if (is_array($message)) {
        $message['subject'] = @trim($message['subject']);

		$smarty->assign('action', 'modify');
        $smarty->assign('message', $message);
		$fill_error = array();
        foreach (array('subject', 'body') as $key) {
            if (empty($message[$key])) {
				$fill_error[$key] = true;
			}
		}

        if (!count($fill_error)) {
			cw_load('product');
            $mode = '';

			$products1 = array();
			if (!empty($message['products1'][0]['id'])) {
				foreach ($message['products1'] as $product) {
					$products1[] = cw_func_call('cw_product_get', array('id' => $product['id'], 'info_type' => 0));
				}
			}
			$products2 = array();
			if (!empty($message['products2'][0]['id'])) {
				foreach ($message['products2'] as $product) {
					$products2[] = cw_func_call('cw_product_get', array('id' => $product['id'], 'info_type' => 0));
				}
			}
			if ($products1 || $products2) {
				$smarty->assign('products1', $products1);
				$smarty->assign('products2', $products2);
			}
			// compile body
			$template = $message['body'];
			require_once($smarty->_get_plugin_filepath('function', 'eval'));
			$compiled_body = smarty_function_eval(array('var' => $template), $smarty);
			// update newsletter
			if (!empty($message['news_id'])) {
				$news_id = cw_vertical_response_edit_message($list_id, $message['news_id'], $compiled_body, $message['subject']);
				if ($news_id) {
					cw_array2update('newsletter',
						array(
							'news_id' => $news_id,
							'updated_date' => cw_core_get_time(),
							'subject' => $message['subject'],
							'body' => $message['body'],
							'allow_html' => 1
						),
						"news_id = '$message[news_id]'"
					);
					$top_message['content'] = cw_get_langvar_by_name("msg_adm_news_message_upd");
				}
            } else {
				$news_id = cw_vertical_response_edit_message($list_id, 0, $compiled_body, $message['subject']);
				if ($news_id) {
					cw_array2insert('newsletter',
						array(
							'news_id' => $news_id,
							'list_id' => $list_id,
							'send_date' => cw_core_get_time(),
							'updated_date' => cw_core_get_time(),
							'created_date' => cw_core_get_time(),
							'subject' => $message['subject'],
							'body' => $message['body'],
							'allow_html' => 1,
							'show_as_news' => 2
						)
					);
					$message['news_id'] = $news_id;
					$top_message['content'] = cw_get_langvar_by_name("msg_adm_news_message_add");
				}
            }
			// update newsletter products
			if ($news_id) {
				db_query("DELETE FROM $tables[newsletter_products] WHERE list_id='$list_id'");
				if (!empty($message['products1'][0]['id'])) {
					foreach ($message['products1'] as $product) {
						cw_array2insert('newsletter_products',
							array(
								'list_id' => $list_id,
								'product_id' => $product['id'],
								'product' => $product['name'],
								'product_num' => 1
							)
						);
					}
				}
				$products2 = array();
				if (!empty($message['products2'][0]['id'])) {
					foreach ($message['products2'] as $product) {
						cw_array2insert('newsletter_products',
							array(
								'list_id' => $list_id,
								'product_id' => $product['id'],
								'product' => $product['name'],
								'product_num' => 2
							)
						);
					}
				}
            	cw_header_location("index.php?target=$target&list_id=".$list_id."&js_tab=message&messageid=".$message['news_id']);
			} else {
				cw_header_location("index.php?target=$target&list_id=".$list_id."&js_tab=message");
			}
        } else {
            $nwslt_object = &cw_session_register('nwslt_object');
		    $nwslt_object['fill_error'] = 'error';
            $nwslt_object['message'] = $message;
            cw_header_location("index.php?target=$target&list_id=".$list_id."&js_tab=message&messageid=".$message['news_id']);
        }
    }

    cw_header_location("index.php?target=$target&js_tab=message&list_id=".$list_id);
}

if ($action == 'delete' && is_array($to_delete)) {
    foreach ($to_delete as $k => $v) {
		$result = cw_vertical_response_delete_list($k);
		if ($result) {
			db_query("DELETE FROM $tables[newslist_subscription] WHERE list_id='$k'");
			db_query("DELETE FROM $tables[newslists] WHERE list_id='$k'");
			db_query("DELETE FROM $tables[newsletter] WHERE list_id='$k'");
			db_query("DELETE FROM $tables[newsletter_countries] WHERE list_id='$k'");
			db_query("DELETE FROM $tables[newsletter_products] WHERE list_id='$k'");
		}
    }
    $top_message['content'] = cw_get_langvar_by_name('msg_adm_newslists_del');
    cw_header_location("index.php?target=$target");
}

if ($action == 'modify') {
    $list = cw_array_map('trim', $list);

    $fill_error = array();
    foreach (array('name', 'descr') as $key) {
        if (empty($list[$key])) {
			$fill_error[$key] = true;
		}
	}

	$list_id = false;
	if (!count($fill_error)) {
        $list_values = $list;
        $list_values['salesman_customer_id'] = 0;
        $list_values['subscribe'] = 0;
        $list_values['show_as_news'] = 2;

        cw_unset($list_values, 'list_id');

        if (!empty($list['list_id'])) {
			$list_id = cw_vertical_response_edit_list($list['list_id'], $list['name']);
			if ($list_id) {
				cw_array2update('newslists', $list_values, "list_id='$list[list_id]'");
				$top_message['content'] = cw_get_langvar_by_name("msg_adm_newslist_upd");
			}
        }
        else {
			$list_id = cw_vertical_response_edit_list(0, $list['name']);
			if ($list_id) {
				$list_values['lngcode'] = empty($edit_lng) ? $current_language : $edit_lng;
				$list_values['list_id'] = $list_id;
				cw_array2insert('newslists', $list_values);
				$list['list_id'] = $list_id;
				$top_message['content'] = cw_get_langvar_by_name("msg_adm_newslists_add");
			}
        }
    } else {
        $top_message = array('content' => cw_get_langvar_by_name('err_filling_form'), 'type' => 'E');
        $nwslt_object = &cw_session_register('nwslt_object');
        $nwslt_object['fill_error'] = $fill_error;
        $nwslt_object['list'] = $list;
    }

	if ($list_id) {
    	cw_header_location("index.php?target=$target&list_id=".$list['list_id']);
	} else {
		cw_header_location("index.php?target=" . $target);
	}
}

if (isset($_GET['list_id'])) {
    if (!empty($list_id)) {
    	$list = cw_query_first("select * from $tables[newslists] WHERE list_id='$list_id'");

		if (
			(!isset($vertical_response_data['subscribers'][$list_id]) || empty($vertical_response_data['subscribers'][$list_id]))
			&& $list['avail'] == 0
		) {
			if (empty($vertical_response_data['time'])) {
				$vertical_response_data['time'] = cw_core_get_time();	// lifetime
			}
			db_query("DELETE FROM $tables[newslist_subscription] WHERE list_id='$list_id'");

			$result = cw_vertical_response_get_subscribers($list_id);
			$vertical_response_data['subscribers'][$list_id] = true;

			if ($result) {
				$data = array();
				foreach ($result as $item) {
					$data['list_id'] = $list_id;
					$data['email'] = addslashes($item['email']);
					$data['since_date'] = $item['create_date'];
					cw_array2insert('newslist_subscription', $data);
				}
			}
		} elseif ($list['avail'] == 1 && $list['salesman_customer_id']) {
			db_query("DELETE FROM $tables[newslist_subscription] WHERE list_id='$list_id'");
			// search subscribers
			$saved_search = $list['salesman_customer_id'];	// salesman_customer_id is saved search id
			$sql_query = cw_query_first_cell("
				SELECT sql_query FROM $tables[saved_search] WHERE ss_id = '$saved_search'
			");
			if ($sql_query) {
				$results = cw_query($sql_query);
				if ($results) {
					$data = array();
					foreach ($results as $result) {
						$count = cw_query_first_cell("
							SELECT COUNT(*) FROM $tables[newslist_subscription]
							WHERE list_id='$list_id' AND email='" . $result['email'] . "'
						");
						if ($count < 1) {
							$data['list_id'] 	= $list_id;
							$data['email'] 		= addslashes($result['email']);
							$data['since_date'] = cw_core_get_time();
							cw_array2insert('newslist_subscription', $data);
						}
					}
				}
			}
		}

        if (empty($list['list_id'])) {
            $top_message['content'] = cw_get_langvar_by_name('msg_adm_err_newslist_not_exists');
            cw_header_location('index.php?target='.$target);
        } else {
            if (
				$list['lngcode'] != $current_language
				&& is_array($d_langs)
				&& !in_array($list['lngcode'], $d_langs)
			) {
                cw_header_location("index.php?target=$target&mode=modify&list_id=$list_id&edit_lng=$list[lngcode]&old_lng=$current_language");
        	}
        }

        $smarty->assign('list_id', $list_id);
        $smarty->assign('list', $list);

		// subscribers tabs
        $total_items = count($subscribers = cw_call('cw\news\get_subscribers', array($list_id)));

        if (!empty($total_items)) {
            $navigation = cw_core_get_navigation($target, $total_items, $page);
            $navigation['script'] = "index.php?target=$target&js_tab=subscriptions&list_id=".$list_id;
            $smarty->assign('navigation', $navigation);

            $subscribers = array_slice($subscribers, $navigation['first_page'], $navigation['objects_per_page']);
        }
        $smarty->assign('subscribers', $subscribers);

		// messages tab
		$message = cw_query_first("
			SELECT * FROM $tables[newsletter] WHERE list_id = '$list_id' AND show_as_news = 2 LIMIT 1
		");

		if (!$message) {
			$message = cw_vertical_response_get_message($list_id, 0);
			if (isset($message['news_id'])) {
				cw_array2insert('newsletter',
					array(
						'news_id' => $message['news_id'],
						'list_id' => $list_id,
						'send_date' => cw_core_get_time(),
						'updated_date' => cw_core_get_time(),
						'created_date' => cw_core_get_time(),
						'subject' => $message['subject'],
						'body' => $message['body'],
						'allow_html' => 1,
						'show_as_news' => 2
					)
				);
			}
		}
        if ($message) {
			$message['products1'] = cw_query("
				SELECT product_id as id, product as name FROM $tables[newsletter_products]
				WHERE list_id = '$list_id' AND product_num = 1
			");
			$message['products2'] = cw_query("
				SELECT product_id as id, product as name FROM $tables[newsletter_products]
				WHERE list_id = '$list_id' AND product_num = 2
			");
            $smarty->assign('message', $message);
            $smarty->assign('messageid', $message['news_id']);
        }

        $smarty->assign('main', 'management');
    } else {
        $smarty->assign('main', 'details');
	}
} else {
	if (!isset($vertical_response_data['news']) || empty($vertical_response_data['news'])) {
		if (empty($vertical_response_data['time'])) {
			$vertical_response_data['time'] = cw_core_get_time();	// lifetime
		}

		$result = cw_vertical_response_get_newslists();
		$vertical_response_data['news'] = true;

		if ($result) {
			$exist_list_id = array();
			foreach ($result as $item) {
				$exist_list_id[] = $item['id'];
				// if list exist
				if (cw_query_first_cell("SELECT list_id FROM $tables[newslists] WHERE list_id = '".$item['id']."'")) {
					// update it
					cw_array2update('newslists',
						array('name' => $item['name'], 'descr' => $item['descr']),
						"list_id = '".$item['id']."'"
					);
				} else {
					// add new list
					$data = array();
					$data['list_id'] = $item['id'];
					$data['name'] = $item['name'];
					$data['descr'] = $item['descr'];
					$data['show_as_news'] = 2;
					$data['lngcode'] = empty($edit_lng) ? $current_language : $edit_lng;
					cw_array2insert('newslists', $data);
				}
			}
			// delete not exist lists
			$result = cw_query("
				SELECT list_id FROM $tables[newslists]
				WHERE list_id NOT IN ('" . implode("','", $exist_list_id) . "') AND show_as_news = 2
			");
			db_query("
				DELETE FROM $tables[newslists]
				WHERE list_id NOT IN ('" . implode("','", $exist_list_id) . "') AND show_as_news = 2
			");
			// delete subscribers
			if ($result) {
				foreach ($result as $item) {
					db_query("DELETE FROM $tables[newslist_subscription] WHERE list_id = '" . $item['list_id'] . "'");
				}
			}
		}
	}

	$lists = cw_query("SELECT * FROM $tables[newslists] WHERE lngcode='$current_language' $conditions");
	$smarty->assign('lists', $lists);
    $smarty->assign('main', 'news');
}

if (cw_session_is_registered('nwslt_object')) {
	$nwslt_object = &cw_session_register('nwslt_object');
	if (is_array($nwslt_object)) {
		foreach ($nwslt_object as $k => $v) {
			$smarty->assign($k, $v);
		}
	}

	cw_session_unregister("nwslt_object");
}

$smarty->assign('saved_searches', cw_vertical_response_get_saved_search());
$smarty->assign('action', $action);
$smarty->assign('mode', $mode);
$smarty->assign('js_tab', $js_tab);
