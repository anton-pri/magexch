{capture name=section}
{if $flexible_diff_rep}

{*$flexible_diff_rep|@debug_print_var*}
{foreach from=$flexible_diff_rep key=table_name item=diff_data}
<h2>Table: {$table_name}</h2>

<br />
{assign var='added_entries_count' value=0}
{capture assign='added_entries_list'}
{foreach from=$diff_data.add_del item=d}
  {if $d.diff eq 'add'}
    {if $added_entries_count eq 0}
      <tr>{foreach from=$d item=col_val key=col_name}{if $col_name ne 'diff'}<th align="center" nowrap="nowrap"  style="border-right:solid 1px black">{$col_name}</th>{/if}{/foreach}</tr>
    {/if}
    <tr>{foreach from=$d item=col_val key=col_name}{if $col_name ne 'diff'}<td align="right">{$col_val}</td>{/if}{/foreach}</tr>    
    {math equation='x+1' x=$added_entries_count assign='added_entries_count'} 
  {/if}
{/foreach}
{/capture}
{if $added_entries_count gt 0}
<div><h3>Added ({$added_entries_count}):</h3></div>
<div style="width:1150px; height:400px; overflow: scroll">
<table cellpadding="2">
{$added_entries_list}
</table>
</div>
{else}
<div><i>No entries added</i></div>
{/if}
<br />
{assign var='changed_entries_count' value=0}
{capture assign='changed_entries_list'}
{foreach from=$diff_data.changed item=c}
  {if !$changed_entries_count}
    <tr><th>&nbsp;</th>{foreach from=$c.0 item=col_val key=col_name}{if $col_name ne 'diff'}<th align="center" nowrap="nowrap" style="border-right:solid 1px black">{$col_name}</th>{/if}{/foreach}</tr>    
  {/if}
  <tr><td>{$c.0.diff}:</td>{foreach from=$c.0 item=col_val key=col_name}{if $col_name ne 'diff'}<td align="right" {if $c.0.$col_name ne $c.1.$col_name}style="color:red"{/if}>{$col_val}</td>{/if}{/foreach}</tr>  
  <tr><td>{$c.1.diff}:</td>{foreach from=$c.1 item=col_val key=col_name}{if $col_name ne 'diff'}<td align="right" {if $c.0.$col_name ne $c.1.$col_name}style="color:green"{/if}>{$col_val}</td>{/if}{/foreach}</tr>
  <tr><td>&nbsp;</td>{foreach from=$c.1 item=col_val key=col_name}{if $col_name ne 'diff'}<td>&nbsp;</td>{/if}{/foreach}</tr>
  {math equation='x+1' x=$changed_entries_count assign='changed_entries_count'}
{/foreach}
{/capture}
{if $changed_entries_count gt 0}
<div><h3>Changed ({$changed_entries_count}):</h3></div>
<div style="width:1150px; height:400px; overflow: scroll">
<table cellpadding="2">
{$changed_entries_list}
</table>
{else}
<div><i>No entries changed</i></div>
{/if}

<HR>
{/foreach}
{/if}
{/capture}
{$smarty.capture.section}
{*include file="common/section.tpl" content=$smarty.capture.section extra='width="100%"' title="Test import of profile '`$profile.name`' (#`$profile.id`)"*}
