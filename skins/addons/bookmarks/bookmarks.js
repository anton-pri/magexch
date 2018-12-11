    $(document).ready(function () {
        // Open/close panel
        $('.open_bm').on('click', function(){
          var bm = $('#bookmarks_container');
          //show_bm_panel(!bm.hasClass('open'));
          if (!bm.data('loaded')) load_bm_panel();
          bm.data('loaded',1);
        });

        // Load panel
        function load_bm_panel() {
            aAJAXClickHandler.apply($('#bm_content'));
        }

        function add_bm() {
            document.current_page.url.value = document.URL;
            document.current_page.name.value = document.title;
            submitFormAjax('current_page');
            //show_bm_panel(true);
        }

        function show_bm_panel(show) {
          var bm = $('#bookmarks_container');
          if (show != true) { // Hide
            bm.animate({
              left : '-171px'
            }, 500).removeClass('open');
          } else { // Show
            bm.animate({
              left : 0
            }, 500).addClass('open');
          }
        }

        $('.add_bm').bind('click',add_bm);

        $('#bookmarks_container').on('click','a.control',aAJAXClickHandler);
    });
