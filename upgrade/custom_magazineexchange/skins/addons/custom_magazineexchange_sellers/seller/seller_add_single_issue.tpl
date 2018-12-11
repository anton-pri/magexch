
{capture name=dialog}







<div style="margin-left: 20%;">
<div style="float:left; width:450px;" class="block block-themed animated fadeIn">
<div class="block-header bg-green"><h3 style="text-align: center;" class="block-title">{$lng.lbl_add_single_issue}</h3></div>
<div class="col-sm-12" style="padding:10px 0 10px 15px;">{$lng.lbl_single_issue_note}</div>
<div style="width:450px;" class="jasellerblock-content">



<iframe allowtransparency="true" src="
https://form.jotform.com/70513863664965?yourUsername={$user_account.email}" frameborder="0" style="width:400px; height:1200px; border:none;" scrolling="no">
	</iframe>






</div></div><div>
<div style="margin-left: 500px;"><img src="/cw/images/Avatar_Single_Issue.gif" width="116" height="413"><br><a target="_blank" href="{$catalogs.customer}/help-centre-selling-trade-services-adding-single-issue.html"><img src="/cw/images/Avatar_Single_Issue_2.gif" width="116" height="377"></a></div>
<div style="clear: both;"></div>











{/capture}
{include file="admin/wrappers/section.tpl" title="Add Single Issue" content=$smarty.capture.dialog extra='width="100%"'}
