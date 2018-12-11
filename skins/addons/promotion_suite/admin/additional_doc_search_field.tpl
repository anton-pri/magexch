{if $docs_type eq 'O' && $current_target eq 'docs_O'}
{tunnel func='cw_ps_get_offers' param1=false param2=false via='cw_call' assign='offers'} 
    <div class="form-group">
        <label class="col-xs-12">Applied offer</label>
        <div class=" col-xs-12">
            <select name="posted_data[promotion_suite][offer]" class='form-control'>
            <option value=''>{$lng.lbl_please_select}</option>
            {foreach from=$offers item='offer'}
            <option value='{$offer.offer_id}' {if $search_prefilled.promotion_suite.offer eq $offer.offer_id}selected='selected'{/if}>#{$offer.offer_id} {$offer.title|truncate:72}</option>
            {/foreach}
            </select>
        </div>
    </div>
{/if}
