<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright(c) 2008-2013 PhreeSoft, LLC (www.PhreeSoft.com)       |

// +-----------------------------------------------------------------+
// | This program is free software: you can redistribute it and/or   |
// | modify it under the terms of the GNU General Public License as  |
// | published by the Free Software Foundation, either version 3 of  |
// | the License, or any later version.                              |
// |                                                                 |
// | This program is distributed in the hope that it will be useful, |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of  |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the   |
// | GNU General Public License for more details.                    |
// +-----------------------------------------------------------------+
//  Path: /includes/template_index.php
//
if ($custom_html) { // load the template only as the rest of the html will be generated by the template
  if (is_file($template_path)) { require($template_path); } else die('No template file. Looking for: ' . $template_path);
} else {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php echo HTML_PARAMS; ?>>
 <head>
  <!-- module: <?php echo $module; ?> - page: <?php echo $page; ?> -->
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>" />
<?php if ($force_reset_cache) { header("Cache-Control: no-cache, must-revalidate"); header("Expires: ".date('D, j M \2\0\0\0 G:i:s T')); } ?>  
  <title><?php echo PAGE_TITLE; ?></title>
  <link rel="shortcut icon" type="image/ico" href="favicon.ico" />
  <link rel="stylesheet" type="text/css" href="<?php echo DIR_WS_THEMES.'css/'.MY_COLORS.'/stylesheet.css'; ?>" />
  <link rel="stylesheet" type="text/css" href="<?php echo DIR_WS_THEMES.'css/'.MY_COLORS.'/jquery_datatables.css'; ?>" />
  <link rel="stylesheet" type="text/css" href="<?php echo DIR_WS_THEMES.'css/'.MY_COLORS.'/jquery-ui.css'; ?>" />
  <script type="text/javascript">
  var module              = '<?php echo $module; ?>';
  var pbBrowser           = (document.all) ? 'IE' : 'FF';
  var text_search         = '<?php echo TEXT_SEARCH; ?>';
  var date_format         = '<?php echo DATE_FORMAT; ?>';
  var date_delimiter      = '<?php echo DATE_DELIMITER; ?>';
  var inactive_text_color = '#cccccc';
  var form_submitted      = false;
  // Variables for script generated combo boxes
  var icon_path           = '<?php echo DIR_WS_ICONS; ?>';
  var combo_image_on      = '<?php echo DIR_WS_ICONS . '16x16/phreebooks/pull_down_active.gif'; ?>';
  var combo_image_off     = '<?php echo DIR_WS_ICONS . '16x16/phreebooks/pull_down_inactive.gif'; ?>';
<?php if (is_object($currencies)) { // will not be defined unless logged in and db defined ?>
  var decimal_places      = <?php  echo $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']; ?>;
  var decimal_precise     = <?php  echo $currencies->currencies[DEFAULT_CURRENCY]['decimal_precise']; ?>;
  var decimal_point       = "<?php echo $currencies->currencies[DEFAULT_CURRENCY]['decimal_point']; ?>"; // leave " for ' separator
  var thousands_point     = "<?php echo $currencies->currencies[DEFAULT_CURRENCY]['thousands_point']; ?>";
  var formatted_zero      = "<?php echo $currencies->format(0); ?>";
<?php } ?>
  </script>
  <script type="text/javascript" src="includes/jquery-1.6.2.min.js"></script>
  <script type="text/javascript" src="includes/jquery-ui-1.8.16.custom.min.js"></script>
  <script type="text/javascript" src="includes/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
  <script type="text/javascript" src="includes/common.js"></script>
<?php 
require_once(DIR_FS_ADMIN . DIR_WS_THEMES . '/config.php');
// load the jquery and javascript translations
if      (file_exists($js_i18n = 'modules/phreedom/custom/language/'.$_SESSION['language'].'/jquery_i18n.js')) {
} elseif(file_exists($js_i18n = 'modules/phreedom/language/'       .$_SESSION['language'].'/jquery_i18n.js')) {
} else               $js_i18n = 'modules/phreedom/language/en_us/jquery_i18n.js';
echo '  <script type="text/javascript" src="'.$js_i18n.'"></script>'."\n";
// load the javascript specific, required
$js_include_path = DIR_FS_WORKING . 'pages/' . $page . '/js_include.php';
if (file_exists($js_include_path)) { require_once($js_include_path); } 
  else die('No js_include file, looking for the file: ' . $js_include_path);
// load the custom javascript if present
$js_include_path = DIR_FS_WORKING . 'custom/pages/' . $page . '/extra_js.php';
if (file_exists($js_include_path)) { require_once($js_include_path); }
if (SESSION_AUTO_REFRESH == '1') echo '  <script type="text/javascript">addLoadEvent(refreshSessionClock);</script>' . chr(10);
?>
  <script type="text/javascript">addLoadEvent(init);</script>
 </head>
 <body>
  <script type="text/javascript" src="modules/phreedom/includes/wz_tooltip/wz_tooltip.js"></script>
  <script type="text/javascript" src="modules/phreedom/includes/wz_tooltip/tip_balloon.js"></script>  
  <div id="please_wait"><p><?php echo html_icon('phreebooks/please_wait.gif', TEXT_PLEASE_WAIT, 'large'); ?></p></div>
  <!-- Menu -->
  <?php if ($include_header) { require_once(DIR_FS_ADMIN . DIR_WS_THEMES . '/menu.php'); } else echo "<div>\n"?>
  <!-- Template -->
  <?php if (is_file($template_path)) { require($template_path); } else trigger_error('No template file: ' . $template_path, E_USER_ERROR); ?>
  </div>
  <!-- Footer -->
  <?php if ($include_footer) { // Hook for custom logo
  $image_path = defined('FOOTER_LOGO') ? FOOTER_LOGO : (DIR_WS_ADMIN . 'modules/phreedom/images/phreesoft_logo.png');
  ?>
  <div style="clear:both;text-align:center;font-size:9px">
    <a href="http://www.PhreeSoft.com" target="_blank"><?php echo html_image($image_path, TEXT_PHREEDOM_INFO, NULL, '64'); ?></a><br />
  <?php 
  $footer_info  = COMPANY_NAME.' | '.TEXT_ACCOUNTING_PERIOD.': '.CURRENT_ACCOUNTING_PERIOD.' | '.TEXT_PHREEDOM_INFO.' ('.MODULE_PHREEDOM_VERSION.') ';
  if ($module <> 'phreedom') $footer_info .= '(' . $module . ' ' . constant('MODULE_' . strtoupper($module) . '_STATUS') . ') ';
  $footer_info .= '<br />' . TEXT_COPYRIGHT .  ' &copy;' . date('Y') . ' <a href="http://www.PhreeSoft.com" target="_blank">PhreeSoft, LLC&trade;</a>';
  $footer_info .= '(' . (int)(1000 * (microtime(true) - PAGE_EXECUTION_START_TIME)) . ' ms) ' . $db->count_queries . ' SQLs (' . (int)($db->total_query_time * 1000).' ms)';
  echo $footer_info;
  ?>
  </div>
  <?php } // end if include_footer ?>
</body>
</html>
<?php } // end else if (custom_html) ?>
