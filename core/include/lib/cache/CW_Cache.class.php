<?php
require_once('Cache_Lite/Cache/Lite.php');

class CW_Cache extends Cache_Lite
{
    
   function _setFileName($id, $group) {
       
       // Include group as part of cache file path
       
       $_cacheDir = $this->_cacheDir;
       
       $this->_cacheDir = $this->_cacheDir.'cache_'.$group.'/';
       
       $fname = parent::_setFileName($id, $group);
       
       $this->_cacheDir =$_cacheDir;
       
       return $fname;
   }

    
    function _write($data) {

        // Create all dirs in cache path
        $dirs_to_create = explode('/',rtrim(str_replace(array($this->_cacheDir,$this->_fileName),'',$this->_file),'/'));
        $root = $this->_cacheDir;
        foreach ($dirs_to_create as $dirname) {
            $root = $root . $dirname. '/';
            if (!(@is_dir($root))) {
                @mkdir($root, $this->_hashedDirectoryUmask);
            }           
        }
        
        return parent::_write($data);
    }

    
    function raiseError($msg, $code) {

        // Do not use PEAR as in default
        if (function_exists('cw_log_add')) {
          cw_log_add('cache_lite', array('msg'=>$msg, 'code'=>$code, 'pearErrorMode'=>$this->_pearErrorMode));
        }
        return $code;
    }


    function _cleanDir($dir, $group = false, $mode = 'ingroup') {
        
       $_hashedDirectoryLevel = $this->_hashedDirectoryLevel;
       
       $this->_hashedDirectoryLevel = 1; // Force recursive cleanup
       
       $r = parent::_cleanDir($dir, $group, $mode);
       
       $this->_hashedDirectoryLevel =$_hashedDirectoryLevel;
       
       return $r;        
    }


}
