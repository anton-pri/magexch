{capture name=section}
<div class="block transparent">
{jstabs}
default_tab={$js_tab|default:'arrivals'}

[arrivals]
title={$lng.lbl_arrivals}
template="admin/sections/arrivals.tpl"

[hot_deals]
title = "{$lng.lbl_hot_deals}"
template="admin/sections/hot_deals.tpl"

[clearance]
title = "{$lng.lbl_clearance}"
template="admin/sections/clearance.tpl"

[super_deals]
title = "{$lng.lbl_super_deals}"
template="admin/sections/super_deals.tpl"

[accessories]
title = "{$lng.lbl_accessories}"
template="admin/sections/accessories.tpl"

[bottom_line]
title = "{$lng.lbl_bottom_line}"
template="admin/sections/bottom_line.tpl"

{/jstabs}

{include file='admin/tabs/js_tabs.tpl' style="default"}
</div>
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_special_sections local_config='special_sections'}
