select pos.* from pos inner join xfer_products_SWE on pos.`Alternate Lookup` = xfer_products_SWE.catalogid where pos.`Custom Price 3` != '';
