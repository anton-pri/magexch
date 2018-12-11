{tunnel func='cw_config_advanced_search_attributes' via='cw_call' assign='pf_search_attributes_data'}
{assign var='pf_search_attributes' value=$pf_search_attributes_data.attributes}
{if $pf_search_attributes_data.count_numeric gt 0}
  {foreach from=$pf_search_attributes item=pf_s_attr key=attr_id}
    {if $pf_s_attr.enabled_adv_search_more && in_array($pf_s_attr.type, array('decimal', 'integer'))}
    <div class="input_field_0">
      <label>{$pf_s_attr.name}: </label>
      <input type="text" size="10" maxlength="15" 
        name="{$prefix}[by_pf_attr_numeric][{$attr_id}][min]" value="{$search_prefilled.by_pf_attr_numeric.$attr_id.min|formatprice}" /> -
      <input type="text" size="10" maxlength="15" 
        name="{$prefix}[by_pf_attr_numeric][{$attr_id}][max]" value="{$search_prefilled.by_pf_attr_numeric.$attr_id.max|formatprice}" />
    </div>
    {/if}
  {/foreach}
{/if}

{if $pf_search_attributes_data.count_multiselect gt 0}

  {foreach from=$pf_search_attributes item=pf_s_attr key=attr_id}
    {if $pf_s_attr.enabled_adv_search_more && in_array($pf_s_attr.type, array('selectbox', 'multiple_selectbox'))}

    {load_head_resource file="jquery/pqselect/pqselect.min.css" type="css"}
    {load_head_resource file="jquery/pqselect/pqselect.min.js" type="js"}

    <script type="text/javascript">
      $(document).ready(function(){ldelim} 
        $("#by_pf_attr_multi_{$attr_id}").pqSelect({ldelim}
          multiplePlaceholder: '{$pf_s_attr.name|escape:javascript}',    
          checkbox: true //adds checkbox to options    
        {rdelim});
      {rdelim});
    </script>

    <div class="input_field_0">
      <label>{$pf_s_attr.name}: </label>
      <select name="{$prefix}[by_pf_attr_multi][{$attr_id}][]" multiple="multiple" id="by_pf_attr_multi_{$attr_id}">
        {foreach from=$pf_s_attr.options item=opt}
          <option value="{$opt.attribute_value_id}" {if $search_prefilled.by_pf_attr_multi.$attr_id ne ''}{if in_array($opt.attribute_value_id, $search_prefilled.by_pf_attr_multi.$attr_id)}selected="selected"{/if}{/if}>{$opt.value}</option>
        {/foreach}
      </select>
    </div>
    {/if}
  {/foreach}
{/if}
