SELECT @section_id:=section_id FROM cw_register_fields_sections WHERE name='photo' AND type='U';
SELECT @field_id:=field_id FROM cw_register_fields WHERE section_id=@section_id AND field='image';

DELETE FROM cw_register_fields WHERE section_id = @section_id;
DELETE FROM cw_register_fields_avails WHERE field_id = @field_id;

INSERT INTO `cw_register_fields` (`section_id`, `field`, `type`, `variants`, `def`, `orderby`, `is_non_modify`) VALUES (@section_id, 'image', 'D', '', '', -1, 1);
SET @field_id = LAST_INSERT_ID();

INSERT INTO `cw_register_fields_avails` (`field_id`, `area`, `is_avail`, `is_required`) VALUES 
(@field_id, 'A', 1, 0),
(@field_id, 'C', 1, 0),
(@field_id, 'C_0', 1, 0),
(@field_id, 'C_2', 1, 0),
(@field_id, 'C_1', 1, 0),
(@field_id, '#A', 1, 0);