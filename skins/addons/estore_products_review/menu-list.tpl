
<tr>
    <td valign="top" height="15" class="Text">
        {capture name="page_url"}{pages_url var="estore_testimonials"}{/capture}
        {include file='buttons/button.tpl' button_title=$lng.lbl_testimonials href=$smarty.capture.page_url}
    </td>
</tr>