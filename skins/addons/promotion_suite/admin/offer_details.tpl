
<div class="box form-horizontal">

{include file='common/subheader.tpl' title=$lng.lbl_ps_details}

<div class="form-group form-inline">
    <label class='multilan col-xs-12 required'>
        {$lng.lbl_ps_offer_date}
        
    </label>
    <div class="col-xs-12">
    	<div class="form-group">{include file='main/select/date.tpl' name='offer_data[startdate]' value=$offer_data.startdate class='required'}</div>
    	<div class="form-group"> - </div>
    	<div class="form-group">{include file='main/select/date.tpl' name='offer_data[enddate]' value=$offer_data.enddate class='required'}</div>
	</div>
</div>

<div class="form-group">
    <label class='col-xs-12 multilan required'>
        {$lng.lbl_ps_offer_title} 
    </label>
    <div class="col-xs-12">
    	<input type="text" class="form-control" size="50" maxlength="255" name="offer_data[title]" value="{$offer_data.title|default:$lng.lbl_ps_unknown|escape}"{if $read_only} disabled{/if} class='required' />
	</div>
</div>

<div class="form-group">
    <label class='col-xs-12 multilan required'>
        {$lng.lbl_ps_offer_desc} 
    </label>
    <div class="col-xs-12">{include file='main/textarea.tpl' name="offer_data[description]" data="`$offer_data.description`" init_mode='exact' class='required'}</div>
</div>

<div class="form-group">
    <label class="col-xs-12">
        {$lng.lbl_ps_offer_active} <input type="checkbox" name="offer_data[active]" value="1"{if $offer_data.active eq 1} checked{/if} />
    </label>
</div>

<div class="form-group">
    <label class="col-xs-12">
        {$lng.lbl_ps_offer_position}
    </label>
    <div class="col-xs-12 col-md-2"><input type="text" class="form-control" size="6" maxlength="11" name="offer_data[position]" class='micro integer' value="{$offer_data.position|default:0|escape}"{if $read_only} disabled{/if} /></div>
</div>

<div class="form-group">
    <label class="col-xs-12">
        {$lng.lbl_ps_offer_priority}
    </label>
    <div class="col-xs-12 col-md-2"><input type="text" class="form-control" size="6" maxlength="11" name="offer_data[priority]" class='micro integer' value="{$offer_data.priority|default:0|escape}"{if $read_only} disabled{/if} /></div>
</div>

<div class="form-group">
    <label class="col-xs-12">
        {$lng.lbl_ps_times_to_repeat}
    </label>
    <div class="col-xs-6 col-md-2"><input type="text" class="form-control" size="6" maxlength="4" name="offer_data[repeatable]" class='micro integer' value="{$offer_data.repeatable|default:1|escape}"{if $read_only} disabled{/if} /></div>
</div>

<div class="form-group">
    <label class="col-xs-12">
        {$lng.lbl_ps_offer_excl} <input type="checkbox" name="offer_data[exclusive]"value="1"{if $offer_data.exclusive eq 1} checked{/if} />
    </label>
</div>

<div class="form-group">
    <label class="col-xs-12">
        {$lng.lbl_ps_offer_image}
    </label>
    <div class="col-xs-12">
    	{include file='admin/images/edit.tpl' image=$offer_data.image delete_js="cw_submit_form('offer_details', 'delete_image');" button_name=$lng.lbl_browse in_type="ps_offer_images"}
	<div>
</div>

{include file='admin/attributes/object_modify.tpl'}

</div>
