var states = new Array();

function cw_flush_select(name, none) {
    if (!name) return;
    select = document.getElementById(name)
    if (!select) return;
    while (select.options.length > 0)
        select.options[select.options.length-1] = null;
    if (none)
        select.options[select.options.length] = new Option(lbl_none, 0);
}

function cw_set_cities(cities_count, city_name, city_value, cities) {
    if (!city_name) return;
    city_name_select = city_name+'_select';
    city_name_text = city_name+'_text';

    select = document.getElementById(city_name_select);
    text = document.getElementById(city_name_text);

    if (!select) return;

    cw_flush_select(city_name_select, 0);
    if (cities_count > 0) {
        sel_index = 0;
        index = 1;
        select.options[select.options.length] = new Option(lbl_please_select, 0);
        for (i in cities) {
            select.options[select.options.length] = new Option(cities[i].city, cities[i].city);
            if (cities[i].city == city_value) sel_index = index;
            index++;
        }
        select.selectedIndex = sel_index;
        select.disabled=false;
        select.style.display='';
        text.disabled=true;
        text.style.display='none';
    }
    else {
        select.options[select.options.length] = new Option(lbl_none, 0);
        select.disabled=true;
        select.style.display='none';
        text.disabled=false;
        text.style.display='';
    }
}

function handler_cities_list(data) {
    cw_set_cities(data.cities_count, data.city_name, data.city_value, data.cities);
}

function handler_states_list(data) {
    select = document.getElementById(data.state_name);

    if (!select) return;

    if (data.disabled) {
        select.disabled=true;
    }

    cw_flush_select(data.state_name, 0);

    states = data.states;

    if (data.states_count > 0) {
        select.disabled=false;
        sel_index = 0;
        index = 1;
        select.options[select.options.length] = new Option(lbl_please_select, 0);
        for (i in data.states) {
            select.options[select.options.length] = new Option(data.states[i].state, data.states[i].code);
           if (data.states[i].code == data.selected) sel_index = index;
            index++;
        }

//        if (sel_index > 0)
 //       select.selectedIndex = sel_index;

//        if (typeof(select.onchange) == 'function')
//            select.onchange();

       // var selected_states =  [(data.selected!='')?(data.selected):''];//data.selected!=''?JSON.parse(data.selected):'';
if (data.selected!='') {

        var selected_states = [data.selected];         

        var state_selector = '#'+data.state_name;

        if(selected_states.length>0)
            $(state_selector+' option[value="0"]').prop("selected", false);
        for (var n = 0; n < selected_states.length; n++)
            $(state_selector+'  option[value='+selected_states[n]+']').attr('selected', 'selected');

        setTimeout(function() {

        if(selected_states.length>0)
            $(state_selector+' option[value="0"]').prop("selected", false);
        for (var n = 0; n < selected_states.length; n++)
            $(state_selector+'  option[value='+selected_states[n]+']').attr('selected', 'selected');

        if (typeof(select.onchange) == 'function')
            select.onchange();

        }, 500);         
}
/*
        if (typeof(select.onchange) == 'function')
            select.onchange();
*/

    }
    else
        select.options[select.options.length] = new Option(lbl_none, 0);

}



function handler_counties_list(data) {
    select = document.getElementById(data.county_name);
    if (!select) return;

    if (data.disabled) {
        select.disabled=true;
        cw_set_cities(data.cities_count, data.city_name, data.city_value, data.cities);
        return;
    }
    select.disabled=false;

    cw_flush_select(data.county_name, 0);

    if (data.counties_count > 0) {
        sel_index = 0;
        index = 1;
        select.options[select.options.length] = new Option(lbl_please_select, 0);
        for (i in data.counties) {
            select.options[select.options.length] = new Option(data.counties[i].county, i);
            if (i == data.selected) sel_index = index;
            index++;
        }
        select.selectedIndex = sel_index;
    }
    else
        select.options[select.options.length] = new Option(lbl_none, 0);

    cw_set_cities(data.cities_count, data.city_name, data.city_value, data.cities);
}

function handler_regions_list(data) {
    select = document.getElementById(data.region_name);
    if (!select) return;

    if (data.disabled)
        select.disabled=true;
    else
        select.disabled=false;

    cw_flush_select(data.region_name, 0);

    if (data.disabled) {
        data.disabled = 0;
        select.options[select.options.length] = new Option(lbl_none, 0);
        data.selected = data.state_selected;
        handler_states_list(data);
    }
    else if (data.regions_count > 0) {
        sel_index = 0;
        index = 1;
        select.options[select.options.length] = new Option(lbl_please_select, 0);
        for (i in data.regions) {
            select.options[select.options.length] = new Option(data.regions[i].region, i);
            if (i == data.selected) sel_index = index;
            index++;
        }
        select.selectedIndex = sel_index;
        select.onchange();
    }
    else
        select.options[select.options.length] = new Option(lbl_none, 0);

}

function cw_map_ajax_update_states_list(country, region, state_name, state_value, county_name, county_value, show_code) {

    if (!isset(state_name)) return;

    cw_flush_select(county_name, 1);

    $.ajax({
        "url": 'index.php?target=ajax&mode=states&region='+region+'&country='+country+'&state_name='+state_name+'&show_code='+show_code+'&selected='+state_value,
        "success": handler_states_list,
        "dataType":"json",
        "type":"post"
    });
}

function cw_map_ajax_update_counties_list(state, county_name, county_value, city_name, city_value) {

    if (!isset(county_name)) return;

    var state_id;
    state_id = 0;
    for(i in states) {
        if (states[i].code == state) {
            state_id = i;
            break;
        }
    }

    $.ajax({
        "url":'index.php?target=ajax&mode=counties&state='+state_id+'&county_name='+county_name+'&selected='+county_value+'&city_name='+city_name+'&city_value='+city_value,
        "success":handler_counties_list,
        "dataType":"json", "type":"post"});
}

function cw_map_ajax_update_regions_list(country, region_name, region_value, state_name, state_value, county_name, county_value) {
    if (!isset(region_name)) return;

    cw_flush_select(state_name, 1);
    cw_flush_select(county_name, 1);

    $.ajax({
        "url":'index.php?target=ajax&mode=regions&country='+country+'&region_name='+region_name+'&selected='+region_value+'&state_name='+state_name+'&state_selected='+state_value,
        "success":handler_regions_list,
        "dataType":"json", "type":"post"
    });
}

function cw_map_ajax_update_cities_list(county_id, city_name, city_value) {
    $.ajax({
        "url":'index.php?target=ajax&mode=cities&county='+county_id+'&city_name='+city_name+'&city_value='+city_value,
        "success":handler_cities_list,
        "dataType":"json", "type":"post"});
}

/* TODO: Remove if register page / checkout works well. This is old way of working with address book, now everyhing controlled in PHP
function cw_load_addresses() {
    $.ajax({
        'url':'index.php?target=ajax&action=load_addresses&user='+user,
        "success": function(xml) {
            var ind = 0;
            for(i in addresses_list) {
                cw_flush_select(i);
                var index = sel_index = 1;
                var select = document.getElementById(i);
                select.options[select.options.length] = new Option(lbl_add_new, 0);
                $(xml).find('address').each(function() {
                    select.options[select.options.length] = new Option($(this).text(), $(this).attr('address_id'));
                    if (ind == 0 && $(this).attr('is_main') == 1) sel_index = index;
                    if (ind == 1 && $(this).attr('is_current') == 1) sel_index = index;
                    index++;
                });
                select.selectedIndex = sel_index;
                ind++;
                cw_load_address(addresses_list[i], i);
            }
        },
        "dataType":"xml",
        "type":"post"
    });
}

function cw_delete_address(name, elid) {
    var selector=$('#'+elid);
    $.ajax({
        'url':'index.php?target=ajax&action=delete_address&address_name='+name+'&address_id='+selector.val()+'&user='+user,
        "success": function(data) {
            cw_load_addresses();
        },
        "dataType":"html",
        "type":"post"
    });

}

function cw_save_address(name, elid, is_main) {
    var container = $('#address_'+elid);
    var data = '';
    $('#address_'+elid+' :input').each(function() {
        data += $(this).attr('name')+'='+$(this).val()+'&';
    });
    var selector=$('#'+elid);
    $.ajax({
        'url':'index.php?target=ajax&action=update_address&address_name='+name+'&is_main='+is_main+'&address_id='+selector.val()+'&user='+user,
        'data': data,
        "success": function(data) {
            $('#address_'+elid).html(data);
            if (selector.val() == 0) cw_load_addresses();
        },
        "dataType":"html",
        "type":"post"
    });
}

function cw_load_address(name, elid, is_checkout, is_main) {
    var selector=$('#'+elid);
    $.ajax({
        "url":'index.php?target=ajax&action=load_address&address_name='+name+'&address_id='+selector.val()+'&user='+user+'&is_checkout='+is_checkout+'&is_main='+is_main,
        "success": function(data) {
            $('#address_'+elid).html(data);
            if (is_checkout) cw_check_address(-1);
        },
        "dataType":"html",
        "type":"post"
    });
}
*/
