<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

/*
|--------------------------------------------------------------------------
| Sistema MLC 2
|--------------------------------------------------------------------------
*/
defined('TITLE') OR define('TITLE', 'TAD');

if (empty($_SERVER['HTTP_HOST']))
{
	$host = '';
}
else
{
	$host = $_SERVER['HTTP_HOST'];
}



switch ($host) {
        case 'tad.lujandecuyo.gob.ar':
            defined('SIS_BASE_URL') OR define('SIS_BASE_URL', 'http://localhost/');
            defined('SIS_AUTO_VER_PATH') OR define('SIS_AUTO_VER_PATH', '/');
            defined('SIS_SUB_DOMAIN') OR define('SIS_SUB_DOMAIN', 'tramites');
            break;
        default:
            defined('SIS_BASE_URL') OR define('SIS_BASE_URL', 'http://localhost/sistemamlc2/');
            defined('SIS_AUTO_VER_PATH') OR define('SIS_AUTO_VER_PATH', '/sistemamlc2/');
            defined('SIS_SUB_DOMAIN') OR define('SIS_SUB_DOMAIN', 'tramites');
            break;
}



defined('SIS_EMAIL_MODULO') OR define('SIS_EMAIL_MODULO', FALSE);
defined('SIS_BASE_URL') OR define('SIS_BASE_URL', 'http://localhost/sistemamlc2/');
defined('SIS_AUTO_VER_PATH') OR define('SIS_AUTO_VER_PATH', '/sistemamlc2/');
defined('SIS_SUB_DOMAIN') OR define('SIS_SUB_DOMAIN', 'tramites_online');
defined('SIS_AUD_DB') OR define('SIS_AUD_DB', 'wi_dev_aud');
defined('SIS_REST_SERVER') OR define('SIS_REST_SERVER', 'http://localhost/servermlc/');
defined('SIS_REST_SERVER2') OR define('SIS_REST_SERVER2', 'https://sistemamlc.lujandecuyo.gob.ar/ws2/');
// AUTH0
defined('SIS_AUTH_MODE') OR define('SIS_AUTH_MODE', 'local');
defined('SIS_AUTH0_CLIENT_ID') OR define('SIS_AUTH0_CLIENT_ID', 'vXRTVxk8cd7pZmKRFx3n7W9eyY7jT2ym');
defined('SIS_AUTH0_CLIENT_SECRET') OR define('SIS_AUTH0_CLIENT_SECRET', 'L1kICR8F8NAQjzzch-0xG6t7Vl2LalUMB3W7sTaDK4MfLCb2fajWdKuvQHOcaFO2');
defined('SIS_AUTH0_CONNECTION_ID') OR define('SIS_AUTH0_CONNECTION_ID', 'con_M3MnzkYcj8azkMGf');
defined('SIS_AUTH0_CONNECTION_NAME') OR define('SIS_AUTH0_CONNECTION_NAME', 'Username-Password-Authentication');
defined('SIS_AUTH0_DOMAIN') OR define('SIS_AUTH0_DOMAIN', 'lujandecuyo.us.auth0.com');
defined('SIS_AUTH0_MANAGEMENT_AUDIENCE') OR define('SIS_AUTH0_MANAGEMENT_AUDIENCE', 'https://lujandecuyo.us.auth0.com/api/v2/');

// ORO CRM
defined('SIS_ORO_ACTIVE') OR define('SIS_ORO_ACTIVE', FALSE);
defined('SIS_ORO_URL') OR define('SIS_ORO_URL', 'https://crm.lujandecuyo.gob.ar/api/rest/latest/');
defined('SIS_ORO_USER') OR define('SIS_ORO_USER', 'lcorsino');
defined('SIS_ORO_KEY') OR define('SIS_ORO_KEY', '8feb6d6fb58c78abd52b81fbfb2a407de5188726');

// AUTENTICAR
defined('SIS_AUTEN_ACTIVE') OR define('SIS_AUTEN_ACTIVE', TRUE);
defined('SIS_AUTEN_SECRET_AFIP') OR define('SIS_AUTEN_SECRET_AFIP', '5330e2db-bc3c-47ea-88c8-9ecb4c7b6059');
defined('SIS_AUTEN_SECRET_ANSES') OR define('SIS_AUTEN_SECRET_ANSES', 'd08dc697-29f6-446c-ae8a-9a8a8308c872');
defined('SIS_AUTEN_SECRET_MIARG') OR define('SIS_AUTEN_SECRET_MIARG', 'bd5e2c66-edcc-4469-b664-6ee33b3a289a');
defined('SIS_AUTEN_SECRET_RENAPER') OR define('SIS_AUTEN_SECRET_RENAPER', '');
defined('SIS_AUTEN_TRAMITE_URL') OR define('SIS_AUTEN_TRAMITE_URL', 'https://tad.lujandecuyo.gob.ar/auth/logout');
