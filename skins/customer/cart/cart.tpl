<div class="cart-container">

{include_once file='js/cart_update.tpl'}


	{capture name=dialog}

{if $products ne ""}

<!-- cw@cart_note [ -->
	{if $cart ne ''}
	    {*$lng.txt_cart_header*}
	    {if $addons.estore_gift}<p>{$lng.txt_cart_note}</p><br />{/if}
	{/if}
<!-- cw@cart_note ] -->

{/if}

		{if $products ne ""}


				
				{if $config.Appearance.show_cart_summary eq 'Y'}
			<form action="index.php?target={$current_target}" method="post" name="cartform" id="cartform">
				<input type="hidden" name="action" value="update" />
					{foreach from=$warehouses_cart item=tmp_cart}
						<div class="cart_content">
						    <div class="warehouse">{$tmp_cart.warehouse_customer_id|user_title:'W'}</div>
						    {include file='customer/cart/content.tpl' products=$tmp_cart.products wcart=$tmp_cart use_ajax=false}
						
						    <div id="cart_totals_{$tmp_cart.warehouse_customer_id}" class="margin20">
						    	{include file="customer/cart/totals.tpl" shipping=$tmp_cart.shipping shipping_name="shipping_arr[`$tmp_cart.warehouse_customer_id`]" cart=$tmp_cart carrier_name="carrier_arr[`$tmp_cart.warehouse_customer_id`]" use_ajax=false cart_warehouse=$tmp_cart.warehouse_customer_id}
						    </div>
						</div>
					{/foreach}
					{if $enought_count}
						<hr align="left" noshade size="1">
						<div class="total">{$lng.lbl_grand_total}</div>
						<div id="cart_totals">{include file="customer/cart/totals.tpl" need_shipping=false use_ajax=false}</div>
					{/if}
               </form>
				{else}

					<div class="cart_content">
<!-- cw@cart_content [ -->

                                       <table class="cart_table">
            <form action="index.php?target={$current_target}" method="post" name="cartform" id="cartform">
                <input type="hidden" name="action" value="update" />
					    {include file='customer/cart/content.tpl' use_ajax=false}
            </form>
            <!-- cw@totals [ -->

                                       <tr class="light_gray">
                                         <td colspan="2" class="discount_coupon">
                                             <!-- cw@discount_coupon [ -->
	                                      {if $addons.discount_coupons && $cart.info.coupon eq ''}
			                        {include file='addons/discount_coupons/add_coupon.tpl'}
	                                      {/if}
                                             <!-- cw@discount_coupon ] -->
                                         </td>
					      <td class="totals_td" colspan="5" style="padding: 0;">
<div id="cart_totals">{include file="customer/cart/totals.tpl" need_shipping=true use_ajax=false}</div>
                        </td>
                                       </tr>
            <!-- cw@totals ] -->

                                       </table>
<!-- cw@cart_content ] -->

					</div>

				{/if}
	
<!-- cw@cart_buttons [ -->

				{include file='customer/cart/buttons.tpl'}
                            <div class="clear"></div>
				<div class="checkout_button">{include file='buttons/button.tpl' button_title=$lng.lbl_proceed_to_checkout style='button' href="javascript: cw_submit_form('cartform', 'checkout')"}</div>
	
				<div class="cart_butt">
                                   <div class="button_left_align">
                                      {include file='buttons/button.tpl' button_title=$lng.lbl_continue_shopping href="index.php" style='button'}
                                   </div>
					{if !$from_quote}
						{include file="buttons/update.tpl" href="javascript: cw_submit_form('cartform')"}
					{/if}
					{include file='buttons/button.tpl' button_title=$lng.lbl_clear_cart href="index.php?target=`$current_target`&action=clear_cart"  style='button'}
					<div class="clear"></div>
				</div>
<!-- cw@cart_buttons ] -->

	
		{else}
			<p class="empty">{$lng.txt_your_shopping_cart_is_empty}</p>
			<div class="clear"></div>
		{/if}
	{/capture}
{include file='common/section.tpl' is_dialog=1 content=$smarty.capture.dialog title=$lng.lbl_cart}


{if $cart.coupon_discount eq 0 and $products}

    {if $addons.recommended_products}
		{capture name=prod}
			{include file='addons/recommended_products/recommends.tpl'}
		{/capture}
		{include file='common/section.tpl' is_dialog=1 content=$smarty.capture.prod}
    {/if}

	<div class="coupon">

	</div>

{/if}

</div>
