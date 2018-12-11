<script language="JavaScript">
var lbl_no_search_term = '{$lng.lbl_no_search_term|default:"No search terms entered! You need to type something first, before clicking the search button"}';

{literal}
function quick_search() {
    var choice = $('select[name=choice]').val();
    var term = $('#search_term').val();

    if (term == '') {
        alert(lbl_no_search_term);
        return false;
    }

    $('#search_by_' + choice).find('input:first').val(term);
    $('#search_by_' + choice).submit();
    return false;
}

$(function() {
	$("#search_term").autocomplete({
		source: function(request, response) {
			var type = $('input[name=choice]:checked').val();
			var data = {};

			if (type == "oid") {
				var url = "index.php?target=quick_data";
				data["type"] = "docs_O";
				data["posted_data[doc_id]"] = request.term;
			}

			if (type == "user") {
				var url = "index.php?target=quick_data";
				data["type"] = "user_C";
				data["posted_data[substring]"] = request.term;
			}

			if (type == "sku") {
				var url = "index.php?target=quick_data";
				data["type"] = "products";
				data["posted_data[productcode]"] = request.term;
			}

			if (type == "pid") {
				var url = "index.php?target=quick_data";
				data["type"] = "products";
				data["posted_data[product_id]"] = request.term;
			}

			$.ajax({
				url		: url,
				type	: "POST",
				dataType: "json",
				data	: data,
				success	: function(data) {
					response($.map(data, function(item) {
						return {
							label: item.name,
							id: item.id
						};
					}));
				}
			});
		},
		minLength	: 3,
		select		: function(event, ui) {
			var type = $('input[name=choice]:checked').val();
			var link_to = "";

			if (type == "oid") {
				var link_to = "index.php?target=docs_O&doc_id=" + ui.item.id;
			}

			if (type == "user") {
				var link_to = "index.php?target=user_C&mode=modify&user=" + ui.item.id;
			}

			if (type == "pid" || type == "sku") {
				var link_to = "index.php?target=products&mode=details&product_id=" + ui.item.id;
			}

			if (link_to != "") {
				window.location.href = link_to;
			}
		}
	});
});
{/literal}
</script>

<div class='dashboard_search_orders'>
    <div class="col-xs-4">
      <div class="form-material form-material-primary input-group remove-margin-t remove-margin-b">
	 <select name='choice' class="form-control">
	   <option value='oid' selected >{$lng.lbl_order_id}</option>
	   <option value='user'>{$lng.lbl_user}</option>
	   <option value='sku'>{$lng.lbl_sku}</option>
	   <option value='pid'>{$lng.lbl_product_id}</option>
	 </select>
      </div>
    </div>
    <div class="col-xs-8">
      <div class="form-material form-material-primary input-group remove-margin-t remove-margin-b">
        <input type='text' id='search_term' name='term' value="" size="40" class="form-control" placeholder="Search.." />
        <span class="input-group-addon"  onclick='quick_search();' ><i class="si si-magnifier"></i></span>
        {*<input type='button' value='search' class="btn btn-minw btn-info" onclick='quick_search();' />*}
      </div>
    </div>
</div>

<div style="display:none;">
<form action="index.php?target=products" id='search_by_pid' method='POST'>
    <input type='hidden' name='posted_data[product_id]' />
    <input type="hidden" value="search" name="action" />
    <input type="hidden" value="1" name="search_sections[tab_add_search]" />
</form>
<form action="index.php?target=products" id='search_by_sku' method='POST'>
    <input type='hidden' name='posted_data[productcode]' />
    <input type="hidden" value="search" name="action" />
    <input type="hidden" value="1" name="search_sections[tab_add_search]" />
</form>
<form action="index.php?target=docs_O&sort=date" id='search_by_oid' method='POST' onSubmit='$(this).find("input:eq(1)").val($(this).find("input:eq(0)").val());'>
    <input type='hidden' name='posted_data[basic][doc_id_start]' />
    <input type='hidden' name='posted_data[basic][doc_id_end]' />
    <input type="hidden" value="search" name="action" />
    <input type="hidden" name="search_sections[tab_search_orders]" value="1" />
</form>
<form action="index.php?target=docs_I" id='search_by_iid' method='POST' onSubmit='$(this).find("input:eq(1)").val($(this).find("input:eq(0)").val());'>
    <input type='hidden' name='posted_data[basic][display_id_start]' />
    <input type='hidden' name='posted_data[basic][display_id_end]' />
    <input type="hidden" value="search" name="action" />
    <input type="hidden" name="search_sections[tab_search_orders]" value="1" />
</form>
<form action="index.php?target=user_C" id='search_by_user' method='POST'>
    <input type="hidden" name="posted_data[basic_search][substring]" />
    <input type="hidden" value="1" name="search_sections[tab_basic_search]">
    <input type="hidden" value="search" name="action">
    <input type="hidden" name="posted_data[basic_search][by_username]" value='1'>
    <input type="hidden" name="posted_data[basic_search][by_firstname]" value='1'>
    <input type="hidden" name="posted_data[basic_search][by_lastname]" value='1'>
    <input type="hidden" name="posted_data[basic_search][by_email]" value='1'>
    <input type="hidden" name="posted_data[basic_search][by_customer_id]" value='1'>
    <input type="hidden" name="posted_data[basic_search][by_ssn]" value='1'>
    <input type="hidden" name="posted_data[basic_search][by_company]" value='1'>

</form>
</div>
