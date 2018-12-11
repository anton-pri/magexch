{capture name=section}

{capture name=block}

{* <p>About this page</p> *}

<div class="box">

<table width="100%" cellpadding="2" cellspacing="1" class="table table-striped dataTable vertical-center">
<thead>
  <tr>
    <th>
        {$lng.lbl_cs_title}
    </th>
    <th width="3%">{$lng.lbl_active}</th>
    <th width="3%">Show</th>
  </tr>
</thead>
  {if $promopages}
    {foreach from=$promopages item="page"}
     <tr>
        <td><a href="index.php?target=cms&mode=view&page_id={$page.contentsection_id}">{$page.name}</a></td>
        <td>{if $page.active eq 'Y'}Active{else}Disabled{/if}</td>
        <td><a href="{$catalogs.customer}/index.php?target=pages&page_id={$page.contentsection_id}" target="_blank">Preview</a></td>
     </tr>
    {/foreach}
  {else}
    <tr>
      <td colspan="3">{$lng.txt_cs_there_are_no_contentsections_found}</td>
    </tr>
  {/if}
</table>

<p>{$lng.txt_Edit_Promotion_Page_Footer}</p>

</div>

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title="Promotion Pages"}

