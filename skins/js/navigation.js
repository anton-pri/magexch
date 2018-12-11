// Init AJAX naviagation
// {literal}
$(document).ready(function() {
    $('a.page, a.page_arrow').bind('click',aAJAXClickHandler);
    $('tr.sort_order a').bind('click',aAJAXClickHandler);
    $('div.navigation_pages select').removeAttr('onchange');
    $('div.navigation_pages select').bind('change',function(){
            var url = $(this).attr('href')+this.value;
            $(this).attr('href',url);
            aAJAXClickHandler.apply(this);
        });
});
// {/literal}
