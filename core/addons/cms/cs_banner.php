<?php

$presaved_content_section  = &cw_session_register('presaved_content_section', array());
$file_upload_data = &cw_session_register('file_upload_data');
$top_message = &cw_session_register('top_message');

cw_load('category', 'image', 'attributes','files');

cw_image_clear(array('cms_images'));

if ($REQUEST_METHOD == 'POST') {
  switch ($action) {
    case 'update_content_section':
    case 'add_new_content_section':
      if ($action == 'update_content_section' && empty($contentsection_id)) cw_header_location('index.php?target=cms&mode=add');

      if (!empty($content_section) && is_array($content_section)) {

        //$content_section = array_map('trim', $content_section);

        cw_core_process_date_fields($content_section, array(0 => array('start_date' => 0, 'end_date' => 1)));

        $content_section['orderby']    = intval($content_section['orderby']);
        $content_section['display_on_404']     = empty($content_section['display_on_404']) ? 'N' : 'Y';
        $content_section['active']     = empty($content_section['active']) ? 'N' : 'Y';
        $content_section['parse_smarty_tags']     = !empty($content_section['parse_smarty_tags']);
        $presaved_content_section = $content_section;

        /*
         * Validation
         */
        cw_include('include/lib/formvalidator/formvalidator.php', INCLUDE_NO_GLOBALS);

        // Errors
        $validator = new FormValidator();
        $validator->addValidation("service_code","req",cw_get_langvar_by_name('msg_ab_err_servicecode_is_empty'));
        $validator->addValidation("service_code","varname",cw_get_langvar_by_name('msg_ab_err_wrong_servicecode_format'));
        if (!$validator->ValidateForm($content_section)) {
			cw_add_top_message($validator->GetErrors(),'E');
            cw_header_location('index.php?target=cms&edit=Y&mode='.($action=='add_new_content_section'?'add':'update&contentsection_id='.$contentsection_id));
        }
        // Warnings
        $validator = new FormValidator();
        $validator->addValidation("name","req",cw_get_langvar_by_name('msg_ab_warn_empty_contentsection_name'));

        if ($content_section['type'] == 'html' || $content_section['type'] == 'image') 
          $validator->addValidation("url","req",cw_get_langvar_by_name('msg_ab_warn_empty_contentsection_url'));

//        $validator->addValidation("url","url",'Invalid URL');
        if ($content_section['type'] == 'image') $validator->addValidation("alt","req",cw_get_langvar_by_name('msg_ab_warn_empty_contentsection_alt_text'));
        if (!$validator->ValidateForm($content_section)) {
			cw_add_top_message($validator->GetErrors(),'W');
        }
        unset($validator);

        /*
         *  Insert or Update
         */
        if ($action == 'add_new_content_section') {
          $contentsection_id = cw_array2insert('cms', $content_section);
        }
        elseif ($action == 'update_content_section') {
          cw_array2update('cms', $content_section, "contentsection_id = '".$contentsection_id."'");
          db_query("DELETE FROM $tables[cms_restrictions] WHERE contentsection_id = '".$contentsection_id."'");
        }
        $data = array(
          'skin' => $content_section['skin']
        );
        cw_array2update('cms', $data, "service_code = '".addslashes($content_section['service_code'])."'");
        $presaved_content_section['contentsection_id'] = $contentsection_id;
        if ($current_language == $config['default_customer_language']) {
          $data = array(
            'alt'  => $content_section['alt'],
            'name' => $content_section['name'],
            'url'  => $content_section['url']
          );
          cw_array2update('cms', $data, "contentsection_id = '".$contentsection_id."'");
        }
        $data = array(
          'contentsection_id' => $contentsection_id,
          'code'      => $current_language,
          'alt'       => $content_section['alt'],
          'name'      => $content_section['name'],
          'url'       => $content_section['url']
        );
        cw_array2insert('cms_alt_languages', $data, true);

        cw_call('cw_attributes_save', array('item_id' => $contentsection_id, 'item_type' => 'AB', 'attributes' => $attributes, 'language' => $edited_language));
        if (!empty($content_section_clean_urls) && is_array($content_section_clean_urls) && !empty($contentsection_id)) {
            foreach ($content_section_clean_urls as $url) {
               if (trim($url['value'])=="") continue;
                   $data = array(
						'contentsection_id'   => $contentsection_id,
						'object_type' => 'URL',
						'value' => $url['value']
                  );
                cw_array2insert('cms_restrictions', $data, true);
              }
        }

        if (!empty($content_section_categories) && is_array($content_section_categories) && !empty($contentsection_id)) {
          foreach ($content_section_categories as $category_id) {
            if (intval($category_id)==0) continue;
            $data = array(
              'contentsection_id'   => $contentsection_id,
              'object_type' => 'C',
              'object_id' => intval($category_id)
            );
            cw_array2insert('cms_restrictions', $data, true);
          }
        }
        if (!empty($content_section_products) && !empty($content_section_products)) {
          $content_section_products = array_unique($content_section_products,SORT_NUMERIC);
          foreach ($content_section_products as $cms_product) {
			if (intval($cms_product['id'])==0) continue;
            cw_array2insert(
              'cms_restrictions',
              array(
                'contentsection_id'  => $contentsection_id,
                'object_type' => 'P',
                'object_id' => $cms_product['id']
              )
            );
          }
        }
        if (!empty($content_section_manufacturers) && is_array($content_section_manufacturers) && !empty($contentsection_id)) {
          foreach ($content_section_manufacturers as $manufacturer_id) {
            cw_array2insert(
              'cms_restrictions',
              array(
                'contentsection_id'       => $contentsection_id,
                'object_type' => 'M',
                'object_id' => $manufacturer_id
              )
            );
          }
        }

       if (isset($content_section_attributes)) {
          foreach ($content_section_attributes as $cs_attr) {
            $cs_attr_values = array();
            if (is_array($cs_attr['value'])) { 
              foreach ($cs_attr['value'] as $cs_attr_value) {
//cw_attributes_get_attribute_default_value - TODO
                $cs_value_string = cw_query_first_cell("select value from $tables[attributes_default] where attribute_id='$cs_attr[attribute_id]' and attribute_value_id='$cs_attr_value'");
                cw_array2insert('cms_restrictions', $st =array(
                  'contentsection_id' => $contentsection_id,
                  'object_type' => 'A',
                  'object_id' => $cs_attr['attribute_id'],
                  'operation' => $cs_attr['operation'],
                  'value_id' => !empty($cs_value_string)?$cs_attr_value:0,
                  'value' => !empty($cs_value_string)?$cs_value_string:$cs_attr_value
                ), true);
              } 
            } 
          }
        }  

        switch ($content_section['type']) {
          case 'staticpage':
          case 'staticpopup':
          case 'html':
            if (isset($content_section_content) && strlen($content_section_content) > 0 && !empty($contentsection_id)) {
                $data = array(
                  'content' => htmlspecialchars_decode(trim($content_section_content))
                );
                if ($current_language == $config['default_customer_language']) {
                  cw_array2update('cms', $data, "contentsection_id = '".$contentsection_id."'");
                }
                cw_array2update('cms_alt_languages', $data, "contentsection_id = '".$contentsection_id."' AND code = '".$current_language."'");
            }
            // no break here because 'html' type needs image processing as well
          case 'image':
            if (!empty($contentsection_id) && !empty($file_upload_data) && is_array($file_upload_data)) {
              $is_image_uploaded_and_saved = false;
              if (cw_image_check_posted($file_upload_data['cms_images'])) {
                if (cw_image_save($file_upload_data['cms_images'], array('id' => $contentsection_id, 'code' => $current_language))) $is_image_uploaded_and_saved = true;
              }
              if (!$is_image_uploaded_and_saved) {
				cw_add_top_message(cw_get_langvar_by_name('msg_ab_err_banner_image_not_uploaded_or_saved'), 'E');
                cw_header_location('index.php?target=cms&mode=add');
              }
            }
           break;
        }

        if (!empty($link_item_id) && !empty($link_attribute_id) && !empty($link_item_type)) {
            $link_item_id = intval($link_item_id);
            $link_attribute_id = intval($link_attribute_id);
            $link_item_type = substr($link_item_type,0,2);

            db_query("delete from $tables[attributes_values] where item_id='$link_item_id' and item_type='$link_item_type' and attribute_id='$link_attribute_id' and code='$current_language'");               

            cw_array2insert('attributes_values', 
                array('item_id' => $link_item_id, 
                      'item_type' => $link_item_type, 
                      'attribute_id' => $link_attribute_id,
                      'code' => $current_language, 
                      'value' => $contentsection_id
                )
            ); 
        }       
 
        cw_event('on_cms_update', array($contentsection_id, $content_section));
        
        $presaved_content_section = array();
        cw_header_location('index.php?target=cms&mode=update&contentsection_id='.$contentsection_id);
      }
      break;
  }
  cw_header_location('index.php?target=cms&mode=add');
}

$categories    = cw_ab_get_cms_categories($contentsection_id);
$products      = cw_ab_get_cms_products($contentsection_id);
$manufacturers = cw_ab_get_cms_manufacturers($contentsection_id);
$clean_urls    = cw_ab_get_cms_clean_urls($contentsection_id);
$restricted_attributes = cw_ab_get_cms_restrict_attributes($contentsection_id);

$skins = cw_files_get_dir($app_dir.'/skins/addons/cms/skins',2);
$skins = array_map('basename', $skins);

$presaved_content_section['image'] = 0; //cw_image_get('cms_images', intval($presaved_content_section['contentsection_id']));

$attributes = cw_func_call('cw_attributes_get', array('item_id' => 0, 'item_type' => 'AB', 'language' => $edited_language));

if (!empty($contentsection_id)) {
  $query = "SELECT *, service_code as service_code FROM $tables[cms] WHERE contentsection_id = '".intval($contentsection_id)."'";
  $content_section = cw_query_first($query);

  if (!empty($content_section) && is_array($content_section) && $mode != 'add') {
    $content_section_alt_languages = cw_query_first("SELECT name, alt, url, content FROM $tables[cms_alt_languages] WHERE contentsection_id = '".intval($contentsection_id)."' AND code = '".$current_language."'");
    if (!empty($content_section_alt_languages) && is_array($content_section_alt_languages)) {
      $content_section['name']    = $content_section_alt_languages['name'];
      $content_section['alt']     = $content_section_alt_languages['alt'];
      $content_section['url']     = $content_section_alt_languages['url'];
      $content_section['content'] = $content_section_alt_languages['content'];
    }
  }
  $content_section['image_id'] = 0;

  $content_section['image'] = cw_image_get('cms_images', $contentsection_id);

  $attributes = cw_func_call('cw_attributes_get', array('item_id' => $contentsection_id, 'item_type' => 'AB', 'language' => $edited_language));
} else {
	$content_section['service_code'] = $service_code;
}

if ($edit == 'Y') {
    $content_section = $presaved_content_section;
}

$smarty->assign('skins', $skins);
$smarty->assign('attributes', $attributes);
$smarty->assign('clean_urls', $clean_urls);
$smarty->assign('categories', $categories);
$smarty->assign('products', $products);
$smarty->assign('manufacturers', $manufacturers);
$smarty->assign('restricted_attributes', $restricted_attributes);

$smarty->assign('presaved_content_section', $presaved_content_section);
$smarty->assign('content_section', $content_section);
$smarty->assign('contentsection_id', $contentsection_id);
$smarty->assign('mode', $mode);

$smarty->assign('link_item_id', $link_item_id);
$smarty->assign('link_item_type', $link_item_type);
$smarty->assign('link_attribute_id', $link_attribute_id);

$smarty->assign('create_type', $create_type);

$smarty->assign('current_main_dir', 'addons');
$smarty->assign('current_section_dir', 'cms');
$smarty->assign('main', 'cs_banner');
