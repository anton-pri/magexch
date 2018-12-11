<li class="section_tab {if $is_checkboxes}check{/if}" id="{$id}" onclick="{$onclick}">
{if $is_checkboxes}
        <input type="checkbox" name="{$name}[{$id}]" value="1"{if $value.$id} checked{/if} />
{/if}
    <a>{$title}</a>
</li>
