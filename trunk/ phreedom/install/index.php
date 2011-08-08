<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright (c) 2008, 2009, 2010, 2011 PhreeSoft, LLC             |
// | http://www.PhreeSoft.com                                        |
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
// Path: /install/index.php
//

error_reporting(E_ALL & ~E_NOTICE);
require('pages/main/pre_process.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml" <?php echo HTML_PARAMS; ?>>
 <head>
  <title><?php echo PAGE_TITLE; ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>" />
  <link rel="stylesheet" type="text/css" href="../themes/default/css/stylesheet.css" />
  <script type="text/javascript">
    var combo_image_on  = '';
    var combo_image_off = '';
  </script>
  <script type="text/javascript" src="../includes/common.js"></script>
  <?php require('pages/main/js_include.php'); ?>
 </head>
 <body>
  <?php require('pages/main/' . $include_template); ?>
 </body>
</html>
