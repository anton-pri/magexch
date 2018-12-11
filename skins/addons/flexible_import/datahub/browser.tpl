<div class="block">
    <ul class="nav nav-tabs nav-tabs-alt" data-toggle="tabs" id="flexible-tabs">
{foreach from=$datahub_browse_tables item=dh_browse_table key=dh_brs}
{assign var='_dh_browse_table_title' value=''}
{if is_array($dh_browse_table)}
{assign var='_dh_browse_table' value=$dh_browse_table.table_name}
{assign var='_dh_browse_table_title' value=$dh_brs}
{else}
{assign var='_dh_browse_table' value=$dh_browse_table}
{/if}
        <li id="dh_browse_{$_dh_browse_table}">
            <a href="{$catalogs.admin}/index.php?target=datahub_browser&_browse_table={$_dh_browse_table}">{$_dh_browse_table_title|default:$_dh_browse_table}</a>
        </li>
{/foreach}
    </ul>
</div>

<script>
$(document).ready(function(){ldelim}
        $('#dh_browse_{$_browse_table}').addClass('active');
{rdelim});
</script>


{capture name=section}
  {capture name=block}

  {include file='main/datatable/datatable.tpl' dt_rsrc="dh_browser_`$_browse_table`"}

  {/capture}
  {include file="admin/wrappers/block.tpl" content=$smarty.capture.block title=$_dh_browse_table_title|default:$_browse_table}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_datahub_tables_browser|default:'Datahub Tables Browser'}
