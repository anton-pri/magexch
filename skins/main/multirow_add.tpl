{include_once file='main/multirow.tpl'}
<a href="javascript: void(0);" onclick="javascript: add_inputset('{$mark}', this{if $is_lined}, true{/if});"><img src="{$ImagesDir}/admin/plus.png" /></a>
{if $possible_delete}
<a href="javascript: void(0);" onclick="javascript: remove_inputset(this);"><img src="{$ImagesDir}/admin/plus.png" /></a>
{/if}
