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
//  Path: /modules/phreebooks/pages/status/pre_process.php
//
/**************   Check user security   *****************************/
define('JOURNAL_ID', $_GET['jID']);
switch (JOURNAL_ID) {
  case  2: $security_token = SECURITY_ID_JOURNAL_ENTRY;      break;
  case  3: $security_token = SECURITY_ID_PURCHASE_QUOTE;     break;
  case  4: $security_token = SECURITY_ID_PURCHASE_ORDER;     break;
  case  6: $security_token = SECURITY_ID_PURCHASE_INVENTORY; break;
  case  7: $security_token = SECURITY_ID_PURCHASE_CREDIT;    break;
  case  9: $security_token = SECURITY_ID_SALES_QUOTE;        break;
  case 10: $security_token = SECURITY_ID_SALES_ORDER;        break;
  case 12: $security_token = SECURITY_ID_SALES_INVOICE;      break;
  case 13: $security_token = SECURITY_ID_SALES_CREDIT;       break;
  case 18: $security_token = SECURITY_ID_CUSTOMER_RECEIPTS;  break;
  case 20: $security_token = SECURITY_ID_PAY_BILLS;          break;
  default:
    die('Bad or missing journal id found (filename: modules/phreebooks/status.php), Journal_ID needs to be passed to this script to identify the correct procedure.');
}
$security_level = validate_user($security_token);
/**************  include page specific files    *********************/
require(DIR_FS_WORKING . 'functions/phreebooks.php');
/**************   page specific initialization  *************************/
$list_order  = isset($_GET['list_order']) ? $_GET['list_order']             : 'post_date-desc';
$acct_period = $_GET['search_period']     ? $_GET['search_period']          : CURRENT_ACCOUNTING_PERIOD;
$search_text = $_POST['search_text']      ? db_input($_POST['search_text']) : db_input($_GET['search_text']);
$action      = isset($_GET['action'])     ? $_GET['action']                 : $_POST['todo'];
if ($search_text == TEXT_SEARCH)    $search_text = '';
if (!$action && $search_text <> '') $action      = 'search'; // if enter key pressed and search not blank
$date_today = date('Y-m-d');
/***************   hook for custom actions  ***************************/
$custom_path = DIR_FS_WORKING . 'custom/pages/status/extra_actions.php';
if (file_exists($custom_path)) { include($custom_path); }
/***************   Act on the action request   *************************/
switch ($action) {
  case 'toggle':
    $id     = db_prepare_input($_POST['rowSeq']);
	$result = $db->Execute("select waiting from " . TABLE_JOURNAL_MAIN . " where id = '" . $id . "'");
	$toggle = $result->fields['waiting'] ? '0' : '1';
	$db->Execute("update " . TABLE_JOURNAL_MAIN . " set waiting = '" . $toggle . "' where id = '" . $id . "'");
    break;
  case 'dn_attach':
	$oID = db_prepare_input($_POST['rowSeq']);
	if (file_exists(PHREEBOOKS_DIR_MY_ORDERS . 'order_' . $oID . '.zip')) {
	  require_once(DIR_FS_MODULES . 'phreedom/classes/backup.php');
	  $backup = new backup();
	  $backup->download(PHREEBOOKS_DIR_MY_ORDERS, 'order_' . $oID . '.zip', true);
	}
	die;
  case 'go_first':    $_GET['list'] = 1;     break;
  case 'go_previous': $_GET['list']--;       break;
  case 'go_next':     $_GET['list']++;       break;
  case 'go_last':     $_GET['list'] = 99999; break;
  case 'search':
  case 'search_reset':
  case 'go_page':
  default:
}

/*****************   prepare to display templates  *************************/
// Set up some default lists
$heading_array = array(
  'post_date'           => TEXT_DATE,
  'purchase_invoice_id' => constant('ORD_HEADING_NUMBER_' . JOURNAL_ID),
  'bill_primary_name'   => in_array(JOURNAL_ID, array(9,10,12,13,19)) ? ORD_CUSTOMER_NAME : ORD_VENDOR_NAME,
  'purch_order_id'      => TEXT_REFERENCE,
  'closed'              => TEXT_CLOSED,
  'total_amount'        => TEXT_AMOUNT,
);
$extras     = array(TEXT_ACTION);
$field_list = array('id', 'journal_id', 'post_date', 'purchase_invoice_id', 'purch_order_id', 'store_id', 'closed', 'waiting', 
  'bill_primary_name', 'total_amount', 'currencies_code', 'currencies_value', 'shipper_code');
$search_fields = array('bill_primary_name', 'purchase_invoice_id', 'purch_order_id', 'store_id', 'bill_postal_code', 'ship_primary_name', 'total_amount');

switch (JOURNAL_ID) {
  case  2:	// Purchase Quote Journal
	define('POPUP_FORM_TYPE','');
	$heading_array['bill_primary_name'] = TEXT_DESCRIPTION;
	$page_title = ORD_TEXT_2_WINDOW_TITLE;
	break;
  case  3:	// Purchase Quote Journal
	define('POPUP_FORM_TYPE','vend:quot');
	$page_title = ORD_TEXT_3_WINDOW_TITLE;
	break;
  case  4:	// Purchase Order Journal
	define('POPUP_FORM_TYPE','vend:po');
	$page_title = ORD_TEXT_4_WINDOW_TITLE;
	break;
  case  6:	// Purchase Journal
	define('POPUP_FORM_TYPE','');
	$heading_array['closed'] = ORD_WAITING;
	$page_title = ORD_TEXT_6_WINDOW_TITLE;
	break;
  case  7:	// Vendor Credit Memo Journal
	define('POPUP_FORM_TYPE','vend:cm');
	$heading_array['closed'] = ORD_WAITING;
	$page_title = ORD_TEXT_7_WINDOW_TITLE;
	break;
  case  9:	// Sales Quote Journal
	define('POPUP_FORM_TYPE','cust:quot');
	$page_title = ORD_TEXT_9_WINDOW_TITLE;
	break;
  case 10:	// Sales Order Journal
	define('POPUP_FORM_TYPE','cust:so');
	$page_title = ORD_TEXT_10_WINDOW_TITLE;
	break;
  case 12:	// Invoice Journal
	if (defined('MODULE_SHIPPING_STATUS')) {
	  $shipping_modules = load_all_methods('shipping');
	  $heading_array['shipper_code'] = TEXT_CARRIER;
	}
	define('POPUP_FORM_TYPE','cust:inv');
	$heading_array['closed'] = TEXT_PAID;
	$page_title = ORD_TEXT_12_WINDOW_TITLE;
	break;
  case 13:	// Customer Credit Memo Journal
	define('POPUP_FORM_TYPE','cust:cm');
	$heading_array['closed'] = TEXT_PAID;
	$page_title = ORD_TEXT_13_WINDOW_TITLE;
	break;
  case 18:	// Cash Receipts Journal
	define('POPUP_FORM_TYPE','bnk:rcpt');
	$page_title = ORD_TEXT_18_C_WINDOW_TITLE;
	break;
  case 20:	// Cash Distribution Journal
	define('POPUP_FORM_TYPE','bnk:chk');
	$page_title = ORD_TEXT_20_V_WINDOW_TITLE;
	break;
default:
}

$result      = html_heading_bar($heading_array, $list_order, $extras);
$list_header = $result['html_code'];
$disp_order  = $result['disp_order'];

// build the list for the page selected
$period_filter = ($acct_period == 'all') ? '' : (' and period = ' . $acct_period);
if (isset($search_text) && $search_text <> '') {
  // hook for inserting new search fields to the query criteria.
  if (is_array($extra_search_fields)) $search_fields = array_merge($search_fields, $extra_search_fields);
  $search = ' and (' . implode(' like \'%' . $search_text . '%\' or ', $search_fields) . ' like \'%' . $search_text . '%\')';
} else {
  $search = '';
}

// hook to add new fields to the query return results
if (is_array($extra_query_list_fields) > 0) $field_list = array_merge($field_list, $extra_query_list_fields);

$query_raw    = "select " . implode(', ', $field_list) . " from " . TABLE_JOURNAL_MAIN . " 
	where journal_id = " . JOURNAL_ID . $period_filter . $search . " order by $disp_order, purchase_invoice_id DESC";
$query_split  = new splitPageResults($_GET['list'], MAX_DISPLAY_SEARCH_RESULTS, $query_raw, $query_numrows);
$query_result = $db->Execute($query_raw);

$include_header   = true;
$include_footer   = true;
$include_tabs     = false;
$include_calendar = true;
$include_template = 'template_main.php';
define('PAGE_TITLE', sprintf(BOX_STATUS_MGR, $page_title));

?>