{if $current_target eq 'profit_reports' || $current_target eq 'report_cost_history'}
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_exclude_by_product_name|default:'Exclude by product name'}</label>
    <div class="col-md-4 col-xs-12">
      <input class="form-control" type="text" name="posted_data[products][product_excl]" size="30" value="{$search_prefilled.products.product_excl}" />
    </div>
</div>
{/if}
