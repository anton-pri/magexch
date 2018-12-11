
{literal}
<script type="text/javascript">
function openFirstPanel(){
  $('.accordion > .accordion_title:first-child').next().addClass('active').slideDown();
  $('.accordion > .accordion_title:first-child').addClass('open');

}

$( document ).ready(function() {
    
  var allPanels = $('.accordion > .accordion_content').hide();
  openFirstPanel();
    
  $('.accordion > .accordion_title').click(function() {
      $this = $(this);
      $target =  $this.next();
      $allTitles = $('.accordion > .accordion_title');

    
      if($target.hasClass('active')){
        $target.removeClass('active').slideUp(); 
      }else{
        allPanels.removeClass('active').slideUp();
        $target.addClass('active').slideDown();
      }

      if($this.hasClass('open')){
        $this.removeClass('open'); 
      }else{
        $allTitles.removeClass('open');
        $this.addClass('open');
      }
        

        

      
    return false;
  });
});
</script>
{/literal}
