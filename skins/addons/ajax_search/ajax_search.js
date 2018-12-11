$(document).ready(function(){

    $('.search  input[type=text]').keydown(function(e){
       if (e.keyCode == 13) {
            $('.search  input[type=text]').autocomplete('destroy');
            return true;
        }
       if($('.search  input[type=text]').val().length>=autocomplete_min_chars){
        $('html').css('cursor', 'wait');
       }
    })

$('.search  input[type=text]').autocomplete({
    source:function( request, response) {

        $.ajax({
            type: 'get',
            url: "index.php?target=ajax_product_search",
            data: 'search='+encodeURIComponent(request.term),
            dataType: 'json',
            success: function(data) {
                response( $.map( data, function( item ) {
                    return {
                       label:  item.label,
                        value: item.value
                    }}));
            },
            error: function() {
                console.log('Error occured (debug: JS ajax_search)');
            },
            complete:function(){$('html').css('cursor', 'auto'); $("input[name=posted_data\\[substring\\]]").removeClass('search_waiting');}
        })
    },search:function(event, ui){
        $("input[name=posted_data\\[substring\\]]").addClass('search_waiting');
    },select:function(event, ui){
        var search_str = $('<div />').html(ui.item.value).text();

        if (search_str.indexOf('search/') == 0) {
            $("input[name=posted_data\\[substring\\]]").attr('value', $('<div />').html(ui.item.label).text());
            $(location).attr('href', current_location+'/'+search_str);
            return false;
        } else {
          if(search_str!=''){
             $('.search  input[type=text]').attr('value', search_str);
             cw_submit_form('products_search_form');
          }
        }
    },minLength: autocomplete_min_chars, delay: 100

});

    /* this allows us to pass in HTML tags to autocomplete. Without this they get escaped */
    $[ "ui" ][ "autocomplete" ].prototype["_renderItem"] = function( ul, item) {
        return $( "<li></li>" )
            .data( "item.autocomplete", item )
            .append( $( "<a></a>" ).html( item.label ) )
            .appendTo( ul );
    };

});
