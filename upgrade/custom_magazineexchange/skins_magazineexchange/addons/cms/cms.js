$(document).ready(
  function () {
	  
    $(".ab_slideshow").owlCarousel({
        items : 1,
        itemsDesktop: false,
        itemsDesktopSmall: false,
        itemsTablet: false
    });
    $( ".ab_slideshow" ).show();
 
    $('div.ab_content').click(
      function () {
        var contentsectionId = parseInt($(this).attr('contentsection_id'));
        var href     = $(this).attr('href');
        if (isNaN(contentsectionId)) return;
        $.get(
          'index.php?target=ab_count_click',
          {'contentsection_id': contentsectionId}
        );
        return true;
      }
    );

    var elm = "<div id='cms_staticpopup_dialog' title='Static popup'></div>";
    $('body').append(elm);

    $("#cms_staticpopup_dialog").dialog({
        autoOpen: false,
        modal   : false,
        height  : "auto",
        width   : 376
    });

    $('a[rel="cms_link_staticpopup"]').click(
      function () {
        $('#cms_staticpopup_dialog').dialog('option', 'title', $(this).attr('title'));
        $("#cms_staticpopup_dialog").html('<iframe id="staticpopup_modal_iframe" width="100%" height="95%" marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto" />').dialog("open");
        $("#staticpopup_modal_iframe").attr("src", $(this).attr('href'));
        return false; 
      }
    );

    $('a[rel="cms_link_staticpopup_preload"]').click(
      function () {
        $('#cms_staticpopup_dialog').dialog('option', 'title', $(this).attr('title'));
        $("#cms_staticpopup_dialog").html($($(this).attr('href')).html()).dialog("open");
        return false;
      }
    );

  }
);
