{include file='mail/mail_header.tpl'}
<br><br>
{assign var="reminders_count" value=$reminders|@count}
{$lng.eml_review_product_text|substitute:"count":$reminders_count}
<br>
{foreach from=$reminders item=r}
   {*<a href="{$r.link}" target="_new">*}{$r.product_name}{*</a>*}<br>
  {if $config.estore_products_review.customer_voting eq "Y"}
    {tunnel func='cw_review_get_product_rates' via='cw_call' assign='product_rates' param1=$r.product_id}
    {if $product_rates ne ''}
      <table>
        {foreach from=$product_rates item=av}
          <tr>
            <td><span>{$av.name}: </span></td>
            <td>&nbsp;</td>
            <td>
              <div attribute-id="{$av.attribute_id}" data-score='{$review.rating[$av.attribute_id]}'>
                <a href="{$r.link}&rating[{$av.attribute_id}]=1" target="_new"><img title="Poor" alt="1" src="http://dev.cartworks.com/saratoga/skins/addons/estore_products_review/star_off.png"></a>&nbsp;
                <a href="{$r.link}&rating[{$av.attribute_id}]=2" target="_new"><img title="Fair" alt="2" src="http://dev.cartworks.com/saratoga/skins/addons/estore_products_review/star_off.png"></a>&nbsp;
                <a href="{$r.link}&rating[{$av.attribute_id}]=3" target="_new"><img title="Good" alt="3" src="http://dev.cartworks.com/saratoga/skins/addons/estore_products_review/star_off.png"></a>&nbsp;
                <a href="{$r.link}&rating[{$av.attribute_id}]=4" target="_new"><img title="Very Good" alt="4" src="http://dev.cartworks.com/saratoga/skins/addons/estore_products_review/star_off.png"></a>&nbsp;
                <a href="{$r.link}&rating[{$av.attribute_id}]=5" target="_new"><img title="Excellent" alt="5" src="http://dev.cartworks.com/saratoga/skins/addons/estore_products_review/star_off.png"></a>
             </div>
             </td>
          </tr>
        {/foreach}
      </table>
    {/if}
  {/if}
{/foreach}

{include file='mail/signature.tpl'}
