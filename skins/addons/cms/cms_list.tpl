{*
vim: set ts=2 sw=2 sts=2 et:
*}

<script type="text/javascript">
  var txt_delete_selected_contentsections_warning = "{$lng.txt_delete_selected_contentsections_warning|escape:javascript|strip_tags}";
</script>

{*include file='common/page_title.tpl' title=$lng.lbl_cs_content_sections*}
{capture name=section}
{capture name=block}

{if $contentsections}
<div class="row">
<div class="col-xs-6 left-align">
  {include file="common/navigation_counter.tpl"}
</div>
<div class="col-xs-6">
  {include file="common/navigation.tpl"}
</div>
</div>
{/if}

<form action="index.php?target=cms{if $mode ne ""}&amp;mode={$mode}{/if}" method="post" name="updateContentSectionsForm">

<div class="box">

<input type="hidden" name="action" value="" />
<input type="hidden" name="page" value="{$page|default:"1"}" />

{assign var="page_param" value=""}
{if $page gt 0}{assign var="page_param" value="&amp;page=$page"}{/if}

<input type="hidden" name="cs_type" value="{$cs_type}" />

<table width="100%" cellpadding="2" cellspacing="1" class="table table-striped dataTable vertical-center">
<thead>
  <tr>
    <th align="center">{if $contentsections}<input type='checkbox' class='select_all' class_to_select='cms_item' />{else}&nbsp;{/if}</th>
    <th width="10%">
      {if $contentsections_filter.sort_field eq "service_code"}
        {include file="buttons/sort_pointer.tpl" dir=$contentsections_filter.sort_direction}&nbsp;
      {/if}
      <a href="{$navigation.script}&amp;sort=service_code{if $contentsections_filter.sort_field eq "service_code"}&amp;sort_direction={if $contentsections_filter.sort_direction eq 1}0{else}1{/if}{/if}{$page_param}">{$lng.lbl_cs_service_code}</a>
    </th>
    <th>
      {if $contentsections_filter.sort_field eq "type"}
        {include file="buttons/sort_pointer.tpl" dir=$contentsections_filter.sort_direction}&nbsp;
      {/if}
      <a href="{$navigation.script}&amp;sort=type{if $contentsections_filter.sort_field eq "type"}&amp;sort_direction={if $contentsections_filter.sort_direction eq 1}0{else}1{/if}{/if}{$page_param}">{$lng.lbl_cs_type}</a>
    </th>
    <th>
      {if $contentsections_filter.sort_field eq "name"}
        {include file="buttons/sort_pointer.tpl" dir=$contentsections_filter.sort_direction}&nbsp;
      {/if}
      <a href="{$navigation.script}&amp;sort=name{if $contentsections_filter.sort_field eq "name"}&amp;sort_direction={if $contentsections_filter.sort_direction eq 1}0{else}1{/if}{/if}{$page_param}">{$lng.lbl_cs_title}</a>
    </th>
    <th>
      {if $contentsections_filter.sort_field eq "url"}
        {include file="buttons/sort_pointer.tpl" dir=$contentsections_filter.sort_direction}&nbsp;
      {/if}
      <a href="{$navigation.script}&amp;sort=url{if $contentsections_filter.sort_field eq "url"}&amp;sort_direction={if $contentsections_filter.sort_direction eq 1}0{else}1{/if}{/if}{$page_param}">{$lng.lbl_cs_url}</a>
    </th>
    <th width="3%">
      {if $contentsections_filter.sort_field eq "viewed"}
        {include file="buttons/sort_pointer.tpl" dir=$contentsections_filter.sort_direction}&nbsp;
      {/if}
      <a href="{$navigation.script}&amp;sort=viewed{if $contentsections_filter.sort_field eq "viewed"}&amp;sort_direction={if $contentsections_filter.sort_direction eq 1}0{else}1{/if}{/if}{$page_param}">{$lng.lbl_cs_viewed}</a>
    </th>
    <th width="3%">
      {if $contentsections_filter.sort_field eq "clicked"}
        {include file="buttons/sort_pointer.tpl" dir=$contentsections_filter.sort_direction}&nbsp;
      {/if}
      <a href="{$navigation.script}&amp;sort=clicked{if $contentsections_filter.sort_field eq "clicked"}&amp;sort_direction={if $contentsections_filter.sort_direction eq 1}0{else}1{/if}{/if}{$page_param}">{$lng.lbl_cs_clicked}</a>
    </th>
    <th width="3%" style="width: 90px;">
      {if $contentsections_filter.sort_field eq "orderby"}
        {include file="buttons/sort_pointer.tpl" dir=$contentsections_filter.sort_direction}&nbsp;
      {/if}
      <a href="{$navigation.script}&amp;sort=orderby{if $contentsections_filter.sort_field eq "orderby"}&amp;sort_direction={if $contentsections_filter.sort_direction eq 1}0{else}1{/if}{/if}{$page_param}">{$lng.lbl_sort_by}</a>
    </th>
    <th width="3%">{$lng.lbl_active}</th>
  </tr>
</thead>
  {if $contentsections}
    {foreach from=$contentsections item="contentsection"}
      {assign var="contentsection_id" value=$contentsection.contentsection_id}
      {assign var="edit_contentsection_link" value="index.php?target=cms&amp;mode=update&amp;contentsection_id=`$contentsection_id`"}
      <tr class="{cycle values=',cycle'}">
        <td valign="top"  align="center">
          <input type="checkbox" name="delete_contentsections[{$contentsection_id}]" class="cms_item" />
        </td>
        <td valign="top" style="padding-left:10px;">
          <a href="{$edit_contentsection_link}">{$contentsection.service_code|truncate:30}</a>
        </td>
        <td valign="top" style="padding-left:10px;">
          <a href="{$edit_contentsection_link}">{$contentsection.type|truncate:30}</a>
        </td>
        <td valign="top" style="padding-left:10px;">
          <a href="{$edit_contentsection_link}">{$contentsection.name|truncate:50}</a>
        </td>
        <td valign="top">
          <a href="{$edit_contentsection_link}">{$contentsection.url|truncate:50}</a>
        </td>
        <td valign="top" align="center">
          {$contentsection.viewed|default:"0"}
        </td>
        <td valign="top" align="center">
          {$contentsection.clicked|default:"0"}
         </td>
        <td align="center">
          <input type="text" class="form-control" name="contentsections[{$contentsection_id}][orderby]" size="8" maxlength="10" value="{$contentsection.orderby}"  />
        </td>
        <td valign="top" align="center">
          <input type="checkbox" name="contentsections[{$contentsection_id}][active]"{if $contentsection.active eq "Y"} checked="checked"{/if} />
        </td>
      </tr>
    {/foreach}
  {else}
    <tr>
      <td colspan="10">{$lng.txt_cs_there_are_no_contentsections_found}</td>
    </tr>
  {/if}
</table>

</div>

{if $contentsections}
<div class="row">
<div class="col-xs-6 left-align">
  {include file="common/navigation_counter.tpl"}
</div>
<div class="col-xs-6">
  {include file="common/navigation.tpl"}
</div>
</div>

<div id="sticky_content" class="buttons">
  {include file="admin/buttons/button.tpl" href="javascript: cw_submit_form('updateContentSectionsForm', 'update_contentsections');" button_title=$lng.lbl_update acl=$page_acl style="btn-green push-20 push-5-r"}
  {include file="admin/buttons/button.tpl" href="javascript: if (checkMarks(document.updateContentSectionsForm, new RegExp('delete_contentsections\[[0-9]+\]', 'gi'))) if (confirm(txt_delete_selected_contentsections_warning)) cw_submit_form('updateContentSectionsForm', 'delete_contentsections');" button_title=$lng.lbl_delete_selected|escape acl=$page_acl style="btn-danger push-20 push-5-r"}
  {include file="admin/buttons/button.tpl" href="index.php?target=cms&mode=add" button_title=$lng.lbl_add_new style="btn-green push-20 push-5-r"}
</div>
{/if}

</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_cs_content_sections}
