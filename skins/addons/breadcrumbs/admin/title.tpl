{tunnel func='cw_get_langvar_by_name' assign='areaname' via='cw_call' param1="lbl_area_`$smarty.const.APP_AREA`" param2='' param3=false param4=true}
{strip}
    {if $title_breadcrumb}
        {$title_breadcrumb|strip_tags|escape} - {$location_breadcrumbs[0].title}
    {else}
	{$location_breadcrumbs[0].title}
    {/if}
{/strip}
