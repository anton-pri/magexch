{capture name=section}

{if $addons.product_tabs && $app_area eq 'admin'}
{if $current_target eq 'products' || $current_target eq 'product_tabs'}
{if $action eq 'tabs_details'}
{include file='addons/product_tabs/admin/tab_details.tpl'}
{else}

{if $smarty.get.mode eq 'add'}
{include file='addons/product_tabs/admin/tab_new.tpl'}
{else}
{include file='addons/product_tabs/admin/tabs_list.tpl'}
{/if}

{/if}
<script type="text/javascript">
  {literal}
  var _tab_content_width = '650';
  var _ifrm_tab_content = document.getElementById('_new_tab_content__WYS' + '___Frame');
  if (_ifrm_tab_content != undefined) {
      _ifrm_tab_content.width = _tab_content_width;
  }
  var _ifrm_tab_content = document.getElementById('_tab_data_content__WYS' + '___Frame');
  if (_ifrm_tab_content != undefined) {
      _ifrm_tab_content.width = _tab_content_width;
  }

$(document).ready(function(){

$('.tabs span ').on('click',function(){
     var tab_id =  $(this).attr('id')
     if(!tab_id) return;
     var tmp_url = window.location.href.split( '#' );
     var url_part = tmp_url[0].split( '&js_tab=' );
     var tab_name = tab_id.replace('tab_','');
     var new_url = url_part[0];
     var current_tab_name = url_part[1];
     if((current_tab_name==tab_name)||(( current_tab_name === undefined)&&(tab_name=='main'))) return;
    if(tab_name != 'main'){
       new_url = new_url+'&js_tab='+tab_name;
    }
        window.history.pushState(tab_id, new_url, new_url);
})

    $(window).on('popstate', function() {
       var tab_name = window.location.href.split( '&js_tab=' )[1];
        if(tab_name !== undefined){
           tab_name = tab_name.split('&')[0];
           var js_tab = 'tab_'+tab_name;
           var tab_contents = 'contents_'+tab_name;
          switchOn(js_tab, tab_contents, tab_name, '');
      }else{
          switchOn('tab_main', 'contents_main', 'main', '');
        }
    });

  var tab_name = window.location.href.split( '&js_tab=' )[1];
  if(tab_name !== undefined){
     tab_name = tab_name.split('&')[0];
     var js_tab = 'tab_'+tab_name;
     $('#tab_main').removeClass('section_tab_selected').addClass('section_tab');
     var tab_contents = 'contents_'+tab_name;
     switchOn(js_tab, tab_contents,  tab_name, '');
  }

})
  {/literal}

</script>
{/if}
{/if}

{/capture}
{if $main eq "product_modify"}
{include file='admin/wrappers/block.tpl' title=$lng.lbl_pt_product_tabs content=$smarty.capture.section}
{else}
{include file='admin/wrappers/section.tpl' title=$lng.lbl_pt_product_tabs content=$smarty.capture.section}
{/if}