<?php
/**
 * Users
 */

/**
 *  Mail 
 */
// Time limit to send mail spool at once
define('MAIL_SPOOL_TIMEOUT', 40); // sec
// TTL for mail in spool. After this period mail will be considered as obsolete
define('MAIL_SPOOL_TTL',    SECONDS_PER_DAY*3);


/**
 * Benchmark settings
 */
// General option to enable/disable benchmarking
define('BENCH',         false);
// Display bench result
define('BENCH_DISPLAY', true);
// Log bench result
define('BENCH_LOG',     false);
// Enable benchmarking only if specified GET param is set
define('BENCH_GET_PARAM', null);
// Limits by time and memory when log item should be marked by red
define('BENCH_TIME_MAX',    0.05);
define('BENCH_MEM_MAX',     0.2);
