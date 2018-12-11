<script type="text/javascript" language="JavaScript 1.2">
<!--
var config_default_country = "{$config.General.default_country}";

// used in check_zip_code_field() from js/check_zipcode.js
// note: you should update language variables after change this table
{literal}
var check_zip_code_rules = {
	AT: { lens:{4:true} },
	CA: { lens:{6:true,7:true} },
	CH: { lens:{4:true} },
	DE: { lens:{5:true}, re:/\D/ },
	LU: { lens:{4:true} },
	US: { lens:{5:true}, re:/\D/ }
};
{/literal}

check_zip_code_rules.AT.error='{$lng.txt_error_at_zip_code|strip_tags|escape:javascript}';
check_zip_code_rules.CA.error='{$lng.txt_error_ca_zip_code|strip_tags|escape:javascript}';
check_zip_code_rules.CH.error='{$lng.txt_error_ch_zip_code|strip_tags|escape:javascript}';
check_zip_code_rules.DE.error='{$lng.txt_error_de_zip_code|strip_tags|escape:javascript}';
check_zip_code_rules.LU.error='{$lng.txt_error_lu_zip_code|strip_tags|escape:javascript}';
check_zip_code_rules.US.error='{$lng.txt_error_us_zip_code|strip_tags|escape:javascript}';

-->
</script>
{include_once_src file="main/include_js.tpl" src="js/check_zipcode.js"}
