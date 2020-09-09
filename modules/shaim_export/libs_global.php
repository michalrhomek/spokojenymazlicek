<?php

if (class_exists('LibsGlobal')) { # If loaded, return true #
    return true;
}

Class LibsGlobal
{

    public function __construct()
    {


        if (PHP_MAJOR_VERSION < 5) {
            die('Only for PHP5 and newer!');
        } elseif (version_compare(PHP_VERSION, '5.3.0', '<')) {
            echo 'low PHP - ' . PHP_VERSION;
            defined('LOW_PHP') || define('LOW_PHP', true);
        } else {
            defined('LOW_PHP') || define('LOW_PHP', false);
        }
        if (!ob_get_status()) {
            ob_start('ob_gzhandler');
        }
        mb_internal_encoding('UTF-8');

        $this->start = microtime(true);
        if ($_SERVER['REMOTE_ADDR'] == '77.236.206.161' || $_SERVER['REMOTE_ADDR'] == '77.236.208.97' || $_SERVER['REMOTE_ADDR'] == '77.236.208.111' || $_SERVER['REMOTE_ADDR'] == '77.236.220.152' || $_SERVER['REMOTE_ADDR'] == '90.183.26.11') {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL ^ E_DEPRECATED);
            ini_set('error_reporting', E_ALL ^ E_DEPRECATED);
            defined('SHAIM') || define('SHAIM', true);
        } else {
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
            error_reporting(0);
            ini_set('error_reporting', 0);
            defined('SHAIM') || define('SHAIM', false);
        }
        $this->max_execution_time = ini_get('max_execution_time');
        if ($this->max_execution_time < 300) {
            ini_set('max_execution_time', 300);
        }

        ini_set('max_input_time', 300);
        if ((int)ini_get('memory_limit') < 512) {
            ini_set('memory_limit', '512M');
        }
        ini_set('date.timezone', 'Europe/Prague');
        date_default_timezone_set('Europe/Prague');
        ini_set('log_errors', 1);
        ini_set('error_log', 'rest/errors.log');

        $this->Connect();


        echo '<pre>';
    }

    protected function human_filesize($file, $full_path = false, $decimals = 2)
    {

        //$tmp_file_location = ($full_path == false) ? $this->location . $file : $file;
        if (SHOP == 1) {
            $tmp_file_location = _PS_ROOT_DIR_ . '/xml/' . $file;
        } elseif (SHOP == 2) {
            $tmp_file_location = DIR_APPLICATION . '../xml/' . $file;
        } elseif (SHOP == 3) {
            $tmp_file_location = $this->config_path . '/' . $file;
        }

        if (!file_exists($tmp_file_location)) {
            return 0;
        }
        $bytes = filesize($tmp_file_location);
        $sz = array('B', 'K', 'M', 'G', 'T', 'P');
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }


    public function Sanitize($type, $value0, $format = 's', $inout = 'o')
    {

        if (empty($format)) {
            $this->Log('Bad Sanitize format ' . $format, 'die');
        }
        $value = trim($value0);
        Switch ($type) {
            /*
              filter_input()
              INPUT_SESSION (Not yet implemented)
              INPUT_REQUEST (Not yet implemented)
             */
            case 'SER':
            case 'SERVER':
                $value = (isset($_SERVER[$value0]) ? $_SERVER[$value0] : '');
                // $value = htmlspecialchars($_SERVER[$value0], ENT_QUOTES);
                break;
#case 'SES': case 'SESS': case 'SESSION':
#    $value = (isset($_SESSION[$value])) ? $_SESSION[$value] : '';
#    break;
            case 'P':
            case 'POS':
            case 'POST':
                if (!LOW_PHP) {
                    $value = filter_input(INPUT_POST, $value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                } else {
                    $value = htmlspecialchars($_POST[$value], ENT_QUOTES);
                }
                break;
            case 'G':
            case 'GET':
                if (!LOW_PHP) {
                    $value = filter_input(INPUT_GET, $value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                } else {
                    $value = htmlspecialchars($_GET[$value], ENT_QUOTES);
                }
                break;
#case 'F': case 'FIL': case 'FILE': case 'FILES':
#    $value = (isset($_FILES[$value])) ? $_FILES[$value] : '';
#    break;
#case 'R': case 'REQ': case 'REQUEST':
#    $value = (isset($_REQUEST[$value])) ? $_REQUEST[$value] : '';
#               break;
#           case 'E': case 'ENV':
#               $value = (filter_input(INPUT_ENV, $value, FILTER_SANITIZE_FULL_SPECIAL_CHARS)) ? filter_input(INPUT_ENV, FILTER_SANITIZE_FULL_SPECIAL_CHARS, $value) : '';
#               break;
#           case 'C': case 'COO': case 'COOKIE': case 'COOKIES':
#               $value = (filter_input(INPUT_COOKIE, $value, FILTER_SANITIZE_FULL_SPECIAL_CHARS)) ? filter_input(INPUT_COOKIE, $value, FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';
#               break;
        }

        if ($format == 's') {
            $function = (mysqli_ping($this->link)) ? 'mysqli_real_escape_string' : 'addslashes';
            $ret = (isset($value)) ? trim(($inout == 'o') ? $function($this->link, $value) : $value) : false;

        } elseif ($format == 'i') {
            $ret = (isset($value)) ? (int)abs($value) : false;
        } elseif ($format == 'f') {
            $ret = (isset($value)) ? (float)$value : false;
        } elseif ($format == 'b') {
            $ret = (isset($value)) ? (bool)$value : false;
        } else {
            $ret = 'bad format - ' . $format;
        }
        return $ret;
    }


    private function Connect()
    {


        $this->config_path = realpath(dirname(__FILE__) . '/../..');
        // $this->config_path2 = realpath(dirname(__FILE__) . '/..');
        $exists = array();
        $port = 3306;

        if (file_exists("$this->config_path/config/settings.inc.php")) {
            $shop = 1;
            $exists['ps'] = true;
            require_once "$this->config_path/config/settings.inc.php";
            // 1.2 nema tento soubor
            if (file_exists("$this->config_path/config/defines.inc.php")) {
                require_once "$this->config_path/config/defines.inc.php";
            } else {
                require_once "$this->config_path/config/config.inc.php";
            }

            if (file_exists($this->config_path . '/app/config/parameters.php')) {
                $this->config_ps17 = require_once $this->config_path . '/app/config/parameters.php';
                defined('_DB_SERVER_') OR define('_DB_SERVER_', $this->config_ps17['parameters']['database_host']);

                defined('_DB_USER_') OR define('_DB_USER_', $this->config_ps17['parameters']['database_user']);
                defined('_DB_PASSWD_') OR define('_DB_PASSWD_', $this->config_ps17['parameters']['database_password']);
                defined('_DB_NAME_') OR define('_DB_NAME_', $this->config_ps17['parameters']['database_name']);
                defined('_DB_PREFIX_') OR define('_DB_PREFIX_', $this->config_ps17['parameters']['database_prefix']);
                $port = $this->config_ps17['parameters']['database_port'];


                defined('_COOKIE_KEY_') OR define('_COOKIE_KEY_', $this->config_ps17['parameters']['cookie_iv']);
            }
            $server = _DB_SERVER_;
            if (preg_match("/(.*):([0-9]{4})$/", $server)) {
                $e = explode(':', $server);
                $server = $e[0];
                $port = $e[1];
            } elseif ($server == ':/tmp/mysql51.sock' || $server == 'mysql51.websupport.sk') { // websupport, specific
                $server = 'mysql51.websupport.sk';
                $port = 3309;
            } elseif ($server == ':/tmp/mysql57.sock' || $server == 'mysql57.websupport.sk') { // websupport, specific
                $server = 'mysql57.websupport.sk';
                $port = 3311;
            } elseif ($server == ':/tmp/mariadb55.sock' || $server == 'mariadb55.websupport.sk') { // websupport, specific
                $server = 'mariadb55.websupport.sk';
                $port = 3310;
            } elseif ($server == ':/tmp/mariadb101.sock' || $server == 'mariadb101.websupport.sk') { // websupport, specific
                $server = 'mariadb101.websupport.sk';
                $port = 3312;
            }
            $user = _DB_USER_;
            $pass = _DB_PASSWD_;
            $db = _DB_NAME_;


        } elseif (file_exists("$this->config_path/config.php")) {
            $shop = 2;
            $exists['oc'] = true;

            require_once "$this->config_path/config.php";
            $matches = array();
            preg_match("/define\('VERSION', '(.*)'\);/i", file_get_contents($this->config_path . '/admin/index.php'), $matches);

            defined('_OC_VERSION_') OR define('_OC_VERSION_', $matches[1]);
            $server = DB_HOSTNAME;
            $user = DB_USERNAME;
            $pass = DB_PASSWORD;
            $db = DB_DATABASE;
            defined('_DB_PREFIX_') OR define('_DB_PREFIX_', DB_PREFIX);
        } elseif (file_exists("$this->config_path/wp-config.php")) {
            $shop = 3;
            $exists['wp'] = true;
            // Musíme parsovat, what the hell!!

            $config_parse = file_get_contents("$this->config_path/wp-config.php");

            $matches = array();
            preg_match("/define\('DB_HOST', '(.*)'\);/i", $config_parse, $matches);

            $server = $matches[1];

            $matches = array();
            preg_match("/define\('DB_USER', '(.*)'\);/i", $config_parse, $matches);
            $user = $matches[1];

            $matches = array();
            preg_match("/define\('DB_PASSWORD', '(.*)'\);/i", $config_parse, $matches);
            $pass = $matches[1];

            $matches = array();
            preg_match("/define\('DB_NAME', '(.*)'\);/i", $config_parse, $matches);
            $db = $matches[1];

            $matches = array();
            preg_match("/table_prefix.*=.*'(.*)';/i", $config_parse, $matches);
            defined('_DB_PREFIX_') OR define('_DB_PREFIX_', $matches[1]);

        }


        if (count($exists) == 0) {
            die('Unknown shop!');
        } elseif (count($exists) != 1) {
            die('More config files, specifity manual please!');
        }

        defined('SHOP') OR define('SHOP', $shop);
        if (empty($port)) {
            $port = 3306;
        }
        $this->link = mysqli_connect($server, $user, $pass, $db, $port);
        if (!$this->link) {
            die("can't connect!");
        }

        $this->SetCharset('utf8');

    }

    protected function chr_utf8($code)
    {
        if ($code < 0) {
            return false;
        } elseif ($code < 128) {
            return chr($code);
        } elseif ($code < 2048) {
            return chr(192 | ($code >> 6)) . chr(128 | ($code & 63));
        } elseif ($code < 65536) {
            return chr(224 | ($code >> 12)) . chr(128 | (($code >> 6) & 63)) . chr(128 | ($code & 63));
        } else {
            return chr(240 | ($code >> 18)) . chr(128 | (($code >> 12) & 63)) . chr(128 | (($code >> 6) & 63)) . chr(128 | ($code & 63));
        }
    }


    protected function Short($text, $chars = 4500)
    {
        if (strlen($text) <= $chars) {
            return $text;
        } else {
            $text = substr($text, 0, $chars + 1);
            $pos = strrpos($text, " "); // v PHP 5 by se dal použít parametr offset
            return substr($text, 0, ($pos ? $pos : -1)) . "&hellip;";
        }
    }

    protected function html_entity_replace($matches)
    {
        /*  if ($matches[2]) {
          return $this->chr_utf8(hexdec($matches[3]));
          } elseif ($matches[1]) {
          return $this->chr_utf8($matches[3]);
          } */

        $entity_to_unicode = array('&AElig;' => $this->chr_utf8(198), '&Aacute;' => $this->chr_utf8(193), '&Acirc;' => $this->chr_utf8(194), '&Agrave;' => $this->chr_utf8(192), '&Alpha;' => $this->chr_utf8(913), '&Aring;' => $this->chr_utf8(197), '&Atilde;' => $this->chr_utf8(195), '&Auml;' => $this->chr_utf8(196), '&Beta;' => $this->chr_utf8(914),
            '&Ccedil;' => $this->chr_utf8(199), '&Chi;' => $this->chr_utf8(935), '&Dagger;' => $this->chr_utf8(8225), '&Delta;' => $this->chr_utf8(916), '&ETH;' => $this->chr_utf8(208), '&Eacute;' => $this->chr_utf8(201), '&Ecirc;' => $this->chr_utf8(202), '&Egrave;' => $this->chr_utf8(200), '&Epsilon;' => $this->chr_utf8(917), '&Eta;' => $this->chr_utf8(919), '&Euml;' => $this->chr_utf8(203),
            '&Gamma;' => $this->chr_utf8(915), '&Iacute;' => $this->chr_utf8(205), '&Icirc;' => $this->chr_utf8(206), '&Igrave;' => $this->chr_utf8(204), '&Iota;' => $this->chr_utf8(921), '&Iuml;' => $this->chr_utf8(207), '&Kappa;' => $this->chr_utf8(922), '&Lambda;' => $this->chr_utf8(923), '&Mu;' => $this->chr_utf8(924), '&Ntilde;' => $this->chr_utf8(209),
            '&Nu;' => $this->chr_utf8(925), '&OElig;' => $this->chr_utf8(338), '&Oacute;' => $this->chr_utf8(211), '&Ocirc;' => $this->chr_utf8(212), '&Ograve;' => $this->chr_utf8(210), '&Omega;' => $this->chr_utf8(937), '&Omicron;' => $this->chr_utf8(927), '&Oslash;' => $this->chr_utf8(216), '&Otilde;' => $this->chr_utf8(213), '&Ouml;' => $this->chr_utf8(214),
            '&Phi;' => $this->chr_utf8(934), '&Pi;' => $this->chr_utf8(928), '&Prime;' => $this->chr_utf8(8243), '&Psi;' => $this->chr_utf8(936), '&Rho;' => $this->chr_utf8(929), '&Scaron;' => $this->chr_utf8(352), '&Sigma;' => $this->chr_utf8(931), '&THORN;' => $this->chr_utf8(222), '&Tau;' => $this->chr_utf8(932), '&Theta;' => $this->chr_utf8(920), '&Uacute;' => $this->chr_utf8(218),
            '&Ucirc;' => $this->chr_utf8(219), '&Ugrave;' => $this->chr_utf8(217), '&Upsilon;' => $this->chr_utf8(933), '&Uuml;' => $this->chr_utf8(220), '&Xi;' => $this->chr_utf8(926), '&Yacute;' => $this->chr_utf8(221), '&Yuml;' => $this->chr_utf8(376), '&Zeta;' => $this->chr_utf8(918), '&aacute;' => $this->chr_utf8(225), '&acirc;' => $this->chr_utf8(226),
            '&acute;' => $this->chr_utf8(180), '&aelig;' => $this->chr_utf8(230), '&agrave;' => $this->chr_utf8(224), '&alefsym;' => $this->chr_utf8(8501), '&alpha;' => $this->chr_utf8(945), '&amp;' => $this->chr_utf8(38), '&and;' => $this->chr_utf8(8743), '&ang;' => $this->chr_utf8(8736), '&apos;' => $this->chr_utf8(39), '&aring;' => $this->chr_utf8(229), '&asymp;' => $this->chr_utf8(8776),
            '&atilde;' => $this->chr_utf8(227), '&auml;' => $this->chr_utf8(228), '&bdquo;' => $this->chr_utf8(8222), '&beta;' => $this->chr_utf8(946), '&brvbar;' => $this->chr_utf8(166), '&bull;' => $this->chr_utf8(8226), '&cap;' => $this->chr_utf8(8745), '&ccedil;' => $this->chr_utf8(231), '&cedil;' => $this->chr_utf8(184), '&cent;' => $this->chr_utf8(162), '&chi;' => $this->chr_utf8(967),
            '&circ;' => $this->chr_utf8(710), '&clubs;' => $this->chr_utf8(9827), '&cong;' => $this->chr_utf8(8773), '&copy;' => $this->chr_utf8(169), '&crarr;' => $this->chr_utf8(8629), '&cup;' => $this->chr_utf8(8746), '&curren;' => $this->chr_utf8(164), '&dArr;' => $this->chr_utf8(8659), '&dagger;' => $this->chr_utf8(8224), '&darr;' => $this->chr_utf8(8595),
            '&deg;' => $this->chr_utf8(176), '&delta;' => $this->chr_utf8(948), '&diams;' => $this->chr_utf8(9830), '&divide;' => $this->chr_utf8(247), '&eacute;' => $this->chr_utf8(233), '&ecirc;' => $this->chr_utf8(234), '&egrave;' => $this->chr_utf8(232), '&empty;' => $this->chr_utf8(8709), '&emsp;' => $this->chr_utf8(8195), '&ensp;' => $this->chr_utf8(8194),
            '&epsilon;' => $this->chr_utf8(949), '&equiv;' => $this->chr_utf8(8801), '&eta;' => $this->chr_utf8(951), '&eth;' => $this->chr_utf8(240), '&euml;' => $this->chr_utf8(235), '&euro;' => $this->chr_utf8(8364), '&exist;' => $this->chr_utf8(8707), '&fnof;' => $this->chr_utf8(402), '&forall;' => $this->chr_utf8(8704), '&frac12;' => $this->chr_utf8(189),
            '&frac14;' => $this->chr_utf8(188), '&frac34;' => $this->chr_utf8(190), '&frasl;' => $this->chr_utf8(8260), '&gamma;' => $this->chr_utf8(947), '&ge;' => $this->chr_utf8(8805), '&gt;' => $this->chr_utf8(62), '&hArr;' => $this->chr_utf8(8660), '&harr;' => $this->chr_utf8(8596), '&hearts;' => $this->chr_utf8(9829), '&hellip;' => $this->chr_utf8(8230),
            '&iacute;' => $this->chr_utf8(237), '&icirc;' => $this->chr_utf8(238), '&iexcl;' => $this->chr_utf8(161), '&igrave;' => $this->chr_utf8(236), '&image;' => $this->chr_utf8(8465), '&infin;' => $this->chr_utf8(8734), '&int;' => $this->chr_utf8(8747), '&iota;' => $this->chr_utf8(953), '&iquest;' => $this->chr_utf8(191), '&isin;' => $this->chr_utf8(8712),
            '&iuml;' => $this->chr_utf8(239), '&kappa;' => $this->chr_utf8(954), '&lArr;' => $this->chr_utf8(8656), '&lambda;' => $this->chr_utf8(955), '&lang;' => $this->chr_utf8(9001), '&laquo;' => $this->chr_utf8(171), '&larr;' => $this->chr_utf8(8592), '&lceil;' => $this->chr_utf8(8968), '&ldquo;' => $this->chr_utf8(8220), '&le;' => $this->chr_utf8(8804),
            '&lfloor;' => $this->chr_utf8(8970), '&lowast;' => $this->chr_utf8(8727), '&loz;' => $this->chr_utf8(9674), '&lrm;' => $this->chr_utf8(8206), '&lsaquo;' => $this->chr_utf8(8249), '&lsquo;' => $this->chr_utf8(8216), '&lt;' => $this->chr_utf8(60), '&macr;' => $this->chr_utf8(175), '&mdash;' => $this->chr_utf8(8212), '&micro;' => $this->chr_utf8(181),
            '&middot;' => $this->chr_utf8(183), '&minus;' => $this->chr_utf8(8722), '&mu;' => $this->chr_utf8(956), '&nabla;' => $this->chr_utf8(8711), '&nbsp;' => $this->chr_utf8(160), '&ndash;' => $this->chr_utf8(8211), '&ne;' => $this->chr_utf8(8800), '&ni;' => $this->chr_utf8(8715), '&not;' => $this->chr_utf8(172), '&notin;' => $this->chr_utf8(8713), '&nsub;' => $this->chr_utf8(8836),
            '&ntilde;' => $this->chr_utf8(241), '&nu;' => $this->chr_utf8(957), '&oacute;' => $this->chr_utf8(243), '&ocirc;' => $this->chr_utf8(244), '&oelig;' => $this->chr_utf8(339), '&ograve;' => $this->chr_utf8(242), '&oline;' => $this->chr_utf8(8254), '&omega;' => $this->chr_utf8(969), '&omicron;' => $this->chr_utf8(959), '&oplus;' => $this->chr_utf8(8853),
            '&or;' => $this->chr_utf8(8744), '&ordf;' => $this->chr_utf8(170), '&ordm;' => $this->chr_utf8(186), '&oslash;' => $this->chr_utf8(248), '&otilde;' => $this->chr_utf8(245), '&otimes;' => $this->chr_utf8(8855), '&ouml;' => $this->chr_utf8(246), '&para;' => $this->chr_utf8(182), '&part;' => $this->chr_utf8(8706), '&permil;' => $this->chr_utf8(8240),
            '&perp;' => $this->chr_utf8(8869), '&phi;' => $this->chr_utf8(966), '&pi;' => $this->chr_utf8(960), '&piv;' => $this->chr_utf8(982), '&plusmn;' => $this->chr_utf8(177), '&pound;' => $this->chr_utf8(163), '&prime;' => $this->chr_utf8(8242), '&prod;' => $this->chr_utf8(8719), '&prop;' => $this->chr_utf8(8733), '&psi;' => $this->chr_utf8(968), '&quot;' => $this->chr_utf8(34),
            '&rArr;' => $this->chr_utf8(8658), '&radic;' => $this->chr_utf8(8730), '&rang;' => $this->chr_utf8(9002), '&raquo;' => $this->chr_utf8(187), '&rarr;' => $this->chr_utf8(8594), '&rceil;' => $this->chr_utf8(8969), '&rdquo;' => $this->chr_utf8(8221), '&real;' => $this->chr_utf8(8476), '&reg;' => $this->chr_utf8(174), '&rfloor;' => $this->chr_utf8(8971),
            '&rho;' => $this->chr_utf8(961), '&rlm;' => $this->chr_utf8(8207), '&rsaquo;' => $this->chr_utf8(8250), '&rsquo;' => $this->chr_utf8(8217), '&sbquo;' => $this->chr_utf8(8218), '&scaron;' => $this->chr_utf8(353), '&sdot;' => $this->chr_utf8(8901), '&sect;' => $this->chr_utf8(167), '&shy;' => $this->chr_utf8(173), '&sigma;' => $this->chr_utf8(963),
            '&sigmaf;' => $this->chr_utf8(962), '&sim;' => $this->chr_utf8(8764), '&spades;' => $this->chr_utf8(9824), '&sub;' => $this->chr_utf8(8834), '&sube;' => $this->chr_utf8(8838), '&sum;' => $this->chr_utf8(8721), '&sup1;' => $this->chr_utf8(185), '&sup2;' => $this->chr_utf8(178), '&sup3;' => $this->chr_utf8(179), '&sup;' => $this->chr_utf8(8835),
            '&supe;' => $this->chr_utf8(8839), '&szlig;' => $this->chr_utf8(223), '&tau;' => $this->chr_utf8(964), '&there4;' => $this->chr_utf8(8756), '&theta;' => $this->chr_utf8(952), '&thetasym;' => $this->chr_utf8(977), '&thinsp;' => $this->chr_utf8(8201), '&thorn;' => $this->chr_utf8(254), '&tilde;' => $this->chr_utf8(732), '&times;' => $this->chr_utf8(215),
            '&trade;' => $this->chr_utf8(8482), '&uArr;' => $this->chr_utf8(8657), '&uacute;' => $this->chr_utf8(250), '&uarr;' => $this->chr_utf8(8593), '&ucirc;' => $this->chr_utf8(251), '&ugrave;' => $this->chr_utf8(249), '&uml;' => $this->chr_utf8(168), '&upsih;' => $this->chr_utf8(978), '&upsilon;' => $this->chr_utf8(965), '&uuml;' => $this->chr_utf8(252),
            '&weierp;' => $this->chr_utf8(8472), '&xi;' => $this->chr_utf8(958), '&yacute;' => $this->chr_utf8(253), '&yen;' => $this->chr_utf8(165), '&yuml;' => $this->chr_utf8(255), '&zeta;' => $this->chr_utf8(950), '&zwj;' => $this->chr_utf8(8205), '&zwnj;' => $this->chr_utf8(8204));
        return strtr($matches, $entity_to_unicode);
    }

    private function SetCharset($charset = 'utf8')
    {
#$this->Query("SET character_set_client = '$charset', character_set_connection = '$charset', character_set_database = '$charset', character_set_results = '$charset', character_set_server = '$charset', character_set_system = '$charset';");
#mysqli_set_charset($this->link, 'utf8');
        mysqli_query($this->link, "SET NAMES '$charset';");
    }

    private function CheckCharset()
    {
        $charset = $this->QueryFA('SHOW VARIABLES LIKE "%character_set%";') or die();
        var_dump($charset);
    }

    protected function AddVat($data, $vat)
    {
        if (empty($vat)) {
            return $data;
        }

        $data *= (($vat / 100) + 1);
        return (float)$data;
    }

    protected function RemoveVat($data, $vat)
    {
        if (empty($vat)) {
            return $data;
        }
        $data /= (($vat / 100) + 1);
        return (float)$data;

    }

    protected function AddPercent($data, $vat)
    {
        if (empty($vat)) {
            return $data;
        }
        $data += $data / 100 * $vat;
        return (float)$data;
    }


    protected function RemovePercent($data, $vat)
    {
        if (empty($vat)) {
            return $data;
        }
        $data -= $data / 100 * $vat;
        return (float)$data;

    }


    protected function ReturnVat($data, $vat)
    {
        if (empty($vat)) {
            return 0;
        }
        return (float)$data / (1 + $vat / 100) * ($vat / 100);
    }

    protected function CountVat($price_without_vat, $price_with_vat)
    {

        return round(($price_with_vat / $price_without_vat - 1) * 100, 0);
    }

    protected function Query($query, $debug = false)
    {
        // $debug = true;

//        if (strlen($query) < 5) {
//            $this->Log('Short Query - ' . $query, 'die');
        //}
        //if (substr($query, -1) != ';') {
        //  $query .= ';';
        //}

        /*
          #$query = 'INSERT IGNORE INTO TABLE NECO () VALUES ();';
          $query = 'SELECT * FROM NECO;';
          #$query = 'SELECT COUNT(*) FROM NECO;';
          #$query = 'SELECT idd FROM NECO;';
          #$query = 'INSERT INTO TABLE NECO () VALUES ();';
          var_dump($query);
          echo PHP_EOL;


          $pattern = array(
          '/^SELECT (\w+) FROM/i',
          '/^INSERT (IGNORE )?INTO (\w+) /i'
          );
          $replacement = array(
          'SELECT `$1` FROMS',
          'INSERT $1INTO `$2` HEUREKA'
          );
          $query2 = preg_replace($pattern, $replacement, $query);
          var_dump($query2);

         */

        $pattern = array(
            "/ (key|group) ((?)= (?))/i"
        );
        $replace = array(
            ' `$1` $2'
        );

        $query = str_replace(array(
            'FROM ',
            'INTO ',
            'UPDATE ',
            'TRUNCATE ',
            'CREATE TABLE IF NOT EXISTS ',
            'DROP TABLE IF EXISTS ',
            'ALTER TABLE ',
            ' JOIN '
        ), array(
            'FROM ' . _DB_PREFIX_,
            'INTO ' . _DB_PREFIX_,
            'UPDATE ' . _DB_PREFIX_,
            'TRUNCATE ' . _DB_PREFIX_,
            'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_,
            'DROP TABLE IF EXISTS ' . _DB_PREFIX_,
            'ALTER TABLE ' . _DB_PREFIX_,
            ' JOIN ' . _DB_PREFIX_
        ), preg_replace($pattern, $replace, $query));


        if ($debug == true) {
            $this->queries[] = $query;
        }
        $this->lastq = $query;


        $res = mysqli_query($this->link, $query);
        /*
if ($res->num_rows==0){
    echo $query.'AAAAA';
}
        */
        if (mysqli_errno($this->link)) {
            $this->mysqli_errors[] = date('Y-m-d H:i:s') . ':' . mysqli_errno($this->link) . ' : ' . mysqli_error($this->link) . ' : ' . $query . PHP_EOL . 'Class: ' . __CLASS__ . PHP_EOL . 'Function: ' . __FUNCTION__ . PHP_EOL . 'PHP Error: ' . error_get_last() . PHP_EOL . 'Dir: ' . dirname(__FILE__) . PHP_EOL . 'File: ' . __FILE__ . PHP_EOL . 'Line: ' . __LINE__ . PHP_EOL . 'Method: ' . __METHOD__ . PHP_EOL;
        }
        return $res;
    }

    protected function Fetch($res, $type = 'object')
    {

        //$types = array('object' => true, 'array' => true, 'row' => true, 'assoc' => true, 'field' => true, 'lenghts' => true, 'flags' => true, 'len' => true, 'name' => true, 'seek' => true, 'table' => true, 'type' => true);
        //if (!isset($types[$type]) || $types[$type] !== true) {
        //    $this->Log('Bad Fetch type' . $type, 'die');
        //    $type = 'object';
        //}
        $function = 'mysqli_fetch_' . $type;
        return $function($res);
    }


    protected function FetchAll($res, $type = 'object')
    {

        //      $types = array('object' => true, 'array' => true, 'row' => true, 'assoc' => true, 'field' => true, 'lenghts' => true, 'flags' => true, 'len' => true, 'name' => true, 'seek' => true, 'table' => true, 'type' => true);
        //      if (!isset($types[$type]) || $types[$type] !== true) {
        //          $this->Log('Bad Fetch type' . $type, 'die');
//            $type = 'object';
        //      }
        $function = 'mysqli_fetch_' . $type;
        $data = array();
        while ($tmp = $function($res)) {
            /*
            if (memory_get_usage() + 10000 > ini_get('memory_limit')){
                echo $this->tmp_query;
                die;
            }
            */
            $data[] = $tmp;
        }
        return $data;
    }


    private function Mysqli_Result($res, $row = 0, $col = 0)
    {
        $numrows = mysqli_num_rows($res);
        if ($numrows && $row <= ($numrows - 1) && $row >= 0) {
            mysqli_data_seek($res, $row);
            $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
            if (isset($resrow[$col])) {
                return $resrow[$col];
            }
        }
        return false;
    }

    protected function Result($res, $number = 0)
    {
        //  if (empty($res)) {
        //       $this->Log("Bad Result ($number)- {$this->lastq}", 'die');
        //   }
        if (Mysqli_Num_Rows($res) > 0) {
            return $this->Mysqli_Result($res, 0, $number);
        }
        return false;
    }

    protected function QueryR($query, $debug = false, $number = 0, $link = '')
    {
        return $this->Result($this->Query($query, $debug, $link), $number);
    }

    protected function QueryF($query, $debug = false, $type = 'object', $link = '')
    {
        return $this->Fetch($this->Query($query, $debug, $link), $type);
    }

    protected function QueryFA($query, $debug = false, $type = 'object', $link = '')
    {
        $this->tmp_query = $query;
        return $this->FetchAll($this->Query($query, $debug, $link), $type);
    }

    protected function FetchAll2($res, $type = 'object')
    {
// OK
        $types = array('object' => true, 'array' => true, 'row' => true, 'assoc' => true, 'field' => true, 'lenghts' => true, 'flags' => true, 'len' => true, 'name' => true, 'seek' => true, 'table' => true, 'type' => true);
        if (!isset($types[$type]) || $types[$type] != true) {
            die('Bad Fetch type' . $type);
            $type = 'object';
        }
        $function = 'mysqli_fetch_' . $type;
        $all = array();

        while ($row = $function($res)) {
            $all[] = $row;
        }

        return $all;
    }

    protected function QueryFA2($query, $type = 'row', $debug = false)
    {
        return $this->FetchAll2($this->Query($query, $debug), $type);
    }


    public function __destruct()
    {
        if (isset($this->mysqli_errors)) {
            print_R(array_filter($this->mysqli_errors));
        }
        if (isset($this->queries)) {
            print_R(array_filter($this->queries));
        }
        if ($this->link) {
            mysqli_close($this->link);
        }
        if (SHAIM) {
            echo PHP_EOL . 'Start time: ' . date("Y-m-d H:i:s", $this->start);
            echo PHP_EOL . 'End time: ' . date("Y-m-d H:i:s") . PHP_EOL;
            echo 'Running time: ' . round(microtime(true) - $this->start, 4) . 's' . PHP_EOL;
            echo 'getcwd(): ' . getcwd() . PHP_EOL;
            $unit = array('B', 'K', 'M', 'G', 'T', 'P');
            if (function_exists('ini_get')) {
                echo 'memory_limit: ' . ini_get('memory_limit') . PHP_EOL;
                echo 'max_execution_time: ' . ini_get('max_execution_time') . 's' . PHP_EOL;
            }
            echo 'get_memory_usage: ' . ceil(memory_get_usage(false) / pow(1024, ($i = floor(log(memory_get_usage(false), 1024))))) . $unit[$i] . PHP_EOL;
        }

        echo '</pre>';

    }

}
