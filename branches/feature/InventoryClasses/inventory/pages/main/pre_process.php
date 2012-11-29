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
//  Path: /modules/inventory/pages/main/pre_process.php
//
$security_level = validate_user(SECURITY_ID_MAINTAIN_INVENTORY);
/**************  include page specific files    *********************/
require_once(DIR_FS_WORKING . 'defaults.php');
require_once(DIR_FS_MODULES . 'phreebooks/functions/phreebooks.php');
require_once(DIR_FS_WORKING . 'functions/inventory.php');
require_once(DIR_FS_WORKING . 'classes/inventory_fields.php');
/**************   page specific initialization  *************************/
$error       = false;
$processed   = false;
$criteria    = array();
//$cInfo       = new objectInfo();
$fields		 = new inventory_fields();
$type        = isset($_GET['inventory_type']) ? $_GET['inventory_type'] : 'si'; // default to stock item
$search_text = ($_POST['search_text']) ? db_input($_POST['search_text']) : db_input($_GET['search_text']);
if ($search_text == TEXT_SEARCH) $search_text = '';
if (isset($_POST['search_text'])) $_GET['search_text'] = $_POST['search_text']; // save the value for get redirects 
$action      = isset($_GET['action']) ? $_GET['action'] : $_POST['todo'];
if (!$action && $search_text <> '') $action = 'search'; // if enter key pressed and search not blank
$first_entry = isset($_GET['add']) ? true : false;
// load the filters
$f0 = $_GET['f0'] = isset($_POST['todo']) ? (isset($_POST['f0']) ? '1' : '0') : $_GET['f0']; // show inactive checkbox
$f1 = $_GET['f1'] = isset($_POST['f1']) ? $_POST['f1'] : $_GET['f1']; // inventory_type dropdown
$id = isset($_POST['rowSeq']) ? (int)db_prepare_input($_POST['rowSeq']) : (int)db_prepare_input($_GET['cID']);

// getting the right inventory type.
$result = $db->Execute("select inventory_type from " . TABLE_INVENTORY . " where id = '" . $id  . "'");
if($action == 'create') $type = db_prepare_input($_POST['inventory_type']);
else if($result->RecordCount()>0) $type = $result->fields['inventory_type'];
else $type ='si';
if($type == 'as') $type = 'ma'; 
if (file_exists(DIR_FS_WORKING . 'custom/classes/type/'.$type.'.php')) { 
	require_once(DIR_FS_WORKING . 'custom/classes/type/'.$type.'.php'); 
}else{
	require_once(DIR_FS_WORKING . 'classes/type/'.$type.'.php'); // is needed here for the defining of the class and retriving the security_token
}
$cInfo = new $type();
/***************   hook for custom actions  ***************************/
$custom_path = DIR_FS_WORKING . 'custom/pages/main/extra_actions.php';
if (file_exists($custom_path)) { include($custom_path); }
/***************   Act on the action request   *************************/
switch ($action) {
  case 'create':
	if ($security_level < 2) {
	  $messageStack->add_session(ERROR_NO_PERMISSION, 'error');
	  gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
	  break;
	}
	if($cInfo->check_create_new()){
		$action = 'edit';
		$sku_history = gather_history($cInfo->sku);
	}
	//gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('cID', 'action')) . 'cID=' . $id . '&action=edit&add=1', 'SSL'));
	break;
  case 'save':
	if ($security_level < 2) {
	  $messageStack->add_session(ERROR_NO_PERMISSION,'error');
	  gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
	  break;
	}
	if(!$cInfo->save()){
		$action = 'edit';
		$sku_history = gather_history($cInfo->sku);
	}
	break;
/*	$id              = (int)db_prepare_input($_POST['rowSeq']);
	$sku             = db_prepare_input($_POST['sku']);
	$image_with_path = db_prepare_input($_POST['image_with_path']); // the current image name with path relative from my_files/company_db/inventory/images directory
	$inventory_path  = db_prepare_input($_POST['inventory_path']);
	if (substr($inventory_path, 0, 1) == '/')  $inventory_path = substr($inventory_path, 1); // remove leading '/' if there
	if (substr($inventory_path, -1, 1) == '/') $inventory_path = substr($inventory_path, 0, strlen($inventory_path)-1); // remove trailing '/' if there
	$inventory_type  = db_prepare_input($_POST['inventory_type']);
    $sql_data_array = $fields->what_to_save();
	$sql_data_array['last_update'] = date('Y-m-d H-i-s');
	// special cases for checkboxes of system fields (don't return a POST value if unchecked)
	$remove_image = $_POST['remove_image'] == '1' ? true : false;
	unset($sql_data_array['remove_image']); // this is not a db field, just an action
	$sql_data_array['inactive']   = ($sql_data_array['inactive'] == '1' ? '1' : '0');
	// special cases for monetary values in system fields
	$sql_data_array['full_price'] = $currencies->clean_value($sql_data_array['full_price']);
	if ($_SESSION['admin_security'][SECURITY_ID_PURCHASE_INVENTORY] > 1) {
	  $sql_data_array['item_cost']  = $currencies->clean_value($sql_data_array['item_cost']);
	}
	if ($inventory_type == 'ms') { // if it's a master stock item, process
	  $attributes = array(
		'attr_name_0' => db_prepare_input($_POST['attr_name_0']),
		'ms_attr_0'   => substr(db_prepare_input($_POST['ms_attr_0']), 0, -1),
		'attr_name_1' => db_prepare_input($_POST['attr_name_1']),
		'ms_attr_1'   => substr(db_prepare_input($_POST['ms_attr_1']), 0, -1),
	  );
	  save_ms_items($sql_data_array, $attributes);
	}
	// if it's an assembly, retrieve the BOM (if no journal_entries have been posted)
	$update_bom = false;
	if ($inventory_type == 'as' || $inventory_type == 'sa') {
	  $result = $db->Execute("select last_journal_date from " . TABLE_INVENTORY . " where id = " . $id);
	  if ($result->fields['last_journal_date'] == '0000-00-00 00:00:00') { // only update if no posting has been performed
		$x = 1;
		$bom_array = array();
		for($x=0; $x<count($_POST['assy_sku']); $x++) {
		  $assy_sku = db_prepare_input($_POST['assy_sku'][$x]);
		  $assy_qty = $currencies->clean_value(db_prepare_input($_POST['assy_qty'][$x]));
		  if (gen_validate_sku($assy_sku) && $assy_qty > 0) { // error check sku is valid and qty > 0
			$bom_array[] = array(
			  'ref_id'      => $id,
			  'sku'         => $assy_sku,
			  'description' => db_prepare_input($_POST['assy_desc'][$x]),
			  'qty'         => $assy_qty,
			);
		  } elseif ($assy_sku <> '' || $assy_qty < 0) { // show error, bad sku, negative quantity, skip the blank lines
			$messageStack->add(INV_ERROR_BAD_SKU . $assy_sku, 'error');
		  }
		}
		$update_bom = true;
	  }
	}
	$img = $db->Execute("select image_with_path from " . TABLE_INVENTORY . " where id = " . $id);
	$file_path = DIR_FS_MY_FILES . $_SESSION['company'] . '/inventory/images';
	if ($remove_image) { // update the image with relative path
	  if ($img->fields['image_with_path'] && file_exists($file_path . '/' . $img->fields['image_with_path'])) unlink ($file_path . '/' . $img->fields['image_with_path']);
	  $_POST['image_with_path'] = '';
	  $sql_data_array['image_with_path'] = ''; 
	}
	if (!$error && is_uploaded_file($_FILES['inventory_image']['tmp_name'])) {
	  if ($img->fields['image_with_path'] && file_exists($file_path . '/' . $img->fields['image_with_path'])) unlink ($file_path . '/' . $img->fields['image_with_path']);
      $inventory_path = str_replace('\\', '/', $inventory_path);
	  // strip beginning and trailing slashes if present
	  if (substr($inventory_path, -1, 1) == '/') $inventory_path = substr($inventory_path, 0, -1);
	  if (substr($inventory_path, 0, 1) == '/') $inventory_path = substr($inventory_path, 1);
	  if ($inventory_path) $file_path .= '/' . $inventory_path;
	  $temp_file_name = $_FILES['inventory_image']['tmp_name'];
	  $file_name = $_FILES['inventory_image']['name'];
	  if (!validate_path($file_path)) {
		$messageStack->add(INV_IMAGE_PATH_ERROR, 'error');
		$error = true;
	  } elseif (!validate_upload('inventory_image', 'image', 'jpg')) {
		$messageStack->add(INV_IMAGE_FILE_TYPE_ERROR, 'error');
		$error = true;
	  } else { // passed all test, write file
		if (!copy($temp_file_name, $file_path . '/' . $file_name)) {
		  $messageStack->add(INV_IMAGE_FILE_WRITE_ERROR, 'error');
		  $error = true;
		} else {
		  $image_with_path = ($inventory_path ? ($inventory_path . '/') : '') . $file_name;
		  $_POST['image_with_path'] = $image_with_path;
		  $sql_data_array['image_with_path'] = $image_with_path; // update the image with relative path
		}
	  }
	}
	if (!$error) {
	  if (($inventory_type == 'as' || $inventory_type == 'sa') && $update_bom) {
		$result = $db->Execute("delete from " . TABLE_INVENTORY_ASSY_LIST . " where ref_id = " . $id);
		while ($list_array = array_shift($bom_array)) {
		  db_perform(TABLE_INVENTORY_ASSY_LIST, $list_array, 'insert');
		}
	  }
	  if (is_array($extra_sql_data_array)) $sql_data_array = array_merge($sql_data_array, $extra_sql_data_array);
	  db_perform(TABLE_INVENTORY, $sql_data_array, 'update', "id = " . $id);
	  gen_add_audit_log(INV_LOG_INVENTORY . TEXT_UPDATE, $sku . ' - ' . $sql_data_array['description_short']);
	} else if ($error == true) {
	  $tab_list = $db->Execute("select id, tab_name, description 
		from " . TABLE_EXTRA_TABS . " where module_id='inventory' order by sort_order");
	  $_POST['id'] = $id;
	  $cInfo = new objectInfo($_POST);
	  $processed = true;
	}*/
	//break;

  case 'delete':
	if ($security_level < 4) {
	  $messageStack->add_session(ERROR_NO_PERMISSION,'error');
	  gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
	  break;
	}
	$id  = db_prepare_input($_GET['cID']);
	$cInfo->check_remove($id);
	break;

  case 'copy': 	// Pictures are not copied over...
	if ($security_level < 2) {
	  $messageStack->add_session(ERROR_NO_PERMISSION,'error');
	  gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
	  break;
	}
	$id  = db_prepare_input($_GET['cID']);
	$sku = db_prepare_input($_GET['sku']);
	if($cInfo->copy($id, $sku)){
		$action = 'edit';
		$sku_history = gather_history($cInfo->sku);
	}
	break;
/*	// check for duplicate skus
	$result = $db->Execute("select id from " . TABLE_INVENTORY . " where sku = '" . $sku . "'");
	if ($result->Recordcount() > 0) {	// error and reload
	$messageStack->add(INV_ERROR_DUPLICATE_SKU, 'error');
	break;
	}
	$result = $db->Execute("select * from " . TABLE_INVENTORY . " where id = " . $id);
	$old_sku = $result->fields['sku'];
	// clean up the fields (especially the system fields, retain the custom fields)
	$output_array = array();
	foreach ($result->fields as $key => $value) {
	  switch ($key) {
		case 'id':	// Remove from write list fields
		case 'last_journal_date':
		case 'item_cost':
		case 'upc_code':
		case 'image_with_path':
		case 'quantity_on_hand':
		case 'quantity_on_order':
		case 'quantity_on_sales_order':
		  break;
		case 'sku': // set the new sku
		  $output_array[$key] = $sku;
		  break;
		case 'creation_date':
		case 'last_update':
		  $output_array[$key] = date('Y-m-d H:i:s');
		  break;
		default:
		  $output_array[$key] = $value;
	  }
	}
	db_perform(TABLE_INVENTORY, $output_array, 'insert');
	$new_id = db_insert_id();
	if ($result->fields['inventory_type'] == 'ms') { // create master stock items
	  $ms_result   = $db->Execute("select * from " . TABLE_INVENTORY_MS_LIST . " where sku = '" . $old_sku . "'");
	  $ms_attr_0   = ($ms_result->RecordCount() > 0) ? $ms_result->fields['attr_0'] : '';
	  $attr_name_0 = ($ms_result->RecordCount() > 0) ? $ms_result->fields['attr_name_0'] : '';
	  $ms_attr_1   = ($ms_result->RecordCount() > 0) ? $ms_result->fields['attr_1'] : '';
	  $attr_name_1 = ($ms_result->RecordCount() > 0) ? $ms_result->fields['attr_name_1'] : '';
	  $attributes  = array(
		'attr_name_0' => $attr_name_0,
		'ms_attr_0'   => $ms_attr_0,
		'attr_name_1' => $attr_name_1,
		'ms_attr_1'   => $ms_attr_1);
	  save_ms_items($output_array, $attributes);
	}
	if ($result->fields['inventory_type'] == 'as' ||$result->fields['inventory_type'] == 'sa') { // copy assembly list if it's an assembly
	  $result = $db->Execute("select sku, description, qty from " . TABLE_INVENTORY_ASSY_LIST . " where ref_id = " . $id);
	  while (!$result->EOF) {
		$output_array = array(
		  'ref_id'      => $new_id,
		  'sku'         => $result->fields['sku'],
		  'description' => $result->fields['description'],
		  'qty'         => $result->fields['qty'],
		);
		db_perform(TABLE_INVENTORY_ASSY_LIST, $output_array, 'insert');
		$result->MoveNext();
	  }
	}
	// copy over price sheet
	$result = $db->Execute("select price_sheet_id, price_levels 
	  from " . TABLE_INVENTORY_SPECIAL_PRICES . " where inventory_id = " . $id);
	while(!$result->EOF) {
	  $output_array = array(
		'inventory_id'   => $new_id,
		'price_sheet_id' => $result->fields['price_sheet_id'],
		'price_levels'   => $result->fields['price_levels'],
	  );
	  db_perform(TABLE_INVENTORY_SPECIAL_PRICES, $output_array, 'insert');
	  $result->MoveNext();
	}
	// now continue with newly copied item by editing it
	gen_add_audit_log(INV_LOG_INVENTORY . TEXT_COPY, $old_sku . ' => ' . $sku);
	$_POST['rowSeq'] = $new_id;	// set item pointer to new record
	$action = 'edit'; // fall through to edit case
*/
  case 'edit':
  case 'properties':
    /*$id = isset($_POST['rowSeq']) ? (int)db_prepare_input($_POST['rowSeq']) : (int)db_prepare_input($_GET['cID']);
    $result = $db->Execute("select * from " . TABLE_INVENTORY . " where id = '" . $id  . "'");
    $type = $result->fields['inventory_type'];
    if (file_exists(DIR_FS_WORKING . 'custom/classes/type/'.$type.'.php')) { 
		require_once(DIR_FS_WORKING . 'custom/classes/type/'.$type.'.php'); 
	}else{
    	require_once(DIR_FS_WORKING . 'classes/type/'.$type.'.php'); // is needed here for the defining of the class and retriving the security_token
	}
	$cInfo = new $type();*/
	$cInfo->get_item_by_id($id);
	// gather the history
	$sku_history = gather_history($cInfo->sku);
	break;

  case 'rename':
	if ($security_level < 4) {
	  $messageStack->add_session(ERROR_NO_PERMISSION,'error');
	  gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
	  break;
	}
	$id  = db_prepare_input($_GET['cID']);
	$sku = db_prepare_input($_GET['sku']);
	$cInfo->rename($id, $sku);
	// check for duplicate skus
/*	$result = $db->Execute("select id from " . TABLE_INVENTORY . " where sku = '" . $sku . "'");
	if ($result->Recordcount() > 0) {	// error and reload
	  $messageStack->add(INV_ERROR_DUPLICATE_SKU, 'error');
	  break;
	}
	$result = $db->Execute("select sku, inventory_type from " . TABLE_INVENTORY . " where id = " . $id);
	$orig_sku = $result->fields['sku'];
	$inventory_type = $result->fields['inventory_type'];
	$sku_list = array($orig_sku);
	if ($inventory_type == 'ms') { // build list of sku's to rename (without changing contents)
	  $result = $db->Execute("select sku from " . TABLE_INVENTORY . " where sku like '" . $orig_sku . "-%'");
	  while(!$result->EOF) {
		$sku_list[] = $result->fields['sku'];
		$result->MoveNext();
	  }
	}
	// start transaction (needs to all work or reset to avoid unsyncing tables)
	$db->transStart();
	// rename the afffected tables
	for ($i = 0; $i < count($sku_list); $i++) {
	  $new_sku = str_replace($orig_sku, $sku, $sku_list[$i], $count = 1);
	  $result = $db->Execute("update " . TABLE_INVENTORY .           " set sku = '" . $new_sku . "' where sku = '" . $sku_list[$i] . "'");
	  $result = $db->Execute("update " . TABLE_INVENTORY_ASSY_LIST . " set sku = '" . $new_sku . "' where sku = '" . $sku_list[$i] . "'");
	  $result = $db->Execute("update " . TABLE_INVENTORY_COGS_OWED . " set sku = '" . $new_sku . "' where sku = '" . $sku_list[$i] . "'");
	  $result = $db->Execute("update " . TABLE_INVENTORY_HISTORY .   " set sku = '" . $new_sku . "' where sku = '" . $sku_list[$i] . "'");
	  $result = $db->Execute("update " . TABLE_INVENTORY_MS_LIST .   " set sku = '" . $new_sku . "' where sku = '" . $sku_list[$i] . "'");
	  $result = $db->Execute("update " . TABLE_JOURNAL_ITEM .        " set sku = '" . $new_sku . "' where sku = '" . $sku_list[$i] . "'");
	}
	$db->transCommit();*/
	break;

  case 'go_first':    $_GET['list'] = 1;     break;
  case 'go_previous': $_GET['list']--;       break;
  case 'go_next':     $_GET['list']++;       break;
  case 'go_last':     $_GET['list'] = 99999; break;
  case 'search':
  case 'search_reset':
  case 'go_page':
  case 'new':
  default:
}

/*****************   prepare to display templates  *************************/
// build the type filter list
$type_select_list = array( // add some extra options
  array('id' => '0',   'text' => TEXT_ALL),
  array('id' => 'cog', 'text' => TEXT_INV_MANAGED),
);
foreach ($inventory_types as $key => $value) $type_select_list[] = array('id' => $key,  'text' => $value);

// load the tax rates
$tax_rates        = inv_calculate_tax_drop_down('c',false);
$purch_tax_rates  = inv_calculate_tax_drop_down('v',false);
// load gl accounts
$gl_array_list    = gen_coa_pull_down();
$include_header   = true;
$include_footer   = true;
$include_tabs     = true;
$include_calendar = false;

switch ($action) {
  case 'new':
    define('PAGE_TITLE', BOX_INV_NEW);
	$include_tabs     = false;
    $include_template = 'template_id.php';
	break;
  case 'create':
  case 'edit':
    define('PAGE_TITLE', BOX_INV_MAINTAIN);
    $include_tabs     = true;
    $include_template = 'template_detail.php';
    break;
  case 'properties':
    define('PAGE_TITLE', BOX_INV_MAINTAIN);
	$include_header   = false;
	$include_footer   = false;
    $include_tabs     = true;
    $include_template = 'template_detail.php';
    break;
  default:
    // build the list header
	$heading_array = array(
	  'sku'                     => TEXT_SKU,
	  'inactive'                => TEXT_INACTIVE,
	  'description_short'       => TEXT_DESCRIPTION,
	  'quantity_on_hand'        => INV_HEADING_QTY_ON_HAND,
	  'quantity_on_sales_order' => INV_HEADING_QTY_ON_SO,
	  'quantity_on_allocation'  => INV_HEADING_QTY_ON_ALLOC,
	  'quantity_on_order'       => INV_HEADING_QTY_ON_ORDER,
	);
	$result      = html_heading_bar($heading_array, $_POST['sort_field'], $_POST['sort_order']);
	$list_header = $result['html_code'];
	$disp_order  = $result['disp_order'];
	if($disp_order == 'sku') $disp_order ='(sku+0)';
	if($disp_order == 'sku DESC')$disp_order ='(sku+0) DESC';
	// build the list for the page selected
    if (isset($search_text) && $search_text <> '') {
      $search_fields = array('sku', 'description_short', 'description_purchase');
	  // hook for inserting new search fields to the query criteria.
	  if (is_array($extra_search_fields)) $search_fields = array_merge($search_fields, $extra_search_fields);
  	  $criteria[] = '(' . implode(' like \'%' . $search_text . '%\' or ', $search_fields) . ' like \'%' . $search_text . '%\')';
	}
	if (!$f0) $criteria[] = "inactive = '0'"; // inactive flag
	if ($f1) { // sort by inventory type
	  switch ($f1) {
		case 'cog': 
		  $cog_types = explode(',',COG_ITEM_TYPES);
		  $criteria[] = "inventory_type in ('" . implode("','", $cog_types) . "')";
		  break;
		default:
		  $criteria[] = "inventory_type = '$f1'";
		  break;
	  }
	}
	// build search filter string
	$search = (sizeof($criteria) > 0) ? (' where ' . implode(' and ', $criteria)) : '';
	$field_list = array('id', 'sku', 'inactive', 'inventory_type', 'description_short', 'item_cost', 'full_price', 
			'quantity_on_hand', 'quantity_on_order', 'quantity_on_sales_order', 'quantity_on_allocation');
	// hook to add new fields to the query return results
	if (is_array($extra_query_list_fields) > 0) $field_list = array_merge($field_list, $extra_query_list_fields);
    $query_raw    = "select " . implode(', ', $field_list)  . " from " . TABLE_INVENTORY . $search . " order by " . $disp_order;
    $query_split  = new splitPageResults($_GET['list'], MAX_DISPLAY_SEARCH_RESULTS, $query_raw, $query_numrows);
    $query_result = $db->Execute($query_raw);
	define('PAGE_TITLE', BOX_INV_MAINTAIN);
    $include_template = 'template_main.php';
	break;
}

?>