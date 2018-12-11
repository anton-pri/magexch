$(function() {

  var sContainer = $('#sticky_content');

  if (sContainer.length <= 0) {
    return;
  }

  // Create layer
  var stickyHTML    = sContainer.html();
  var sticky        = $(document.createElement('div'))
    .attr('id', 'sticky')
    .css('display', 'none')
    .addClass('sticky-inactive');
  
  var stickyForm    = sContainer.parents('form');
  
  var stickyBg      = $(document.createElement('div')).addClass('bg ui-corner-all');
  var stickyContent = $(document.createElement('div')).addClass('content');

  stickyContent.html(sContainer.html());
  sticky.append(stickyBg).append(stickyContent).insertAfter(sContainer);
  
  //sticky.width(sContainer.width());
  function stickyWidth() {
    if ($('#sidebar').width() > 230) {
  	  sticky.width($(window).width());
    } else {
      sticky.width($(window).width() - $('#sidebar').width());
    }
  }
	stickyWidth();
  
  $(window).resize(function() {
    console.log($('#sidebar').width());
    stickyWidth();
  });

  $('input:text, input:password, textarea', stickyForm).bind('keydown', function() {
    enableSticky()
  });

  $('input:radio, input:checkbox, select', stickyForm).bind('change', function() {
    enableSticky()
  });

  function enableSticky() {

    $('#sticky').removeClass('sticky-inactive');
    if ($(window).scrollTop() < sContainer.position().top - $(window).height()) {
      $('#sticky').fadeIn('slow');
    }
  }

  $(window).scroll(function(){

    if (sticky.length <= 0 || sticky.hasClass('sticky-inactive')) {
      return false;
    }

    if (
      $(window).scrollTop() > sContainer.position().top - $(window).height()
      && sticky.not(':hidden')
    ) {
      sticky.fadeOut('slow');
    } else {
      if (sticky.is(':hidden')) {
        sticky.fadeIn('slow');
      }
      
    }
  });

});
