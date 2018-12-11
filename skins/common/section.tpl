

{if $is_dialog}
<section class="dialog{if $additional_class} {$additional_class}{/if}{if $noborder} noborder{/if}{if $sort and $printable ne 'Y'} list-dialog{/if}{if $title and not $noborder} dialog-with-title{/if}" {if $section_id}id="{$section_id}"{/if}>
  <div class="dialog-header"><div class="dialog-header1">
    {if not $noborder}
    <h1><span>{$title}</span></h1>
    {/if}
  </div></div>
  <div class="content">

    {$content}<div class="clear"></div></div>
  <div class="dialog-footer"><div class="dialog-footer1"></div></div>
</section >
{else}
<section class="section{if $style} {$style}{/if}" {if $section_id}id="{$section_id}"{/if}>
    <h2 class="title"{if $title_id} id="{$title_id}"{/if}>{if $hidden}{include file='main/visiblebox_link.tpl' mark=$title|id title=''}{/if}{$title}</h2>
    <div class="content"{if $hidden} style="display: none;" id="{$title|id}"{/if}>{$content}</div>
{if $alt_bottom and $bottom_button_href}
    <div class="bottom">{include file='buttons/button.tpl' button_title=$button_title href=$bottom_button_href style='rma'}</div>
{/if}
</section>
{/if}
