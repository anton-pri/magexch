{strip}
    {if $title_breadcrumb}
        {$title_breadcrumb|strip_tags|escape} - {$lng.lbl_area_seller}
    {else}
        {$lng.lbl_area_seller}
    {/if}
{/strip}
