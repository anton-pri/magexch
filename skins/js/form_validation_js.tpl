<script type="text/javascript">
function FormValidation() {ldelim}

{* kornev, TOFIX *}
    {if $product_options ne ''}
    if(!check_exceptions()) {ldelim}
        alert(exception_msg);
        return false;
    {rdelim}
	{if $product_options_js ne ''}
	{$product_options_js}
	{/if}
    {/if}

	if(document.getElementById('product_avail'))
	    if(document.getElementById('product_avail').value == 0) {ldelim}
        alert("{$lng.txt_choose_an_amount|escape:javascript|replace:"\n":"<br />"|replace:"\r":" "}");
			return false;
	    {rdelim}

    return true;
{rdelim}
</script>
