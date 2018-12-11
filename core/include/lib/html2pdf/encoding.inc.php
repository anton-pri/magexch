<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/encoding.inc.php,v 1.2 2008-06-08 18:29:03 cvs Exp $

/**
 * Converts a hexadecimal string  representing UCS-2 character code to
 * UTF-8 encoding
 */
function hex_to_utf8($code) {
  return code_to_utf8(hexdec($code));
}

/**
 * Converts an UTF8-encoded character to UCS-2 integer code
 * TODO: handle sequence incorrect length
 */
function utf8_to_code($utf8) {
  $code = 0;

  if ((ord($utf8{0}) & 0xF0) == 0xF0) {
    // 4-byte sequence
    $code = 
      ((ord($utf8{0}) & 0x07) << 18) | 
      ((ord($utf8{1}) & 0x3F) << 12) | 
      ((ord($utf8{2}) & 0x3F) <<  6) | 
      (ord($utf8{3}) & 0x3F);
  } elseif ((ord($utf8{0}) & 0xE0) === 0xE0) {
    // 3-byte sequence
    $code = 
      ((ord($utf8{0}) & 0x0F) << 12) | 
      ((ord($utf8{1}) & 0x3F) <<  6) | 
      (ord($utf8{2}) & 0x3F);
  } elseif ((ord($utf8{0}) & 0xC0) === 0xC0) {

    // 2-byte sequence
    $code = 
      ((ord($utf8{0}) & 0x1F) << 6) | 
      (ord($utf8{1}) & 0x3F);
  } else {
    // Single-byte sequence
    $code = ord($utf8);
  };
  
  return $code;
}

/**
 * Converts an integer UCS-2 character code to UTF-8 encoding
 */
function code_to_utf8($code) {
  if ($code < 128) {
    return chr($code);
  };

  if ($code < 2048) {
    return chr(0xC0 | (($code >> 6) & 0x1F)) . chr(0x80 | ($code & 0x3F));
  };

  if ($code < 65536) {
    return chr(0xE0 | (($code >> 12) & 0x0F)) . chr(0x80 | (($code >> 6) & 0x3F)) . chr(0x80 | ($code & 0x3F));
  };

  return 
    chr(0xF0 | (($code >> 18) & 0x07)) . 
    chr(0x80 | (($code >> 12) & 0x3F)) . 
    chr(0x80 | (($code >>  6) & 0x3F)) . 
    chr(0x80 | ($code & 0x3F));
}

?>