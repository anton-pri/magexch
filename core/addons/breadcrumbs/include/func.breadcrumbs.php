<?php
// get data for breadcrumb list by params
function cw_breadcrumbs_get_management_breadcrumbs($where="", $orderby="", $limit="", $count_query=FALSE) {
    global $tables;

    $result = cw_query("
        SELECT " . ($count_query ? "count(b1.breadcrumb_id) as c" : "b1.*, b2.link as parent_link, b2.title as parent_title") . "
        FROM $tables[breadcrumbs] b1
        LEFT JOIN $tables[breadcrumbs] b2 ON b2.breadcrumb_id = b1.parent_id
        LEFT JOIN $tables[addons] as a ON a.addon = b1.addon
        WHERE (a.active OR a.addon IS NULL) $where
        " . ($count_query ? "" : $orderby) . "
        $limit
    ");

    if ($count_query) {
        return $result[0]['c'];
    }

    return $result;
}

// generate 'where' string for search query
function cw_breadcrumbs_generate_where_search($breadcrumb_data, $mandatory_where=array()) {
    $where = "";
    $where_or_items = $where_and_items = array();

    if (trim($breadcrumb_data['search']['substring']) != "") {
        $substring = trim($breadcrumb_data['search']['substring']);
        $where_and_items[] = "b1.link LIKE '%" . $substring . "%' OR b1.title LIKE '%" . $substring . "%'";
    }

    if (!empty($breadcrumb_data['search']['unknown_links'])) {
        $where_or_items[] = "b1.parent_id < 0";
    }

    if (!empty($breadcrumb_data['search']['uniting'])) {
        $where_or_items[] = "b1.uniting = 1";
    }

    if (!empty($breadcrumb_data['search']['area'])) {
        $where_or_items[] = 'b1.area = "'.trim($breadcrumb_data['search']['area']).'"';
    }

    if (!empty($mandatory_where)) {
        $where = " AND " . implode(" AND ", $mandatory_where);
    }

    if (!empty($where_or_items) || !empty($where_and_items)) {
        $where .= (!empty($where_and_items) ? " AND (" . implode(") AND (", $where_and_items) . ")" : "");
        $where .= (!empty($where_or_items) ? " AND (" . implode(" OR ", $where_or_items) . ")" : "");
    }

    return $where;
}

// check unique link
function cw_breadcrumbs_is_unique_link($link, $action, $breadcrumb_id) {
    global $tables;

    $where = "";

    if ($action == 'edit_breadcrumb') {
        $where = "AND b.breadcrumb_id <> '$breadcrumb_id'";
    }

    $url = parse_url($link);
    $result = cw_breadcrumbs_get_breadcrumb($link, $url['query'], $where);

    return empty($result);
}

// get breadcrumb by full link or by part(uniting link)
function cw_breadcrumbs_get_breadcrumb($full_link, $query_link, $where="") {
    global $tables;

    // search by all link
    $breadcrumb = cw_query_first("
        SELECT b.*
        FROM $tables[breadcrumbs] b
        LEFT JOIN $tables[addons] as a ON a.addon = b.addon
        WHERE (a.active OR a.addon IS NULL) AND b.link = '$full_link' $where
        ORDER BY area='".APP_AREA."' DESC
    ");

    if (empty($breadcrumb)) {
        // search by part of link for uniting link
        parse_str($query_link, $str);

        if (!empty($str) && is_array($str)) {
            $test_link = '/index.php';
            $delimeter = '?';

            foreach ($str as $param => $value) {
                $test_link .= $delimeter . $param . '=' . preg_replace('/[0-9]+/', '[[ANY]]', $value);
                $breadcrumb = cw_query_first("
                    SELECT b.*
                    FROM $tables[breadcrumbs] b
                    LEFT JOIN $tables[addons] as a ON a.addon = b.addon
                    WHERE (a.active OR a.addon IS NULL) AND b.link = '$test_link' AND b.uniting = 1 $where
                    ORDER BY area='".APP_AREA."' DESC
                ");

                if (!empty($breadcrumb)) {
                    return $breadcrumb;
                }
                $delimeter = '&';
            }
        }
    }

    return $breadcrumb;
}

// get breadcrumbs array
function cw_breadcrumbs_get_breadcrumbs($link, $query_link) {
    global $tables;

    $breadcrumbs = array();
    preg_match('/[0-9]+/', $query_link, $matches_id);

    $breadcrumb = cw_breadcrumbs_get_breadcrumb($link, $query_link, "AND b.parent_id <> -1");

    if (!empty($breadcrumb)) {
        $all_get_breadcrumb_links = array(); // for stop while if has some problem

        while (
            $breadcrumb['parent_id'] != 0
            && !in_array($breadcrumb['link'], $all_get_breadcrumb_links)
        ) {
            array_unshift($breadcrumbs, $breadcrumb);
            $all_get_breadcrumb_links[] = $breadcrumb['link'];
            $breadcrumb = cw_query_first("
                SELECT b.*
                FROM $tables[breadcrumbs] b
                LEFT JOIN $tables[addons] as a ON a.addon = b.addon
                WHERE (a.active OR a.addon IS NULL) AND b.breadcrumb_id = '$breadcrumb[parent_id]' AND b.parent_id <> -1
                ORDER BY area='".APP_AREA."' DESC
            ");

            if (!empty($matches_id) && is_numeric($matches_id[0])) {
                $breadcrumb['link'] = str_replace('[[ANY]]', $matches_id[0], $breadcrumb['link']);
            }
        }
        array_unshift($breadcrumbs, $breadcrumb);
    }

    return $breadcrumbs;
}

// delete not active breadcrumb
function cw_breadcrumbs_delete_breadcrumb($breadcrumb_id) {
    global $tables;

    db_query("DELETE FROM $tables[breadcrumbs] WHERE breadcrumb_id = '$breadcrumb_id' AND parent_id = -1");
}
