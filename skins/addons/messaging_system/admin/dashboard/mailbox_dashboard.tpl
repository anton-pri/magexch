<div class="list-group remove-margin-b">
  <div class=''>
    <div class="list-group-item active ">
      <a href='index.php?target=message_box'><i class="fa fa-fw fa-inbox push-5-r"></i> Incoming 
      <span style='float:right'> {$messages_counter.incoming}{if $messages_counter.new} <b>({$messages_counter.new})</b>{/if}</span>
      </a>
    </div>
    <div class="list-group-item ">
        <a href='index.php?target=message_box&mode=sent'><i class="fa fa-fw fa-send push-5-r"></i> Sent 
        <span style='float:right'>{$messages_counter.sent}</span></a>
    </div>
    <div class="list-group-item ">
        <a href='index.php?target=message_box&mode=archive'><i class="fa fa-fw fa-archive push-5-r"></i> Archive 
        <span style='float:right'>{$messages_counter.archive}</span></a>
    </div>

{*
    <div class="list-group-item ">
        <i class="fa fa-fw fa-edit push-5-r"></i> Drafts
    </div>
    <div class="list-group-item ">
        <i class="fa fa-fw fa-trash push-5-r"></i> Trash
    </div>
*}
  </div>
</div>

