<?php


/* 
this script converts images paths like ./xc_skin to ./files/images/{img type dir} 
*/

$tables2process = array('products_images_thumb', 'products_images_det', 'products_detailed_images');

function cw_magexch_transfer_images_after_import($img_table) {
    global $tables, $app_dir, $var_dirs;

    $images = cw_query("SELECT * FROM ".$tables[$img_table]." WHERE image_path LIKE '%./xc_skin%'");

    foreach ($images as $img) {
        $image_path = ltrim($img['image_path'], '.');
        $full_image_path = $app_dir.$image_path; 
/*
        print('<pre>');
        print_r(
            array($img,
                $full_image_path,
                file_exists($full_image_path)
            )
        ); 
        print('</pre>');
*/
        if (!file_exists($full_image_path)) {
            print("<b>FILE IS NOT FOUND!!!</b>&nbsp; $full_image_path <br>");
        }
        print("<hr>Processing file $full_image_path for item #$img[id] <br>");
        $f_name = basename($full_image_path);
        $new_filename = $var_dirs['images'].'/'.$img_table.'/'.$f_name;
        print("New file location: $new_filename ".(file_exists($new_filename)?" <b>ALREADY EXISTS</b><br>":" Will be created now<br>"));

        if (file_exists($new_filename)) {
            $is_file_same = (md5_file($new_filename) == md5_file($full_image_path));

            if ($is_file_same) 
                print("And this is already the same file<br>");

            if (!$is_file_same) {
                $new_pathinfo = pathinfo($new_filename);
                $new_filename = $new_pathinfo['dirname'].'/'.$new_pathinfo['filename'].'_'.$img['id'].'.'.$new_pathinfo['extension'];        
            }            

        }

        if (!file_exists($new_filename) || $is_file_same) {
            if (!file_exists($new_filename)) { 
                if (copy($full_image_path, $new_filename)) {
                    print("File is copied successfully<br>");  
                } else {
                   print("<b>ERROR OCCURRED ON FILE COPY</b>");
                }
            }
            $new_db_path = '.'.str_replace($app_dir, '', $new_filename);
            print("Saving new image path $new_db_path <br>");
            db_query("UPDATE ".$tables[$img_table]." SET image_path='$new_db_path' WHERE image_id=$img[image_id]");
        } else {
            print("<b>Cannot pick new name for copied file!!!</b><br>");
        }
    }
}

cw_magexch_transfer_images_after_import($tables2process[2]);
die;
