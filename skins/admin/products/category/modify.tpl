{if $categories && $ge_id}
{include file='common/navigation.tpl'}

{capture name=section}
<table class="header" width="100%">
<tr>
    <th>{$lng.lbl_category}</th>
</tr>
{foreach from=$categories item=v}
<tr{cycle name="ge" values=', class="cycle"'}>
    <td>
<a href="index.php?target=categories&mode=edit&cat={$v.obj_id}{if $section ne 'main'}&amp;section={$section}{/if}&amp;ge_id={$ge_id}"{if $cat eq $v.obj_id} class="selected"{/if}>{$v.category|escape}</a>
    </td>
</tr>
{/foreach}
</table>
{/capture}
{include file='admin/wrappers/section.tpl' content=$smarty.capture.section title=$lng.lbl_categories}
{/if}


{capture name=section2}
{capture name=block}
<div>


{if !$current_category.status}
<div class="ErrorMessage">{$lng.txt_category_disabled}</div>
{/if}

{include file='main/select/edit_lng.tpl' script="index.php?target=`$current_target`&mode=edit&cat=`$cat`&js_tab=category_lng"}

<form  class="form-horizontal " name="category_form" id="category_form" action="index.php?target={$current_target}" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="{$mode}" />
<input type="hidden" name="ge_id" value="{$ge_id}" />
<input type="hidden" name="action" value="update" />
<input type="hidden" name="cat" value="{$cat}" />
      {include file='common/subheader.tpl' title=$lng.lbl_category_icon}

<div class="form-group">

	<label class="col-xs-12">
        {if $ge_id}<input type="checkbox" value="1" name="fields[image]" />{/if}
        {$lng.lbl_category_icon}

       </label>
       <div class="col-xs-12">
        {include file='admin/images/edit.tpl' image=$current_category.image in_type='categories_images_thumb' delete_url="index.php?target=`$current_target`&mode=`$mode`&action=delete_icon&amp;cat=`$cat`" button_name=$lng.lbl_save}
       </div>
</div>
<div class="form-group">
	<label class="col-xs-12">
        {if $ge_id}<input type="checkbox" value="1" name="fields[order_by]" />{/if}
        {$lng.lbl_position}
      </label>
      <div class="col-xs-12 col-sm-4 col-md-2">
        <input type="text" name="category_update[order_by]" size="5" value="{$current_category.order_by}" class="form-control" />
      </div>
</div>

<div class="form-group">
	<label class='required multilan col-xs-12'>
        {if $ge_id}<input type="checkbox" value="1" name="fields[category]" />{/if}
        {$lng.lbl_category}  
       </label>
       <div class="col-xs-12 col-md-6">
         <input type="text" name="category_update[category]" maxlength="255" size="65" value="{$current_category.category|escape:"html"}" class="form-control required"/>
       </div>
</div>

<div class="form-group">
	<label class='multilan col-xs-12'>
        {if $ge_id}<input type="checkbox" value="1" name="fields[description]" />{/if}
        {$lng.lbl_description}
        
       </label>
       <div class="col-xs-12 col-md-6">
         {include file='main/textarea.tpl' name='category_update[description]' cols=65 rows=15 data=$current_category.description init_mode='exact'}
       </div>
</div>

<div class="form-group">
	<label class="col-xs-12">
        {if $ge_id}<input type="checkbox" value="1" name="fields[membership_ids]" />{/if}
        {$lng.lbl_membership}
       </label>
       <div class="col-xs-12 col-md-6">
         {include file='admin/select/membership.tpl' value=$current_category.membership_ids name='category_update[membership_ids][]' multiple=5}
       </div>
</div>

<div class="form-group">
	<label class="col-xs-12">
        {if $ge_id}<input type="checkbox" value="1" name="fields[status]" />{/if}
        {$lng.lbl_availability}
       </label>
       <div class="col-xs-12 col-md-6">
         {include file='admin/select/availability.tpl' name='category_update[status]' value=$current_category.status}
       </div>
</div>

<div class="form-group">
    <label class="col-xs-12">
        {if $ge_id}<input type="checkbox" value="1" name="fields[attribute_class_ids]" />{/if}
        {$lng.lbl_attribute_class}
    </label>
    <div class="col-xs-12 col-md-6">
      {include file='admin/select/attribute_classes.tpl' name='category_update[attribute_class_ids][]' values=$current_category.attribute_class_ids multiple=5 replicate_attribute_classes="Y"}
    </div>
</div>


<div class="form-group">
    <label class="col-xs-12">
        {if $ge_id}<input type="checkbox" value="1" name="fields[featured]" />{/if}
        {$lng.lbl_featured_category}
    </label>
    <div class="col-xs-6 col-sm-2 col-md-1">
      {include file='admin/select/yes_no.tpl' name='category_update[featured]' value=$current_category.featured}
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12">
        {if $ge_id}<input type="checkbox" value="1" name="fields[short_list]" />{/if}
        {$lng.lbl_short_list_category}
    </label>
    <div class="col-xs-6 col-sm-2 col-md-1"> 
      {include file='admin/select/yes_no.tpl' name='category_update[short_list]' value=$current_category.short_list}
    </div>
</div>

{include file='admin/attributes/object_modify.tpl'}

</div>


<div id="sticky_content">
  {if !$ge_id || $cw_group_edit_count eq 1}
  <script type="text/javascript">
  var txt_category_clone_confirmation = '{$lng.txt_category_clone_confirmation}';
  var txt_please_enter_cloned_category_name = '{$lng.txt_please_enter_cloned_category_name}';
  {literal}
    function submit_clone_category() {
      if ($('#cloned_name').val().length === 0) { 
        alert(txt_please_enter_cloned_category_name); 
        $('#cloned_name').focus();
      } else {
        if (confirm(txt_category_clone_confirmation)) {
          cw_submit_form('category_form', 'clone');
        }  
      }
    }
  {/literal}
  </script>
    {include 
      file='admin/buttons/button.tpl' 
      button_title=$lng.lbl_clone_as|default:'Clone As' 
      href="javascript: submit_clone_category()" 
      acl='__1200'  
      style='btn-green push-20 clone_category'}
    <div class="form-group">
      <div class="col-xs-12 col-md-6" style="padding-left:0">
        <input type="text" id="cloned_name" name="cloned_name" maxlength="255" size="65" value="{$current_category.category|escape:"html"} clone" class="form-control required"/>
      </div>
    </div>
    <br><br>
  {/if}
  {include file='admin/buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('category_form')" acl='__1200'  style='btn-green push-20 save_category'} 
</div>
</form>

{if $mode eq 'edit' && $accl.__1200}
<form name="category_location_form" id="category_location_form" action="index.php?target={$current_target}" method="post">
<input type="hidden" name="mode" value="{$mode}" />
<input type="hidden" name="ge_id" value="{$ge_id}" />
<input type="hidden" name="action" value="update" />
<input type="hidden" name="cat" value="{$cat}" />

<div class="form-horizontal">
{include file='common/subheader.tpl' title=$lng.lbl_category_location_title}

<div class="form-group">
	<label class="col-xs-12">
        {if $ge_id}<input type="checkbox" value="1" name="fields[category_location]" />{/if}
        {$lng.lbl_category_location}
    </label>
    <div class="col-xs-12 col-md-6">
      {include file='admin/select/category.tpl' name='cat_location' value=$current_category.parent_id}
    </div>
</div>

<!-- cw@category_location_select -->

</div>
<div class="button_left_align">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('category_location_form', 'move')" style='btn-green push-20'}
<div class="clear"></div>
</div>
</form>
<!-- cw@category_modify_form_end -->
{/if}
{/capture}
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{if $mode eq 'edit'}
  {include file="admin/wrappers/section.tpl" content=$smarty.capture.section2 title="`$lng.lbl_current_category`: `$current_category.category`"}
{else}
  {include file="admin/wrappers/section.tpl" content=$smarty.capture.section2 title=$lng.lbl_add_category}
{/if}
<script type="text/javascript">
	{literal}
	$(document).ready(function(){
		var category_form = $("#category_form");
		category_form.validate();
	});
	{/literal}
</script>
