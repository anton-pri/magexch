<!-- skins_magazineexchange/_autoload/_seller/main/users/address_book.tpl -->

<ul class='address_book' id='address_book'>
<script type="text/javascript">
// {literal}
$(document).ready(cw_address_book_init);
// {/literal}
</script>

{if $addresses}
{foreach from=$addresses item=address}
<li href='index.php?target=user&mode=addresses&action=load&address_id={$address.address_id}' id='address_label_{$address.address_id}' {if $address.address_id eq $address_id}class='active'{/if}>
    <div class='address_label_info'>
    {if $address.main}<span>{$lng.lbl_billing_address}</span>{/if}
    {if $address.current}{if $address.main} / {/if}<span>{$lng.lbl_shipping_address}</span>{/if}
    {if !$address.current and !$address.main}{$lng.lbl_address}{/if}
    </div>
    {include file='main/users/address_label.tpl' class='address_book'}
    <div class='address_book_controls'>
    <a href='index.php?target=user&mode=addresses&action=delete&address_id={$address.address_id}'>Delete</a>
    {if !$address.main} <br> <a href='index.php?target=user&mode=addresses&action=set_main&address_id={$address.address_id}'>Set as {$lng.lbl_billing_address}</a>{/if}
    {if !$address.current} <br> <a href='index.php?target=user&mode=addresses&action=set_current&address_id={$address.address_id}'>Set as {$lng.lbl_shipping_address}</a>{/if}
<br><br>
    </div>
</li>
{/foreach}
<li href='index.php?target=user&mode=addresses&action=load&address_id=0&user={$user}' class='new'>
<div>Add new</div>
</li>
{/if}
</ul>
