{tunnel func='dashboard_display_prepare' via='cw_call' assign='dashboard'}

<div class="content">
  <div class="col-lg-8">
  {foreach from=$dashboard item=dash_section key=ds}
    {if $dash_section.size eq "medium"}
      {include file='addons/dashboard/admin/section.tpl' dash=$dash_section class=$ds}
    {/if}
  {/foreach}
  </div>

  <div class="col-lg-4">
  {foreach from=$dashboard item=dash_section key=ds}
    {if $dash_section.size eq "small"}
      {include file='addons/dashboard/admin/section.tpl' dash=$dash_section class=$ds}
    {/if}
  {/foreach}
  </div>
</div>
