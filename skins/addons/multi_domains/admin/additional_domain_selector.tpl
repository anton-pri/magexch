{if $attribute_data.field}
	<div class="form-group">
		<label class="col-xs-12">{$lng.lbl_domains}</label>
		{tunnel func='cw_md_get_domains' assign='domains'}
		<div class="domain-selector-main col-md-4 col-xs-12">
			<select class="form-control" class='{if $attribute_data.is_required}required{/if}' name="{$attribute_data.field}" multiple>
				<option value="0"{if in_array('0', (array)$attribute_data.values)} selected{/if}>{$lng.lbl_all_domains}</option>
				{foreach from=$domains item=domain}
					<option value="{$domain.domain_id}"{if in_array($domain.domain_id, (array)$attribute_data.values)} selected{/if}>{$domain.name}</option>
				{/foreach}
			</select>
		</div>
	</div>
{/if}
