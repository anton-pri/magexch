
function init_webmaster_tool() {
    
    var href = $(this).attr('href');
    var type = $(this).attr('type');
    var key = $(this).attr('key');
    var tool = $('<span href="index.php?target=webmaster&type='+type+'&key='+key+'"></span>');
    tool.attr('id','wm_'+type+'_'+key);
    tool.addClass('ajax');
    tool.addClass('webmaster_modify');
    tool.addClass('webmaster_tool');

    
   // tool.css('position','absolute');
    //tool.css({left: $(this).position().left, top: $(this).position().top});
    tool.insertAfter($(this));
   
    //console.log(key,$(this).offset());
};

if (webmaster_status) {
    $(document).ready(function(){
      $('webmaster').each(init_webmaster_tool);
    });
}

