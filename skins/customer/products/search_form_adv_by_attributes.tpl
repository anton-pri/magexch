{tunnel func='cw_config_advanced_search_attributes' via='cw_call' assign='pf_search_attributes_data'}
{assign var='pf_search_attributes' value=$pf_search_attributes_data.attributes}
{if $pf_search_attributes}
  {if $pf_search_attributes_data.count_text gt 0}
  <div class="input_field_0">
    <label>{$lng.lbl_search_also_in}:</label>
    <div class="labels">
    {foreach from=$pf_search_attributes item=pf_s_attr key=attr_id}
    {if $pf_s_attr.enabled_adv_search && in_array($pf_s_attr.type, array('text','selectbox','textarea','multiple_selectbox'))}
    <label>
      <input type="checkbox" name="{$prefix}[by_pf_s_attr][{$attr_id}]"{if $search_prefilled.by_pf_s_attr.$attr_id} checked="checked"{/if} value="1" />{$pf_s_attr.name}&nbsp;
    </label>
    {/if}
    {/foreach}
    </div>
    <div class="clear"></div>
  </div>
  {/if}
{/if}
