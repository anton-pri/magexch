<span class="section_tab {if $is_checkboxes}check{/if}" {if $tab_extra_cls}{$tab_extra_cls} {/if} id="{$id}" onclick="{$onclick}">
{if $is_checkboxes}
        <input type="checkbox" name="{$name}[{$id}]" value="1"{if $value.$id} checked{/if} />
{/if}
    <span>{$title}</span>
</span>
