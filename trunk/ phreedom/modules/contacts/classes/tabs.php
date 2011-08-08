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
//  Path: /modules/contacts/classes/tabs.php
//

class tabs {
  function tabs() {
  	$_SESSION['admin_security'][SECURITY_ID_CONFIGURATION] = $_SESSION['admin_security'][SECURITY_ID_CONFIGURATION];
	$this->help_path   = '';
  }

  function btn_save($id = '') {
  	global $db, $messageStack;
	if ($_SESSION['admin_security'][SECURITY_ID_CONFIGURATION] < 2) {
	  $messageStack->add_session(ERROR_NO_PERMISSION,'error');
	  return false;
	}
    $tab_name = db_prepare_input($_POST['tab_name']);
	$sql_data_array = array(
	  'module_id'   => 'contacts',
	  'tab_name'    => db_prepare_input($_POST['tab_name']),
	  'description' => db_prepare_input($_POST['description']),
	  'sort_order'  => db_prepare_input($_POST['sort_order']),
	);
    if ($id) {
	  db_perform(TABLE_EXTRA_TABS, $sql_data_array, 'update', "id = " . $id);
      gen_add_audit_log(sprintf(EXTRA_TABS_LOG, TEXT_UPDATE), $tab_name);
	} else {
	  // Test for duplicates.
	  $result = $db->Execute("select id from " . TABLE_EXTRA_TABS . " where module_id='contacts' and tab_name='" . $tab_name . "'");
	  if ($result->RecordCount() > 0) {
	  	$messageStack->add(EXTRA_TABS_DELETE_ERROR,'error');
		return false;
	  }
	  $sql_data_array['id'] = db_prepare_input($_POST['rowSeq']);
      db_perform(TABLE_EXTRA_TABS, $sql_data_array);
	  gen_add_audit_log(sprintf(EXTRA_TABS_LOG, TEXT_ADD), $tab_name);
	}
	return true;
  }

  function btn_delete($id = 0) {
  	global $db, $messageStack;
	if ($_SESSION['admin_security'][SECURITY_ID_CONFIGURATION] < 4) {
	  $messageStack->add_session(ERROR_NO_PERMISSION,'error');
	  return false;
	}
	$result = $db->Execute("select field_name from " . TABLE_EXTRA_FIELDS . " where id = '" . $id . "'");
	if ($result->RecordCount() > 0) {
	  $messageStack->add(ASSETS_CATEGORY_CANNOT_DELETE . $result->fields['field_name'],'error');
	  return false;
	}
	$result = $db->Execute("select tab_name from " . TABLE_EXTRA_TABS . " where id = '" . $id . "'");
	$db->Execute("delete from " . TABLE_EXTRA_TABS . " where id = '" . $id . "'");
	gen_add_audit_log(sprintf(EXTRA_TABS_LOG, TEXT_DELETE), $result->fields['tab_name']);
	return true;
  }

  function build_main_html() {
  	global $db, $messageStack;
    // Build heading bar
	$output  = '<table border="0" width="100%" cellspacing="0" cellpadding="1">' . chr(10);
	$output .= '  <tr class="dataTableHeadingRow" valign="top">' . chr(10);
	$heading_array = array(
	  'tab_name'    => TEXT_TITLE,
	  'description' => TEXT_DESCRIPTION,
	  'sort_order'  => TEXT_SORT_ORDER,
	);
	$result = html_heading_bar($heading_array, $_GET['list_order']);
	$output .= $result['html_code'];
	$disp_order = $result['disp_order'];
    $output .= '  </tr>' . chr(10);
	// Build field data
    $query_raw = "select id, tab_name, description, sort_order from " . TABLE_EXTRA_TABS . " where module_id='contacts' order by $disp_order";
    $page_split = new splitPageResults($_GET['list'], MAX_DISPLAY_SEARCH_RESULTS, $query_raw, $query_numrows);
    $result = $db->Execute($query_raw);
    while (!$result->EOF) {
      $output .= '  <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">' . chr(10);
      $output .= '    <td class="dataTableContent" onclick="loadPopUp(\'tabs_edit\', \'' . $result->fields['id'] . '\')">' . htmlspecialchars($result->fields['tab_name']) . '</td>' . chr(10);
      $output .= '    <td class="dataTableContent" onclick="loadPopUp(\'tabs_edit\', \'' . $result->fields['id'] . '\')">' . htmlspecialchars($result->fields['description']) . '</td>' . chr(10);
      $output .= '    <td class="dataTableContent" onclick="loadPopUp(\'tabs_edit\', \'' . $result->fields['id'] . '\')">' . $result->fields['sort_order'] . '</td>' . chr(10);
      $output .= '    <td class="dataTableContent" align="right">' . chr(10);
	  if ($_SESSION['admin_security'][SECURITY_ID_CONFIGURATION] > 1) $output .= html_icon('actions/edit-find-replace.png', TEXT_EDIT, 'small', 'onclick="loadPopUp(\'tabs_edit\', \'' . $result->fields['id'] . '\')"') . chr(10);
	  if ($_SESSION['admin_security'][SECURITY_ID_CONFIGURATION] > 3) $output .= html_icon('emblems/emblem-unreadable.png', TEXT_DELETE, 'small', 'onclick="if (confirm(\'' . EXTRA_TABS_DELETE_INTRO . '\')) subjectDelete(\'tabs\', ' . $result->fields['id'] . ')"') . chr(10);
      $output .= '    </td>' . chr(10);
      $output .= '  </tr>' . chr(10);
      $result->MoveNext();
    }
    $output .= '</table>' . chr(10);
    $output .= '<div class="page_count_right">' . $page_split->display_ajax($query_numrows,  MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['list'], '', 'tabs_list', 'tabs') . '</div>' . chr(10);
    $output .= '<div class="page_count">'       . $page_split->display_count($query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['list'], TEXT_DISPLAY_NUMBER . TEXT_TABS) . '</div>' . chr(10);
	return $output;
  }

  function build_form_html($action, $id = '') {
    global $db;
    $sql = "select id, tab_name, description, sort_order from " . TABLE_EXTRA_TABS . " where id = " . $id;
    $result = $db->Execute($sql);
	if ($action == 'new') {
	  $cInfo = '';
	} else {
      $cInfo = new objectInfo($result->fields);
	}
	$output  = '<table border="0" width="100%" cellspacing="0" cellpadding="1">' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '    <th colspan="2">' . ($action=='new' ? TEXT_NEW_TAB : TEXT_EDIT_TAB) . '</th>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  <tr class="dataTableRow">' . chr(10);
	$output .= '    <td colspan="2">' . ($action=='new' ? EXTRA_TABS_INSERT_INTRO : SETUP_CURR_EDIT_INTRO) . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  <tr class="dataTableRow">' . chr(10);
	$output .= '    <td>' . TEXT_TAB_NAME . '</td>' . chr(10);
	$output .= '    <td>' . html_input_field('tab_name', $cInfo->tab_name) . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  <tr class="dataTableRow">' . chr(10);
	$output .= '    <td>' . TEXT_DESCRIPTION . '</td>' . chr(10);
	$output .= '    <td>' . html_textarea_field('description', 30, 10, $cInfo->description) . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  <tr class="dataTableRow">' . chr(10);
	$output .= '    <td>' . TEXT_SORT_ORDER . '</td>' . chr(10);
	$output .= '    <td>' . html_input_field('sort_order', $cInfo->sort_order) . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
    $output .= '</table>' . chr(10);
    return $output;
  }
}

?>