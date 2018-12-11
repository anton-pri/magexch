{*include file='common/page_title.tpl' title=$lng.lbl_google_base*}
{capture name=section}
{capture name=block}

{$lng.txt_google_base_note|default:'txt_google_base_note'}

<form method="post" name="gps_xmlform">
<input type="hidden" name="mode" value="gb_xml_create" />

<div class="box">
    <table class="table table-striped dataTable vertical-center">
    <thead>
        <tr>
        <th width='20'>&nbsp;</th>
        <th>{$lng.lbl_domains}</th>
        <th width='140' class="text-center">{$lng.lbl_google_base}</th>
        </tr>
    </thead>
{if $all_domains}
    {foreach from=$all_domains item=domain}
        {assign var="gps_link" value="`$current_location`/files/googlebase/`$domain.http_host`.`$config.google_base.gb_filename`"}
        <tr class="{cycle values='cycle,'}">
        <td>
            <input type="radio" name="domain_id" value="{$domain.domain_id}" />
        </td>
        <td>{$domain.name}</td>
        <td align='center'><a href='{$gps_link}' target='_blank'>View</a></td>
        </tr>
    {/foreach}
{else}
        {assign var="gps_link" value="`$current_location`/files/googlebase/`$config.google_base.gb_filename`"}
        <tr class="{cycle values='cycle,'}">
        <td>
            <input type="hidden" name="domain_id" value="0" />
        </td>
        <td>{$app_http_host}</td>
        <td align='center'><a href='{$gps_link}' target='_blank'>View</a></td>
        </tr>

{/if}

    </table>
 </div>

<div class="buttons"><input type="submit" value="Generate" class="btn btn-green push-20" /></div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_google_base}
