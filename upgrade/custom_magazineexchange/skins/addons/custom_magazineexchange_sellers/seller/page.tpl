{capture name=section}

{capture name=block}

{* <p>About this page</p> *}

<div class="box">
   
  <form action="index.php?target=cms" method="post" name="editcontentsectionForm" id="editcontentsectionForm">
  <input type="hidden" name="action"    value="update" />
  <input type="hidden" name="page_id" value="{$page.contentsection_id}" />   
 
  <div class="form-horizontal">   
    <div class="form-group">
        <label class="col-xs-12">{$lng.lbl_cs_title}:</label>
       	<div class="col-xs-12">{$page.name}</div>
    </div>
    
    <div class="form-group" id="cs_page_url">
        <label class="col-xs-12">{$lng.lbl_url}:</label>
        {pages_url var="pages" page_id=$page.contentsection_id assign='url'}
        <div class="col-xs-12"><a href="http://{$app_http_host}{$url}" target='_blank'>http://{$app_http_host}{$url}</a></div>
    </div>
    
    <div class="form-group">
    	<label class="col-xs-12">{$lng.lbl_status}:</label>
        <div class="col-xs-12">{if $page.active eq "Y"}Active{else}Disabled{/if}</div>
    </div>

    <div class="form-group" id="cs_html_container">
    	<label class="col-xs-12">{$lng.lbl_content}:</label>
    	<div class="col-xs-12">
          {include file="main/textarea.tpl" name="html_section_content" cols=45 rows=8 class="form-control" data=$page.content width="80%" btn_rows=4 no_wysywig="N"}
		</div>
    </div>
    
  </div>

  <div id="sticky_content" class="buttons">
   {include file="admin/buttons/button.tpl" href="javascript: cw_submit_form('editcontentsectionForm');" button_title=$lng.lbl_cs_save_content_section|escape acl=$page_acl style="btn-green push-20 push-5-r"}
  </div>
      
    </form>
</div>

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title="Promotion Page"}

