<?php

function smarty_function_webmaster($p, $smarty) {
    global $identifiers, $request_prepared;
   
    $webmaster_status = cw_session_register('webmaster_status');
     
    if (empty($identifiers['A']) || empty($webmaster_status)) {
        return '';
    }
    
    $tag = (!empty($p['tag'])?$p['tag']:'a');

    if (!empty($p['imgkey'])) {
        $imgkey = "&amp;imgkey=$p[imgkey]";
    }

    return "$p[pre_button_title]<$tag class='ajax webmaster_modify webmaster_modify_$p[type]' type='$p[type]' key='$p[key]' href='index.php?target=webmaster&amp;mode=view&amp;type=$p[type]&amp;key=$p[key]$imgkey' title='$p[title]' $p[extra]>$p[button_title]</$tag>$p[post_button_title]";
}
