{select_categories category_id=0 current_category_id=$cat assign='top_categories'}
{if $top_categories}
<div class="top_categories">
{foreach from=$top_categories item=c name='speed_bar'}
<a href="{pages_url var="index" cat=$c.category_id}"{if $c.selected} class="selected"{/if}{if $smarty.foreach.speed_bar.last} id="category_{$c.category_id}"{/if}>{$c.category}</a>
{/foreach}
</div>
{/if}
