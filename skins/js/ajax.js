// Implement various features for work with AJAX

/* Function sends AJAX get request to url. */
/* "blocking" and "callback" are optional, ommit or set to null */
function ajaxGet(url, blocking, callback) {
    blockElements(blocking, true);
    $.ajax({
        type: 'get',
        url: url,
        data: 'is_ajax=1',
        dataType: 'xml',
        success: function(data) {
            var r;
            r = processXMLResponse(data);
            if (callback != null) {
                if (typeof callback == 'function') {
                    r = callback(data);
                } else if (typeof callback == 'string' && typeof window[callback] == 'function') {
                    r = window[callback](data);
                } 
            }
            blockElements(blocking, false);
            return r;
        },
        error: function() {if ( window.console && window.console.log ) console.log('Error occured (debug: JS ajaxGet)');}
    });
};

/*
 * Expects XML with tags named as ID of containers to be updated.
 * Updates all content in containers, evals all tags <script> if exist
 *
 * Example:
    <?xml version="1.0" encoding="utf-8" standalone="yes" ?>
    <xml>
        <div_id action='update'><![CDATA[HTML CONTENT GOES HERE]]></div_id>
        <script><![CDATA[JS CONTENT GOES HERE]]></script>
        <another_id action='after'><![CDATA[HTML CONTENT GOES HERE]]></another_id>
        <script><![CDATA[JS CONTENT GOES HERE]]></script>
    </xml>
*/
function processXMLResponse(xml) {
    $(xml).find('xml').children().each(function(){
        var id = this.tagName.toLowerCase();
        var action = $(this).attr('action');
        if (id == 'script' || action == 'script') {
            eval($(this).text());
        } else {
            if (action == 'update')
                $('#'+id).html($(this).text());
            if (action == 'replace')
                $('#'+id).replaceWith($(this).text());
            if (action == 'remove')
                $('#'+id).remove();
            if (action == 'hide')
                $('#'+id).hide();
            if (action == 'show')
                $('#'+id).show();
            if (action == 'append') {
                $('#'+id).append($(this).text());
            }
            if (action == 'prepend') {
                $('#'+id).prepend($(this).text());
            }
            if (action == 'before') {
                $('#'+id).before($(this).text());
            }
            if (action == 'after') {
                $('#'+id).after($(this).text());
            }
            if (action == 'console' || action=='debug') {
                console.log($(this).text());
            }
            if (action == 'redirect') {
                window.location.href = $(this).text();
            }
            if (action == 'popup') {
                if ($('#'+id).length) {
                    hm(id);
                    $('#'+id).remove();
                }
                $('body').append('<div id="'+id+'" style="display:hidden"></div>');
                $('#'+id).html($(this).text());
                sm(id,null,null,true,$(this).attr('title'));   
            }
            if (action == 'show_popup') {
                sm(id,null,null,true,$(this).attr('title'));   
            }
            if (action == 'hide_popup') {
                hm(id);
            }
            if (action == 'json') {
                // Create global var with response data
                window[id] = JSON.parse($(this).text()); 
            }
     
        }
    });
}

/** Blocks UI of selected elements
 *  Params
 *  blockArray: array of IDs or string of comma separated IDs to be blocked
 *  toBlock:    boolean
 */
function blockElements(blockArray, toBlock) {
    var UIids = new Array();

    if (blockArray == null) {
        return false;
    }

    if (typeof blockArray == 'string') {
        UIids = blockArray.split(',');
    } else if (typeof blockArray == 'object') {
        UIids = blockArray;
    }
    for (var i in UIids) {
        UIids[i] = $.trim(UIids[i]);
        if (toBlock)
            $('#'+UIids[i]).block({
               theme: true
            });
        else
            $('#'+UIids[i]).unblock();
    }
}

/** Function is onClick handler, sends AJAX get request to url specified in href attribute
 * You should bind it to onClick event of <a> tag
 * Closest parent with attribute "blockUI" defines coma separated IDs of elements which have to be blocked during request
 * Returns false to avoid normal processing of <a> tag
 */
function aAJAXClickHandler() {
    var UIControl = $(this).closest('[blockUI]').attr('blockUI');
    var callback = $(this).closest('[callback]').attr('callback');
    ajaxGet($(this).attr('href'),UIControl, callback);
    return false;
}

function cw_onload() {

    // All elements with "onload" class must have href attribute with ajax URL to load
    $('.onload').each(function(){
        if ($(this).data('loaded') != true) {
            aAJAXClickHandler.apply(this);
            $(this).data('loaded', true)
        }
    });
}

/** Function sends AJAX post request to url specified in href attribute of container attaching all form fields inside container
 * Closest parent with attribute "blockUI" defines coma separated IDs of elements which have to be blocked during request
 */
function submitFormPart(container, callback) {

    if (typeof container == 'string') {
        container = $('#'+container);
    }
    var UIControl = $(this).closest('[blockUI]').attr('blockUI');
    var copy = container.clone();

    var form = $(document.createElement('form'));
    form.attr({
    'action' : container.attr('href'),
    'method' : 'post'
    });


    var is_ajax = $(document.createElement('input'));
    is_ajax.attr({
    'type'  : 'hidden',
    'name'  : 'is_ajax',
    'value' : '1'
    });

    // Selectboxes are cloned without active values, move it as input fields
    copy.find('select').remove();
    container.find('select').each(function() {
        var sel = $(document.createElement('input'));
        sel.attr({
        'type'  : 'hidden',
        'name'  : $(this).attr('name'),
        'value' : $(this).val()
        });
        form.append(sel);
    });

    form.append(copy);
    form.append(is_ajax);

    blockElements(UIControl, true);

    form.ajaxSubmit({dataType:'xml',success: function(data) {
            processXMLResponse(data);
            if (callback != null) {
                if (typeof callback == 'function') {
                    callback(data);
                } else if (typeof callback == 'string' && typeof window[callback] == 'function') {
                    window[callback](data);
                } 
            }
            blockElements(UIControl, false);
        }});
    return false;
}

function submitFormAjax(form, callback) {

    var UIControl = $(this).closest('[blockUI]').attr('blockUI');
    
    if (typeof form == 'string') {
        form = $('form[name='+form+']');
    }

    if (form.length>0) {
    blockElements(UIControl, true);

    var is_ajax = $(document.createElement('input'));
    is_ajax.attr({
    'type'  : 'hidden',
    'name'  : 'is_ajax',
    'value' : '1'
    });

    form.append(is_ajax);

    form.ajaxSubmit({dataType:'xml',success: function(data) {
            processXMLResponse(data);
            if (callback != null) {
                if (typeof callback == 'function') {
                    callback(data);
                } else if (typeof callback == 'string' && typeof window[callback] == 'function') {
                    window[callback](data);
                } 
            }
            blockElements(UIControl, false);
        }});

    }

    return false;
}

$(document).ready(function() {
    // All anchors/links with ajax class are processed by ajax handler
    $(document).on('click', 'span.ajax, a.ajax', aAJAXClickHandler); // It is new form of obsolete .live()
    cw_onload(); }
);
