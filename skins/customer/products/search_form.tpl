<div class="input_field_1 search_for_pattern">
    <label>{$lng.lbl_search_for_pattern}</label>
    <input type="text" name="{$prefix}[substring]" value="{$search_prefilled.substring|escape}" />
{if $form_name}
    {include file='buttons/button.tpl' button_title=$lng.lbl_search style='btn' href="javascript: cw_submit_form('`$form_name`');"}
{/if}
</div>
<div class="input_field_0 search_by_words">
    <label>&nbsp;</label>
{if $config.search.allow_search_by_words eq 'Y'}
    <span>
    <input type="radio" name="{$prefix}[including]" value="all"{if $search_prefilled eq '' or $search_prefilled.including eq '' or $search_prefilled.including eq 'all'} checked="checked"{/if} />
    {$lng.lbl_all_word}&nbsp;
    </span>
    <span>
    <input type="radio" name="{$prefix}[including]" value="any"{if $search_prefilled.including eq 'any'} checked="checked"{/if} />
    {$lng.lbl_any_word}&nbsp;
    </span>
    <span>
    <input type="radio" name="{$prefix}[including]" value="phrase"{if $search_prefilled.including eq 'phrase'} checked="checked"{/if} />
    {$lng.lbl_exact_phrase}&nbsp;
    </span>

{/if}
</div>
<div class="input_field_0 search_also_in">
    <label>{$lng.lbl_search_in}</label>
    <div class="labels"><label class="search_title"><input type="checkbox" name="{$prefix}[by_title]"{if $search_prefilled eq '' or $search_prefilled.by_title} checked="checked"{/if} value="1" />
    {$lng.lbl_product_title}&nbsp;</label>
    <label class="search_descr"><input type="checkbox" name="{$prefix}[by_shortdescr]"{if $search_prefilled eq '' or $search_prefilled.by_shortdescr} checked="checked"{/if} value="1" />
    {$lng.lbl_short_description}&nbsp;</label>
    <label class="search_detailed"><input type="checkbox" name="{$prefix}[by_fulldescr]"{if $search_prefilled eq '' or $search_prefilled.by_fulldescr} checked="checked"{/if} value="1" />
    {$lng.lbl_det_description}&nbsp;</label>
    <label class="search_ean"><input type="checkbox" name="{$prefix}[by_ean]"{if $search_prefilled eq '' or $search_prefilled.by_ean} checked="checked"{/if} value="1" />
    {$lng.lbl_eancode}</label>
    <label class="search_sku"><input type="checkbox" name="{$prefix}[by_sku]"{if $search_prefilled eq '' or $search_prefilled.by_sku} checked="checked"{/if} value="1" />
    {$lng.lbl_sku}</label></div>
    <div class="clear"></div>
</div>

{include file="customer/products/search_form_adv_by_attributes.tpl"}

{if $form_name}
<div class="advs">{include file='main/customer_visiblebox_link.tpl' mark="advanced_search_1" title=$lng.lbl_advanced_search_options}</div>
<div id="advanced_search_1" style="display: none;">
{/if}

{include file='customer/products/search_form_adv.tpl'}

{if $form_name}
    {include file='buttons/button.tpl' button_title=$lng.lbl_search style='btn' href="javascript: cw_submit_form('`$form_name`');"}
    <div class="clear"></div>
</div>
{/if}

{if $search_prefilled.need_advanced_options}
<script type="text/javascript">
visibleBox('advanced_search_1');
</script>
{/if}
