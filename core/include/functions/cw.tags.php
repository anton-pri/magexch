<?php
define('DEFAULT_TAG_WEIGHT', 14);

/**
 * get all tags
 *
 * @return array
 */
function cw_tags_get_tags() {
    global $tables;

    $tags = array();
    $result = cw_query("
        SELECT name
        FROM $tables[tags]
    ");

    if (!empty($result) && is_array($result)) {
        foreach ($result as $tag) {
            $tags[] = $tag['name'];
        }
    }

    return $tags;
}

/**
 * get tags with weight data
 *
 * @return array
 */
function cw_tags_get_ex_tags() {
    global $tables, $config;

    $tags = array();
    $result = cw_query("
        SELECT name, product_count
        FROM $tables[tags]
        ORDER BY product_count
    ");

    if (!empty($result) && is_array($result)) {
        if ($config['Appearance']['cloud_is_weighted'] == 'Y') {
            foreach ($result as $tag) {
                $tags[] = array('name' => $tag['name'], 'weight' => $tag['product_count']);
            }
            $min = $tags[0]['weight'];
            $max = $tags[count($tags) - 1]['weight'];
            $ratio = $min / $max;

            $new_tags = array();
            for ($i=0; $i<count($tags); $i++) {
                $weight = round($tags[$i]['weight'] * $ratio * 10 + 10);
                $new_tags[] = array('name' => $tags[$i]['name'], 'weight' => $weight);
            }
            $tags = $new_tags;
        } else {
            foreach ($result as $tag) {
                $tags[] = array('name' => $tag['name'], 'weight' => DEFAULT_TAG_WEIGHT);
            }
        }
    }

    return $tags;
}

/**
 * get first N popular tags
 *
 * @return array
 */
function cw_tags_get_popular_tags() {
    global $tables, $config;

    $tags = array();
    $limit = intval($config['Appearance']['count_first_popular_tags']);
    $limit = empty($limit) || $limit < 0 ? 0 : $limit;

    $result = cw_query("
        SELECT DISTINCT t.name, t.product_count
        FROM $tables[tags] t
        LEFT JOIN $tables[tags_products] tp ON tp.tag_id = t.tag_id
        LEFT JOIN $tables[products_stats] ps ON ps.product_id = tp.product_id
        WHERE IFNULL(ps.views_stats,0) > 0 and IFNULL(ps.sales_stats,0) > 0
        ORDER BY ps.sales_stats DESC, ps.views_stats DESC
        LIMIT $limit
    ");

    if (!empty($result) && is_array($result)) {
        if ($config['Appearance']['cloud_is_weighted'] == 'Y') {
            foreach ($result as $tag) {
                $tags[] = array('name' => $tag['name'], 'weight' => $tag['product_count']);
            }
            $max = $tags[0]['weight'];
            $min = $tags[count($tags) - 1]['weight'];
            $ratio = $min / $max;

            $new_tags = array();
            for ($i=0; $i<count($tags); $i++) {
                $weight = round($tags[$i]['weight'] * $ratio * 10 + 10);
                $new_tags[] = array('name' => $tags[$i]['name'], 'weight' => $weight);
            }
            $tags = $new_tags;
        } else {
            foreach ($result as $tag) {
                $tags[] = array('name' => $tag['name'], 'weight' => DEFAULT_TAG_WEIGHT);
            }
        }
    }

    return $tags;
}

/**
 * get string tags
 *
 * @param $tags
 * @param string $separator
 * @return string
 */
function cw_tags_get_string_tags($tags, $separator=',') {
    if (empty($tags)) {
        return "";
    }

    $_tags = array();

    foreach ($tags as $tag) {
        $_tags[] = $tag['name'];
    }

    return implode($separator, $_tags);
}

/**
 * get tags data
 *
 * @param $product_id
 * @return array
 */
function cw_tags_get_product_tags($product_id) {
    global $tables, $config;

    if (!is_numeric($product_id)) {
        return;
    }

    $tags = array();
    $result = cw_query("
        SELECT t.name, t.product_count
        FROM $tables[tags] t
        LEFT JOIN $tables[tags_products] tp ON tp.tag_id = t.tag_id
        WHERE tp.product_id = '$product_id'
        ORDER BY t.product_count
    ");

    if (!empty($result) && is_array($result)) {
        if ($config['Appearance']['cloud_is_weighted'] == 'Y') {
            foreach ($result as $tag) {
                $tags[] = array('name' => $tag['name'], 'weight' => $tag['product_count']);
            }
            $min = $tags[0]['weight'];
            $max = $tags[count($tags) - 1]['weight'];
            $ratio = $min / $max;

            $new_tags = array();
            for ($i=0; $i<count($tags); $i++) {
                $weight = round($tags[$i]['weight'] * $ratio * 10 + 10);
                $new_tags[] = array('name' => $tags[$i]['name'], 'weight' => $weight);
            }
            $tags = $new_tags;
        } else {
            foreach ($result as $tag) {
                $tags[] = array('name' => $tag['name'], 'weight' => DEFAULT_TAG_WEIGHT);
            }
        }
    }

    return $tags;
}

/**
 * get tags from $tags2 that are not in the $tags1
 *
 * @param $tags1
 * @param $tags2
 * @return array
 */
function cw_tags_get_diff_tags($tags1, $tags2) {
    if (empty($tags2)) {
        return array();
    }

    if (empty($tags1)) {
        return $tags2;
    }

    $result = array();
    foreach ($tags2 as $tag) {
        if (!in_array($tag, $tags1)) {
            $result[] = $tag;
        }
    }

    return $result;
}

/**
 * set product tags
 *
 * @param $tags
 * @param $product_id
 */
function cw_tags_set_product_tags($tags, $product_id) {
    global $tables;

    if (empty($tags) || !is_numeric($product_id)) {
        return;
    }

	$old_tags = array();
    $_tags = cw_tags_get_product_tags($product_id);
	if (!empty($_tags)) {
		foreach ($_tags as $tag) {
			$old_tags[] = $tag['name'];
		}
	}
	$new_tags = cw_tags_get_diff_tags($old_tags, $tags);
	$del_tags = cw_tags_get_diff_tags($tags, $old_tags);

	if (count($new_tags)) {
        foreach ($new_tags as $tag) {
            if (!($tag_id = cw_query_first_cell("SELECT tag_id FROM $tables[tags] WHERE name = '$tag'"))) {
                $tag_id = cw_array2insert(
                    'tags',
                    array(
                        'name' => $tag,
                        'product_count' => 1
                    )
                );
            } else {
                db_query("UPDATE $tables[tags] SET product_count = product_count + 1 WHERE tag_id = '$tag_id'");
            }
            cw_array2insert(
                'tags_products',
                array(
                    'tag_id' => $tag_id,
                    'product_id' => $product_id
                )
            );
        }
    }

    if (count($del_tags)) {
        foreach ($del_tags as $tag) {
            $tag_id = cw_query_first_cell("SELECT tag_id FROM $tables[tags] WHERE name = '$tag'");
            db_query("DELETE FROM $tables[tags_products] WHERE tag_id = '$tag_id' AND product_id = '$product_id'");
            db_query("UPDATE $tables[tags] SET product_count = product_count - 1 WHERE tag_id = '$tag_id'");
        }
    }

    cw_tags_clear_empty_tags();
}

/**
 * clear all product tags
 *
 * @param $product_id
 */
function cw_tags_clear_product_tags($product_id) {
    global $tables;

    if (!is_numeric($product_id)) {
        return;
    }

    $tags = cw_query("SELECT tag_id FROM $tables[tags_products] WHERE product_id = $product_id");
    if (!empty($tags)) {
        foreach ($tags as $tag) {
            db_query("UPDATE $tables[tags] SET product_count = product_count - 1 WHERE tag_id = '$tag[tag_id]'");
        }
    }

    db_query("DELETE FROM $tables[tags_products] WHERE product_id = $product_id");

    cw_tags_clear_empty_tags();
}

/**
 * just delete not used tags
 */
function cw_tags_clear_empty_tags() {
    global $tables;

    db_query("DELETE FROM $tables[tags] WHERE product_count <= 0");
}
