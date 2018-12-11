<div class="block">
{if $title}
    <div class="block-header">
      <h3 class="block-title">{$title}</h3>
    </div>
{/if}
    <div class="block-content" {if $inline_style_content ne ''}style="{$inline_style_content}"{/if}>
     {$content}
    </div>
</div>
