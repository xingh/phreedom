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
//  Path: /modules/payment/pages/admin/template_tab_methods.php
//

?>
<div id="methods" class="tabset_content">
  <h2 class="tabset_label"><?php echo TEXT_PAYMENT_METHODS; ?></h2>
  <fieldset class="formAreaTitle">
    <table border="0" width="100%" cellspacing="0" cellpadding="1">
	  <tr>
	    <th colspan="2"><?php echo TEXT_PAYMENT_MODULES_AVAILABLE; ?></th>
	    <th><?php echo TEXT_SORT_ORDER; ?></th>
	    <th><?php echo TEXT_ACTION; ?></th>
	  </tr>
<?php 
  if (sizeof($methods) > 0) foreach ($methods as $method) {
    $installed = defined('MODULE_PAYMENT_' . strtoupper($method) . '_STATUS');
	$bkgnd = $installed ? ' style="background-color:lightgreen"' : '';
	if (file_exists(DIR_WS_MODULES . 'payment/methods/' . $method . '/images/logo.png')) {
	  $logo = DIR_WS_MODULES . 'payment/methods/' . $method . '/images/logo.png';
	} elseif (file_exists(DIR_WS_MODULES . 'payment/methods/' . $method . '/images/logo.jpg')) {
	  $logo = DIR_WS_MODULES . 'payment/methods/' . $method . '/images/logo.jpg';
	} elseif (file_exists(DIR_WS_MODULES . 'payment/methods/' . $method . '/images/logo.gif')) {
	  $logo = DIR_WS_MODULES . 'payment/methods/' . $method . '/images/logo.gif';
	} else {
	  $logo = DIR_WS_MODULES . 'payment/images/no_logo.png';
	}
	echo '      <tr' . $bkgnd . '>' . chr(10);
	echo '        <td>' . html_image($logo, constant('MODULE_PAYMENT_' . strtoupper($method) . '_TEXT_TITLE'), $width = '', $height = '32', $params = '') . '</td>' . chr(10);
	echo '        <td>' . 
	  constant('MODULE_PAYMENT_' . strtoupper($method) . '_TEXT_TITLE') . ' - ' . 
	  constant('MODULE_PAYMENT_' . strtoupper($method) . '_TEXT_DESCRIPTION') . '</td>' . chr(10);
	if (!$installed) {
      echo '        <td align="center">&nbsp;</td>' . chr(10);
	  if ($security_level > 1) echo '        <td align="center">' . html_button_field('btn_' . $method, TEXT_INSTALL, 'onclick="submitToDo(\'install_' . $method . '\')"') . '</td>' . chr(10);
	  echo '      </tr>' . chr(10);
	} else {
	  echo '        <td align="center">' . constant('MODULE_PAYMENT_' . strtoupper($method) . '_SORT_ORDER') . '</td>' . chr(10);
	  echo '        <td align="center" nowrap="nowrap">' . chr(10);
	  if ($security_level > 3) echo html_button_field('btn_' . $method, TEXT_REMOVE, 'onclick="if (confirm(\'' . TEXT_REMOVE_MESSAGE . '\')) submitToDo(\'remove_' . $method . '\')"') . chr(10);
	  echo html_icon('categories/preferences-system.png', TEXT_PROPERTIES, 'medium', 'onclick="toggleProperties(\'prop_' . $method . '\')"') . chr(10);
	  echo '</td>' . chr(10);
	  echo '      </tr>' . chr(10);
	  // load the method properties
	  require_once($method_dir . $method . '/' . $method . '.php');
	  $properties = new $method();
	  echo '      <tr id="prop_' . $method . '" style="display:none"><td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="1">' . chr(10);
	  if (defined('MODULE_PAYMENT_' . strtoupper($method) . '_TEXT_INTRODUCTION')) {
	    echo '<tr><td colspan="2">' . constant('MODULE_PAYMENT_' . strtoupper($method) . '_TEXT_INTRODUCTION') . '</td></tr>';
	  }
	  $keys = $properties->keys();
	  $custom_method = (method_exists($properties, 'configure')) ? true : false;
	  foreach ($keys as $value) {
	    echo '<tr><td colspan="2">' . constant($value['key'] . '_DESC') . '</td><td nowrap="nowrap">';
		if ($custom_method) { echo $properties->configure($value['key']); } 
		else { echo html_input_field(strtolower($value['key']), constant($value['key']), $value['properties']); }
		echo '</td></tr>';
	  }
	  echo '      </table></td></tr>' . chr(10);
	}
  }
?>
	</table>
  </fieldset>
</div>
