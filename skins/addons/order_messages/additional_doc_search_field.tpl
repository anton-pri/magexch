{if $docs_type eq 'O' && $current_target eq 'docs_O'}
        <div class="form-group">
            <label class="col-xs-12">
<input type="checkbox" name="posted_data[order_messages][unread_messages]"{if $search_prefilled eq "" or $search_prefilled.order_messages.unread_messages} checked="checked"{/if} /> {$lng.lbl_with_unread_messages|default:"With unread messages"}</label>
        </div>
{/if}
