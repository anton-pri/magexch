{include file='common/subheader.tpl' title=$lng.lbl_select_breadcrumb}

<form name="breadcrumb_select_search_form" action="{$target_select_breadcrumb}" method="post">
    <input type="hidden" name="action" value="search" />

    <div class="box">

        <div class="input_field_1">
            <label>{$lng.lbl_search_for_pattern}:</label>
            <input type="text" name="breadcrumb_data[search][substring]"  value="{$breadcrumb_data.search.substring}" />
        </div>

    </div>
    {include file='buttons/button.tpl' button_title=$lng.lbl_search href="javascript: cw_submit_form('breadcrumb_select_search_form');"}
    {include file='buttons/button.tpl' button_title=$lng.lbl_reset href="javascript: cw_submit_form('breadcrumb_select_search_form', 'reset');"}

</form>

{include file='common/navigation_counter.tpl'}

{if $navigation.total_items gt 9}
    {include file='common/navigation.tpl'}
{/if}

<form action="{$target_select_breadcrumb}" method="post" name="breadcrumbs_select_form">
    <input type="hidden" name="action" value="process" />
    {assign var="pagestr" value="`$target_select_breadcrumb`"}
    <div class="box">
    <table class="header" width="100%" cellspacing="5" cellpadding="5">
        <tr>
            <th>&nbsp;</th>
            <th>
                {if $breadcrumb_data.sort_field eq "link"}
                    {include file="buttons/sort_pointer.tpl" dir=$breadcrumb_data.sort_direction}&nbsp;
                    <a href="{$pagestr}&action=process&sort=link&direction={if $breadcrumb_data.sort_direction eq 0}1{else}0{/if}&page={$breadcrumb_data.page}">{$lng.lbl_link}</a>
                {else}
                    <a href="{$pagestr}&action=process&sort=link&direction=0&page={$breadcrumb_data.page}">{$lng.lbl_link}</a>
                {/if}
            </th>
            <th>
                {if $breadcrumb_data.sort_field eq "title"}
                    {include file="buttons/sort_pointer.tpl" dir=$breadcrumb_data.sort_direction}&nbsp;
                    <a href="{$pagestr}&action=process&sort=title&direction={if $breadcrumb_data.sort_direction eq 0}1{else}0{/if}&page={$breadcrumb_data.page}">{$lng.lbl_title}</a>
                {else}
                    <a href="{$pagestr}&action=process&sort=title&direction=0&page={$breadcrumb_data.page}">{$lng.lbl_title}</a>
                {/if}
            </th>
            <th>
                {if $breadcrumb_data.sort_field eq "parent_link"}
                    {include file="buttons/sort_pointer.tpl" dir=$breadcrumb_data.sort_direction}&nbsp;
                    <a href="{$pagestr}&action=process&sort=parent_link&direction={if $breadcrumb_data.sort_direction eq 0}1{else}0{/if}&page={$breadcrumb_data.page}">{$lng.lbl_parent_link}</a>
                {else}
                    <a href="{$pagestr}&action=process&sort=parent_link&direction=0&page={$breadcrumb_data.page}">{$lng.lbl_parent_link}</a>
                {/if}
            </th>
        </tr>
        {if $breadcrumbs}
            {foreach from=$breadcrumbs item=b}
                <tr{cycle values=', class="cycle"'}>
                    <td width="5"><input type="radio" name="select_breadcrumb" id="{$b.breadcrumb_id}" /></td>
                    <td>
                        <a href="{if strpos($b.link, "[[ANY]]") !== false}javascript:void(0);{else}{$current_location}/admin{$b.link|escape}{/if}" title="{$lng.lbl_link}">{$b.link|escape}</a>
                    </td>
                    <td>{$b.title}</td>
                    <td>
                        <a href="{if strpos($b.parent_link, "[[ANY]]") !== false}javascript:void(0);{else}{$current_location}/admin{$b.parent_link|escape}{/if}" title="{$lng.lbl_link}">{$b.parent_link|escape}</a>
                    </td>
                </tr>
            {/foreach}
        {else}
            <tr>
                <td align="center" colspan="4">
                    {$lng.lbl_no_items_available}
                </td>
            </tr>
        {/if}
    </table>
    {include file='buttons/button.tpl' href="javascript:void(0);" onclick="select_breadcrumb()" button_title=$lng.lbl_select}
    </div>

</form>

{if $navigation.total_items gt 9}
    {include file='common/navigation.tpl'}
{/if}

<script type="text/javascript">
    {literal}
    function select_breadcrumb() {

        if ($('input[name=select_breadcrumb]:checked').length) {
            var el = $('input[name=select_breadcrumb]:checked');
            //id
            window.parent.$("#breadcrumb_parent_id").val($(el).attr('id'));
            // link
            var td = $(el).parent().parent().next();
            var link = td.children().text();
            window.parent.$("#breadcrumb_new_parent_link").val(link);

            window.parent.breadcrumb_management_callback();
        }
        return true;
    }
    {/literal}
</script>