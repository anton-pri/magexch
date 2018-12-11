<script type="text/javascript">
    {literal}
    $(document).ready(function() {
        if (!$('#myCanvas').tagcanvas({
            textColour: '#0300FF',
            outlineColour: '#378042',
            reverse: true,
            depth: 0.8,
            maxSpeed: 0.05,
            weight: true,
            weightMode: 'size'
        },'tags')) {
            // something went wrong, hide the canvas container
            $('#myCanvasContainer').hide();
        }
    });
    {/literal}
</script>
