{literal}
<script type='text/javascript'>
$(document).ready(function() {
    function toggle_active_section() {
        var id = $(this).attr('id')+'_section';
        if ($(this).attr('checked')) {
            $('#'+id).show();
        } else {
            $('#'+id).hide();
        }
    };
    $('#active_sections').find('input').each(toggle_active_section);
    $('#active_sections').find('input').bind('click',function(){
      var id = $(this).attr('id')+'_section';
      $('#'+id).toggle();
    });

});
</script>
{/literal}
