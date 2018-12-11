<?php
if (!defined('APP_START')) die('Access denied');

function cw_magnifier_create($imageFile, $image_id) {
	global $config, $tables, $app_dir, $all_languages, $var_dirs;

	if(!file_exists($imageFile))
		return false;

	$blockSize = 200; # one tile for image
	$imagesPerFolder = 256;
	$jpegQuality = 85;
	
	$dirName = $var_dirs['images'].'/magnifier_images/'.$image_id;
		
	list($width, $height, $type) = getimagesize($imageFile);
	
	if (!is_dir($dirName))
		mkdir($dirName);

	$iH = imagecreatefromstring(file_get_contents($imageFile));
	
	# divide to find # of levels
	$levelsNeeded = 0;
	$levelsDict = array();
	$currentSize = max($width, $height);

	if (!is_dir($dirName))
		mkdir($dirName);

	if (!is_dir("$dirName/TileGroup0"))
		mkdir("$dirName/TileGroup0");

	while(true)
	{
		$currentSize /= 2;
		$levelsNeeded += 1;
	
		$currentLevel = $levelsNeeded;
		$currentWidth = $width;
		$currentHeight = $height;
	
		$numBlocks = 0;
	
		while ($currentLevel >= 0) {
			$levelsDict[$currentLevel] = array();
	
			if ($currentWidth != $width or $currentHeight != $height) {
				$ciH = imagecreatetruecolor($currentWidth, $currentHeight);
				imagecopyresized($ciH, $iH, 0, 0, 0, 0, $currentWidth, $currentHeight, $width, $height);
			}
			else
				$ciH = $iH;
	
			$blocksWidth = ceil($currentWidth / $blockSize);
			$blocksHeight = ceil($currentHeight / $blockSize);
	
			$currentTileGroup = 0;
	
			foreach(range(0, $blocksHeight-1) as $blockVert) {
				$levelsDict[$currentLevel][$blockVert] = array();
				foreach(range(0, $blocksWidth-1) as  $blockHoriz) {
					if (!in_array('dimensions', $levelsDict[$currentLevel]))
						$levelsDict[$currentLevel]['dimensions'] = array($blocksWidth, $blocksHeight);
				
					$minCornerX = $blockSize * $blockHoriz;
					$minCornerY = $blockSize * $blockVert;
				
					$maxCornerX = min($minCornerX + $blockSize, $currentWidth);
					$maxCornerY = min($minCornerY + $blockSize, $currentHeight);
			  
					$levelsDict[$currentLevel][$blockVert][$blockHoriz] = imagecreatetruecolor($maxCornerX-$minCornerX, $maxCornerY-$minCornerY);
					imagecopy($levelsDict[$currentLevel][$blockVert][$blockHoriz], $ciH, 0, 0, $minCornerX, $minCornerY, $maxCornerX-$minCornerX, $maxCornerY-$minCornerY);
	
	//echo $currentHeight.'x'.$currentWidth."\n";
	//echo $minCornerX.','.$minCornerY.'-'.$maxCornerX.','.$maxCornerY.' file:'."$currentLevel-$blockHoriz-$blockVert\n";
	
					imagejpeg($levelsDict[$currentLevel][$blockVert][$blockHoriz], "$dirName/TileGroup0/$currentLevel-$blockHoriz-$blockVert.jpg", $jpegQuality);
				
					$numBlocks += 1;
				
					unset($levelsDict[$currentLevel][$blockVert][$blockHoriz]);
				}
			}
			$currentLevel -= 1;
	
			$currentWidth = floor($currentWidth / 2);
			$currentHeight = floor($currentHeight / 2);
		}
	
		if ($currentSize <= $blockSize)
			break;
	}
	
	file_put_contents("$dirName/ImageProperties.xml", '<IMAGE_PROPERTIES WIDTH="'.$width.'" HEIGHT="'.$height.'" NUMTILES="'.$numBlocks.'" NUMIMAGES="1" VERSION="1.8" TILESIZE="'.$blockSize.'" />');
	
	if ($numBlocks > 256) {
		$reBlockCounter = 0;
		$reTileGroup = 0;
		foreach(range(1, $levelsNeeded + 1) as $level) {
			foreach(range(1, $levelsDict[$level]['dimensions'][1]) as $yblock) {
				foreach(range(1, $levelsDict[$level]['dimensions'][0]) as $xblock) {
					if (($reBlockCounter % 256) == 0 and ($reBlockCounter != 0)) {
						$reTileGroup += 1;
						if (!is_dir($dirName."/TileGroup".$reTileGroup))
							mkdir($dirName."/TileGroup".$reTileGroup);
					}
					if ($reTileGroup > 0)
						rename("$dirName/TileGroup0/$level-$xblock-$yblock.jpg", "$dirName/TileGroup$reTileGroup/$level-$xblock-$yblock.jpg");
					$reBlockCounter += 1;
				}
			}
		}
	}

	if($imageFile != $from)
	unlink($imageFile);

	return $dirName;
}

function cw_zoomer_remove($dest)
{
	global $tables, $app_dir;

	$dirName = $app_dir."/".$dest;

	if(is_dir($dirName))
	{
		if(substr($dirName,-1,1)!="/") $dirName .= "/";
	cw_zoomer_tree($dirName);
	rmdir($dirName);
	}
	elseif(file_exists($dirName))
		unlink($dirName);
}

function cw_zoomer_tree($dir,$int="")
{
	if(is_dir($dir.$int))
	if($handle = opendir($dir.$int))
	{
	while (($file = readdir($handle)))
	{
		$full = $int.$file;
		$is_dir = is_dir($full_ = $dir.$full);
		if($is_dir) { $full.="/"; $tmp = $full;} else $tmp = $file;
		if(!preg_match("/\.{1,2}\/$/",$full))
		{
			if($is_dir)
			{
				cw_zoomer_tree($dir,$full);
				rmdir($full_);
			}
			else
				unlink($full_);
		}
	}
    	closedir($handle);
	}
}

function cw_zoomer_resize($from, $prefix, $dest)
{
	global $config, $app_dir, $all_languages;

	$prefix = strtolower($prefix);

# name='fdwidth', comment='Width, px', value='423', category='Watermark';
# name='fdheight', comment='Height, px', value='566', category='Watermark';
# name='fdquality', comment='Quality, %', value='90', category='Watermark';
# name='fdtype', comment='Filetype', value='jpg', category='Watermark', type='selector', variants='png:png\njpg:jpg';

	if(!file_exists($from))
		return false;

	list($sw, $sh, $type) = getimagesize($from);

	if($type == 2)
		$from_img = ImageCreateFromJPEG($from);
	elseif($type == 3)
		$from_img = ImageCreateFromPNG($from);
	else
		$from_img = false;

	if($from_img)
	{
		list($w_,$h_) = array($config["Watermark"][$prefix."width"], $config["Watermark"][$prefix."height"]); // TOFIX: these config params are empty

		list($sw_, $sh_) = $sw/$sh > $w_/$h_ ? array(round($sh/$h_*$w_), $sh) : array($sw, round($sw/$w_*$h_));
		$to_img = ImageCreateTrueColor($w_, $h_);

		ImageCopyResampled($to_img, $from_img, 0, 0, round(($sw-$sw_)/2), round(($sh-$sh_)/2), $w_, $h_, $sw_, $sh_);

		$dirname__= "images/".strtoupper($prefix)."/".$dest;
		$dirname_ = $app_dir."/".$dirname__;

		if($config["Watermark"][$prefix."water"]=="Y")
		{
			if (!is_dir($dirname_))
				mkdir($dirname_);

			$tmp = cw_zoomer_save_image($to_img, $prefix, false, 100); # save temporary file for watermarking :)

			foreach($all_languages as $code)
			{
				$dirname = $dirname_."/".$code["code"];

                $imgf = cw_zoomer_save_image($to_img, $prefix, $dirname);
			}

		}
		else
			$imgf = cw_zoomer_save_image($to_img, $prefix, $dirname_);


		return $imgf ? $dirname__.".".$config["Watermark"][$prefix."type"] : false;
	}
	else
		return false;
}

function cw_zoomer_save_image($to_img, $prefix, $dest = false, $q = false)
{
	global $config;

	if($q)
		;
	elseif($config["Watermark"][$prefix."quality"]>100)
		$q = 100;
	elseif($config["Watermark"][$prefix."quality"]<1)
		$q = 1;
	else
		$q = $config["Watermark"][$prefix."quality"];

	if(!$dest)
		$dest = cw_temp_store("");

	$dest .= ".".$config["Watermark"][$prefix."type"]; 

	if($config["Watermark"][$prefix."type"]=="png")
	{
		$q = 9-floor(0.09*$q); # 0..9, where 0 - no compression
		ImagePNG($to_img, $dest, $q);
	}
	else
		ImageJPEG($to_img, $dest, $q);

	return $dest;
} 

?>
