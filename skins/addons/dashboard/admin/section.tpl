{*<div class="dashboard_section {if $dash_section.frame eq 1}section{/if} dashboard_section_{$dash_section.size} {$class}" >*}
<div class="block">
{if $dash_section.header eq 1}
  <div class="block-header">
    <h3 class="block-title">{$dash_section.title}</h3>
  </div>
{/if}
<div class="block-content block-content-full{if $dash_section.size eq "medium"} bg-gray-lighter{/if}" id='dashboard_name_{$ds}'>
{$dash_section.content}  
{if $dash_section.template}
    {include file=$dash_section.template}
{/if}
</div>
</div>
{*</div>*}

