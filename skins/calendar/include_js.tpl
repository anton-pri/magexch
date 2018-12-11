{assign var='_calendar_included' value=true}

<link rel="stylesheet" type="text/css" media="all" href="{$SkinDir}/calendar/css/theme.css" />

{include_once_src file='main/include_js.tpl' src='calendar/calendar.js'}
{include_once_src file='main/include_js.tpl' src='calendar/lang/calendar-en.js'}

<script language="Javascript">
var start_cal_year = '{math equation="a-10" a=$config.Company.start_year|default:2010}';
var end_cal_year = '{math equation="a+10" a=$config.Company.end_year|default}';

{literal}
function CalendarSelected(cal, date) {
    cal.sel.value = date;
    if (cal.dateClicked)
        cal.callCloseHandler();
}

function CalendarCloseHandler(cal) {
  cal.hide();
//  cal.destroy();
  _dynarch_popupCalendar = null;
}

function showCalendar(id, date_format) {
    var sel = document.getElementById(id);

    if (_dynarch_popupCalendar != null) {
        _dynarch_popupCalendar.hide();
    }
    else {
    // first-time call, create the calendar.
        var cal = new Calendar(1, sel.value, CalendarSelected, CalendarCloseHandler);
        cal.weekNumbers = false;
        cal.showsTime = false;
        cal.showsOtherMonths = true;
        cal.setRange(start_cal_year, end_cal_year);
        cal.setDateFormat(date_format);

        _dynarch_popupCalendar = cal;
        cal.create();
    }

    _dynarch_popupCalendar.parseDate(sel.value);
    _dynarch_popupCalendar.sel = sel;
    _dynarch_popupCalendar.showAtElement(document.getElementById(id+'Link'), "Br");

    return false;
}
{/literal}
</script>
