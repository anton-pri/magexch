{assign var=imgpath value="`$SkinDir`/addons/top_menu/images/"}

{assign var=ic_edit value="`$imgpath`edit-enbl-ic.png"}
{assign var=ic_save value="`$imgpath`save2.png"}

{assign var=ic_coll value="`$imgpath`collapse.png"}
{assign var=ic_expd value="`$imgpath`expand.png"}
{assign var=ic_rest value="`$imgpath`restore.png"}
{assign var=ic_del value="`$imgpath`del2.png"}

{if $active_parent ne 0}
{if $item.active eq 0}{assign var=active_parent value=0}{else}{assign var=active_parent value=1}{/if}
{/if}
{foreach from=$sub_menu key=mid item=item}
{include file="addons/top_menu/cat.tpl"}
{/foreach}

{*
<tr><td colspan="10" align="left">{$sub_menu|@debug_print_var}</td></tr>
*}