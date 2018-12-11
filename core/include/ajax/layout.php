<?php
cw_load('web');
if ($width)
    cw_web_set_layout_width($layout_id, $id, $width, $height);
else
    cw_web_set_layout_xy($layout_id, $id, $x, $y, $display, $font, $font_size, $decoration, $font_weight, $font_style, $color);
exit(0);
?>
