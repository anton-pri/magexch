<div id='lpop' class='lpop {$area}' style='display:none'>
{$lpop_warning}
</div>
{literal}
<script>
$(document).ready(
    function() {
        $('#lpop').prependTo('body');
        $('#lpop').show();
    }
);
</script>
<style>
.lpop {
    width: 100%;
    padding: 15px 0px;
    border: 2px solid red;
    background: black;
    color: white;
    text-align: center;
}
</style>
{/literal}
