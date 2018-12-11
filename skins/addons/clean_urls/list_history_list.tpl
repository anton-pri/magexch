
{if $clean_urls_history_list}

    {*include file='common/page_title.tpl' title=$lng.lbl_history_clean_urls_list*}
    <form action="index.php?target={$current_target}" method="post" name="clean_urls_history_form">
        <input type="hidden" name="mode" value="history" />
        <input type="hidden" name="h_action" value="" />

        {assign var="h_pagestr" value="index.php?target=`$current_target`&mode=history"}

        <div class="box">
        <table class="table table-striped" width="100%">
        <thead>
            <tr class='sort_order'>
                <th><input type='checkbox' class='select_all' class_to_select='clean_urls_history_list_item' /></th>
                <th>{if $h_sort_field eq "url"}{include file="buttons/sort_pointer.tpl" dir=$h_sort_direction}&nbsp;{/if}<a class="clean_urls_history_sort" href="{$h_pagestr|amp}&amp;sort_field=url&amp;sort_direction={$h_sort_direction}">{$lng.lbl_from_dynamic_url}</a></th>
                <th>{if $h_sort_field eq "to_url"}{include file="buttons/sort_pointer.tpl" dir=$h_sort_direction}&nbsp;{/if}<a class="clean_urls_history_sort" href="{$h_pagestr|amp}&amp;sort_field=to_url&amp;sort_direction={$h_sort_direction}">{$lng.lbl_to_static_url}</a></th>
                <th>{if $h_sort_field eq "type"}{include file="buttons/sort_pointer.tpl" dir=$h_sort_direction}&nbsp;{/if}<a class="clean_urls_history_sort" href="{$h_pagestr|amp}&amp;sort_field=type&amp;sort_direction={$h_sort_direction}">{$lng.lbl_type|capitalize}</a></th>
                <th>{if $h_sort_field eq "entity"}{include file="buttons/sort_pointer.tpl" dir=$h_sort_direction}&nbsp;{/if}<a class="clean_urls_history_sort" href="{$h_pagestr|amp}&amp;sort_field=entity&amp;sort_direction={$h_sort_direction}">{$lng.lbl_name_of_entity}</a></th>
            </tr>
		</thead>
            {foreach from=$clean_urls_history_list item=clean_url}
                <tr{cycle values=', class="cycle"'}>
                    <td width="5"><input type="checkbox" name="history_url[{$clean_url.id}]" class="clean_urls_history_list_item" /></td>
                    <td>{$clean_url.url}</a></td>
                    <td>index.php?target={$clean_url.to_url}</a></td>
                    <td>{$clean_url.type}</td>
                    <td>{$clean_url.entity}</td>
                </tr>
            {/foreach}
        </table>
        </div>

        <div class="buttons">{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected onclick="confirmHistoryDeleteAction('clean_urls_history_form');" style="btn-danger push-20"}</div>

    </form>

{/if}

<script type="text/javascript">
    <!--
    var txt_item_delete_confirmation = '{$lng.txt_item_delete_confirmation}';
    {literal}
    function confirmHistoryDeleteAction(form_name) {

        if ($("input[name^='history_url']:checkbox:checked").length) {

            if (confirm(txt_item_delete_confirmation)) {
                var form = $('form[name=' + form_name + ']');
                form.find('input[name=h_action]').val('delete');
                submitFormAjax(form_name);
            }
        }

        return false;
    }

    $(document).ready(function() {
        $('a.clean_urls_history_sort').bind('click', aAJAXClickHandler);
    });
    {/literal}
    -->
</script>
