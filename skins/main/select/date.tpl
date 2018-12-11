{include_once file='calendar/include_js.tpl'}
<div class="js-datetimepicker input-group date">
  <input type="text"  class="form-control{if $class} {$class}{/if}" name="{$name}" id="{$name|id}" value="{if $value}{$value|date_format:$config.Appearance.date_format}{/if}" size="10" >
  <span class="input-group-addon"  onclick="javascript: return showCalendar('{$name|id}', '{$config.Appearance.date_format}');" id="{$name|id}Link">
    <span class="fa fa-calendar"></span>
  </span>
</div>
