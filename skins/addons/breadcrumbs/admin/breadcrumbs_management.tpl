{include file='common/subheader.tpl' title=$lng.lbl_breadcrumbs_management}

<form name="breadcrumb_search_form" action="{$target_breadcrumbs_management}" method="post">
    <input type="hidden" name="action" value="search" />

    <div class="box">

        <div class="input_field_1">
            <label>{$lng.lbl_search_for_pattern}:</label>
            <input type="text" name="breadcrumb_data[search][substring]"  value="{$breadcrumb_data.search.substring}" />
        </div>
        <div class="input_field_1">
            <label>{$lng.lbl_area}:</label>
            <select name="breadcrumb_data[search][area]">
                <option value=''>{$lng.lbl_any}</option>
            {foreach from=$breadcrumb_areas item=b_area}
                <option value='{$b_area}' {if $breadcrumb_data.search.area eq $b_area}selected='selected'{/if}>{$b_area}</option>
            {/foreach}
            </select>
        </div>
        <div class="input_field_0">
            <label>{$lng.lbl_additional_options}:</label>

            <label><input type="checkbox" name="breadcrumb_data[search][unknown_links]"{if $breadcrumb_data.search.unknown_links} checked="checked"{/if} />
                {$lng.lbl_unknown_links}&nbsp;</label>

            <label><input type="checkbox" name="breadcrumb_data[search][uniting]"{if $breadcrumb_data.search.uniting} checked="checked"{/if} />
                {$lng.lbl_uniting}&nbsp;</label>
        </div>

    </div>
    {include file='buttons/button.tpl' button_title=$lng.lbl_search href="javascript: cw_submit_form('breadcrumb_search_form');"}
    {include file='buttons/button.tpl' button_title=$lng.lbl_reset href="javascript: cw_submit_form('breadcrumb_search_form', 'reset');"}

</form>

{include file='common/navigation_counter.tpl'}

{if $navigation.total_items gt 9}
    {include file='common/navigation.tpl'}
{/if}

<form action="{$target_breadcrumbs_management}" method="post" name="breadcrumbs_management_form">
    <input type="hidden" name="action" value="process" />
    {assign var="pagestr" value="`$target_breadcrumbs_management`"}
    <div class="box">
    <table class="header" width="100%" cellspacing="5" cellpadding="5">
        <tr>
            <th><input type='checkbox' class='select_all' class_to_select='checked_breadcrumb_item' /></th>
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
                {$lng.lbl_uniting}
            </th>
            <th>
                {$lng.lbl_parent_link}
            </th>
            <th>
                {if $breadcrumb_data.sort_field eq "area"}
                    {include file="buttons/sort_pointer.tpl" dir=$breadcrumb_data.sort_direction}&nbsp;
                    <a href="{$pagestr}&action=process&sort=area&direction={if $breadcrumb_data.sort_direction eq 0}1{else}0{/if}&page={$breadcrumb_data.page}">{$lng.lbl_area}</a>
                {else}
                    <a href="{$pagestr}&action=process&sort=area&direction=0&page={$breadcrumb_data.page}">{$lng.lbl_area}</a>
                {/if}
            </th>
        </tr>
        {if $breadcrumbs}
            {foreach from=$breadcrumbs item=b}
                <tr{cycle values=', class="cycle"'}>
                    <td width="5"><input type="checkbox" class="checked_breadcrumb_item" name="delete_breadcrumb[{$b.breadcrumb_id}]" edit="{$b.breadcrumb_id}" id="{$b.parent_id}" /></td>
                    <td>
                        <a href="{if strpos($b.link, "[[ANY]]") !== false}javascript:void(0);{else}{$current_location}/admin{$b.link|escape}{/if}" title="{$lng.lbl_link}">{$b.link|escape}</a>
                    </td>
                    <td>{$b.title}</td>
                    <td><input type="checkbox" disabled="disabled" {if $b.uniting}checked="checked"{/if}></td>
                    <td>
                        <a href="{if strpos($b.parent_link, "[[ANY]]") !== false}javascript:void(0);{else}{$current_location}/admin{$b.parent_link|escape}{/if}" title="{$lng.lbl_link}">{$b.parent_link|escape}</a>
                    </td>
                    <td>{$b.area}</td>
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
    {include file='buttons/button.tpl' href="javascript:void(0);" onclick="edit_breadcrumb();" button_title=$lng.lbl_edit}
    {include file='buttons/button.tpl' href="javascript:void(0);" onclick="confirm_delete_action();" button_title=$lng.lbl_delete}
    </div>

</form>

{if $navigation.total_items gt 9}
    {include file='common/navigation.tpl'}
{/if}

<div class="box">
    {include file="common/subheader.tpl" title=$lng.lbl_add_new_breadcrumb}
    <form action="{$target_breadcrumbs_management}" method="post" name="breadcrumbs_add_form">
        <input type="hidden" name="action" value="add_breadcrumb" />
        <input type="hidden" name="breadcrumb_new[parent_id]" id="breadcrumb_parent_id" value="0" />
        <input type="hidden" name="breadcrumb_new[breadcrumb_id]" id="breadcrumb_edit_id" value="0" />

        <table class="header" width="100%">
            <tr>
                <th width="30%"><label class="required">{$lng.lbl_link}</label></th>
                <th width="20%"><label class="required">{$lng.lbl_title}</label></th>
                <th width="10%"><label>{$lng.lbl_uniting}</label></th>
                <th width="40%"><label class="required">{$lng.lbl_parent_link}</label></th>
                <th width="40%"><label class="required">{$lng.lbl_area}</label></th>
            </tr>
            <tr valign="top">
                <td><input type="text" size="50" name="breadcrumb_new[link]" id="breadcrumb_new_link" value="" /></td>
                <td><input type="text" size="35" name="breadcrumb_new[title]" id="breadcrumb_new_title" value="" /></td>
                <td><input type="checkbox" name="breadcrumb_new[uniting]" id="breadcrumb_new_uniting" /></td>
                <td>
                    <input type="text" size="45" id="breadcrumb_new_parent_link" value="" readonly="readonly" />
                    {include file='buttons/button.tpl' href="javascript:void(0);" onclick="show_breadcrumb_select_dialog()" button_title=$lng.lbl_edit}
                </td>
                <td><input type="text" size="16" class='short' name="breadcrumb_new[area]" id="breadcrumb_new_area" value="" /></td>
            </tr>
        </table>
        <span class="any_note">{$lng.lbl_note}: {$lng.txt_use_any_as_sequence_of_digits}</span><br><br>

        <div id="breadcrumbs_add_button">
            {include file='buttons/button.tpl' href="javascript:void(0);" onclick="add_save_breadcrumb(0);" button_title=$lng.lbl_add}
        </div>
        <div id="breadcrumbs_save_button" style="display: none;">
            {include file='buttons/button.tpl' href="javascript:void(0);" onclick="add_save_breadcrumb(1);" button_title=$lng.lbl_save}
        </div>
    </form>
</div>

<div id="breadcrumb_management_dialog" title="{$lng.lbl_breadcrumbs_management}"></div>

<script type="text/javascript">
    <!--
    var lbl_please_fill_required_fields = '{$lng.lbl_please_fill_required_fields}';
    var txt_item_delete_confirmation = '{$lng.txt_item_delete_confirmation}';
    var txt_you_can_not_delete_active_link = '{$lng.txt_you_can_not_delete_active_link}';
    {literal}
    function show_breadcrumb_select_dialog() {
        $("#breadcrumb_management_dialog").html('<iframe id="breadcrumb_management_modal_iframe" width="100%" height="100%" marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto" />').dialog("open");
        $("#breadcrumb_management_modal_iframe").attr("src", current_location + "/index.php?target=select_breadcrumb");
        return false;
    }

    function breadcrumb_management_callback() {
        $("#breadcrumb_management_dialog").dialog("close");
    }

    $(document).ready(function() {
        $("#breadcrumb_management_dialog").dialog({
            autoOpen: false,
            modal	: true,
            height	: 700,
            width	: 730
        });
    });

    function edit_breadcrumb() {
        $(".checked_breadcrumb_item").each(function() {

            if ($(this).attr('checked') == "checked") {
                //id
                $("#breadcrumb_edit_id").val($(this).attr('edit'));
                //parent id
                $("#breadcrumb_parent_id").val($(this).attr('id'));
                // link
                var td = $(this).parent().next();
                var link = td.children().text();
                $("#breadcrumb_new_link").val(link);
                // title
                var td = td.next();
                var title = $.trim(td.text());
                $("#breadcrumb_new_title").val(title);
                //uniting
                var td = td.next();
                var uniting = td.children().attr('checked');
                $("#breadcrumb_new_uniting").attr('checked', uniting);
                // parent_link
                var td = td.next();
                var parent_link = td.children().text();
                $("#breadcrumb_new_parent_link").val(parent_link);
                // area
                var td = td.next();
                var area = $.trim(td.text());
                $("#breadcrumb_new_area").val(area);
                
                $('#breadcrumbs_add_button').css('float', 'left');
                $('#breadcrumbs_save_button').css('display', 'block');

                return false;
            }
        });
        return true;
    }

    function add_save_breadcrumb(action) {

        if (
            $("#breadcrumb_new_link").val() == ""
            || $("#breadcrumb_new_title").val() == ""
            || $("#breadcrumb_parent_id").val() == ""
        ) {
            alert(lbl_please_fill_required_fields);
            return false;
        }

        if (action == 1) {
            var form = $('form[name=breadcrumbs_add_form]');
            form.find('input[name=action]').val('edit_breadcrumb');
        }

        cw_submit_form('breadcrumbs_add_form');
    }

    function confirm_delete_action() {

        if ($(".checked_breadcrumb_item:checkbox:checked").length) {

            if (confirm(txt_item_delete_confirmation)) {
                var can_delete = true;
                $(".checked_breadcrumb_item:checkbox:checked").each(function() {

                    if ($(this).attr('id') != -1) {
                        alert(txt_you_can_not_delete_active_link);
                        can_delete = false;
                        return false;
                    }
                });

                if (can_delete) {
                    var form = $('form[name=breadcrumbs_management_form]');
                    form.find('input[name=action]').val('delete');
                    form.submit();
                }
            }
        }

        return false;
    }
    {/literal}
    -->
</script>
