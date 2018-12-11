{if $smarty.get.mode neq 'add'}
    <form name="clean_urls_search_form" action="index.php?target={$current_target}" method="post" class="form-horizontal">
        <input type="hidden" name="action" value="search" />

        <div class="box">
            <div class="form-group">
                <label class="col-xs-12">{$lng.lbl_type|capitalize}:</label>
                <div class="col-xs-12">
                <select class="form-control" name="clean_urls_search[type]">
                    <option value="">{$lng.lbl_select}</option>
                    <option value="S" {if $clean_urls_data.search.type eq "S"}selected="selected"{/if}>{$lng.lbl_page}</option>
                    <option value="C" {if $clean_urls_data.search.type eq "C"}selected="selected"{/if}>{$lng.lbl_category}</option>
                    <option value="M" {if $clean_urls_data.search.type eq "M"}selected="selected"{/if}>{$lng.lbl_manufacturer}</option>
                    <option value="P" {if $clean_urls_data.search.type eq "P"}selected="selected"{/if}>{$lng.lbl_product}</option>
                    <option value="O" {if $clean_urls_data.search.type eq "O"}selected="selected"{/if}>{$lng.lbl_owner}</option>
                </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12">{$lng.lbl_search_for_pattern}:</label>
                <div class="col-xs-12">
                	<input class="form-control" type="text" name="clean_urls_search[substring]" value="{$clean_urls_data.search.substring}" />
       			</div>
            </div>
        </div>

        {include file='admin/buttons/button.tpl' button_title=$lng.lbl_search href="javascript: void(0);" onclick="submitFormAjax('clean_urls_search_form');" style="btn-green push-20 push-5-r"}
        {include file='admin/buttons/button.tpl' button_title=$lng.lbl_reset href="javascript: void(0);" onclick="document.forms.clean_urls_search_form.action.value='reset'; submitFormAjax('clean_urls_search_form');" style="btn-danger push-20 push-5-r"}
    </form>



{if $clean_urls_list}
<div class="row">
	<div class="col-sm-12">{include file='common/navigation_counter.tpl'}</div>
{*
    <div class="col-sm-6">{include file='common/navigation.tpl'}</div>
*}
</div>
    <form action="index.php?target={$current_target}" method="post" name="clean_urls_form">
        <input type="hidden" name="action" value="" />

        {assign var="pagestr" value="`$navigation.script`&page=`$navigation.page`&items_per_page=`$navigation.objects_per_page`"}

        <div class="box">
        <table class="table table-striped" width="100%">
        <thead>
            <tr class='sort_order'>
                <th><input type='checkbox' class='select_all' class_to_select='clean_urls_list_item' /></th>
                <th>{if $sort_field eq "value"}{include file="buttons/sort_pointer.tpl" dir=$sort_direction}&nbsp;{/if}<a class="clean_urls_sort" href="{$pagestr|amp}&amp;sort_field=value&amp;sort_direction={$sort_direction}">{$lng.lbl_from_seo_url}</a></th>
                <th>{if $sort_field eq "to_url"}{include file="buttons/sort_pointer.tpl" dir=$sort_direction}&nbsp;{/if}<a class="clean_urls_sort" href="{$pagestr|amp}&amp;sort_field=to_url&amp;sort_direction={$sort_direction}">{$lng.lbl_to_dynamic_url}</a></th>
                <th>{if $sort_field eq "type"}{include file="buttons/sort_pointer.tpl" dir=$sort_direction}&nbsp;{/if}<a class="clean_urls_sort" href="{$pagestr|amp}&amp;sort_field=type&amp;sort_direction={$sort_direction}">{$lng.lbl_type|capitalize}</a></th>
                <th>{if $sort_field eq "entity"}{include file="buttons/sort_pointer.tpl" dir=$sort_direction}&nbsp;{/if}<a class="clean_urls_sort" href="{$pagestr|amp}&amp;sort_field=entity&amp;sort_direction={$sort_direction}">{$lng.lbl_name_of_entity}</a></th>
            </tr>
		</thead>
            {foreach from=$clean_urls_list item=clean_url}
                <tr{cycle values=', class="cycle"'}>
                    <td width="5" height="20">
                        {if $clean_url.item_type eq "O"}
                            <input type="checkbox" name="clean_url_delete_data[{$clean_url.attribute_id}_{$clean_url.item_id}]" class="clean_urls_list_item" />
                        {else}
                            &nbsp;
                        {/if}
                    </td>
                    <td id="{$clean_url.attribute_id}_{$clean_url.item_id}">
                        {if $clean_url.item_type eq "O"}
                            {$clean_url.value}
                        {else}
                            <a href="javascript: void(0);" onclick="editDinamicUrl('{$clean_url.attribute_id}','{$clean_url.item_id}','{$clean_url.item_type}');" title="{$lng.lbl_click_here_to_change}">{$clean_url.value}</a>
                        {/if}
                    </td>
                    <td>{$clean_url.to_url}</td>
                    <td>{$clean_url.type}</td>
                    <td>{$clean_url.entity}</td>
                </tr>
            {/foreach}
        </table>
        </div>

    </form>
<div class="row">
    <div class="col-xs-12">{include file='common/navigation.tpl'}</div>
    
</div>
{/if}

    <div class="buttons">
        {if $clean_urls_list}
        {include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript: void(0);" onclick="confirmDeleteAction('clean_urls_form');" style="btn-danger push-20 push-5-r"}
        {/if}
        {include file='admin/buttons/button.tpl' href="index.php?target=clean_urls_list&mode=add" button_title=$lng.lbl_add_new style="btn-green push-20"}

        
    </div>


{else}

<form action="index.php?target={$current_target}" method="post" name="clean_urls_add_form">
    <input type="hidden" name="action" value="add" />

    <div class="box">
        <table class="header" width="100%">
            <tr>
                <td align="center"><b>{$lng.lbl_from_dynamic_url}</b></td>
                <td align="center"><b>{$lng.lbl_to_static_url}</b></td>
            </tr>
            <tr>
                <td align="center"><input type="text" name="clean_urls_add_data[dinamic_url]" value="" size="50" /></td>
                <td align="center"><input type="text" name="clean_urls_add_data[static_url]" value="" size="50" /></td>
            </tr>
        </table>

        <div class="buttons">{include file='buttons/button.tpl' href="javascript: void(0);" onclick="document.forms.clean_urls_add_form.submit();" button_title=$lng.lbl_add}</div>

    </div>

</form>


{/if}


<script type="text/javascript">
    <!--
    var ImagesDir = '{$ImagesDir}';
    var lbl_cancel = '{$lng.lbl_cancel}';
    var lbl_save = '{$lng.lbl_save}';
    var lbl_click_to_change = '{$lng.lbl_click_here_to_change}';
    var txt_item_delete_confirmation = '{$lng.txt_item_delete_confirmation}';
    var get_ajax_url = 'index.php?target={$current_target}';

    {literal}
    function editDinamicUrl(attribute_id, item_id, item_type) {
        var td = $('#' +  attribute_id + '_' + item_id);
        var current_url = $(td).find('a').text();
        $('body').append('<input type="hidden" id="' + attribute_id + '_' + item_id + '_current" value="' + current_url + '">');
        var content = '<input type="text" value="' + current_url + '">&nbsp;<span id="' + attribute_id + '_' + item_id + '_span">';
        content += '<img src="' + ImagesDir + '/cancel_icon.png" alt="' + lbl_cancel + '" title="' + lbl_cancel + '" style="cursor:pointer;" onclick="cancelEditDinamicUrl('+attribute_id+','+item_id+',\''+item_type+'\')">';
        content += '&nbsp;<img src="' + ImagesDir + '/save_icon.gif" alt="' + lbl_save + '" title="' + lbl_save + '" style="cursor:pointer;" onclick="saveEditedDinamicUrl('+attribute_id+','+item_id+',\''+item_type+'\')"></span>';
        $(td).html(content);
    }

    function cancelEditDinamicUrl(attribute_id, item_id, item_type) {
        var input = $('#' +  attribute_id + '_' + item_id + '_current');
        var current_url = input.val();
        var content = '<a href="javascript: void(0);" onclick="editDinamicUrl(' + attribute_id + ',' + item_id + ',\'' + item_type + '\');" title="' + lbl_click_to_change + '">' + current_url + '</a>';
        $('#' +  attribute_id + '_' + item_id).html(content);
        input.remove();
    }

    function saveEditedDinamicUrl(attribute_id, item_id, item_type) {
        var current_url = $('#' +  attribute_id + '_' + item_id + '_current').val();
        var td = $('#' +  attribute_id + '_' + item_id);
        var new_url = $(td).find('input').val();

        if (current_url != new_url) {
            $('#' +  attribute_id + '_' + item_id + '_span').html('<img src="' + ImagesDir + '/ajax-loader.gif">');
            $.ajax({
                'type': 'post',
                'url': get_ajax_url+'&is_ajax=1',
                'data': {
                    action: 'edit',
                    new_url: new_url,
                    attribute_id: attribute_id,
                    item_id : item_id,
                    item_type : item_type
                },
                'success': function(data) {
                    if (data != null && data != "") {
                        $('#' +  attribute_id + '_' + item_id + '_current').val(data);
                    }
                    cancelEditDinamicUrl(attribute_id, item_id, item_type);
                },
                'error': function() {alert('error');}
            });
        }
        else {
            cancelEditDinamicUrl(attribute_id, item_id, item_type);
        }
    }

    function confirmDeleteAction(form_name) {

        if ($("input[name^='clean_url_delete_data']:checkbox:checked").length) {

            if (confirm(txt_item_delete_confirmation)) {
                var form = $('form[name='+form_name+']');
                form.find('input[name=action]').val('delete');
                form.submit();
            }
        }

        return false;
    }

    $(document).ready(function() {
        //$('#contentscell div#contents_clean_urls_list').attr('blockUI', 'contents_clean_urls_list');
        $('a.page, a.page_arrow').bind('click', aAJAXClickHandler);
        $('a.clean_urls_sort').bind('click', aAJAXClickHandler);
        $('div.navigation_pages select').removeAttr('onchange');
        $('div.navigation_pages select').bind('change', function(){
            var url = $(this).attr('href') + this.value;
            $(this).attr('href', url);
            aAJAXClickHandler.apply(this);
        });
    });
    {/literal}
    -->
</script>
