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

$version = '2.1.3';
$debug_mode = (int)is_debug_mode();
/**
 * @package             ITDCMS
 * @subpackage          Utilities
 * @author              Avtandil Kikabidze aka LONGMAN (akalongman@gmail.com)
 * @copyright           Copyright (C) 2001 - 2015 ITDC, JSC. All rights reserved.
 * @license             Commercial license
 * @version             2.1.3
 */

ini_set('error_reporting', E_ALL);
ini_set('display_errors', $debug_mode);

$mode = isset($_GET['mode']) ? $_GET['mode'] : '';

switch($mode) {

    case 'check':
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $level = isset($_GET['level']) ? $_GET['level'] : 1;

        echo Checker::check($type, $level);
        die;
        break;


    case 'version':
        echo $version;
        die;
        break;

    case 'mr':
        echo '{{MODREWRITEWORKS}}';
        die;
        break;

    case 'phpinfo':
        phpinfo();
        die;
        break;

    case 'checkupdate':
        $return = array();
        $return['status'] = 'error';
        $return['msg'] = 'Something went wrong';
        $return['link'] = '';
        $return['version'] = '';

        if (!function_exists('curl_version')) {
            $return['msg'] = 'CURL Extension not installed!';
            die(json_encode($return));
        }


        $url = 'https://api.github.com/repos/itdc/php-cms-checker/tags';

        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT,
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.80 Safari/537.36');
        $data = curl_exec($curl_handle);
        $error = curl_error($curl_handle);
        curl_close($curl_handle);

        if (!$data) {
            $return['msg'] = 'CURL error: '.$error;
            die(json_encode($return));
        }


        $json = json_decode($data, true);
        if (empty($json)) {
            $return['msg'] = 'Data is invalid';
            die(json_encode($return));
        }



        if (empty($json[0])) {
            $return['msg'] = 'Data is empty';
            die(json_encode($return));
        }
        $data = $json[0];

        $tag_name = $data['name'];

        if (version_compare($version, $tag_name) === -1) {
            // update available
            $return['status'] = 'yes';
            $return['msg'] = 'New version '.$tag_name.' available';
            $return['link'] = $data['zipball_url'];
            $return['version'] = $tag_name;
            die(json_encode($return));
        } else {
            // no updates
            $return['status'] = 'no';
            $return['msg'] = 'No updates';
            $return['link'] = $data['zipball_url'];
            $return['version'] = $tag_name;
            die(json_encode($return));
        }
        break;
}


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

        <style type="text/css">
        span.preloader {
            width: 16px;
            height: 16px;
            display: inline-block;
            background-image: url('data:image/gif;base64,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');
        }
        </style>

    </head>

    <body class="clearfix">

        <div class="row">
            <div class="col-xs-6 col-xs-offset-3">
            <?php

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
                                Server Date: <?php echo $date?>
                            </div>
                        </h5>

                        <h3 class="clearfix">
                            CMS Compatibility Checker <span id="ver-badge" class="small badge" style="background-color:#888888;">v<?php echo $version?></span>
                            <?php
                            if (!empty($disabled_functions) && in_array('phpinfo', $disabled_functions)) {
                                ?>
                                <a title="phpinfo()" href="javascript:void(0);" disabled="disabled" role="button" class="btn btn-primary btn-md pull-right">
                                    phpinfo()
                                </a>
                                <?php
                            } else {
                                ?>
                                <a title="phpinfo()" href="<?php echo $_SERVER['PHP_SELF'].'?mode=phpinfo' ?>" target="_blank" role="button" class="btn btn-primary btn-md pull-right">
                                    phpinfo()
                                </a>
                                <?php
                            }
                            ?>
                        </h3>
                        <h5 id="update" style="display:none;">Update available <a href="asdada">asdsadsadada</a></h5>



                    </div>

                    <div class="clearfix"></div>

                    <div class="panel-body">

                        <!-- Required -->
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="3"><h3>Minimum Requirements</h3></th>
                                </tr>
                                <tr>
                                    <th>
                                        <b>Requirement</b>
                                    </th>
                                    <th>
                                        <div style="text-align:center;">
                                            <b>Current</b>
                                        </div>
                                    </th>
                                    <th>
                                        <div style="text-align:center;">
                                            <b>Required</b>
                                        </div>
                                    </th>
                                    <th>
                                        <div style="text-align:center;">
                                            <b>Status</b>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="table_required">
                                <?php
                                $tokens = Checker::getTokens(1);
                                $js_tokens = array();
                                foreach($tokens as $key=>$token) {
                                    ?>
                                    <tr id="required_tr_<?php echo $key ?>">
                                        <td class="title"><?php echo $token['title'] ?></td>

                                        <td class="current" style="text-align:center;"></td>

                                        <td class="required" style="text-align:center;"><?php echo $token['required'] ?></td>

                                        <td class="status" style="text-align:center;">
                                            <span class="preloader" title="Loading"></span>
                                            <span style="color:green;font-size:16px;font-weight:bold;display:none;" class="glyphicon glyphicon glyphicon-ok success"></span>
                                            <span style="color:red;font-size:16px;font-weight:bold;display:none;" class="glyphicon glyphicon glyphicon-remove error"></span>
                                        </td>
                                    </tr>
                                    <?php
                                    $js_tokens[] = $key;
                                }
                                $js_tokens = '["'.implode('","', $js_tokens).'"]';
                                ?>
                            </tbody>
                        </table>

                        <hr />
                        <hr />


                        <!-- Recommended -->
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="3"><h3>Recommended Requirements</h3></th>
                                </tr>
                                <tr>
                                    <th>
                                        <b>Requirement</b>
                                    </th>
                                    <th>
                                        <div style="text-align:center;">
                                            <b>Current</b>
                                        </div>
                                    </th>
                                    <th>
                                        <div style="text-align:center;">
                                            <b>Recommended</b>
                                        </div>
                                    </th>
                                    <th>
                                        <div style="text-align:center;">
                                            <b>Status</b>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="table_recommended">
                                <?php
                                $tokens = Checker::getTokens(2);
                                $js_tokens2 = array();
                                foreach($tokens as $key=>$token) {
                                    ?>
                                    <tr id="recommended_tr_<?php echo $key ?>">
                                        <td class="title"><?php echo $token['title'] ?></td>

                                        <td class="current" style="text-align:center;"></td>

                                        <td class="required" style="text-align:center;"><?php echo $token['required'] ?></td>

                                        <td class="status" style="text-align:center;">
                                            <span class="preloader" title="Loading"></span>
                                            <span style="color:green;font-size:16px;font-weight:bold;display:none;" class="glyphicon glyphicon glyphicon-ok success"></span>
                                            <span style="color:red;font-size:16px;font-weight:bold;display:none;" class="glyphicon glyphicon glyphicon-remove error"></span>
                                        </td>
                                    </tr>
                                    <?php
                                    $js_tokens2[] = $key;
                                }
                                $js_tokens2 = '["'.implode('","', $js_tokens2).'"]';
                                ?>
                            </tbody>
                        </table>




                    </div>
                </div>


            </div>
        </div>
        <div class="row">
            <div class="col-xs-1 col-xs-offset-11">
                <span>© <a href="http://itdc.ge/" target="_blank" title="ITDC">ITDC</a></span>
            </div>
        </div>





        <script>
             entry = '<?php echo $_SERVER["PHP_SELF"]?>';
             debug_mode = <?php echo $debug_mode?>;


            function startCheck(list, level) {
                var prefix = level == 1 ? 'required_' : 'recommended_';

                $.each(list, function(index, value) {
                    var url = entry+'?mode=check&type='+value+'&level='+level;

                    $.ajax({
                        type: 'GET',
                        url: url,
                        cache: false,
                        timeout: 20000,
                        data: null,
                        beforeSend: function(jqXHR, settings){

                        },
                        success: function(data, textStatus, jqXHR) {
                            var data_arr = data.split("|");
                            var id = data_arr[0];
                            var status = data_arr[1];
                            var comment = data_arr[2];
                            var msg = data_arr[3];

                            $('#'+prefix+'tr_'+value+' td.current').text(comment);

                            if (status) {
                                $('#'+prefix+'tr_'+value+' td.current').removeClass('text-danger');
                                $('#'+prefix+'tr_'+value+' td.status span.success').show();
                            } else {
                                $('#'+prefix+'tr_'+value+' td.current').addClass('text-danger');
                                $('#'+prefix+'tr_'+value+' td.status span.error').show();
                                $('#'+prefix+'tr_'+value+' td.status span.error').prop('title', msg);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            //var error = textStatus+' '+errorThrown;
                            $('#'+prefix+'tr_'+value+' td.status span.error').show();
                            $('#'+prefix+'tr_'+value+' td.status span.error').prop('title', textStatus);
                        },
                        complete: function(jqXHR, textStatus) {
                            $('#'+prefix+'tr_'+value+' td.status span.preloader').hide();

                        }
                    });
                });
            }


            function checkUpdate() {
                var url = entry+'?mode=checkupdate';

                $.ajax({
                    type: 'GET',
                    url: url,
                    cache: false,
                    timeout: 30000,
                    data: null,
                    dataType: 'json',
                    beforeSend: function(jqXHR, settings){

                    },
                    success: function(data, textStatus, jqXHR) {
                        var $status = data.status;
                        var $version = data.version;
                        var $link = data.link;

                        if ($status == 'yes') {
                            var html = 'New version of checker <span id="ver-badge2" class="small badge" style="background-color:#44a944;">v' + $version + '</span> is available! \
                            Download from <a href="'+$link+'" target="_blank">here</a> and update manually';
                            $('#update').html(html).show();
                            $('#ver-badge').css('background-color', '#a94442');
                        } else if ($status == 'error') {
                            console.log(data);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus);
                    },
                    complete: function(jqXHR, textStatus) {

                    }
                });
            }




            $(function() {
                if (debug_mode) {
                    checkUpdate();
                }

                list = <?php echo $js_tokens?>;
                // required
                startCheck(list, 1);

                list = <?php echo $js_tokens2?>;
                // recommended
                startCheck(list, 2);


            });
        </script>
    </body>
</html><?php
ob_get_flush();


abstract class Checker
{
    protected static $level = 1; // 1 - required, 2 - recommended

    protected static $required_list = array(
        'php'=>array(
            'id'=>'check_php',
            'title'=>'PHP Extension',
            'method'=>'checkPHP',
            'required'=>'v5.3.1',
            'recommended'=>'v5.5.0',
        ),
        'mysql'=>array(
            'id'=>'check_mysql',
            'title'=>'MySQL Extension',
            'method'=>'checkMYSQL',
            'required'=>'Installed',
        ),
        'curl'=>array(
            'id'=>'check_curl',
            'title'=>'CURL Extension',
            'method'=>'checkCURL',
            'required'=>'Installed',
        ),
        'json'=>array(
            'id'=>'check_json',
            'title'=>'JSON Extension',
            'method'=>'checkJSON',
            'required'=>'Installed',
        ),
        'gd'=>array(
            'id'=>'check_gd',
            'title'=>'GD Extension',
            'method'=>'checkGD',
            'required'=>'Installed',
        ),
        'zlib'=>array(
            'id'=>'check_zlib',
            'title'=>'ZLib Extension',
            'method'=>'checkGD',
            'required'=>'Installed',
        ),
        'mod_rewrite'=>array(
            'id'=>'check_mod_rewrite',
            'title'=>'Mod Rewrite Extension',
            'method'=>'checkModRewrite',
            'required'=>'Installed',
        ),
        'mbstring'=>array(
            'id'=>'check_mbstring',
            'title'=>'MB String Extension',
            'method'=>'checkMBString',
            'required'=>'Installed',
        ),
        'xml'=>array(
            'id'=>'check_xml',
            'title'=>'XML Extension',
            'method'=>'checkXML',
            'required'=>'Installed',
        ),
        'dom'=>array(
            'id'=>'check_dom',
            'title'=>'DOM Extension',
            'method'=>'checkDOM',
            'required'=>'Installed',
        ),
        'short_open_tag'=>array(
            'id'=>'check_short_open_tag',
            'title'=>'PHP: short_open_tag',
            'method'=>'checkShortOpenTag',
            'required'=>'Enabled',
        ),
        'safe_mode'=>array(
            'id'=>'check_safe_mode',
            'title'=>'PHP: safe_mode',
            'method'=>'checkSafeMode',
            'required'=>'Disabled',
        ),
        'file_uploads'=>array(
            'id'=>'check_file_uploads',
            'title'=>'PHP: file_uploads',
            'method'=>'checkFileUploads',
            'required'=>'Enabled',
        ),
        'connection_service'=>array(
            'id'=>'check_connection_service',
            'title'=>'HTTPS Connection to service.itdc.ge',
            'method'=>'checkConnectionService',
            'required'=>'Allowed',
        ),
        'google_connect'=>array(
            'id'=>'check_google_connect',
            'title'=>'HTTPS Connection to Google servers',
            'method'=>'checkGoogleConnect',
            'required'=>'Allowed',
        ),

    );


    protected static $recommended_list  = array(
        'php'=>array(
            'id'=>'check_php',
            'title'=>'PHP Extension',
            'method'=>'checkPHP',
            'required'=>'v5.6.0',
        ),
        'mysqli'=>array(
            'id'=>'check_mysqli',
            'title'=>'MySQLi Extension',
            'method'=>'checkMYSQLi',
            'required'=>'Installed',
        ),
        'curl'=>array(
            'id'=>'check_curl',
            'title'=>'CURL Extension',
            'method'=>'checkCURL',
            'required'=>'Installed',
        ),
        'json'=>array(
            'id'=>'check_json',
            'title'=>'JSON Extension',
            'method'=>'checkJSON',
            'required'=>'Installed',
        ),
        'gd'=>array(
            'id'=>'check_gd',
            'title'=>'GD Extension',
            'method'=>'checkGD',
            'required'=>'Installed',
        ),
        'zlib'=>array(
            'id'=>'check_zlib',
            'title'=>'ZLib Extension',
            'method'=>'checkGD',
            'required'=>'Installed',
        ),
        'mod_rewrite'=>array(
            'id'=>'check_mod_rewrite',
            'title'=>'Mod Rewrite Extension',
            'method'=>'checkModRewrite',
            'required'=>'Installed',
        ),
        'mbstring'=>array(
            'id'=>'check_mbstring',
            'title'=>'MB String Extension',
            'method'=>'checkMBString',
            'required'=>'Installed',
        ),
        'xml'=>array(
            'id'=>'check_xml',
            'title'=>'XML Extension',
            'method'=>'checkXML',
            'required'=>'Installed',
        ),
        'dom'=>array(
            'id'=>'check_dom',
            'title'=>'DOM Extension',
            'method'=>'checkDOM',
            'required'=>'Installed',
        ),
        'soap'=>array(
            'id'=>'check_soap',
            'title'=>'SOAP Extension',
            'method'=>'checkSOAP',
            'required'=>'Installed',
        ),
        'memcached'=>array(
            'id'=>'check_memcached',
            'title'=>'Memcached Extension',
            'method'=>'checkMemcached',
            'required'=>'Installed',
        ),
        'short_open_tag'=>array(
            'id'=>'check_short_open_tag',
            'title'=>'PHP: short_open_tag',
            'method'=>'checkShortOpenTag',
            'required'=>'Enabled',
        ),
        'safe_mode'=>array(
            'id'=>'check_safe_mode',
            'title'=>'PHP: safe_mode',
            'method'=>'checkSafeMode',
            'required'=>'Disabled',
        ),
        'file_uploads'=>array(
            'id'=>'check_file_uploads',
            'title'=>'PHP: file_uploads',
            'method'=>'checkFileUploads',
            'required'=>'Enabled',
        ),
        'connection_service'=>array(
            'id'=>'check_connection_service',
            'title'=>'HTTPS Connection to service.itdc.ge',
            'method'=>'checkConnectionService',
            'required'=>'Allowed',
        ),
        'google_connect'=>array(
            'id'=>'check_google_connect',
            'title'=>'HTTPS Connection to Google servers',
            'method'=>'checkGoogleConnect',
            'required'=>'Allowed',
        ),

    );






    public static function getTokens($level = 1)
    {
        $list = $level == 1 ? self::$required_list : self::$recommended_list;

        return $list;
    }


    public static function check($mode, $level = 1)
    {
        self::$level = $level;

        //sleep(5);
        $list = self::getList();



        $test = isset($list[$mode]) ? $list[$mode] : '';
        if (empty($test)) {
            die($mode.'|0|Test not found|Test not found');
        }

        if (empty($test['method'])) {
            die($mode.'|0|Test method not found|Test method not found');
        }

        $check = call_user_func(array('Checker', $test['method']));

        $string = $mode.'|'.$check['status'].'|'.$check['comment'].'|'.$check['msg'];


        echo $string;
    }


    private static function getList()
    {
        $list = self::$level == 1 ? self::$required_list : self::$recommended_list;
        return $list;
    }


    public static function checkPHP()
    {
        $list = self::getList();

        $ver = phpversion();
        $return = array();
        $return['comment'] = 'v'.$ver;
        $return['status'] = self::$level == 1 ? version_compare($ver, str_replace('v', '', $list['php']['required']), '>=') : version_compare($ver, str_replace('v', '', $list['php']['required']), '>=');
        $return['msg'] = 'Required '.$list['php']['required'].' or greater';
        return $return;
    }

    public static function checkMYSQL()
    {
        $return = array();
        if (function_exists('mysql_connect')) {
            $return['comment'] = 'Installed';
            $return['status'] = true;
            $return['msg'] = 'MySQL or MySQLi extension installed';
        } else {
            $return['comment'] = 'Not Installed';
            $return['status'] = false;
            $return['msg'] = 'Required MySQL or MySQLi extension';
        }
        return $return;
    }


    public static function checkCURL()
    {
        $return = array();
        if (function_exists('curl_version')) {
            $ver = curl_version();
            $return['comment'] = 'Installed';//'v'.$ver['version'];
            $return['status'] = true;
            $return['msg'] = 'CURL extension installed';
        } else {
            $return['comment'] = 'Not Installed';
            $return['status'] = false;
            $return['msg'] = 'Required CURL extension';
        }
        return $return;
    }



    public static function checkShortOpenTag()
    {
        $return = array();
        if (@ini_get('short_open_tag')) {
            $return['comment'] = 'Enabled';
            $return['status'] = true;
            $return['msg'] = 'PHP short_open_tag enabled';
        } else {
            $return['comment'] = 'Disabled';
            $return['status'] = false;
            $return['msg'] = 'PHP short_open_tag must be enabled';
        }
        return $return;
    }


    public static function checkSafeMode()
    {
        $return = array();
        if (@ini_get('safe_mode')) {
            $return['comment'] = 'Enabled';
            $return['status'] = false;
            $return['msg'] = 'PHP safe_mode disabled';
        } else {
            $return['comment'] = 'Disabled';
            $return['status'] = true;
            $return['msg'] = 'PHP safe_mode must be disabled';
        }
        return $return;
    }

    public static function checkFileUploads()
    {
        $return = array();
        if (@ini_get('file_uploads')) {
            $return['comment'] = 'Enabled';
            $return['status'] = true;
            $return['msg'] = 'PHP: file_uploads enabled';
        } else {
            $return['comment'] = 'Disabled';
            $return['status'] = false;
            $return['msg'] = 'PHP: file_uploads must be enabled';
        }
        return $return;
    }





    public static function checkGD()
    {
        $return = array();
        if (function_exists('gd_info')) {
            $ver = gd_info();
            $return['comment'] = 'Installed';//$ver['GD Version'];
            $return['status'] = true;
            $return['msg'] = 'GD Extension must installed';
        } else {
            $return['comment'] = 'Not Installed';
            $return['status'] = false;
            $return['msg'] = 'GD Extension must be installed';
        }
        return $return;
    }

    public static function checkZLIB()
    {
        $return = array();
        $return['title'] = 'ZLib Extension';
        if (function_exists('gzcompress')) {
            $return['comment'] = 'Installed';
            $return['status'] = true;
            $return['msg'] = 'Zlib Extension installed';
        } else {
            $return['comment'] = 'Not Installed';
            $return['status'] = false;
            $return['msg'] = 'Zlib Extension must be installed';
        }
        return $return;
    }



    public static function checkModRewrite()
    {
        static $return;

        if (empty($return)) {
            $return = array();


            if (function_exists('apache_get_modules') && false) {
                if (in_array('mod_rewrite', apache_get_modules())) {
                    $return['comment'] = 'Installed';
                    $return['status'] = true;
                    $return['msg'] = 'Mod Rewrite Extension installed';
                } else {
                    $return['comment'] = 'Not Installed';
                    $return['status'] = false;
                    $return['msg'] = 'Mod Rewrite Extension must be installed';
                }
            } elseif (file_exists('.htaccess')) {
                ini_set('default_socket_timeout', 15);
                $scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
                $url = $scheme.'://'.$_SERVER['HTTP_HOST'].'/check/checkmodrewriteitdc.php';

                if (function_exists('curl_version')) {
                    $curl_handle = curl_init();
                    curl_setopt($curl_handle, CURLOPT_URL, $url);
                    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl_handle, CURLOPT_USERAGENT,
                        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.80 Safari/537.36');
                    $md = curl_exec($curl_handle);
                    curl_close($curl_handle);
                } else {
                    $md = file_get_contents($url);
                }

                if (!empty($md) && strpos($md, '{{MODREWRITEWORKS}}') !== false) {
                    $return['comment'] = 'Installed';
                    $return['status'] = true;
                    $return['msg'] = 'Mod Rewrite installed';
                } else {
                    $return['comment'] = 'Not Installed';
                    $return['status'] = false;
                    $return['msg'] = 'Mod Rewrite Extension must be installed';
                }
            } else {
                $return['comment'] = 'Upload .htacces file';
                $return['status'] = false;
                $return['msg'] = 'Upload .htacces file';
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
            if (function_exists('curl_version')) {
                $url = 'https://service.itdc.ge/api';
                $curl_handle = curl_init();
                curl_setopt($curl_handle, CURLOPT_URL, $url);
                curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl_handle, CURLOPT_USERAGENT,
                    'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.80 Safari/537.36');
                $data = curl_exec($curl_handle);
                curl_close($curl_handle);
            } else {
                $data = false;
            }

            if ($data) {
                $return['comment'] = 'Allowed';
                $return['status'] = true;
                $return['msg'] = 'Connection to service.itdc.ge allowed';
            } else {
                $return['comment'] = 'Not Allowed';
                $return['status'] = false;
                $return['msg'] = 'Connection to service.itdc.ge is unsuccessfull';
            }
        }
        return $return;
    }


    public static function checkMBString()
    {
        $return = array();
        if (extension_loaded('mbstring')) {
            //$ver = curl_version();
            $return['comment'] = 'Installed';
            $return['status'] = true;
            $return['msg'] = 'MB String Extension installed';
        } else {
            $return['comment'] = 'Not Installed';
            $return['status'] = false;
            $return['msg'] = 'MB String Extension must be installed';
        }
        return $return;
    }

    public static function checkXML()
    {
        $return = array();
        $return['title'] = 'XML Extension';
        if (extension_loaded('xml')) {
            $return['comment'] = 'Installed';
            $return['status'] = true;
            $return['msg'] = 'XML Extension installed';
        } else {
            $return['comment'] = 'Not Installed';
            $return['status'] = false;
            $return['msg'] = 'XML Extension must be installed';
        }
        return $return;
    }

    public static function checkDOM()
    {
        $return = array();
        $return['title'] = 'DOM Extension';
        if (extension_loaded('dom')) {
            $return['comment'] = 'Installed';
            $return['status'] = true;
            $return['msg'] = 'DOM Extension installed';
        } else {
            $return['comment'] = 'Not Installed';
            $return['status'] = false;
            $return['msg'] = 'DOM Extension must be installed';
        }
        return $return;
    }



    public static function checkJSON()
    {
        $return = array();
        $return['title'] = 'JSON Extension';
        if (function_exists('json_encode') && function_exists('json_decode')) {
            $return['comment'] = 'Installed';
            $return['status'] = true;
            $return['msg'] = 'JSON Extension installed';
        } else {
            $return['comment'] = 'Not Installed';
            $return['status'] = false;
            $return['msg'] = 'JSON Extension must be installed';
        }
        return $return;
    }

    public static function checkPDO()
    {
        $return = array();
        $return['title'] = 'PDO Extension';
        if (extension_loaded('pdo')) {
            $return['comment'] = 'Installed';
            $return['status'] = true;
            $return['msg'] = 'PDO Extension installed';
        } else {
            $return['comment'] = 'Not Installed';
            $return['status'] = false;
            $return['msg'] = 'PDO Extension must be installed';
        }
        return $return;
    }

    public static function checkMemcached()
    {
        $return = array();
        $return['title'] = 'Memcached Extension';
        if (extension_loaded('memcached')) {
            $return['comment'] = 'Installed';
            $return['status'] = true;
            $return['msg'] = 'Memcached Extension installed';
        } else {
            $return['comment'] = 'Not Installed';
            $return['status'] = false;
            $return['msg'] = 'Memcached Extension must be installed';
        }
        return $return;
    }

    public static function checkSOAP()
    {
        $return = array();
        $return['title'] = 'SOAP Extension';
        if (extension_loaded('soap')) {
            $return['comment'] = 'Installed';
            $return['status'] = true;
            $return['msg'] = 'SOAP Extension installed';
        } else {
            $return['comment'] = 'Not Installed';
            $return['status'] = false;
            $return['msg'] = 'SOAP Extension must be installed';
        }
        return $return;
    }

    public static function checkAPC()
    {
        $return = array();
        $return['title'] = 'APC Extension';
        if (extension_loaded('apc')) {
            $return['comment'] = 'Installed';
            $return['status'] = true;
            $return['msg'] = 'APC Extension installed';
        } else {
            $return['comment'] = 'Not Installed';
            $return['status'] = false;
            $return['msg'] = 'APC Extension must be installed';
        }
        return $return;
    }

    public static function checkEAccelerator()
    {
        $return = array();
        $return['title'] = 'eAccelerator Extension';
        if (extension_loaded('eaccelerator')) {
            $return['comment'] = 'Installed';
            $return['status'] = true;
            $return['msg'] = 'eAccelerator Extension installed';
        } else {
            $return['comment'] = 'Not Installed';
            $return['status'] = false;
            $return['msg'] = 'eAccelerator Extension must be installed';
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
                    $curl_handle = curl_init();
                    curl_setopt($curl_handle, CURLOPT_URL, $url);
                    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl_handle, CURLOPT_USERAGENT,
                        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.80 Safari/537.36');
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
                $return['comment'] = 'Allowed';
                $return['status'] = true;
                $return['msg'] = 'Connection to Google Servers allowed';
            } else {
                $return['comment'] = 'Not Allowed';
                $return['status'] = false;
                $return['msg'] = 'Connection to Google Servers must be allowed';
            }
        }

        return $return;
    }

    public static function checkMYSQLi()
    {
        $return = array();
        $return['title'] = 'MySQLi Extension';
        if (function_exists('mysqli_connect')) {
            $return['comment'] = 'Installed';
            $return['status'] = true;
            $return['msg'] = 'MySQLi Extension installed';
        } else {
            $return['comment'] = 'Not Installed';
            $return['status'] = false;
            $return['msg'] = 'MySQLi Extension must be installed';
        }
        return $return;
    }


}
function ip_in_network($ip, $net_addr, $net_mask){
    if ($net_mask <= 0) {
        return false;
    }
    $ip_binary_string = sprintf("%032b",ip2long($ip));
    $net_binary_string = sprintf("%032b",ip2long($net_addr));
    return (substr_compare($ip_binary_string,$net_binary_string,0,$net_mask) === 0);
}



function is_debug_mode() {
    $client_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    $is_remote = filter_var(
                $client_ip,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            );

    if (!$is_remote) {
        return true;
    }
    $arr = array('92.241.86.122', '95.104.105.5');
    if (in_array($client_ip, $arr)) {
        return true;
    }
    return false;
}