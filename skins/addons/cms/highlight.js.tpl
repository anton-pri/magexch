{if $identifiers.A.customer_id && $config.cms.allow_edit_from_customer_area == 'Y'}
{literal}
<script type='text/javascript' language='javascript'>
	var informer = '<div class="cms_informer"></div>';
	$(document).ready(function(){

		$('div.ab_container').each(function(){
			$(this).addClass('highlighted');
			var inf = $(informer).clone();
			var code = $(this).attr('service_code');
			$(inf).html(code+' <a href="'+current_location+'/admin/index.php?target=cms&mode=add&service_code='+code+'">[add]</a>');
			$(this).prepend(inf);
		});

		$('div.ab_content').each(function(){
			$(this).addClass('highlighted');
			var inf = $(informer).clone();
			var code = $(this).attr('contentsection_id');
			$(inf).html('<a href="'+current_location+'/admin/index.php?target=cms&mode=update&contentsection_id='+code+'">CMS ID: #'+code+'</a>');
			$(this).prepend(inf);			
		});
	});
</script>
{/literal}
{/if}

