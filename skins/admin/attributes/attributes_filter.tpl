<div class="form-horizontal">
    <h3 class="block-title push-20">
        {$lng.lbl_filter_attributes}
    </h3>
    <form name="atts_filter_form" id="atts_filter_form" method="post" action="index.php?target={$current_target}&mode=att">
        <input type="hidden" name="action" value="search" />
        <div class="row">
          <div class="form-group col-xs-4">
			<label class="col-xs-12">{$lng.lbl_item_type}:</label>
            {foreach from=$attribute_filter.item_type  item=v}
            <div class="attribute_item col-xs-12">
                 <label><input type="checkbox" name="attribute_filter[item_type][{$v.item_type}]" value="{$v.item_type} " {if isset($search_prefilled.item_type[$v.item_type])} checked="checked"{/if} /> {$v.name}&nbsp;</label>
            </div>
            {/foreach}

          </div>
          <div class="form-group col-xs-4" >
            <label class="col-xs-12">{$lng.lbl_addon}:</label>
           {foreach from=$attribute_filter.addon  item=v}
           <div class="attribute_item col-xs-12">
                <label><input type="checkbox" name="attribute_filter[addon][{$v.addon}]" value="{$v.addon}" {if isset($search_prefilled.addon[$v.addon])} checked="checked"{/if} /> {$v.value}&nbsp;</label>
           </div>
           {/foreach}

          </div>
          <div class="form-group col-xs-4" >
            <label class="col-xs-12">{$lng.lbl_feat_class}:</label></td>
            {foreach from=$attribute_filter.classes  item=v}
            <div class="attribute_item col-xs-12">
              <label><input type="checkbox" name="attribute_filter[class][{$v.attribute_class_id}]" value="{$v.attribute_class_id}" {if isset($search_prefilled.class[$v.attribute_class_id])} checked="checked"{/if} /> {$v.name}&nbsp;</label>
            </div>
            {/foreach}
          </div>
        </div>
    </form>

</div>
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_search href="javascript:cw_submit_form('atts_filter_form');" style="btn-green push-20 push-5-r"}
&nbsp;
{include file="admin/buttons/button.tpl" button_title=$lng.lbl_reset  href="javascript:cw_submit_form('atts_filter_form', 'reset');" style="btn-danger push-20 push-5-r"}
