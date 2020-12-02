{capture name=dialog}



<div style="margin-left: 20%;">
<div style="float:left; width:450px;" class="block block-themed animated fadeIn">
<div class="block-header bg-green"><h3 style="text-align: center;" class="block-title">{$lng.lbl_wanted_by_customers}</h3></div>



<div class="col-sm-12" style="padding:10px 0 10px 15px;">{$lng.lbl_wanted_by_customers_note}</div>
<div style="width:450px;" class="jasellerblock-content">

{cms service_code="magazine_wanted_list"}


</div></div><div>
<div style="margin-left: 500px; width:220px;">
<script type="text/javascript" src="https://form.jotform.com/jsform/42977485434973"></script>
</div>
<div style="clear: both;"></div>

{/capture}

{include file="admin/wrappers/section.tpl" title="Items Wanted by Customers" content=$smarty.capture.dialog extra='width="100%"'}

