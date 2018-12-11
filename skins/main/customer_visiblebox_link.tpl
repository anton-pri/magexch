{include_once_src file='main/include_js.tpl' src='js/customer_elm_visibility.js'}
<div class="adv_plus" id="chk_{$mark}" alt="{$lng.lbl_click_to_open|escape}" onclick="javascript: switch_elm_visibility('chk_{$mark}','{$mark}', '{$post_func|escape:javascript}')"/> </div>
{if $title}
<a href="javascript: void(0);" onclick="javascript: switch_elm_visibility('chk_{$mark}','{$mark}', '{$post_func|escape:javascript}');"><b>{$title}</b></a>
{/if}
