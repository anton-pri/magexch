{jstabs}
default_tab="{$js_tab|default:"info"}"

[info]
title="{$lng.lbl_giftreg_edit_event_info|escape:javascript}"
template="addons/estore_gift/event/info.tpl"

{if $event_id}
[wishlist]
title="{$lng.lbl_giftreg_view_wishlist|escape:javascript}"
template="addons/estore_gift/event/wishlist.tpl"

[recipients]
title="{$lng.lbl_giftreg_edit_recipients_list|escape:javascript}"
template="addons/estore_gift/event/recipients.tpl"

[send]
title="{$lng.lbl_giftreg_send_notification|escape:javascript}"
template="addons/estore_gift/event/send.tpl"

[guestbook]
title="{$lng.lbl_giftreg_edit_guestbook|escape:javascript}"
template="addons/estore_gift/event/guestbook.tpl"
{/if}

{/jstabs}

{include file='tabs/js_tabs.tpl'}
