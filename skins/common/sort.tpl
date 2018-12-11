{strip}
{if $search_prefilled.sort_field eq $field}
    <img src="{$ImagesDir}/r_{if $search_prefilled.sort_direction}bottom{else}top{/if}.gif" class="sorting" alt="" />
{/if}
<a href="{$navigation.script|amp}&amp;sort={$field}&amp;sort_direction={if $search_prefilled.sort_field eq $field}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$title}</a>
{/strip}
