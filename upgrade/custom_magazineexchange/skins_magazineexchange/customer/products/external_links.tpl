{tunnel func='magexch_get_attribute_value' via='cw_call' param1='P' param2=$product.product_id param3='magexch_product_external_links_off' assign='magexch_product_external_links_off'}
{if $magexch_product_external_links_off ne 'Y'}
  <div class="sell_table">
{$lng.txt_digital_editions_other_retailers}

  <table border="0" cellspacing="0" cellpadding="2" width="100%" class="SellerTable">
    <tbody>
      <tr class="ProductTableHead">
          <td align="center" width="90" style="width: 90px;">{$lng.lbl_price}</td>
          <td align="center" width="90" style="width: 90px;">Digital Format</td>
          <td align="center" width="130" style="width: 130px;">{$lng.lbl_seller}</td>
          <td align="center" width="100" style="width: 100px;">{$lng.lbl_free_preview}</td>
      <td align="center" width="100" style="width: 100px;">Comments</td>
          <td align="center" width="295" style="width: 295px;">{$lng.lbl_ready_to_buy}</td>
      </tr>

      {if $external_links}
      {foreach from=$external_links item=link}
      <tr>
          <td align="center"><span class="seller_price">{include file='common/currency.tpl' value=$link.price plain_text_message=true}</span></td>
          <td align="center">{$link.format}</td>
          <td align="center"><a href='{$link.profile}'><span class="seller_name">{$link.seller}</span></a></td>

          <td align="center">&nbsp;</td>
          <td align="center">{$link.comment|escape}</td>
          <td align="center"><a target='_blank' href='{$link.link}' onclick="_gaq.push(['_trackEvent','{$link.category}','{$link.action}','{$link.value}'])"><img src="{$AltImagesDir}/external_link_button.gif" alt="" style="margin-top: 7px;" /></a></td>
      </tr>
      {/foreach}
      {else}
<tr><td colspan="6" align="center"><strong>{$lng.lbl_currently_no_sellers}</strong></td></tr>

      {/if}
    </tbody>
    </table>
  </div>
{/if}
