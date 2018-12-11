{$dbg}

{assign var=imgpath value="`$SkinDir`/addons/top_menu/images/"}

{assign var=ic_edit value="`$imgpath`edit-enbl-ic.png"}
{assign var=ic_save value="`$imgpath`save2.png"}

{assign var=ic_coll value="`$imgpath`collapse.png"}
{assign var=ic_expd value="`$imgpath`expand.png"}
{assign var=ic_rest value="`$imgpath`restore.png"}
{assign var=ic_del value="`$imgpath`del2.png"}

{assign var=inptxt_style value="width: 96%; font-size: 12px; height: 20px; padding: 2px 4px; margin: 2px 0; line-height: 20px;"}

{include file="addons/top_menu/admin_css_js.tpl"}

{capture name=section}
{capture name=block}

<div class="box" id="maintable">
<table id="" class="table table-striped dataTable vertical-center" width="100%" border="0" style="margin-bottom:20px;">
<thead id="trows">
<tr>
<td colspan="6"><A href="javascript:collapse_all();">{$lng.lbl_collapse_all}</A> &nbsp;/&nbsp; <A href="javascript:expand_all();">{$lng.lbl_expand_all}</A></td>
<TD colspan="4" align="right"><A href="#" onclick="add_new_item('0');">{$lng.lbl_add_cat_in_rl}</A>
</TD></tr>
</thead>

<thead>
<tr>
	<th width="1%"><span class="nowrap">+ / -</span>{$lng.lbl_collapse}</th>
	<th width="1%">{$lng.lbl_drop}</th>
	<th>{$lng.lbl_level}</th>
	<th width="1%">{$lng.lbl_restore}</th>
	<th width="1%">{$lng.lbl_edit}</th>
	<th align="left" style="text-align:left;">{$lng.lbl_category_name}</th>
	<th width="5%">{$lng.lbl_sort}</th>
	<th>{$lng.lbl_show}</th>
	<th>{$lng.lbl_products}</th>
	<th align="left" style="text-align:left;">{$lng.lbl_subcategories}</th>
</tr>
</thead>
<tbody>
{assign var=active_parent value=1}
{foreach from=$top_menu key=mid item=item}
{include file="addons/top_menu/cat.tpl"}
{/foreach}
<tr><TD colspan="10" align="right"><A href="#" onclick="add_new_item('0');">{$lng.lbl_add_cat_in_rl}</A></TD></tr>
</tbody>
</table>
<div class="buttons">
<A href="javascript:refuse_changes ();" class="btn btn-green push-20 push-5-r"><span>{$lng.lbl_rld_pg}</span></A>
<A href="#" onclick="add_new_item('0');" class="btn btn-green push-20 push-5-r"><span>{$lng.lbl_add_cat_in_rl}</span></A>
</div>
<div id="before_btns1" style="display:block;">
<p class="alert alert-warning alert-dismissable">* {$lng.txt_SB_w_app_achs}</p></div>
<table id="btns1" style="display:none;">
    <tr>
      <td><a class="btn btn-green" href="javascript:fast_submit_changes();"><span class="button-left">{$lng.lbl_sbm_all_chngs}</span></span></a></td>
      <td>&nbsp;&nbsp;</td>
      <td><a class="btn btn-green" onclick="preview_changes();" href="#"><span class="button-left">{$lng.lbl_prev_bef_sbm}</span></span></a></td>
    </tr>
</table>
</div>


<div class="box" id="previewtable" style="display:none;">
<div id="addnewitem" style="display:none;">
<p>{$lng.txt_new_cat_spc_title}:</p>
<table class="table table-striped dataTable vertical-center" width="100%" id="insert_new_item" style="margin-bottom:20px;">
<thead>
<tr>
	<th width="1%">{$lng.lbl_add_to}</th>
	<th>{$lng.lbl_category_name}</th>
	<th>Link</th>
	<th width="1%">{$lng.lbl_sort}</th>
	<th width="1%">{$lng.lbl_show}</th>
	<th width="1%">{$lng.lbl_delete}</th>
</tr>
</thead>

{section name=indx loop=15}{assign var="j" value=$smarty.section.indx.iteration}
<tr id="a{$j}" style="display:none;">
	<td>
	<span id="a{$j}-cont-sel"></span>
	</td>
	<td><input type="text" class="form-control" style="{*$inptxt_style*}" name="title" value="" id="a{$j}-title" /></td>
	<td><input type="text" class="form-control" style="{*$inptxt_style*}" name="link" value="" id="a{$j}-link" /></td>
	<td>
	<SELECT class="selectBox-tiny form-control" id="a{$j}-pos" style="width: auto;">
		{section name=ind loop=26}{assign var="i" value=$smarty.section.ind.iteration-1}
		<option value="{$i}"{if $i eq 0} selected{/if}>{$i}</option>
		{/section}
	</SELECT>
	</td>
	<td align="center"><input type="checkbox" id="a{$j}-active" /></td>
	<td><A href="javascript:erase_added({$j});"><IMG src="{$ic_del}" align="left" border="0"></A></td>
</tr>
{/section}
<tr><td colspan="6" id="anew"><A href="javascript:anew_show_next();" class="btn btn-green" >{$lng.lbl_add_new}</A></td></tr>
</table>
</div>

<div id="removed" style="display:none;">
<p>{$lng.txt_removed_cat_prev_title}:</p>
<table class="header category" width="100%" id="remove_table" style="margin-bottom:20px;">
<tr>
	<th width="1%"></th>
	<th align="left" style="text-align:left;">{$lng.lbl_category_name}</th>
</tr>
</table>
</div>


<div id="changes" style="display:none;">
<p>{$lng.txt_chngs_prev_title}:</p>
<table class="table table-striped dataTable vertical-center" width="100%" id="pre_update_table">
<tr>
	<th width="1%"></th>
	<th align="left" style="text-align:left;">{$lng.lbl_category_name}</th>
	<th width="5%">{$lng.lbl_sort}</th>
	<th width="1%">{$lng.lbl_show}</th>
</tr>
</table>
</div>


<table id="btns2" style="margin-top:15px;">
    <tr>
      <td><a class="btn btn-green push-5-r push-20" href="javascript:submit_changes();"><span class="button-left">{$lng.lbl_sbm_all_chngs}</span></span></a></td>
      <td>&nbsp;&nbsp;</td>
      <td><a class="btn btn-green push-20" href="javascript:refuse_changes();"><span class="button-left">{$lng.lbl_refuse_all_ch}</span></span></a></td>
    </tr>
</table>

<div id="nothingtosubmit" style="display:none;">
<p class="msgbx">{$lng.txt_nth_to_sbm}</p>
<p><A href="javascript:refuse_changes();">{$lng.lbl_rld_pg}</A></p>
</div>

{* <p><A href="javascript:debug_changes();">Debug</A></p> *}
</div>



{* $top_menu|@debug_print_var *}


<form id="update" action="index.php" method="POST">
<INPUT type="hidden" name="area" value="admin">
<INPUT type="hidden" name="target" value="top_menu">
<INPUT type="hidden" id="mode" name="mode" value="">
<INPUT type="hidden" id="update_data" name="update_data" value="">
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_top_menu}
