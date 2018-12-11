{if $product_tabs ne '' && $app_area eq 'customer'}
    {foreach from=$product_tabs item=tab}
        {assign var="ct" value="`$ct+1`"}
        <div class="tab_content_not_selected" id="contents_{$ct}">
        {$tab.content}
        </div>
    {/foreach}
{/if}