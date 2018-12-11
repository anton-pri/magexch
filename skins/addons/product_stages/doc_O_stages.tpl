{tunnel func='cw\\product_stages\\cw_product_stages_get_doc_stages_history' via='cw_call' param1=$products assign='stages_history'}
{foreach from=$products item=p}
{assign var='p_item_id' value=$p.item_id}
<h2>#{$p.product_id} {$p.product}</h2>
{if $stages_history.$p_item_id ne ''}
  <h3>Processed stages:</h3><p />
  {assign var='item_stages' value=$stages_history.$p_item_id.processed_stages} 
  {if $item_stages ne ''}
    {foreach from=$item_stages item=is}
      <b>{$is.title}</b> is emailed at {$is.date|date_format:$config.Appearance.date_format}, within {$is.stage_period} working day(s) after the {include file="main/select/doc_status.tpl" status=$is.log_status mode="static"} status is set<p />
    {/foreach}
  {else} 
    <i>No stages processed yet</i> 
    <p />
  {/if}

  <h3>Expected stages:</h3><p />
  {assign var='item_stages' value=$stages_history.$p_item_id.expected_stages} 
  {if $item_stages ne ''}
    {foreach from=$item_stages item=is}
      <b>{$is.title}</b>
      {if $is.triggering_status ne ''}will be emailed at {$is.triggering_status.date_due|date_format:$config.Appearance.date_format}, within {$is.stage_period} working day(s) after the {include file="main/select/doc_status.tpl" status=$is.triggering_status.status mode="static"} status is set
      {else}
         may be emailed within {$is.stage_period} working day(s) after either of the following statuses is set: {foreach from=$is.stage_statuses item=ss name=sts}{include file="main/select/doc_status.tpl" status=$ss mode="static"}{if !$smarty.foreach.sts.last}or {/if}{/foreach}    
      {/if}<br />
    {/foreach}
  {else} 
    <i>No stages are expected</i> 
    <p />
  {/if}

{else}
  <i>no stages defined for this product</i>
  <p />
{/if}
<hr />
{/foreach}
