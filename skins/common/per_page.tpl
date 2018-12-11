{assign var="max_nav_pages" value=$config.Appearance.max_nav_pages}
{if $max_nav_pages lt 2}{assign var="max_nav_pages" value=2}{/if}
{assign var='navigation_script' value=$navigation.script|amp}

{if $usertype ne 'C'}
<div class="dataTables_length">
<label>
  <select class="form-control" onchange="javascript: window.location.href='{$navigation_script}{if $navigation.page gt 0}&page={$navigation.page}{/if}&items_per_page='+this.value" href='{$navigation_script}&page={$navigation.page}&items_per_page='>
    {foreach from=$app_config_file.interface.items_per_page item=ipp}
    <option value="{$ipp}"{if $ipp eq $navigation.objects_per_page} selected{/if}>{$ipp}</option>
    {/foreach}
  </select>
</label>
</div>
{/if}
