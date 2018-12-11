{if $attribute.type eq 'staticpage-selector'}
{assign var='link_item_id' value=''}
{if $main eq 'category_modify' && $current_category ne ''}
{assign var='link_item_id' value=$current_category.category_id}
{assign var='link_item_type' value='C'}
{/if}
{tunnel func='cw_cms_get_staticpages' assign='staticpages'}
    <div>
        <select class='{if $attribute.is_required}required{/if}' name="attributes[{$attribute.field}]">
        <option value="0"{if in_array('0', $attribute.values)} selected="selected"{/if}>{$lng.lbl_none}</option>
        {foreach from=$staticpages item=page}
        <option value="{$page.contentsection_id}"{if in_array($page.contentsection_id, $attribute.values)} selected="selected"{/if}>{$page.name} (#{$page.contentsection_id})</option>
        {/foreach}
        </select>
{if $link_item_id ne ''}
        <a href="index.php?target=cms&mode=add&link_item_id={$link_item_id}&link_item_type={$link_item_type}&link_attribute_id={$attribute.attribute_id}&create_type=staticpage" target="_blank">{$lng.lbl_add_new}</a>
{/if}
    </div>
    <div class="clear"></div>
{/if}
