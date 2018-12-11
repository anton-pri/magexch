{include_once_src file="main/include_js.tpl" src="addons/accessories/func.js"}
<table width="100%">
  <tr>
    <td valign="top">
      {include file="common/subheader.tpl" title=$lng.lbl_ac_accessories_list_settings}
      <br />
      <table>
        <tr>
          <td align="right" valign="top">
            <label>{$lng.lbl_ac_acc_display_thumbnail}:</label>
          </td>
          <td>&nbsp;</td>
          <td align="left" valign="top">
            <input type="checkbox" name="accessories_config[ac_acc_display_thumbnail]"{if $accessories_config.ac_acc_display_thumbnail eq "Y"} checked="checked"{/if}{if $read_only} disabled="disabled"{/if} onChange="javascript: accessoriesChangeFormActionField(this.form, 'update_accessories_config');" />
          </td>
        </tr>
        <tr>
          <td align="right" valign="top">
            <label>{$lng.lbl_ac_acc_display_options}:</label>
          </td>
          <td>&nbsp;</td>
          <td align="left" valign="top">
            <input type="checkbox" name="accessories_config[ac_acc_display_options]"{if $accessories_config.ac_acc_display_options eq "Y"} checked="checked"{/if}{if $read_only} disabled="disabled"{/if} onChange="javascript: accessoriesChangeFormActionField(this.form, 'update_accessories_config');" />
          </td>
        </tr>
        <tr>
          <td align="right" valign="top">
            <label>{$lng.lbl_ac_acc_display_qty_selector}:</label>
          </td>
          <td>&nbsp;</td>
          <td align="left" valign="top">
            <input type="checkbox" name="accessories_config[ac_acc_display_qty_selector]"{if $accessories_config.ac_acc_display_qty_selector eq "Y"} checked="checked"{/if}{if $read_only} disabled="disabled"{/if} onChange="javascript: accessoriesChangeFormActionField(this.form, 'update_accessories_config');" />
          </td>
        </tr>
        <tr>
          <td align="right" valign="top">
            <label>{$lng.lbl_ac_acc_display_price}:</label>
          </td>
          <td>&nbsp;</td>
          <td align="left" valign="top">
            <input type="checkbox" name="accessories_config[ac_acc_display_price]"{if $accessories_config.ac_acc_display_price eq "Y"} checked="checked"{/if}{if $read_only} disabled="disabled"{/if} onChange="javascript: accessoriesChangeFormActionField(this.form, 'update_accessories_config');" />
          </td>
        </tr>
        <tr>
          <td align="right" valign="top">
            <label>{$lng.lbl_ac_acc_display_weight}:</label>
          </td>
          <td>&nbsp;</td>
          <td align="left" valign="top">
            <input type="checkbox" name="accessories_config[ac_acc_display_weight]"{if $accessories_config.ac_acc_display_weight eq "Y"} checked="checked"{/if}{if $read_only} disabled="disabled"{/if} onChange="javascript: accessoriesChangeFormActionField(this.form, 'update_accessories_config');" />
          </td>
        </tr>
        <tr>
          <td align="right" valign="top">
            <label>{$lng.lbl_ac_acc_display_productcode}:</label>
          </td>
          <td>&nbsp;</td>
          <td align="left" valign="top">
            <input type="checkbox" name="accessories_config[ac_acc_display_productcode]"{if $accessories_config.ac_acc_display_productcode eq "Y"} checked="checked"{/if}{if $read_only} disabled="disabled"{/if} onChange="javascript: accessoriesChangeFormActionField(this.form, 'update_accessories_config');" />
          </td>
        </tr>
        <tr>
          <td align="right" valign="top">
            <label>{$lng.lbl_ac_acc_display_wholesale}:</label>
          </td>
          <td>&nbsp;</td>
          <td align="left" valign="top">
            <input type="checkbox" name="accessories_config[ac_acc_display_wholesale]"{if $accessories_config.ac_acc_display_wholesale eq "Y"} checked="checked"{/if}{if $read_only} disabled="disabled"{/if} onChange="javascript: accessoriesChangeFormActionField(this.form, 'update_accessories_config');" />
          </td>
        </tr>
        <tr>
          <td align="right" valign="top">
            <label>{$lng.lbl_ac_acc_display_qty_in_stock}:</label>
          </td>
          <td>&nbsp;</td>
          <td align="left" valign="top">
            <input type="checkbox" name="accessories_config[ac_acc_display_qty_in_stock]"{if $accessories_config.ac_acc_display_qty_in_stock eq "Y"} checked="checked"{/if}{if $read_only} disabled="disabled"{/if} onChange="javascript: accessoriesChangeFormActionField(this.form, 'update_accessories_config');" />
          </td>
        </tr>
        <tr>
          <td align="right" valign="top">
            <label>{$lng.lbl_ac_acc_display_columns}:</label>
          </td>
          <td>&nbsp;</td>
          <td align="left" valign="top">
            <input type="text" style="text-align: right;" size="4" maxlength="1" name="accessories_config[ac_acc_display_columns]" value="{$accessories_config.ac_acc_display_columns|default:"2"}"{if $read_only} disabled="disabled"{/if} onChange="javascript: accessoriesChangeFormActionField(this.form, 'update_accessories_config');" />
          </td>
        </tr>
      </table>
    </td>
    <td>&nbsp;</td>
    <td valign="top">
      {include file="common/subheader.tpl" title=$lng.lbl_ac_recommended_products_list_settings}
      <br />
      <table>
        <tr>
          <td align="right" valign="top">
            <label>{$lng.lbl_ac_rec_display_thumbnail}:</label>
          </td>
          <td>&nbsp;</td>
          <td align="left" valign="top">
            <input type="checkbox" name="accessories_config[ac_rec_display_thumbnail]"{if $accessories_config.ac_rec_display_thumbnail eq "Y"} checked="checked"{/if}{if $read_only} disabled="disabled"{/if} onChange="javascript: accessoriesChangeFormActionField(this.form, 'update_accessories_config');" />
          </td>
        </tr>
        <tr>
          <td align="right" valign="top">
            <label>{$lng.lbl_ac_rec_display_options}:</label>
            </td>
            <td>&nbsp;</td>
            <td align="left" valign="top">
              <input type="checkbox" name="accessories_config[ac_rec_display_options]"{if $accessories_config.ac_rec_display_options eq "Y"} checked="checked"{/if}{if $read_only} disabled="disabled"{/if} onChange="javascript: accessoriesChangeFormActionField(this.form, 'update_accessories_config');" />
          </td>
        </tr>
        <tr>
          <td align="right" valign="top">
            <label>{$lng.lbl_ac_rec_display_qty_selector}:</label>
          </td>
          <td>&nbsp;</td>
          <td align="left" valign="top">
            <input type="checkbox" name="accessories_config[ac_rec_display_qty_selector]"{if $accessories_config.ac_rec_display_qty_selector eq "Y"} checked="checked"{/if}{if $read_only} disabled="disabled"{/if} onChange="javascript: accessoriesChangeFormActionField(this.form, 'update_accessories_config');" />
          </td>
        </tr>
        <tr>
          <td align="right" valign="top">
            <label>{$lng.lbl_ac_rec_display_price}:</label>
          </td>
          <td>&nbsp;</td>
          <td align="left" valign="top">
            <input type="checkbox" name="accessories_config[ac_rec_display_price]"{if $accessories_config.ac_rec_display_price eq "Y"} checked="checked"{/if}{if $read_only} disabled="disabled"{/if} onChange="javascript: accessoriesChangeFormActionField(this.form, 'update_accessories_config');" />
          </td>
        </tr>
        <tr>
          <td align="right" valign="top">
            <label>{$lng.lbl_ac_rec_display_weight}:</label>
          </td>
          <td>&nbsp;</td>
          <td align="left" valign="top">
            <input type="checkbox" name="accessories_config[ac_rec_display_weight]"{if $accessories_config.ac_rec_display_weight eq "Y"} checked="checked"{/if}{if $read_only} disabled="disabled"{/if} onChange="javascript: accessoriesChangeFormActionField(this.form, 'update_accessories_config');" />
          </td>
        </tr>
        <tr>
          <td align="right" valign="top">
            <label>{$lng.lbl_ac_rec_display_productcode}:</label>
          </td>
          <td>&nbsp;</td>
          <td align="left" valign="top">
            <input type="checkbox" name="accessories_config[ac_rec_display_productcode]"{if $accessories_config.ac_rec_display_productcode eq "Y"} checked="checked"{/if}{if $read_only} disabled="disabled"{/if} onChange="javascript: accessoriesChangeFormActionField(this.form, 'update_accessories_config');" />
          </td>
        </tr>
        <tr>
          <td align="right" valign="top">
            <label>{$lng.lbl_ac_rec_display_wholesale}:</label>
          </td>
          <td>&nbsp;</td>
          <td align="left" valign="top">
            <input type="checkbox" name="accessories_config[ac_rec_display_wholesale]"{if $accessories_config.ac_rec_display_wholesale eq "Y"} checked="checked"{/if}{if $read_only} disabled="disabled"{/if} onChange="javascript: accessoriesChangeFormActionField(this.form, 'update_accessories_config');" />
          </td>
        </tr>
        <tr>
          <td align="right" valign="top">
            <label>{$lng.lbl_ac_rec_display_qty_in_stock}:</label>
          </td>
          <td>&nbsp;</td>
          <td align="left" valign="top">
            <input type="checkbox" name="accessories_config[ac_rec_display_qty_in_stock]"{if $accessories_config.ac_rec_display_qty_in_stock eq "Y"} checked="checked"{/if}{if $read_only} disabled="disabled"{/if} onChange="javascript: accessoriesChangeFormActionField(this.form, 'update_accessories_config');" />
          </td>
        </tr>
        <tr>
          <td align="right" valign="top">
            <label>{$lng.lbl_ac_rec_display_columns}:</label>
          </td>
          <td>&nbsp;</td>
          <td align="left" valign="top">
            <input type="text" style="text-align: right;" size="4" maxlength="1" name="accessories_config[ac_rec_display_columns]" value="{$accessories_config.ac_rec_display_columns}"{if $read_only} disabled="disabled"{/if} onChange="javascript: accessoriesChangeFormActionField(this.form, 'update_accessories_config');" />
          </td>
        </tr>
        <tr>
          <td align="right" valign="top">
            <label>{$lng.lbl_ac_rec_products_limit}:</label>
          </td>
          <td>&nbsp;</td>
          <td align="left" valign="top">
            <input type="text" style="text-align: right;" size="4" maxlength="3" name="accessories_config[ac_rec_products_limit]" value="{$accessories_config.ac_rec_products_limit}"{if $read_only} disabled="disabled"{/if} onChange="javascript: accessoriesChangeFormActionField(this.form, 'update_accessories_config');" />
          </td>
        </tr>
        <tr>
          <td align="right" valign="top">
            <label>{$lng.lbl_ac_rec_list_source}:</label>
          </td>
          <td>&nbsp;</td>
          <td align="left" valign="top">
            <select name="accessories_config[ac_rec_list_source]"{if $read_only} disabled="disabled"{/if} onChange="javascript: accessoriesChangeFormActionField(this.form, 'update_accessories_config');">
              <option value="S"{if $accessories_config.ac_rec_list_source eq "S"} selected="selected"{/if}>{$lng.lbl_ac_sales_hits}</option>
              <option value="T"{if $accessories_config.ac_rec_list_source eq "T"} selected="selected"{/if}>{$lng.lbl_ac_bought_together}</option>
            </select>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
