{if $stop_list}
    {foreach from=$stop_list item=s}
        <div class="estore_container_item">
            <div class="estore_container_delete"><a href="javascript:delete_from_stop_list('{$s.review_id}');">Delete from stop list</a></div>
            {if $s.customer_id ne 0}
                <a href="index.php?target=user_C&mode=modify&user={$s.customer_id}"><b>{$s.email}</b></a> (ID{$s.customer_id}, {$s.real_email})
            {else}
                <b>{$s.email}</b>
            {/if}
            {if $s.remote_ip ne ""}
                [{$s.remote_ip}]
            {/if}
            <br>
            &nbsp;<i><a href="index.php?target=products&mode=details&product_id={$s.product_id}">{$s.product}</a></i><br><br>
            {$s.message}
        </div>
    {/foreach}
{else}
    <div class="estore_container_item">
        {$lng.lbl_no_items_available}
    </div>
{/if}