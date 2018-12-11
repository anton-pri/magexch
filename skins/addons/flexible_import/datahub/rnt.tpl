{include file="addons/flexible_import/flexible_import_menu.tpl" active="5"}

<style>
{literal}
#found_items {
  width: 97%;
  margin: 20px;
  background-color: Beige;
  border-left: 1px solid black;
  border-top: 1px solid black;
}
#found_items tr {
  /*border: 1px solid black;*/
  height: 35px;
  cursor: pointer;
}

#found_items tr:hover {
  /*background-color: yellow;*/
}

#found_items tr td table tr td {
  text-align: left;
}

#found_items tr td {
  text-align: center;
  border-right: 1px solid black;
  border-bottom: 1px solid black;
  font-size: 13px;
}
#btm_buttons {
  padding: 0px 20px 0px 20px; 
}
#search_alu_list_note {
  color: #9E0B0F;
  font-size: 16px;
  padding: 10px;
}
#not_found_items_alus {
  color: red;
  font-size: 15px;
  padding: 0px 20px 10px 20px;
}
#no_items_found {
  color: red;
  font-size: 15px;
  padding: 10px;
}
#print_koeff {
  padding: 0px 20px 10px 20px;
}
/*
        Pagination mechanism
*/
.RNT_NavigationTitle {
        white-space: nowrap;
        padding-right: 10px;
        font-size: 15px;
}
.RNT_NavigationCell, .RNT_NavigationCellSel {
        text-align: center;
        vertical-align: middle;
        font-size: 15px;
        padding-right: 5px;
}
.RNT_NavigationCell a:link, .RNT_NavigationCell a:visited, .RNT_NavigationCell a:hover, .RNT_NavigationCell a:active {
        text-decoration: underline;
}
.RNT_NavigationCellSel {
        font-weight: bold;
}
.RNT_NavigationArrow {
    vertical-align: middle;
    padding-right: 5px;
}
{/literal}
</style>

<script type="text/javascript">
{literal}
$(document).ready(function() {
    if (typeof scroll2list == 'function') 
        scroll2list();
});
{/literal}
</script>

{capture name=section}
{capture name=block1}
<div>
<form name="alusearch" 	action="index.php?target=datahub_rnt" method="post">
<input type="hidden" name="action" value="search" />
<div id="search_alu_list_note">Please enter Lookup Code(s) search for the products listed in the main hub table. Separate multiple lookup codes with line-break or space characters</div>
<table cellpadding="3" cellspacing="0" border="0" style="width:97%;" >
<tr><td width="50%" valign="top">
<textarea name="search_alu_list" style="width:97%; height: 345px;">{$search_alu}</textarea>
</td><td>
<input type="checkbox" name="live_only" value="1" id="live_only" {if $live_only}checked="checked"{/if} />&nbsp;<label for="live_only">Search in live products only</label> and with images present&nbsp;<select name="with_images_present">
<option value="">no matter</option>
<option value="1" {if $with_images_present eq 1}selected{/if}>yes</option>
<option value="2" {if $with_images_present eq 2}selected{/if}>no</option>
</select>

<br />
<input type="checkbox" name="hidden_only" value="1" id="hidden_only" {if $hidden_only}checked="checked"{/if} />&nbsp;<label for="hidden_only">Search in hidden products only</label>
<br />
<table>
<tr><td colspan="2">Search in:</td></tr>
<tr>
<td nowrap='nowrap'><select name="rating_empty_nonempty"><option value="">empty</option><option value="1" {if $rating_empty_nonempty eq 1}selected{/if}>non-empty</option></select> ratings</td>
<td nowrap='nowrap'><select name="review_empty_nonempty"><option value="">empty</option><option value="1" {if $review_empty_nonempty eq 1}selected{/if}>non-empty</option></select> reviews</td></tr>
<tr><td>
<select multiple="multiple" name="empty_rating_search[]" size="8" style="width:200px"> 
{foreach from=$rating_cols item=colgrp}
{assign var='keyname' value=$colgrp.0}
<option value="{$colgrp.0}" {if $empty_rating_search.$keyname eq '1'}selected="selected"{/if}>{$colgrp.0}</option>
{/foreach}
</select>
</td><td>
<select multiple="multiple" name="empty_review_search[]" size="8" style="width:200px"> 
{foreach from=$rating_cols item=colgrp}
{assign var='keyname' value=$colgrp.1}
<option value="{$colgrp.1}" {if $empty_review_search.$keyname eq '1'}selected="selected"{/if}>{$colgrp.1}</option>
{/foreach}
</select>
</td></tr>
<tr>
<td><label for="ratings_or">OR </label>&nbsp;<input type="radio" id="ratings_or" name="ratings_and_or" value="1" {if $ratings_and_or eq 1 or $ratings_and_or eq ''}checked="checked"{/if}/>&nbsp;&nbsp;&nbsp;&nbsp;
<label for="ratings_and">AND </label>&nbsp;<input type="radio" id="ratings_and" name="ratings_and_or" value="2" {if $ratings_and_or eq 2}checked="checked"{/if}/></td>
<td><label for="reviews_or">OR </label>&nbsp;<input type="radio" id="reviews_or" name="reviews_and_or" value="1" {if $reviews_and_or eq 1 or $reviews_and_or eq ''}checked="checked"{/if}/>&nbsp;&nbsp;&nbsp;&nbsp;
<label for="reviews_and">AND </label>&nbsp;<input type="radio" id="reviews_and" name="reviews_and_or" value="2" {if $reviews_and_or eq 2}checked="checked"{/if}/></td>
</tr>

</table>
Items per page:&nbsp;<br />
<input type="text" name="objects_per_page" value="{if $objects_per_page ne ''}{$objects_per_page}{else}100{/if}" /><br />
<br />
</td></tr></table>
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_submit|default:'Submit' href="javascript:cw_submit_form('alusearch');" style='btn-green'}
<br><br>
</form>
</div>

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block1 title=$lng.lbl_items_search_options|default:'Items search options' inline_style_content="padding-top:0px;"}


{if $smarty.get.mode eq 'no_tags_sel'}
<div id="no_tags_sel_message">You have not selected any tags for print! Please check the boxes in leftmost column and submit form for tags printing.</div>
{/if}

{if $search_is_run}

{capture name=block2}

{if $found_items ne ''}

{if $not_found_items_alus ne ''}
<div id="not_found_items_alus"><span>No items found for Lookup Codes: </span>{foreach from=$not_found_items_alus name=nfialu item=nfi_alu}{$nfi_alu}{if !$smarty.foreach.nfialu.last}, {else}. {/if}{/foreach}</div>
{/if}
<div style="width:100%; overflow-x:scroll;">
<div id="list_top"></div>
{assign var="navigation_script" value=$navigation_script}
{if $total_pages gt 2}
{capture assign="navigation_bar"}
<div>
  <table cellpadding="2" cellspacing="2">
  <tr>
  <td class="RNT_NavigationTitle">Result pages:</td>
  {if $current_super_page gt 1}
    <td class="RNT_NavigationArrow"><a href="{$navigation_script}&amp;page={$start_page-1}"><img src="/skins/addons/flexible_import/datahub/images/nav_arrow_left_2.gif" alt="Prev group pages" /></a></td>
  {/if}
  {section name=page loop=$total_pages start=$start_page}
    {if %page.first%}
      {if $navigation_page gt 1}
         <td class="RNT_NavigationArrow"><a href="{$navigation_script}&amp;page={$navigation_page-1}"><img src="/skins/addons/flexible_import/datahub/images/nav_arrow_left.gif" alt="Prev page" /></a></td>
      {/if} 
    {/if}
    {if %page.index% eq $navigation_page}
       <td class="RNT_NavigationCellSel" title="Current page: #{%page.index%}">{%page.index%}</td>
    {else}
       <td class="RNT_NavigationCell"><a href="{$navigation_script}&amp;page={%page.index%}" title="Page #{%page.index%}">{%page.index%}</a></td>
    {/if}
    {if %page.last%}
      {math equation="pages-1" pages=$total_pages assign="total_pages_minus"}
      {if $navigation_page lt $total_super_pages*$max_nav_pages}
         <td class="RNT_NavigationArrow"><a href="{$navigation_script}&amp;page={$navigation_page+1}"><img src="/skins/addons/flexible_import/datahub/images/nav_arrow_right.gif" alt="Next page" /></a></td>
      {/if}
    {/if}
  {/section}
  {if $current_super_page lt $total_super_pages}
    <td class="RNT_NavigationArrow"><a href="{$navigation_script}&amp;page={$total_pages_minus+1}"><img src="/skins/addons/flexible_import/datahub/images/nav_arrow_right_2.gif" alt="Next group pages" /></a></td>
  {/if}
  </tr>
  </table>
  <p />
</div>
{/capture}
{$navigation_bar}
{/if}

<script language="JavaScript" type="text/javascript">
<!--
var all_tr_elems = [{foreach from=$found_items item=itemvals name=products}{$itemvals.catalog_id}{if !$smarty.foreach.products.last},{/if}{/foreach}];
{literal}
function scroll2list() {
    if (document.getElementById('list_top') != null) {
        self.location.hash='list_top';
    }  
}

function change_all(flag, formname, arr) {
        if (!formname)
                formname = checkboxes_form;
        if (!arr)
                arr = checkboxes;
        if (!document.forms[formname] || arr.length == 0)
                return false;
        for (var x = 0; x < arr.length; x++) {
                if (arr[x] != '' && document.forms[formname].elements[arr[x]] && !document.forms[formname].elements[arr[x]].disabled) {
                        document.forms[formname].elements[arr[x]].checked = flag;
                        if (document.forms[formname].elements[arr[x]].onclick)
                                document.forms[formname].elements[arr[x]].onclick();
                }
        }
}
function checkAll(flag, form, prefix) {
        if (!form)
                return;

        if (prefix)
                var reg = new RegExp("^"+prefix, "");
        for (var i = 0; i < form.elements.length; i++) {
                if (form.elements[i].type == "checkbox" && (!prefix || form.elements[i].name.search(reg) == 0) && !form.elements[i].disabled)
                        form.elements[i].checked = flag;
        }
}

function toggle_ratings(itemid, flag) {
    var elem_tr_id = 'ratings_edit_'+itemid;
    if (document.getElementById(elem_tr_id) != null) {
        if (flag == 'open') {
            document.getElementById(elem_tr_id).style.display = '';
        } else if (flag == 'close') { 
            document.getElementById(elem_tr_id).style.display = 'none';
        } else if (flag == '') {                 
            if (document.getElementById(elem_tr_id).style.display == 'none')
                document.getElementById(elem_tr_id).style.display = '';
            else
                document.getElementById(elem_tr_id).style.display = 'none';    
        }  
    }
}

function expandAll(flag) {
    for (var i = 0; i < all_tr_elems.length; i++) {
        toggle_ratings(all_tr_elems[i], flag); 
    } 
}

{/literal}
-->
</script>

<div>
<div id="expand_all" align="left"><a href="javascript: expandAll('open');">Expand All</a> / <a href="javascript: expandAll('close');">Collapse All</a></div>
</div>

<form name="items_edit" action="index.php?target=datahub_rnt" method="post">
<input type="hidden" name="action" value="save_ratings" />
<input type="hidden" name="sales_tag" value="" />
<table class="table table-striped dataTable vertical-center" width="100%">
{capture assign='rnt_header_footer'}
<thead>
<tr>
<th align='center'>Print <input type='checkbox' class='select_all' class_to_select='print_tag_item' title='Print' /></th>
<th align='center'>Hide <input type='checkbox' class='select_all' class_to_select='hide_tag_item' title='Hide' /></th>
{foreach from=$display_cols item=colval key=colname}
<th align='center' nowrap='nowrap'>
<a href="index.php?target=datahub_rnt&sortby={$colname}{if $sortby_col eq $colname}&sort_dir={if $sort_dir eq 1}0{else}1{/if}{/if}">{$colval}{if $sortby_col eq $colname}  {if $sort_dir eq 1}<img src="/skins/addons/flexible_import/datahub/images/sort_asc.png" alt="Sort Asc" />{else}<img src="/skins/addons/flexible_import/datahub/images/sort_desc.png" alt="Sort Desc" />{/if}{/if}</a>
</th>
{/foreach}
</tr>
</thead>
{/capture}
{$rnt_header_footer}
{foreach from=$found_items item=itemvals}
<tr>
<td><input type="checkbox" name="print_items[{$itemvals.catalog_id}]" value="1" class="print_tag_item" /></td>
<td>
<input type="checkbox" name="hide_items[{$itemvals.catalog_id}]" value="1" {if $itemvals.hiddenID ne ''}checked="checked"{/if} class="hide_tag_item"/>
<input type="hidden" name="process_hide_items[{$itemvals.catalog_id}]" value="1" />
</td>
{foreach from=$display_cols item=colval key=colname}
<td {if $colname ne 'Regular_Price'}onclick="javascript: toggle_ratings('{$itemvals.catalog_id}', '');"{/if}>&nbsp;{if $colname eq 'Regular_Price'}{*include file='common/currency.tpl' value=$itemvals.$colname*}<nobr>$<input type="text" size="6" name="print_items_prices[{$itemvals.catalog_id}]" value="{$itemvals.MSRP|default:$itemvals.$colname|abs_value|formatprice}" /></nobr>{else}{$itemvals.$colname}{/if}&nbsp;</td>
{/foreach}
</tr>
<tr id="ratings_edit_{$itemvals.catalog_id}" style="display:none;"><td colspan="13">
<table width="100%" >
<tr>
{foreach from=$rating_cols key=colgrpid item=colgrp}<td width="25%">
{assign var="keyname" value=$colgrp.0}
{$keyname}:&nbsp;<br />
<input type="text" name="ratings[{$itemvals.catalog_id}][{$keyname}]" style="width:95%" value="{$itemvals.$keyname}"><br />
{assign var="keyname" value=$colgrp.1}
{$keyname}:&nbsp;<br />
<textarea name="ratings[{$itemvals.catalog_id}][{$keyname}]" style="width:95%; height:110px">{$itemvals.$keyname}</textarea>
</td>{if $colgrpid eq 3}</tr><tr>{/if}{/foreach}
</tr>
</table>
 </td></tr>
{/foreach}
{$rnt_header_footer}
</table>
</form>

<div>
<div id="expand_all" align="left"><a href="javascript: expandAll('open');">Expand All</a> / <a href="javascript: expandAll('close');">Collapse All</a></div><br />
</div>

{if $total_pages gt 2}
{$navigation_bar}
{/if}
</div>

<br />
<div id="print_koeff" style="display:none;">
Small tags size coefficients X:<input type="text" name="x_k1" value="{$x_k1}"/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Y:<input type="text" name="y_k1" value="{$y_k1}"/><br />
Large tags size coefficients X:<input type="text" name="x_k2" value="{$x_k2}"/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Y:<input type="text" name="y_k2" value="{$y_k2}"/>
</div>

<div>
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_save_reviews_ratings_changes|default:'Save reviews/rating changes' href="javascript:cw_submit_form('items_edit');" style='btn-green push-15-r'}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_print_small_tags|default:'Print Small Tags' href="javascript: document.items_edit.action.value='print_tag1'; document.items_edit.submit();" style='btn-green push-15-r'}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_print_large_tags|default:'Print Large Tags' href="javascript: document.items_edit.action.value='print_tag2'; document.items_edit.submit();" style='btn-green push-15-r'}
<div class="btn-group dropup">
<a class="btn btn-green push-15-r dropdown-toggle" data-toggle="dropdown" href="#">
   Print Sales Tag 
    <span class="caret"></span>
  </a>
  <ul class="dropdown-menu">
    <li><a href="javascript:document.items_edit.sales_tag.value='sales'; cw_submit_form('items_edit','print_tag1');">Print Small Sales Tag</a></li>
    <li><a href="javascript:document.items_edit.sales_tag.value='sales'; cw_submit_form('items_edit','print_tag2');">Print Large Sales Tag</a></li>
  </ul>
</div>
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_hide_display_selected|default:'Hide/display selected' href="javascript:document.items_edit.action.value='hide_show'; document.items_edit.submit();" style='btn-green push-15-r'}
</div>
<br>
{else}
{if $search_alu ne ''}<div id="no_items_found">No Items Found for Alternate Lookup Codes: {$search_alu}</div>
{/if}

{/if}

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block2 title=$lng.lbl_items_search_results|default:'Items search results' inline_style_content="padding-top:0px;"}

{/if}
{/capture}

{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_print_tags|default:'Print Tags'}

{if $items2print_1 eq 'Y' || $items2print_2 eq 'Y'}
<script type="text/javascript">
var printwindow = window.open("index.php?target=datahub_rnt_tag_print&type={if $items2print_1 eq 'Y'}print1{elseif $items2print_2 eq 'Y'}print2{/if}&sales_tag={$sales_tag}", "PrintTags");
printwindow.focus();
</script>
{/if}
