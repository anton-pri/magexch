{*
<div class="block">
{if $dash_section.header eq 1}
  <div class="block-header">
    <h3 class="block-title" style="color: black;">{$dash_section.title}</h3>
  </div>
{/if}
<div class="block-content block-content-full{if $dash_section.size eq "medium"} bg-gray-lighter{/if}" id='dashboard_name_{$ds}'>
{$dash_section.content}
{if $dash_section.template}
    {include file=$dash_section.template}
{/if}
</div>
</div>
*}

{capture name=block}

<div style="margin-left: auto; margin-right: auto;" class="block block-themed animated fadeIn">
<div class="block-header bg-green"><h3 style="text-align: center;" class="block-title">{$dash_section.title}</h3></div>
<div class="jasellerblock-content"><br>

<div class="block-content block-content-full{if $dash_section.size eq "medium"} bg-gray-lighter{/if}" id='dashboard_name_{$ds}'>
{$dash_section.content}
{if $dash_section.template}
    {include file=$dash_section.template}
{/if}
</div>

</div></div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
<br />
