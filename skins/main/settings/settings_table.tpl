{select_config category=$included_tab assign="settings"}

{if $settings}

<div class="box">
<table class="table table-striped dataTable vertical-center" width="100%">
{foreach from=$settings item=set}
{if $set.type ne ''} {* kornev, the hidden elements might be located in the categories - for addon *}
    {if $set.type eq "separator"}
<thead>
<tr {*cycle values=", class='cycle'"*}>
    <th colspan="2">{$set.title}</th>
</tr>
</thead>
    {else}
<tr {cycle values=", class='cycle'"}>
    <td width="40%">{if $set.title_lng}{lng name="opt_`$set.name`"}{else}{$set.title}{/if}:</td>
    <td width="60%">{include file='main/settings/setting.tpl' name="configuration[`$set.name`]" value=$set.value type=$set.type variants=$set.variants auto_submit=$set.auto_submit}</td>
</tr>
    {/if}
{/if}

{if $set.name eq 'use_speed_up_css'}
	<tr>
		<td colspan="2">
			<strong>{$lng.msg_edit_the_file_to_insert|replace:"%file%":"`$cache_htaccess_location`"}:</strong>
			<pre style="width:500px; overflow: auto; padding: 2px 4px; background-color: white;">&lt;FilesMatch "\.(css|js)$"&gt;
	Allow from all
&lt;/FilesMatch&gt;</pre>
		</td>
	</tr>
{/if}
{/foreach}

</table>
</div>

{/if}


{if $included_tab eq 'search'}
<table  class="table table-striped dataTable vertical-center" width="100%" style='margin: -28px 0px 10px 10px ! important;'>

{assign var='curr_group_title' value=-1}
  {tunnel func='cw_config_advanced_search_attributes' via='cw_call' assign='config_advanced_search_attributes_data' param1=0}
  {assign var='config_advanced_search_attributes' value=$config_advanced_search_attributes_data.attributes}
  {if $config_advanced_search_attributes}
    {foreach from=$config_advanced_search_attributes item=set key=attr_id}
      {if $curr_group_title ne $set.addon_name}
      <thead>
        <tr>
          <th colspan="4">{$set.addon_name|default:$set.addon|default:'Common features'} {if $set.addon ne '' && $set.addon_active ne 1 && $set.addon ne 'core'}(addon disabled){/if}</th>
        </tr>
      </thead>
        <tr>
        <td>{$lng.lbl_feature_name|default:'Feature name'}</td>
        <td>{$lng.lbl_enable_text_search|default:'Enable text search'}</td>
        <td>{$lng.lbl_enable_more_search_options|default:'Enable in more search options'}</td>
        <td>{$lng.lbl_orderby}</td>
        </tr>
        {assign var='curr_group_title' value=$set.addon_name}
      {/if}
      <tr {cycle values=", class='cycle'"}>
        <td width="30%" align="left"><b>{$set.name}</b>{if $set.active ne 1} ({$lng.lbl_disabled_attribute|default:'disabled attribute'}){/if}</td>
        <td width="20%" align="left"><input {if !in_array($set.type, array('selectbox','text', 'textarea', 'multiple_selectbox'))}style="display:none;"{/if} type="checkbox" name="adv_search_configuration[{$attr_id}][enabled]" {if $set.enabled_adv_search}checked="checked"{/if} title="{$lng.lbl_enable}" value="1" /></td>
        <td width="30%" align="left"><input {if !in_array($set.type, array('selectbox','multiple_selectbox','decimal', 'integer'))}style="display:none;"{/if} type="checkbox" name="adv_search_configuration[{$attr_id}][enabled_more]" {if $set.enabled_adv_search_more}checked="checked"{/if} title="{$lng.lbl_enable}" value="1"/></td>
        <td width="20%" align="left"><input type="text" name="adv_search_configuration[{$attr_id}][orderby]" value="{$set.orderby_adv_search|default:0}"/></td>
      </tr>
    {/foreach}
  {/if}
</table>
{/if}
