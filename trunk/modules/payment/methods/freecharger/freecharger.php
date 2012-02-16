<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright (c) 2008, 2009, 2010, 2011, 2012 PhreeSoft, LLC       |
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
//  Path: /modules/payment/methods/freecharger/freecharger.php
//
// Revision history
// 2011-07-01 - Added version number for revision control
define('MODULE_PAYMENT_FREECHARGER_VERSION','3.2');

class freecharger {
  var $code;

  function freecharger() {
    $this->code = 'freecharger';
    $this->payment_fields = db_prepare_input($_POST['directdebit_ref']);
  }

  function keys() {
    return array(
      array('key' => 'MODULE_PAYMENT_FREECHARGER_SORT_ORDER', 'default' => '45'),
    );
  }

  function update() {
    foreach ($this->keys() as $key) {
      $field = strtolower($key['key']);
      if (isset($_POST[$field])) write_configure($key['key'], $_POST[$field]);
    }
  }

  function javascript_validation() {
    return false;
  }

  function selection() {
    return array(
      'id'     => $this->code,
      'page' => MODULE_PAYMENT_FREECHARGER_TEXT_TITLE,
    );
  }

  function pre_confirmation_check() {
    return false;
  }

  function confirmation() {
    return array('title' => MODULE_PAYMENT_FREECHARGER_TEXT_DESCRIPTION);
  }

  function before_process() {
    return false;
  }
}
?>