
<div class="category_selector_header">
    <div class="category_path path">
    </div>
</div>

<div id="category_selector" class="transp">
    {literal}
    <script type="text/javascript">
    var picked_cat_ids = [];
    var picked_cat_names = [];
    var default_root_category = 282;//{$config.custom_magazineexchange.magexch_default_root_category|default:0};
    </script>
    {/literal}

    {literal}
    <script type="text/javascript">
    //<![CDATA[
        function load_category_select_lvl(lvl, parent_cat = 0, selected_category = null) {
            $.ajax({
                type: 'get',
                url: "index.php?target=seller_category_selector",
                data: "mode=get_subcats&parent=" + parent_cat,
                dataType: 'json',
                success: function(data) {
                    if (data.length) {
                        const lvl_container_id = "cat_select_container_"+lvl;
                        //$('#categories_boxes').append("<div class='cat_select lvl"+lvl+"' lvl='"+lvl+"' id='"+lvl_container_id+"'></div>"); 

                        const lvl_box_id = 'cat_selector_'+lvl;
                        $('#'+lvl_container_id).append("<select id='"+lvl_box_id+"' size='20'></select>");

                        let dropdown = document.getElementById(lvl_box_id);
                        dropdown.length = 0;
                        for (let i = 0; i < data.length; i++) {
                            option = document.createElement('option');
                            option.text = data[i].category_name;
                            option.disabled = !Boolean(data[i].allowed);
                            option.value = data[i].category_id;
                            dropdown.add(option);
                            if (selected_category && selected_category == data[i].category_id)
                                dropdown.selectedIndex = i;
                        }

                        $(dropdown).on('change', function(e) {
                            const sel_cat_id = $(this).val();
                            picked_cat_ids = [];
                            picked_cat_names = [];
                            $('div.cat_select').each(function() {
                                const s_lvl = $(this).attr('lvl');
                                if (s_lvl > lvl) {
                                    $(this).find(">:first-child").remove();
                                } else {
                                    const s_lvl_box_id = 'cat_selector_'+s_lvl;    
                                    picked_cat_ids.push($('#'+s_lvl_box_id).val());
                                    picked_cat_names.push($('#'+s_lvl_box_id+' option:selected').text());
                                }    
                            });
                            load_category_select_lvl(lvl+1, sel_cat_id);
                            activate_apply_buttons();
                            console.log('current popup vals', picked_cat_ids, picked_cat_names);
                        });
                    
                        location.hash = lvl_container_id;
                    }
                },
                error: function() {
                    console.log('Error occured (debug: JS ajax_search)');
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

        function activate_apply_buttons() {
            if (picked_cat_ids.length == 0) {
                $('.category_selector_footer a.btn-green').addClass('disabled').css('color', 'gray');
                $('.category_selector_header .category_path.path').html('<span>not selected</span>');
            } else {
                $('.category_selector_footer a.btn-green').removeClass('disabled').css('color', 'black');
                $('.category_selector_header .category_path.path').html(picked_cat_names.join('&nbsp;<span>></span>&nbsp;'));
            }    
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

