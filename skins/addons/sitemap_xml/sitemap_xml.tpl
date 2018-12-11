{*include file='common/page_title.tpl' title=$lng.lbl_sitemap*}
{capture name=section}
{capture name=block}

<p>{$lng.txt_sitemap_xml_note|substitute:path:$cd_path|substitute:key:$config.sitemap_xml.sm_cron_key}</p>


<form method="post" name="sitemap_xmlform">
<input type="hidden" name="mode" value="sitemap_xml_create" />

<div class="box">
<div>
    <table class="table table-striped dataTable vertical-center">
    <thead>
        <tr>
        <th width='20'>&nbsp;</th>
        <th>{$lng.lbl_domains}</th>
        <th width='180'>{$lng.lbl_sitemap_xml}</th>
        </tr>
    </thead>
{if $all_domains}
{foreach from=$all_domains item=domain}
    {assign var="sitemap_link" value="http://`$domain.http_host``$domain.web_dir`/index.php?target=sitemap&filename=`$domain.name`"}
    <tr class="{cycle values='cycle,'}">
    <td>
        <input type="checkbox" name="domains[]" value="{$domain.domain_id}" checked='checked' />
    </td>
    <td>{$domain.name}</td>
    <td align='center'>{if $domain.filetime}<a href='{$sitemap_link}' target='_blank'>{$domain.filetime}</a>{/if}</td>
    </tr>
{/foreach}
    {assign var="sitemap_link" value="`$current_location`/index.php?target=sitemap&filename=index"}
    <tr class="{cycle values='cycle,'}">
    <td>
        &nbsp;
    </td>
    <td><b>Sitemap XML Index</b></td>
    <td align='center'><b><a href='{$sitemap_link}'>View</a></b></td>
    </tr>
{else}
    {assign var="sitemap_link" value="`$current_location`/index.php?target=sitemap&filename=`$config.sitemap_xml.sm_filename`"}
    <tr class="{cycle values='cycle,'}">
    <td>
        &nbsp;
    </td>
    <td><b>{$app_http_host}</b></td>
    <td align='center'><b><a href='{$sitemap_link}'>View</a></b></td>
    </tr>
{/if}
    </table>
 </div>

</div>
<div class="buttons"><input type="submit" value="{$lng.lbl_generate_sitemap|strip_tags:false|escape}" class="btn btn-green push-20" /></div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_sitemap}
