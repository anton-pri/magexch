
<div class="category_selector_header">
    <div class="category_path path">
    </div>
</div>

<div id="category_selector" class="transp">
    
    <script type="text/javascript">
        var picked_cat_ids = [];
        var picked_cat_names = [];
        var default_root_category = {$config.custom_magazineexchange.magexch_default_root_category|default:0};
        var req_access_link = "{$config.custom_magazineexchange_sellers.mag_seller_request_access_link}";
        var req_access_html = 
            "<a class='request-category-btn button simple  btn-green btn' href='"+req_access_link+"' target='_blank'><span>Request Access or Suggest New Category</span></a>";
        var curr_lvl = 0;
        var curr_sel_cat_id = 0;
        var preselect_lvl = 0;
        var preselect_cat_id = 0;
        //var curr_cat_type = '';            
    </script>
    

    {literal}
    <script type="text/javascript">
    //<![CDATA[

        function get_create_new_cat_html(lvl) {
            return "<a class='add-category-btn button simple btn-green btn disabled' onclick='javascript:add_category_popup("+lvl+")'><span>Create New Category</span></a>";
        }

        function load_category_select_lvl(lvl, parent_cat = 0, selected_category = null) {
            $.ajax({
                type: 'get',
                url: "index.php?target=seller_category_selector",
                data: "mode=get_subcats&parent=" + parent_cat,
                dataType: 'json',
                success: function(data) {
                    if (data.length) {
                        const create_new_cat_html = get_create_new_cat_html(lvl);
                        const lvl_container_id = "cat_select_container_"+lvl;
                        //$('#categories_boxes').append("<div class='cat_select lvl"+lvl+"' lvl='"+lvl+"' id='"+lvl_container_id+"'></div>"); 

                        const lvl_box_id = 'cat_selector_'+lvl;
                        $('#'+lvl_container_id).css('min-width', '160px');

                        let all_cats_are_years = true;
                        for (let i = 0; i < data.length; i++) {
                            if (data[i].category_type != "Year") {
                                all_cats_are_years = false;
                                break;
                            }
                        }
                        $('#'+lvl_container_id).append("<select id='"+lvl_box_id+"' size='20'></select>");

/*
                        if (all_cats_are_years) {
                            $('#'+lvl_container_id).append(
                                "<select id='"+lvl_box_id+"' size='20'></select><div class='buttons' id='cat_selector_button_"+lvl+"'>"+create_new_cat_html+"</div>");
                        } else {
                            $('#'+lvl_container_id).append(
                                "<select id='"+lvl_box_id+"' size='20'></select><div class='buttons' id='cat_selector_button_"+lvl+"'>"+req_access_html+"</div>");
                        }
*/
                        let dropdown = document.getElementById(lvl_box_id);
                        let category_selected_type = '';
                        dropdown.length = 0;
                        for (let i = 0; i < data.length; i++) {
                            option = document.createElement('option');
                            option.text = data[i].category_name;
                            option.disabled = !Boolean(data[i].allowed);
                            option.value = data[i].category_id;
                            option.setAttribute('category_type', data[i].category_type);
                            dropdown.add(option);
                            if (selected_category && selected_category == data[i].category_id) {
                                dropdown.selectedIndex = i;
                                category_selected_type = data[i].category_type;
                            }
                        }

                        if (category_selected_type) {
                            if (category_selected_type == 'Year') {
                                $('#'+lvl_container_id).append(
                                    "<div class='buttons' id='cat_selector_button_"+lvl+"'>"+create_new_cat_html+"</div>");
                                $('#cat_selector_button_'+lvl+' a').removeClass('disabled');    
                            } else {
                                $('#'+lvl_container_id).append(
                                    "<div class='buttons' id='cat_selector_button_"+lvl+"'>"+req_access_html+"</div>");   
                            }

                        } else {
                            if (all_cats_are_years) {
                                $('#'+lvl_container_id).append(
                                    "<div class='buttons' id='cat_selector_button_"+lvl+"'>"+create_new_cat_html+"</div>");
                            } else {
                                $('#'+lvl_container_id).append(
                                    "<div class='buttons' id='cat_selector_button_"+lvl+"'>"+req_access_html+"</div>");
                            }  
                        }

                        $(dropdown).on('change', function(e) {
                            const sel_cat_id = $(this).val();

                            open_next_lvl_selector(lvl, sel_cat_id);
                        });
                    
                        location.hash = lvl_container_id;
                        if (preselect_lvl && preselect_cat_id) {
                            $('#'+lvl_box_id).val(preselect_cat_id).trigger('change');
                            preselect_lvl = 0;
                            preselect_cat_id = 0;
                        }
                    }
                },
                error: function() {
                    console.log('Error occured (debug: JS Category Selector)');
                },
                complete: function() {
                    //$('html').css('cursor', 'auto'); $("input[name=posted_data\\[substring\\]]").removeClass('search_waiting');
                }
            });
        }

        $(document).ready(function() {

            for (let lvl = 1; lvl < 8; lvl++) {
                let lvl_container_id = "cat_select_container_"+lvl;
                $('#categories_boxes').append("<div class='cat_select transp lvl"+lvl+"' lvl='"+lvl+"' id='"+lvl_container_id+"'></div>"); 
            }

            /*
            $("div.cat_select select").on('click', function(e) {
                console.log(e);
            });
            */
            const init_cats = window.parent.get_selected_category();
            console.log('init_cats', init_cats, init_cats.length);

            picked_cat_ids = init_cats;

            $('body').css('overflow-y', 'hidden').css('background-color', '#FCCB05');
            $("div.content").css('padding', 0).css('position', 'fixed').addClass('transp');
            $("div.block").css('margin-bottom', 0).addClass('transp');
            $('#page-container').addClass('transp');
            if (init_cats.length == 0) {
                load_category_select_lvl(1, default_root_category);
                $('.category_selector_header .category_path.path').html('<span>not selected</span>');
                $('.category_selector_footer a.btn-green').addClass('disabled').css('color', 'gray');
            } else {
                load_category_select_lvl(1, default_root_category, init_cats[0]);
                for (let i = 1; i < init_cats.length; i++) {
                    load_category_select_lvl(i+1, init_cats[i-1], init_cats[i]);        
                }
                load_category_select_lvl(init_cats.length+1, init_cats[init_cats.length-1]);   
                $('.category_selector_header .category_path.path').html(window.parent.get_selected_category_string());
                picked_cat_ids = init_cats;
                picked_cat_names = window.parent.get_selected_category_string();
            }
        });

        function open_next_lvl_selector(lvl, sel_cat_id) {
            const create_new_cat_html = get_create_new_cat_html(lvl);
            const cat_type = $('#cat_selector_' + lvl).find(':selected').attr('category_type');

            console.log('open_next_lvl_selector', lvl, sel_cat_id, cat_type);

            if (cat_type == "Year") {
                $('#cat_selector_button_'+lvl).html(create_new_cat_html);
                $('#cat_selector_button_'+lvl+' a').removeClass('disabled');
            } else {
                $('#cat_selector_button_'+lvl).html(req_access_html);
            }                       

            picked_cat_ids = [];
            picked_cat_names = [];
            $('div.cat_select').each(function() {
                const s_lvl = $(this).attr('lvl');
                if (s_lvl > lvl) {
                    //console.log('empty select', s_lvl, lvl);
                    //$(this).find(">:first-child").remove();
                    $(this).html('');
                    $(this).css('min-width', 'initial');
                } else {
                    const s_lvl_box_id = 'cat_selector_'+s_lvl;    
                    picked_cat_ids.push($('#'+s_lvl_box_id).val());
                    picked_cat_names.push($('#'+s_lvl_box_id+' option:selected').text());
                }    
            });
            load_category_select_lvl(lvl+1, sel_cat_id);
            activate_apply_buttons();
            if (cat_type != 'Year') {
                curr_lvl = lvl;
                curr_sel_cat_id = sel_cat_id;
                //curr_cat_type = cat_type;
            } else {
                curr_lvl = lvl-1;
                curr_sel_cat_id = $('#cat_selector_' + curr_lvl).val();
                //curr_cat_type = cat_type;
            }

            //console.log('current popup vals', picked_cat_ids, picked_cat_names, curr_lvl, curr_sel_cat_id);
        }

        function add_category_popup(lvl) {
            const lvl_selector_id = "cat_selector_" + lvl;
            const master_category = $('#' + lvl_selector_id).val();
            //alert(master_category);

            if ($('#add_category_dialog').length==0)
                $('body').append('<div id="add_category_dialog"></div>');

            // Load iframe with category selector into dialog
            $('#add_category_dialog').html(
                "<iframe frameborder='no' width='470' height='250' src='index.php?target=seller_add_category&master_category="+master_category+"'></iframe>"
            );
            // Show dialog
            sm('add_category_dialog', 500, 300, true, 'Add category');
        }

        function activate_apply_buttons() {
            if (picked_cat_ids.length == 0) {
                $('.category_selector_footer a.btn-green').addClass('disabled').css('color', 'gray');
                $('.category_selector_header .category_path.path').html('<span>not selected</span>');
            } else {
                $('.category_selector_footer a.btn-green').removeClass('disabled').css('color', 'black');
                $('.category_selector_header .category_path.path').html(picked_cat_names.join('&nbsp;<span>></span>&nbsp;'));
            }    
        }

        function add_new_category(new_category, master_category_id) {
            //alert(' ' + new_category + master_category_id);
            $.ajax({
                type: 'get',
                url: "index.php?target=seller_add_category",
                data: "mode=add_category&master_category=" + master_category_id + "&new_category=" + new_category,
                dataType: 'json',
                success: function(data) {
                    console.log('add_new_category_success', curr_lvl, curr_sel_cat_id, data.category_id);
                    preselect_lvl = curr_lvl+1;
                    preselect_cat_id = data.category_id;
                    open_next_lvl_selector(curr_lvl, curr_sel_cat_id);

                },
                error: function() {
                    console.log('Error occured (debug: JS Category Clone)');
                },
                complete: function(data) {
                    /*
                    const new_category_id = data.responseJSON.category_id;
                    const next_lvl = curr_lvl+1;
                    alert(' select new option ' + next_lvl + ' #' + new_category_id);
                    $('#cat_selector_' + next_lvl).val(new_category_id);

                    console.log('add_new_category_complete', data.responseJSON.category_id, next_lvl, new_category_id);
                    */
                    //$('html').css('cursor', 'auto'); $("input[name=posted_data\\[substring\\]]").removeClass('search_waiting');
                }
            });


            hm('add_category_dialog');
        }

    //]]>
    </script>
    {/literal}


    <div id="categories_boxes" class="transp">


    </div>
</div>

<div class="category_selector_footer transp">
    <div class="buttons">
        {include 
            file='buttons/button.tpl' 
            button_title=$lng.lbl_category_select_apply|default:"Apply"
            class="btn-green btn" 
            href="javascript: window.parent.set_selected_category(picked_cat_ids, picked_cat_names);"
        }
    </div>
</div>

