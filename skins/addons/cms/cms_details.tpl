{*
{if $contentsection_id ne ''}
{include file='common/page_title.tpl' title=$lng.lbl_cs_update_content_section|default:'Update Content Section'}
{else}
{include file='common/page_title.tpl' title=$lng.lbl_cs_add_new_content_section}
{/if}
*}
{capture name=section}
{capture name=block}

{if $content_section.service_code}
<p align='right'>
Use <b>{ldelim}cms service_code="{$content_section.service_code}"{rdelim}</b> in your templates to show this content section.
</p>
{/if}
<div class="clear"></div>
<script type="text/javascript">
{literal}

var cs_disabled_fields = ['cs_image_alt','cs_open_link_in','cs_url','cs_layout', 'cs_image_container', 'cs_html_container','cs_page_url'];
var cs_reenabled_fields = new Array();
cs_reenabled_fields['image'] = ['cs_open_link_in','cs_url','cs_layout','cs_image_container'];
cs_reenabled_fields['html'] = ['cs_open_link_in','cs_url','cs_layout','cs_html_container','cs_image_container'];
cs_reenabled_fields['staticpage'] = ['cs_open_link_in','cs_html_container','cs_page_url','cs_layout']; 
cs_reenabled_fields['staticpopup'] = ['cs_html_container','cs_layout'];

function manageTypeField(type) {
  for (x in cs_disabled_fields) { 
    $('#'+cs_disabled_fields[x]).hide();
  }
  for (z in cs_reenabled_fields[type]) 
    $('#'+cs_reenabled_fields[type][z]).show();
}

 var contentsectionForm = null;
  $(document).ready(function(){
    contentsectionForm = $("#editcontentsectionForm");
    contentsectionForm.validate();
  });

{/literal}
</script>


<form action="{$script}" method="post" name="editcontentsectionForm" id="editcontentsectionForm">
  <input type="hidden" name="action"    value="{$action}" />
  <input type="hidden" name="contentsection_id" value="{$contentsection_id}" />
<div class="form-horizontal">

  <input type="hidden" name="link_item_id" value="{$link_item_id}" />
  <input type="hidden" name="link_attribute_id" value="{$link_attribute_id}" />
  <input type="hidden" name="link_item_type" value="{$link_item_type}" />

    <div class="form-group">
    	<label class="col-xs-12">{$lng.lbl_cs_type}:</label>
    	<div class="col-xs-12">
        <select class="form-control" name="content_section[type]" onChange="javascript: manageTypeField(this.value);">
          <option value="html"{if $content_section.type eq "html" or ($content_section.contentsection_id eq '' and $create_type eq 'html')} selected="selected"{/if}>
            {$lng.lbl_cs_type_embedded_html}
          </option>
          <option value="image"{if $content_section.type eq "image" or ($content_section.contentsection_id eq '' and $create_type eq 'image')} selected="selected"{/if}>
            {$lng.lbl_cs_type_embedded_image}
          </option>
          <option value="staticpage"{if $content_section.type eq "staticpage" or ($content_section.contentsection_id eq '' and $create_type eq 'staticpage')} selected="selected"{/if}>
            {$lng.lbl_cs_type_separate_static_page}
          </option>
          <option value="staticpopup"{if $content_section.type eq "staticpopup" or ($content_section.contentsection_id eq '' and $create_type eq 'staticpopup')} selected="selected"{/if}>
            {$lng.lbl_cs_type_static_content_popup}
          </option>
        </select>
        </div>
    </div>

    {if $contentsection_id}
    <div class="form-group" id="cs_page_url">
        <label class="col-xs-12">{$lng.lbl_url}:</label>
        <div class="col-xs-12">http://{$app_http_host}{pages_url var="pages" page_id=$contentsection_id}</div>
    </div>
    {/if}


    <div class="form-group required">
        <label class="col-xs-12 required">{$lng.lbl_cs_service_code}:</label>
        <div class="col-xs-12"><input type="text" class="required alphanumeric form-control" name="content_section[service_code]" maxlength="64" value="{$content_section.service_code}" {if $usertype eq "C" && $content_section.service_code ne "" && $mode eq "add"} readonly="readonly"{/if} /></div>
    </div>

    <div class="form-group">
        <label class="col-xs-12">{$lng.lbl_cs_title}:</label>
        <div class="col-xs-12">
        	<input class="form-control" type="text" name="content_section[name]" maxlength="64" value="{$content_section.name}" />
        </div>
    </div>

    <div class="form-group" id="cs_open_link_in">
    	<label class="col-xs-12">{$lng.lbl_cs_open_link_in}:</label>
    	<div class="col-xs-12">
        <select name="content_section[target]" class="form-control">
          <option value="_self"{if $content_section.target eq "_self"} selected="selected"{/if}>
            {$lng.lbl_cs_target_same_window}
          </option>
          <option value="_blank"{if $content_section.target eq "_blank"} selected="selected"{/if}>
            {$lng.lbl_cs_target_new_window}
          </option>
        </select>
        </div>
    </div>

    <div class="form-group" id="cs_url">
    	<label class="col-xs-12">{$lng.lbl_cs_url}:</label>
    	<div class="col-xs-12">
        	<input class="form-control" type="text" name="content_section[url]" maxlength="128" value="{$content_section.url}"  />
        </div>
    </div>

    <div class="form-group" id="cs_layout">
    	<label class="col-xs-12">{$lng.lbl_cs_layout_of_multiple_sections}:</label>
    	<div class="col-xs-12">
        <select class="form-control" name="content_section[skin]">
            {foreach from=$skins item=skin}
                <option value="{$skin}"{if $content_section.skin eq $skin} selected="selected"{/if}>{$skin}</option>
            {/foreach}
        </select>
        {if $content_section.service_code ne ""}
          <div class="additional_field">{$lng.lbl_cs_layout_comment|substitute:"service_code":$content_section.service_code}</div>
        {/if}
        </div>
    </div>

    <div class="form-group form-inline">
    	<label class="col-xs-12">{$lng.lbl_date}:</label>
        <div align="left" class="ad_date col-xs-12">
        <div class="form-group">{include file="main/select/date.tpl" name="content_section[start_date]" value=$content_section.start_date}</div>
        <div class="form-group"> - </div>
        <div class="form-group">{include file="main/select/date.tpl" name="content_section[end_date]" value=$content_section.end_date}</div>
        </div>
    </div>


    <div class="form-group">
    	<label class="col-xs-12">{$lng.lbl_restrict_clean_urls}:</label>
    	<div class="col-xs-12">
        <table>
           <tr>
               <td id="content_section_clean_urls_box_0"><input type="text" class="form-control" id="content_section_clean_urls_value_0" name="content_section_clean_urls[0][value]" maxlength="64" value="" /></td>
               <td id="content_section_clean_urls_box_1">&nbsp;</td>
               <td id="content_section_clean_urls_add_button" class=>{include file='main/multirow_add.tpl' mark='content_section_clean_urls'}</td>
           </tr>

        </table>
        </div>
        <script type="text/javascript">

            {foreach from=$clean_urls key=key item=url}

            add_inputset_preset('content_section_clean_urls', document.getElementById('content_section_clean_urls_add_button'), true,
                    [
                    {ldelim}regExp: /content_section_clean_urls_value_{$key+1}/, value: '{$url.value}'{rdelim},
                    ]);

            var check_{$key+1}= document.getElementById('content_section_clean_urls_box_1_{$key}');
            check_{$key+1}.innerHTML ='<img {if $url.valid_url!=0}src="{$ImagesDir}/check_correct.png" title="{$lng.lbl_valid_clean_url}"{else}src="{$ImagesDir}/check_wrong.png" title="{$lng.lbl_clean_url_not_exist}"{/if}  />';

            {/foreach}
        </script>
    </div>

	<div class="form-group">
		<label class="col-xs-12">
			{$lng.lbl_display_on_404_page}: <input type="checkbox" name="content_section[display_on_404]"{if $content_section.display_on_404 eq "Y"} checked="checked"{/if} />
		</label>
	</div>

    <div class="form-group">
    	<label class="col-xs-12">{$lng.lbl_cs_restrict_to_categories}:</label>
    	<div class="col-xs-12">
{if $config.cms.cms_use_multiselect eq 'Y'}
    {include file='admin/select/category_multi.tpl' name='content_section_categories[]' id='content_section_categories' value=$categories title=$lng.lbl_cs_restrict_to_categories}
{else}
    {include file='admin/select/category.tpl' name='content_section_categories[]' value=$categories multiple=1}
{/if}
        </div>
    </div>

    <div class="form-group">
    	<label class="col-xs-12">{$lng.lbl_cs_restrict_to_products}:</label>
       <div class="adcontentsection_prod col-xs-12">
        {include file="addons/cms/products_selector.tpl"}
       </div>
    </div>

    {if $addons.manufacturers && $manufacturers}
    <div class="form-group">
    	<label class="col-xs-12">{$lng.lbl_cs_restrict_to_manufacturers}:</label>
    	<div class="col-xs-8">
          {include file="addons/cms/manufacturers_selector.tpl" name="content_section_manufacturers[]" display_field="manufacturer" multiple="multiple" manufacturers=$manufacturers size="15"}
          <p>{$lng.lbl_hold_ctrl_key}</p>
        </div>
    </div>
    {/if}

    <div class="form-group">
    	<label class="col-xs-12">{$lng.lbl_cs_restrict_to_attributes|default:'Restrict to attributes'}:</label>
    	<div class="col-xs-12">
        	{include file='admin/select/attributes_multiple.tpl' name='content_section_attributes' value=$restricted_attributes no_extra_cmp=1}
        </div>
    </div>

    <div class="form-group">
    	<label class="col-xs-12">{$lng.lbl_sort_by}:</label>
    	<div class="col-xs-6 col-md-2">
        	<input type="text" name="content_section[orderby]" maxlength="5" size="5" value="{$content_section.orderby|default:0}" class="form-control" />
        </div>
    </div>

    <div class="form-group">
    	<label class="col-xs-12">
    		{$lng.lbl_active}: 
    		<input type="checkbox" name="content_section[active]"{if $content_section.active eq "Y" || $content_section.service_code eq ""} checked="checked"{/if} />
		</label>
    </div>

    <div class="form-group" id="cs_image_container">
    	<label class="col-xs-12">{$lng.lbl_image}:</label>
    	<div class="col-xs-12">
          {include file="admin/images/edit.tpl" image=$content_section.image delete_js="cw_submit_form('editcontentsectionForm', 'delete_image');" button_name=$lng.lbl_save in_type="cms_images"}
		</div>
    </div>
    
    <div class="form-group" id="cs_html_container">
    	<label class="col-xs-12">{$lng.lbl_content}:</label>
    	<div class="col-xs-12">
          {include file="main/textarea.tpl" name="content_section_content" cols=45 rows=8 class="form-control" data=$content_section.content width="80%" btn_rows=4 no_wysywig="Y"}
		</div>
    </div>

    <div class="form-group">
    	<label class="col-xs-12">
    		{$lng.lbl_cs_parse_smarty_tags}:
    	    <input type="checkbox" name="content_section[parse_smarty_tags]"{if $content_section.parse_smarty_tags} checked="checked"{/if} />
    	</label>
    </div>

    <div class="form-group object">
    	<div class="col-xs-12">
        	{include file='admin/attributes/object_modify.tpl'}
        </div>
    </div>

</div>

<div id="sticky_content" class="buttons">
    {if $contentsection_id}
        {include file="admin/buttons/button.tpl" href="javascript: cw_submit_form('editcontentsectionForm', 'update_content_section');" button_title=$lng.lbl_cs_save_content_section|escape acl=$page_acl style="btn-green push-20 push-5-r"}
    {else}
        {include file="admin/buttons/button.tpl" href="javascript: cw_submit_form('editcontentsectionForm', 'add_new_content_section');" button_title=$lng.lbl_cs_add_content_section|escape acl=$page_acl style="btn-green push-20 push-5-r"}
    {/if}
</div>

</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}

{if $contentsection_id ne ''}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title="Update Content Section"}
{else}{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_cs_add_new_content_section}
{/if}
<script type="text/javascript">
  manageTypeField(document.editcontentsectionForm.elements['content_section[type]'].value);
</script>
