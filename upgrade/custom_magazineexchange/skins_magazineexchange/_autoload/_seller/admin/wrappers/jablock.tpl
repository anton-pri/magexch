<div class="block">
{if $title}
    <div class="block-header bg-green">
      <h3 style="text-align:center;" class="block-title">{$title}</h3>
    </div>
{/if}
    <div class="jasellerblock-content">
{if $undertitle_text ne ''}<span class="jasellerblock_undertitle">{$undertitle_text}</span>{/if}
      {$content}
    </div>
</div>
