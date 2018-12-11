<ul class='address_book list-unstyled' id='address_book'>
<script type="text/javascript">
// {literal}
$(document).ready(cw_address_book_init);
// {/literal}
</script>

{if $addresses}
{foreach from=$addresses item=address}
<li href='index.php?target=user&mode=addresses&action=load&address_id={$address.address_id}' id='address_label_{$address.address_id}' class='{if $address.address_id eq $address_id}active{/if} push-20'>
    <div class='address_label_info'>
    <h3 class="block-title push-10">
    	{if $address.main}{$lng.lbl_billing_address}{/if}
    	{if $address.current}{if $address.main} / {/if}{$lng.lbl_shipping_address}{/if}
    	{if !$address.current and !$address.main}{$lng.lbl_address}{/if}
    </h3>
    </div>
    {include file='main/users/address_label.tpl' class='address_book'}
    <div class='address_book_controls'>
    <a class="btn btn-danger btn-xs" href='index.php?target=user&mode=addresses&action=delete&address_id={$address.address_id}'>Delete</a>
    {if !$address.main}
    	<a class="btn btn-default btn-xs" href='index.php?target=user&mode=addresses&action=set_main&address_id={$address.address_id}'>Set as {$lng.lbl_billing_address}</a>
    {/if}
    {if !$address.current}
    	<a class="btn btn-default btn-xs" href='index.php?target=user&mode=addresses&action=set_current&address_id={$address.address_id}'>Set as {$lng.lbl_shipping_address}</a>
    {/if}
    </div>
</li>
{/foreach}
<li href='index.php?target=user&mode=addresses&action=load&address_id=0&user={$user}' class='new'>
<div class="btn btn-green">Add new</div>
</li>
{/if}
</ul>
