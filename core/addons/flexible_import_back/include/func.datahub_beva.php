<?php

function cw_dh_BevAFeedsMunge() {
    global $tables, $config;
    $sql = "SELECT 
                'Feed_BEVA' as `Source`, 
                u.`current` as `current`, 
                u.`confstock` as `confstock`, 
                u.`companies` as `wholesaler`, 
                u.`producer` as `producer`, 
                u.`bdesc` AS `Wine`, 
                Left(Trim(Replace(Replace(Replace(Replace(If(InStr(u.`bdesc`,'ml'),Left(u.`bdesc`,
                  IF(INSTR(trim(u.`bdesc`), ' ') > 0,
                     length( trim( 
                     SUBSTRING(u.`bdesc`,1, instr(u.`bdesc`, 'ml'))
                     ) ) - length( right( trim( 
                     SUBSTRING(u.`bdesc`,1, instr(u.`bdesc`, 'ml'))
                     ) , instr( reverse( trim( 
                     SUBSTRING(u.`bdesc`,1, instr(u.`bdesc`, 'ml'))
                    ) ) , ' ' ) -1 ) )
                  , 0)
                  ),u.`bdesc`),Coalesce(u.`Vintage`,'~'),''),Coalesce(u.`Producer`,'~'),''),If(IsNull(u.`Producer`),'~',Right(u.`Producer`,LENGTH(u.`Producer`)-IF(INSTR(trim( u.`Producer` ), ' ') > 0, length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , ' ' ) -1 ) ), 0))),''),If(IsNull(u.`Producer`) or not Instr(u.`Producer`,' '),'~',Mid(
                  IF(INSTR(trim( u.`Producer` ), ' ') > 0,
                     length( trim( 
                     SUBSTRING(u.`Producer`,1, 
                     IF(INSTR(trim( u.`Producer` ), ' ') > 0,
                        length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , ' ' ) -1 ) )
                      , 0)
                     )
                     ) ) - length( right( trim( 
                  SUBSTRING(u.`Producer`,1, 
                  IF(INSTR(trim( u.`Producer` ), ' ') > 0,
                       length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , ' ' ) -1 ) )
                  , 0)
                  )
                  ) , instr( reverse( trim( 
                  SUBSTRING(u.`Producer`,1, 
                  IF(INSTR(trim( u.`Producer` ), ' ') > 0,
                      length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , ' ' ) -1 ) )
                  , 0)
                  )
                  ) ) , ' ' ) -1 ) )   
                  , -1)
                  +1,
                  IF(INSTR(trim( u.`Producer` ), ' ') > 0,
                  length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , ' ' ) -1 ) )
                  , 0)
                                                -1-
                                                IF(INSTR(trim( u.`Producer` ), ' ') > 0,
                                                
                                                length( trim( 
                                                SUBSTRING(u.`Producer`,1, 
                                                IF(INSTR(trim( u.`Producer` ), ' ') > 0,
                                                        length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , ' ' ) -1 ) )
                                                , 0)
                                                
                                                )
                                                ) ) - length( right( trim( 
                                                SUBSTRING(u.`Producer`,1, 
                                                IF(INSTR(trim( u.`Producer` ), ' ') > 0,
                                                        length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , ' ' ) -1 ) )
                                                , 0)
                                                
                                                )
                                                ) , instr( reverse( trim( 
                                                SUBSTRING(u.`Producer`,1, 
                                                IF(INSTR(trim( u.`Producer` ), ' ') > 0,
                                                        length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , ' ' ) -1 ) )
                                                , 0)
                                                )
                                                 ) ) , ' ' ) -1 ) )   
                                                
                                                , -1)
                                                +1)
                                                ),'')),255) as `Name`,

                                                u.`vintage` AS `Vintage`, 
                                                If(IsNull(u.`size`) Or u.`size`='0','',If(u.`size`='1500','1.5Ltr',If(u.`size`='3000','3.0Ltr',If(u.`size` <1000,
                                                CONCAT(CAST(u.`size` as CHAR) , 'ml'),
                                                If(u.`size`>=1000,CONCAT(CAST(Round(CAST(u.`size` as DECIMAL(10,2))/1000,3) as CHAR) , 'Ltr'),'Other'))))) as `Size`, 
                                                u.`xref` as `xref`, 
                                                u.`botpercase` AS `bottles_per_case`,
                                                null as `catalogid`,
                                                `r`.`region_2` AS `Region`, 
                                                `r`.`region_1` AS `country`, 
                                                ucwords(u.`grape`) AS `varietal`, 
                                                `r`.`region_3` AS `Appellation`, 
                                                `r`.`region_4` AS `sub-appellation`, 
                                                Round(If(IsNull(u.`case_price`) or u.`case_price` = '0',u.`bot_price`,u.`case_price`/u.`botpercase`),2) AS `cost`, 
                                                null AS `Parker_rating`, 
                                                null AS `Parker_review`,
                                                null AS `Spectator_rating`, 
                                                null AS `Spectator_review`, 
                                                null AS `Tanzer_rating`, 
                                                null AS `Tanzer_review`,
                                                null as `description`
                                                FROM $tables[datahub_BevAccessFeeds] u  LEFT JOIN $tables[datahub_beva_reg_text] r on u.`reg_text` = `r`.`reg_text`
                                                WHERE (u.`descriptio` not like '%Combo%' and u.`descriptio` not like '%PK%' and ((current = 'Y' and confstock = 'Y') 
                                                or InStr('".$config['flexible_import']['BevA_wholesalers_always_on']."',
                                                
                                                Trim(Left(CONCAT(Coalesce(u.`companies`,'xxx') , ' '),Instr(CONCAT(Coalesce(u.`companies`,'xxx') , ' '),' ')))) > 0) 
                                                
                                                ) and LENGTH(Trim(Coalesce(u.`companies`,' '))) > 0";
                return $sql;
        }

function cw_dh_insert_beva() {
    global $tables;
    $query = cw_dh_BevAFeedsMunge();

    $sql = "INSERT INTO $tables[datahub_import_buffer] ( Source, wholesaler, Producer, Wine, Name, Vintage, `size`, item_xref, item_xref_bot_per_case, Region, country, varietal, Appellation, `sub-appellation`, item_xref_cost_per_bottle, Parker_rating, Parker_review, Spectator_rating, Spectator_review, Tanzer_rating, Tanzer_review, Description, store_id )
                SELECT `a`.`Source` AS Source, `a`.`wholesaler` AS wholesaler, ucwords(`a`.`producer`) AS producer, ucwords(`a`.`Wine`) AS Wine, ucwords(`a`.`Name`) AS Name, `a`.`Vintage` AS Vintage, `a`.`Size` AS `Size`, `a`.`xref` AS xref, `a`.`bottles_per_case` AS bottles_per_case,  ucwords(`a`.`Region`) AS Region, ucwords(`a`.`country`) AS country, `a`.`varietal` AS varietal, ucwords(`a`.`Appellation`) AS Appellation, ucwords(`a`.`sub-appellation`) AS `sub-appellation`, `a`.`cost` AS cost, `a`.`Parker_rating` AS Parker_rating, `a`.`Parker_review` AS Parker_review, `a`.`Spectator_rating` AS Spectator_rating, `a`.`Spectator_review` AS Spectator_review, `a`.`Tanzer_rating` AS Tanzer_rating, `a`.`Tanzer_review` AS Tanzer_review, `a`.`description`, '1'
    FROM ($query) AS a";

    cw_csvxc_logged_query($sql);
}

function cw_dh_update_beva_inventory() {
    global $tables, $config;

        cw_csvxc_logged_query("UPDATE $tables[datahub_import_buffer] AS i SET item_xref_qty_avail = '0' WHERE CAST(Left(coalesce(item_xref,'xxx'),3) as SIGNED) > 0");

        cw_csvxc_logged_query("
            UPDATE $tables[datahub_import_buffer] b, $tables[datahub_BevAccessFeeds] f SET
                b.item_xref_qty_avail = 
                    If(
                          (
                              (f.current = 'Y' and f.confstock = 'Y') OR 
                              InStr('".$config['flexible_import']['BevA_wholesalers_always_on']."',
                                     Trim(
                                         Left(
                                             CONCAT(coalesce(f.companies,'xxx'), ' '),
                                             Instr(CONCAT(coalesce(f.companies,'xxx') , ' '),' ')                                                    
                                             )
                                         )
                                   ) > 0
                          )
                          AND NOT Instr('".$config['flexible_import']['BevA_wholesalers_exclude']."', 
                                         Trim(
                                             Left(
                                                 CONCAT(coalesce(f.companies,'xxx'), ' '),
                                                 Instr(CONCAT(coalesce(f.companies,'xxx') , ' '),' ')                                                                                                   )
                                             )
                                       ),
                          9999,0
                      ),
                b.item_xref_min_price = 0, 
                b.item_xref_bot_per_case = CAST(coalesce(f.`botpercase`,'12') as SIGNED), 
                b.item_xref_cost_per_bottle = Round(f.bot_price,2), 
                b.item_xref_cost_per_case = 
                    Round(
                        If(coalesce(f.case_price,'0') = '0', 
                           f.bot_price*(CAST(coalesce(f.`botpercase`,'12') as SIGNED)),
                           f.case_price
                          )
                        ,2
                    ) 
            WHERE f.xref=b.item_xref
        ");
}

function cw_dh_apply_splitcase_to_cost() {
    global $tables;

    $sql = "UPDATE ($tables[datahub_import_buffer] AS i 
            INNER JOIN $tables[datahub_BevAccessFeeds] AS f 
            ON i.item_xref = f.xref) 
            LEFT JOIN $tables[datahub_splitcase_charges] AS c 
            ON Trim(Left(coalesce(CONCAT(f.companies , ' '),'x '),
            InStr(coalesce(CONCAT(f.companies , ' '),'x '),' ')))=c.company 
            SET split_case_charge = coalesce(c.charge*If(coalesce(i.item_xref_bot_per_case,12) > 1 
            AND InStr(c.company,'COLONY'),12/coalesce(i.item_xref_bot_per_case,12),
            If(coalesce(i.item_xref_bot_per_case,12) = 1,0,1)),0.75)";

    cw_csvxc_logged_query($sql);
}
