{include_once_src file='main/include_js.tpl' src='js/elm_visibility.js'}
<img src="{$ImagesDir}/admin/plus.png" id="chk_{$mark}" alt="{$lng.lbl_click_to_open|escape}" onclick="javascript: switch_elm_visibility('chk_{$mark}','{$mark}', '{$post_func|escape:javascript}')"/>
{if $title}
<a href="javascript: void(0);" onclick="javascript: switch_elm_visibility('chk_{$mark}','{$mark}', '{$post_func|escape:javascript}');"><b>{$title}</b></a>
{/if}
