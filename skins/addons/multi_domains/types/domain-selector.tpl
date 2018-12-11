{if $attribute.type eq 'domain-selector'}
{tunnel func='cw_md_get_domains' assign='domains'}
        <select class='{if $attribute.is_required}required{/if} form-control push-5' name="attributes[{$attribute.field}][]" multiple>
        <option value="0"{if in_array('0', $attribute.values) || (empty($attribute.values) && ($current_domain==0 || $current_domain==-1)) } selected{/if}>{$lng.lbl_all_domains}</option>
        {foreach from=$domains item=domain}
        <option value="{$domain.domain_id}"{if in_array($domain.domain_id, $attribute.values) || (empty($attribute.values) && $current_domain==$domain.domain_id)} selected{/if}>{$domain.name}</option>
        {/foreach}
        </select>
    {* kornev, specially for the categories we add the selectbox, generally it's possible to make the same with another attribute *}
    {if $attribute.item_type eq 'C'}
    <label><input type="checkbox" name="attributes[subcats_distribution]" value="1" /> {$lng.lbl_mdm_attach_to_subcategories}</label>
    <label><input type="checkbox" name="attributes[subproducts_distribution]" value="1" /> {$lng.lbl_mdm_attach_to_products}</label>
    {/if}
{/if}
{if $attribute.type eq 'domain-text'}
{tunnel func='cw_md_get_domains' assign='domains'}
{foreach from=$domains item=domain}
{if in_array($domain.domain_id, $attribute.values)}{$domain.name}{/if}
{/foreach}
{/if}
