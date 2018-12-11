<?php

/*
 * Add ajax block to response
*/
/**
 * $arr - element for collector of ajax blocks to be updated as result of AJAX response
  $arr = $ajax_blocks[] = array(
    'id' => <div_id>,
    'action' => update|remove|show|hide|append|prepend|after|before,
    'content' => <html_content>,
    ['template' => <template_path>,]
    );
 *
 * $key - name if element in collection must be named, for example to remove it or overwrite later
*/
function  cw_ajax_add_block($arr, $key=null) {
    global $ajax_blocks;
    if (is_null($key)) $ajax_blocks[] = $arr;
    else $ajax_blocks[strval($key)] = $arr;	
}

/**
 *  Alias for cw_ajax_add_block() for compatibility
 */
function cw_add_ajax_block($arr, $key=null) {
    return cw_ajax_add_block($arr,$key);
}

/**
 * Remove marked ajax block
 */
function cw_ajax_remove_block($key) {
    global $ajax_blocks;
    unset($ajax_blocks[strval($key)]);
}

