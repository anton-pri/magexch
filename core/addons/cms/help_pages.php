<?php

if ($target == "help" && in_array($section, array('about', 'contactus', 'password', 'faq', 'conditions', 'business','general'))) 
    $related_page = cw_query_first("select * from $tables[cms] where service_code='$target"."_"."$section' and active='Y' and type='staticpage'");

if (!empty($related_page)) {

    $related_page_alt_languages = cw_query_first("SELECT name, url, content FROM $tables[cms_alt_languages] WHERE contentsection_id = '$related_page[contentsection_id]' AND code = '$current_language'");

    if (!empty($related_page_alt_languages) && is_array($related_page_alt_languages)) {
        $related_page['url']     = $related_page_alt_languages['url'];
        $related_page['name']    = $related_page_alt_languages['name'];
        $related_page['content'] = $related_page_alt_languages['content'];
    }

    $smarty->assign('page_data', $related_page);
    $smarty->assign('help_page_title', $related_page['name']);
}
