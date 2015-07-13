<?php
/*
 * This file is part of the ITDCMS package.
 *
 * (c) JSC ITDC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
header("Cache-Control: post-check=0, pre-check=0", false);

$version = '1.3.0';
/**
 * @package          ITDCMS
 * @subpackage     Utilities
 * @author             Avtandil Kikabidze aka LONGMAN (akalongman@gmail.com)
 * @copyright         Copyright (C) 2001 - 2015 JSC ITDC. All rights reserved.
 * @license             http://opensource.org/licenses/mit-license.php  The MIT License (MIT)
 * @link                  https://github.com/itdc/cms-compatibility-checker
 * @version            1.3.0
 */
ini_set('max_execution_time', 0);
ini_set('memory_limit', -1);

ini_set('error_reporting', E_ALL);
ini_set('display_errors', false);


$mode = isset($_GET['mode']) ? $_GET['mode'] : '';
ob_start();
?>
<!DOCTYPE html>
<html id="itdc">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>ITDCMS Check v<?php echo $version?></title>
        <meta name="author" content="itdc.ge" />
        <script src="static/js/jquery-2.1.4.min.js"></script>
        <script src="static/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="static/css/bootstrap.min.css">
    </head>

    <body class="clearfix">

        <div class="row">
            <div class="col-xs-6 col-xs-offset-3">
            <?php
            switch($mode) {

                case 'version':
                    echo $version;
                    break;

                case 'mr':
                    echo '{{MODREWRITEWORKS}}';
                    break;

                case 'phpinfo':
                    phpinfo();
                    break;

                default:
                    $disabled_functions = @ini_get('disable_functions');
                    if (!empty($disabled_functions)) {
                        $disabled_functions = explode(',', $disabled_functions);
                        $disabled_functions = array_map('trim', $disabled_functions);
                    }

                    ?>
                    <div class="panel panel-default">
                        <div class="panel-heading clearfix">
                            <?php
                            $date = date('Y-m-d H:i:s');
                            ?>

                            <h5 class="clearfix">
                                <div class="pull-right">
                                    Date: <?php echo $date?>
                                </div>
                            </h5>

                            <h3 class="clearfix">
                                Check CMS to Server Compatibility <small>v<?php echo $version?></small>
                                <?php
                                if (!empty($disabled_functions) && in_array('phpinfo', $disabled_functions)) {
                                    ?>
                                    <a title="phpinfo() is disabled on server" href="javascript:void(0);" disabled="disabled" role="button" class="btn btn-primary btn-md pull-right">
                                        phpinfo()
                                    </a>
                                    <?php
                                } else {
                                    ?>
                                    <a title="Show server phpinfo()" href="<?php echo $_SERVER['PHP_SELF'].'?mode=phpinfo' ?>" role="button" class="btn btn-primary btn-md pull-right">
                                        phpinfo()
                                    </a>
                                    <?php
                                }
                                ?>
                            </h3>


                        </div>

                        <div class="clearfix"></div>

                        <div class="panel-body">

                            <!-- Required -->
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="3"><h3>Required</h3></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $required = Checker::getRequiredTests();
                                    foreach($required as $test) {
                                        ?>
                                        <tr>
                                            <td><?php echo $test['title'] ?></td>
                                            <td style="text-align:center;"><?php echo $test['data'] ?></td>
                                            <td style="text-align:center;">
                                            <?php
                                            if ($test['status']) {
                                                ?>
                                                <span style="color:green;font-size:16px;font-weight: bold;" class="glyphicon glyphicon glyphicon-ok"></span>
                                                <?php
                                            } else {
                                                ?>
                                                <span style="color:red;font-size:16px;font-weight: bold;" class="glyphicon glyphicon glyphicon-remove"></span>
                                                <?php
                                            }
                                            ?>

                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <hr />
                            <hr />

                            <!-- Recommended -->
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="3"><h3>Recommended</h3></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $recommended = Checker::getRecommendedTests();
                                    foreach($recommended as $test) {
                                        ?>
                                        <tr>
                                            <td><?php echo $test['title'] ?></td>
                                            <td style="text-align:center;"><?php echo $test['data'] ?></td>
                                            <td style="text-align:center;">
                                            <?php
                                            if ($test['status']) {
                                                ?>
                                                <span style="color:green;font-size:16px;font-weight: bold;" class="glyphicon glyphicon glyphicon-ok"></span>
                                                <?php
                                            } else {
                                                ?>
                                                <span style="color:red;font-size:16px;font-weight: bold;" class="glyphicon glyphicon glyphicon-remove"></span>
                                                <?php
                                            }
                                            ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                    <?php
                    break;
            }
            ?>

            </div>
        </div>
        <div class="row">
            <div class="col-xs-1 col-xs-offset-11">
                <span>© <a href="http://itdc.ge/" target="_blank" title="ITDC">ITDC</a></span>
            </div>
        </div>

    </body>

</html>


<?php

abstract class Checker
{
    protected static $type = 1;

    public static function getRequiredTests()
    {
        self::$type = 1;
        $tests = array();
        $tests[] = self::checkPHP();
        $tests[] = self::checkMYSQL();
        $tests[] = self::checkCURL();
        $tests[] = self::checkGD();
        $tests[] = self::checkZLIB();
        $tests[] = self::checkModRewrite();
        $tests[] = self::checkMBString();
        $tests[] = self::checkDOM();
        $tests[] = self::checkXML();
        $tests[] = self::checkJSON();


        $tests[] = self::checkShortOpenTag();
        $tests[] = self::checkSafeMode();
        $tests[] = self::checkFileUploads();
        $tests[] = self::checkConnectionService();
        $tests[] = self::checkGoogleConnect();

        return $tests;
    }

    public static function getRecommendedTests()
    {
        self::$type = 2;

        $tests = array();
        $tests[] = self::checkPHP();
        $tests[] = self::checkMYSQLi();
        $tests[] = self::checkCURL();
        $tests[] = self::checkGD();
        $tests[] = self::checkZLIB();
        $tests[] = self::checkModRewrite();
        $tests[] = self::checkMBString();
        $tests[] = self::checkDOM();
        $tests[] = self::checkXML();
        $tests[] = self::checkJSON();
        $tests[] = self::checkPDO();
        $tests[] = self::checkSOAP();
        $tests[] = self::checkMemcached();
        //$tests[] = self::checkAPC();
        //$tests[] = self::checkEAccelerator();


        $tests[] = self::checkShortOpenTag();
        $tests[] = self::checkSafeMode();
        $tests[] = self::checkFileUploads();
        $tests[] = self::checkConnectionService();
        $tests[] = self::checkGoogleConnect();

        return $tests;
    }

    public static function checkPHP()
    {
        $return = array();
        $return['title'] = 'PHP Extension';
        $ver = phpversion();
        $return['data'] = 'v'.$ver;
        $return['status'] = self::$type == 1 ? version_compare($ver, '5.3.1', '>=') : version_compare($ver, '5.5.0', '>=');
        $return['msg'] = self::$type == 1 ? 'Required v5.3.1 or greater' : 'Required v5.5.0 or greater';
        return $return;
    }

    public static function checkMYSQL()
    {
        $return = array();
        $return['title'] = 'MySQL Extension';
        if (function_exists('mysql_connect')) {
            $return['data'] = 'Installed';
            $return['status'] = true;
        } else {
            $return['data'] = '-';
            $return['status'] = false;
        }
        return $return;
    }

    public static function checkMYSQLi()
    {
        $return = array();
        $return['title'] = 'MySQLi Extension';
        if (function_exists('mysqli_connect')) {
            $return['data'] = 'Installed';
            $return['status'] = true;
        } else {
            $return['data'] = '-';
            $return['status'] = false;
        }
        return $return;
    }


    public static function checkCURL()
    {
        $return = array();
        $return['title'] = 'CURL Extension';
        if (function_exists('curl_version')) {
            $ver = curl_version();
            $return['data'] = 'v'.$ver['version'];
            $return['status'] = true;
        } else {
            $return['data'] = '-';
            $return['status'] = false;
        }
        return $return;
    }



    public static function checkShortOpenTag()
    {
        $return = array();
        $return['title'] = 'PHP: short_open_tag';
        if (@ini_get('short_open_tag')) {
            $return['data'] = 'Enabled';
            $return['status'] = true;
        } else {
            $return['data'] = 'Disabled';
            $return['status'] = false;
        }
        return $return;
    }


    public static function checkSafeMode()
    {
        $return = array();
        $return['title'] = 'PHP: safe_mode';
        if (@ini_get('safe_mode')) {
            $return['data'] = 'Enabled';
            $return['status'] = false;
        } else {
            $return['data'] = 'Disabled';
            $return['status'] = true;
        }
        return $return;
    }

    public static function checkFileUploads()
    {
        $return = array();
        $return['title'] = 'PHP: file_uploads';
        if (@ini_get('file_uploads')) {
            $return['data'] = 'Enabled';
            $return['status'] = true;
        } else {
            $return['data'] = 'Disabled';
            $return['status'] = false;
        }
        return $return;
    }





    public static function checkGD()
    {
        $return = array();
        $return['title'] = 'GD Extension';
        if (function_exists('gd_info')) {
            $ver = gd_info();
            $return['data'] = 'Installed';//$ver['GD Version'];
            $return['status'] = true;
        } else {
            $return['data'] = '-';
            $return['status'] = false;
        }
        return $return;
    }

    public static function checkZLIB()
    {
        $return = array();
        $return['title'] = 'ZLib Extension';
        if (function_exists('gzcompress')) {
            $return['data'] = 'Installed';
            $return['status'] = true;
        } else {
            $return['data'] = '-';
            $return['status'] = false;
        }
        return $return;
    }



    public static function checkModRewrite()
    {
        static $return;

        if (empty($return)) {
            $return = array();
            $return['title'] = 'Mod Rewrite Extension';


            if (file_exists('.htaccess')) {
                ini_set('default_socket_timeout', 15);
                $scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
                $path = str_replace('index.php', '', $_SERVER['PHP_SELF']);
                $md = file_get_contents($scheme.'://'.$_SERVER['HTTP_HOST'].$path.'checkmodrewriteitdc.php');
                if (!empty($md) && strpos($md, '{{MODREWRITEWORKS}}') !== false) {
                    $return['data'] = 'Installed';
                    $return['status'] = true;
                } else {
                    $return['data'] = '-';
                    $return['status'] = false;
                }
            } else {
                $return['data'] = 'Upload .htacces file too';
                $return['status'] = false;
            }
        }

        return $return;
    }

    public static function checkConnectionService()
    {
        static $return;
        if (empty($return)) {
            $return = array();
            $return['title'] = 'Connection to service.itdc.ge';
            $data = file_get_contents('http://service.itdc.ge');
            if ($data) {
                $return['data'] = 'Allowed';
                $return['status'] = true;
            } else {
                $return['data'] = '-';
                $return['status'] = false;
            }
        }
        return $return;
    }


    public static function checkMBString()
    {
        $return = array();
        $return['title'] = 'MB String Extension';
        if (extension_loaded('mbstring')) {
            //$ver = curl_version();
            $return['data'] = 'Installed';
            $return['status'] = true;
        } else {
            $return['data'] = '-';
            $return['status'] = false;
        }
        return $return;
    }

    public static function checkXML()
    {
        $return = array();
        $return['title'] = 'XML Extension';
        if (extension_loaded('xml')) {
            $return['data'] = 'Installed';
            $return['status'] = true;
        } else {
            $return['data'] = '-';
            $return['status'] = false;
        }
        return $return;
    }

    public static function checkDOM()
    {
        $return = array();
        $return['title'] = 'DOM Extension';
        if (extension_loaded('dom')) {
            $return['data'] = 'Installed';
            $return['status'] = true;
        } else {
            $return['data'] = '-';
            $return['status'] = false;
        }
        return $return;
    }



    public static function checkJSON()
    {
        $return = array();
        $return['title'] = 'JSON Extension';
        if (function_exists('json_encode') && function_exists('json_decode')) {
            $return['data'] = 'Installed';
            $return['status'] = true;
        } else {
            $return['data'] = '-';
            $return['status'] = false;
        }
        return $return;
    }

    public static function checkPDO()
    {
        $return = array();
        $return['title'] = 'PDO Extension';
        if (extension_loaded('pdo')) {
            $return['data'] = 'Installed';
            $return['status'] = true;
        } else {
            $return['data'] = '-';
            $return['status'] = false;
        }
        return $return;
    }

    public static function checkMemcached()
    {
        $return = array();
        $return['title'] = 'Memcached Extension';
        if (extension_loaded('memcached')) {
            $return['data'] = 'Installed';
            $return['status'] = true;
        } else {
            $return['data'] = '-';
            $return['status'] = false;
        }
        return $return;
    }

    public static function checkSOAP()
    {
        $return = array();
        $return['title'] = 'SOAP Extension';
        if (extension_loaded('soap')) {
            $return['data'] = 'Installed';
            $return['status'] = true;
        } else {
            $return['data'] = '-';
            $return['status'] = false;
        }
        return $return;
    }

    public static function checkAPC()
    {
        $return = array();
        $return['title'] = 'APC Extension';
        if (extension_loaded('apc')) {
            $return['data'] = 'Installed';
            $return['status'] = true;
        } else {
            $return['data'] = '-';
            $return['status'] = false;
        }
        return $return;
    }

    public static function checkEAccelerator()
    {
        $return = array();
        $return['title'] = 'eAccelerator Extension';
        if (extension_loaded('eaccelerator')) {
            $return['data'] = 'Installed';
            $return['status'] = true;
        } else {
            $return['data'] = '-';
            $return['status'] = false;
        }
        return $return;
    }

    public static function checkGoogleConnect()
    {
        static $return;
        if (empty($return)) {
            $return = array();
            $return['title'] = 'Connect to Google Servers';

            $urls = array('https://accounts.google.com', 'https://www.googleapis.com');

            $status = true;
            foreach($urls as $url) {

                if (function_exists('curl_version')) {
                    $curl_handle=curl_init();
                    curl_setopt($curl_handle, CURLOPT_URL, $url);
                    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl_handle, CURLOPT_USERAGENT, 'ITDC');
                    $data = curl_exec($curl_handle);
                    curl_close($curl_handle);
                } else {
                    $data = false;
                }

                if (empty($data)) {
                    $status = false;
                    break;
                }
            }


            if ($status) {
                $return['data'] = 'Allowed';
                $return['status'] = true;
            } else {
                $return['data'] = '-';
                $return['status'] = false;
            }
        }

        return $return;
    }


}


ob_get_flush();
