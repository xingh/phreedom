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
//  Path: /modules/inventory/classes/fields.php
//
class fields {
  function fields() {
    xtra_field_sync_list('inventory', TABLE_INVENTORY);
	$this->help_path   = '07.04.05';
  }

  function btn_save($id = '') {
  	global $db, $messageStack, $currencies;
	if ($_SESSION['admin_security'][SECURITY_ID_CONFIGURATION] < 2) {
	  $messageStack->add_session(ERROR_NO_PERMISSION,'error');
	  return false;
	}
	$field_name   = db_prepare_input($_POST['field_name']);
	$description  = db_prepare_input($_POST['description']);
	$tab_id       = db_prepare_input($_POST['tab_id']);
	$entry_type   = db_prepare_input($_POST['entry_type']);
	$field_name   = preg_replace("[^A-Za-z0-9_]", "", $field_name); // clean out all non-allowed values
	if (!$field_name) $error = $messageStack->add(INV_ERROR_FIELD_BLANK,'error');
	$reserved_names = array('select', 'delete', 'insert', 'update', 'to', 'from', 'where', 'and', 'or',
		'alter', 'table', 'add', 'change', 'in', 'order', 'set', 'inner');
	if (in_array($field_name, $reserved_names)) $error = $messageStack->add(INV_FIELD_RESERVED_WORD,'error');
	// check for duplicate field names
	$sql = "select id from " . TABLE_EXTRA_FIELDS . " where module_id='inventory' and field_name='" . $field_name . "'";
	$result = $db->Execute($sql);
	if ($result->RecordCount() > 0 && $action == 'save') $error = $messageStack->add(INV_ERROR_FIELD_DUPLICATE,'error');
	$values = array();
	$params = array('type' => $entry_type);
	switch ($entry_type) {
	  case 'text':
	  case 'html':
		$params['length']  = intval(db_prepare_input($_POST['text_length']));
		$params['default'] = db_prepare_input($_POST['text_default']);
		if ($params['length'] < 1) $params['length'] = DEFAULT_TEXT_LENGTH;
		if ($params['length'] < 256) {
			$values['entry_type'] = 'varchar(' . $params['length'] . ')';
			$values['entry_params'] = " default '" . $params['default'] . "'";
		} elseif ($_POST['TextLength'] < 65536) { 
			$values['entry_type'] = 'text';
		} elseif ($_POST['TextLength'] < 16777216) {
			$values['entry_type'] = 'mediumtext';
		} elseif ($_POST['TextLength'] < 65535) {
			$values['entry_type'] = 'longtext';
		}
		break;
	  case 'hyperlink':
	  case 'image_link':
	  case 'inventory_link':
		$params['default']      = db_prepare_input($_POST['link_default']);
		$values['entry_type']   = 'varchar(255)';
		$values['entry_params'] = " default '".$params['default']."'";
		break;
	  case 'integer':
		$params['select']  = db_prepare_input($_POST['integer_range']);
		$params['default'] = (int)db_prepare_input($_POST['integer_default']);
		switch ($params['select']) {
			case "0": $values['entry_type'] = 'tinyint';   break;
			case "1": $values['entry_type'] = 'smallint';  break;
			case "2": $values['entry_type'] = 'mediumint'; break;
			case "3": $values['entry_type'] = 'int';       break;
			case "4": $values['entry_type'] = 'bigint';
		}
		$values['entry_params'] = " default '" . $params['default'] . "'";
		break;
	  case 'decimal':
		$params['select']  = db_prepare_input($_POST['decimal_range']);
		$params['display'] = db_prepare_input($_POST['decimal_display']);
		$params['default'] = $currencies->clean_value(db_prepare_input($_POST['decimal_default']));
		if ($params['display']=='') $params['display'] = DEFAULT_REAL_DISPLAY_FORMAT;
		switch ($params['select']) {
			case "0": $values['entry_type'] = 'float(' . $params['display'] . ')'; break;
			case "1": $values['entry_type'] = 'double(' . $params['display'] . ')';
		}
		$values['entry_params'] = " default '" . $params['default'] . "'";
		break;
	  case 'drop_down':
	  case 'radio':
		$params['default'] = db_prepare_input($_POST['radio_default']);
		$choices = explode(',',$params['default']);
		$max_choice_size = 0;
		while ($choice = array_shift($choices)) {
			$a_choice = explode(':',$choice);
			if ($a_choice[2] == 1) $values['entry_params'] = " default '" . $a_choice[0] . "'";
			if (strlen($a_choice[0]) > $max_choice_size) $max_choice_size = strlen($a_choice[0]);
		}
		$values['entry_type'] = 'char(' . $max_choice_size . ')';
		break;
	 case 'multi_check_box':	
		$params['default'] = db_prepare_input($_POST['radio_default']);
		$choices = explode(',',$params['default']);
		$max_choice_size = 0;
		while ($choice = array_shift($choices)) {
			$a_choice = explode(':',$choice);
			if ($a_choice[2] == 1) $values['entry_params'] = " default '" . $a_choice[0] . "'";
			if (strlen($a_choice[0]) > $max_choice_size) $max_choice_size = strlen($a_choice[0]);
		}
		$values['entry_type'] = 'text';
		break;	
	  case 'date':
		$values['entry_type'] = 'date';
		break;
	  case 'time':
		$values['entry_type'] = 'time';
		break;
	  case 'date_time':
		$values['entry_type'] = 'datetime';
		break;
	  case 'check_box':
		$params['select']       = db_prepare_input($_POST['check_box_range']);
		$values['entry_type']   = 'enum("0","1")';
		$values['entry_params'] = " default '" . $params['select'] . "'";
		break;
	  case 'time_stamp':
		$values['entry_type'] = 'timestamp';
		break;
	  default:
	}
	if ($error) return false;
	$sql_data_array = array(
	  'module_id'   => 'inventory',
	  'description' => $description,
	);
	if ($tab_id <> '0') {
	  $sql_data_array['entry_type']  = $entry_type;
	  $sql_data_array['field_name']  = $field_name;
	  $sql_data_array['tab_id']      = $tab_id;
	  $sql_data_array['params']      = serialize($params);
	}
	if ($id) {
	  // load old field name as it may have been changed.
	  $result = $db->Execute("select field_name from " . TABLE_EXTRA_FIELDS . " where id = " . $id);
	  if (isset($values['entry_type']) || $field_name <> $result->fields['field_name']) {
		$sql = "alter table " . TABLE_INVENTORY . " change " . $result->fields['field_name'] . " " . $field_name . " 
		  " . $values['entry_type'] . (isset($values['entry_params']) ? $values['entry_params'] : '');
		$result = $db->Execute($sql);
	  }
	  db_perform(TABLE_EXTRA_FIELDS, $sql_data_array, 'update', "id = " . $id);
	  gen_add_audit_log(INV_LOG_FIELDS . TEXT_UPDATE, $id . ' - ' . $field_name);
	} else {
	  $sql = "alter table " . TABLE_INVENTORY . " 
		add column " . $field_name . " " . $values['entry_type'] . (isset($values['entry_params']) ? $values['entry_params'] : '');
	  $db->Execute($sql);
	  db_perform(TABLE_EXTRA_FIELDS, $sql_data_array, 'insert');
	  $id = db_insert_id();
	  gen_add_audit_log(INV_LOG_FIELDS . TEXT_NEW, $id . ' - ' . $field_name);
	}
	return true;
  }

  function btn_delete($id = 0) {
  	global $db, $messageStack;
	if ($_SESSION['admin_security'][SECURITY_ID_CONFIGURATION] < 4) {
	  $messageStack->add_session(ERROR_NO_PERMISSION,'error');
	  return false;
	}
	$temp = $db->Execute("select field_name, id from " . TABLE_EXTRA_FIELDS . " where id = " . $id);
	$field_name  = $temp->fields['field_name'];
	$id = $temp->fields['id'];
	if ($id <> '0') { // don't allow deletion of system fields
	  $db->Execute("delete from " . TABLE_EXTRA_FIELDS . " where id = " . $id);
	  $db->Execute("alter table " . TABLE_INVENTORY . " drop column " . $field_name);
	  gen_add_audit_log(INV_LOG_FIELDS . TEXT_DELETE, $id . ' - ' . $field_name);
	} else {
	  $messageStack->add_session(INV_CANNOT_DELETE_SYSTEM,'error');
	}
	return true;
  }

  function build_main_html() {
  	global $db, $messageStack;
	$tab_array = xtra_field_get_tabs('inventory');
    // Build heading bar
	$output  = '<table border="0" width="100%" cellspacing="0" cellpadding="1">' . chr(10);
	$output .= '  <tr class="dataTableHeadingRow" valign="top">' . chr(10);
    $heading_array = array(
	  'description' => TEXT_DESCRIPTION,
	  'field_name'  => TEXT_FLDNAME,
	  'tab_id'      => TEXT_TAB_NAME,
	  'entry_type'  => TEXT_TYPE,
	);
	$result     = html_heading_bar($heading_array, $_GET['list_order']);
	$output    .= $result['html_code'];
	$disp_order = $result['disp_order'];
    $output    .= '  </tr>' . chr(10);
	$field_list = array('id', 'field_name', 'entry_type', 'description', 'tab_id');
    $query_raw  = "select " . implode(', ', $field_list)  . " from " . TABLE_EXTRA_FIELDS . " where module_id='inventory' order by $disp_order";
    $page_split = new splitPageResults($_GET['list'], MAX_DISPLAY_SEARCH_RESULTS, $query_raw, $query_numrows);
    $result     = $db->Execute($query_raw);
    while (!$result->EOF) {
      $output .= '  <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">' . chr(10);
      $output .= '    <td class="dataTableContent" onclick="loadPopUp(\'fields_edit\', ' . $result->fields['id'] . ')">' . htmlspecialchars($result->fields['description']) . '</td>' . chr(10);
      $output .= '    <td class="dataTableContent" onclick="loadPopUp(\'fields_edit\', ' . $result->fields['id'] . ')">' . $result->fields['field_name'] . '</td>' . chr(10);
      $output .= '    <td class="dataTableContent" onclick="loadPopUp(\'fields_edit\', ' . $result->fields['id'] . ')">' . $tab_array[$result->fields['tab_id']] . '</td>' . chr(10);
      $output .= '    <td class="dataTableContent" onclick="loadPopUp(\'fields_edit\', ' . $result->fields['id'] . ')">' . $result->fields['entry_type'] . '</td>' . chr(10);
      $output .= '    <td class="dataTableContent" align="right">' . chr(10);
	  if ($_SESSION['admin_security'][SECURITY_ID_CONFIGURATION] > 1) $output .= html_icon('actions/edit-find-replace.png', TEXT_EDIT,   'small', 'onclick="loadPopUp(\'fields_edit\', ' . $result->fields['id'] . ')"') . chr(10);
	  if ($result->fields['tab_id'] && $_SESSION['admin_security'][SECURITY_ID_CONFIGURATION] > 3) $output .= html_icon('emblems/emblem-unreadable.png', TEXT_DELETE, 'small', 'onclick="if (confirm(\'' . EXTRA_TABS_DELETE_INTRO . '\')) subjectDelete(\'fields\', ' . $result->fields['id'] . ')"') . chr(10);
      $output .= '    </td>' . chr(10);
      $output .= '  </tr>' . chr(10);
      $result->MoveNext();
    }
    $output .= '</table>' . chr(10);
    $output .= '<div class="page_count_right">' . $page_split->display_ajax($query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['list'], '', 'fields_list', 'fields') . '</div>' . chr(10);
    $output .= '<div class="page_count">'       . $page_split->display_count($query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['list'], TEXT_DISPLAY_NUMBER . TEXT_FIELDS) . '</div>' . chr(10);
	return $output;
  }

  function build_form_html($action, $id = '') {
    global $db, $messageStack;
	global $integer_lengths, $decimal_lengths, $check_box_choices;
	xtra_field_sync_list('inventory', TABLE_INVENTORY);
	$tab_list = gen_build_pull_down(xtra_field_get_tabs('inventory'));
	array_shift($tab_list);
	if ($action == 'new' && sizeof($tab_list) < 1) {
	  $messageStack->add(EXTRA_FIELDS_ERROR_NO_TABS, 'error');
	  echo $messageStack->output();
	}
	if ($action == 'new') {
	  $cInfo = new objectInfo();
	} else {
	  $result = $db->Execute("select id, entry_type, field_name, description, id, params, tab_id 
	    from " . TABLE_EXTRA_FIELDS . " where id = '" . $id . "'");
	  $params = unserialize($result->fields['params']);
	  foreach ($params as $key => $value) $result->fields[$key] = $value;
	  $form_array = xtra_field_prep_form($result->fields);
      $cInfo = new objectInfo($form_array);
	}
	$disabled = ($cInfo->id !== '0') ? '' : 'disabled="disabled" ';
	$readonly = ($cInfo->id !== '0') ? '' : 'readonly="readonly" ';
	$output  = '<table width="100%" border="1" cellspacing="1" cellpadding="1">' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '	<td>' . INV_FIELD_NAME . '</td>' . chr(10);
	$output .= '	<td>' . html_input_field('field_name', $cInfo->field_name, $readonly . 'size="33" maxlength="32"') . '</td>' . chr(10);
	$output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '	<td colspan="2">' . INV_FIELD_NAME_RULES . '</td>' . chr(10);
	$output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '	<td>' . TEXT_DESCRIPTION . '</td>' . chr(10);
	$output .= '	<td>' . html_input_field('description', $cInfo->description, 'size="65" maxlength="64"') . '</td>' . chr(10);
	$output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '	<td>' . INV_CATEGORY_MEMBER . '</td>' . chr(10);
	$output .= '	<td>' . html_pull_down_menu('tab_id', $tab_list, $cInfo->tab_id, $disabled) . '</td>' . chr(10);
	$output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '	<th colspan="2">' . INV_HEADING_FIELD_PROPERTIES . '</th>' . chr(10);
	$output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '	<td>';
	$output .= html_radio_field('entry_type', 'text', ($cInfo->entry_type=='text' ? true : false), '', $disabled) . '&nbsp;' . INV_LABEL_TEXT_FIELD . '<br />';
	$output .= html_radio_field('entry_type', 'html', ($cInfo->entry_type=='html' ? true : false), '', $disabled) . '&nbsp;' . INV_LABEL_HTML_TEXT_FIELD . '</td>' . chr(10);
	$output .= '	<td>' . INV_LABEL_MAX_NUM_CHARS;
	$output .= '<br />' . html_input_field('text_length', ($cInfo->text_length ? $cInfo->text_length : DEFAULT_TEXT_LENGTH), $readonly . 'size="10" maxlength="9"');
	$output .= '<br />' . INV_LABEL_DEFAULT_TEXT_VALUE . '<br />' . INV_LABEL_MAX_255;
	$output .= '<br />' . html_textarea_field('text_default', 35, 6, $cInfo->text_default, $readonly);
	$output .= '	</td>' . chr(10);
	$output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '	<td>';
	$output .= html_radio_field('entry_type', 'hyperlink',      ($cInfo->entry_type=='hyperlink'      ? true : false), '', $disabled) . '&nbsp;' . INV_LABEL_HYPERLINK  . '<br />';
	$output .= html_radio_field('entry_type', 'image_link',     ($cInfo->entry_type=='image_link'     ? true : false), '', $disabled) . '&nbsp;' . INV_LABEL_IMAGE_LINK . '<br />';
	$output .= html_radio_field('entry_type', 'inventory_link', ($cInfo->entry_type=='inventory_link' ? true : false), '', $disabled) . '&nbsp;' . INV_LABEL_INVENTORY_LINK;
	$output .= '	</td>' . chr(10);
	$output .= '	<td>' . INV_LABEL_FIXED_255_CHARS;
	$output .= '<br />' . INV_LABEL_DEFAULT_TEXT_VALUE;
	$output .= '<br />' . html_textarea_field('link_default', 35, 3, $cInfo->link_default, $readonly);
	$output .= '	</td>' . chr(10);
	$output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '	<td>' . html_radio_field('entry_type', 'integer', ($cInfo->entry_type=='integer' ? true : false), '', $disabled) . '&nbsp;' . INV_LABEL_INTEGER_FIELD . '</td>' . chr(10);
	$output .= '	<td>' . INV_LABEL_INTEGER_RANGE;
	$output .= '<br />' . html_pull_down_menu('integer_range', gen_build_pull_down($integer_lengths), $cInfo->integer_range, $disabled);
	$output .= '<br />' . INV_LABEL_DEFAULT_TEXT_VALUE . html_input_field('integer_default', $cInfo->integer_default, $readonly . 'size="16"');
	$output .= '	</td>' . chr(10);
	$output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '	<td>' . html_radio_field('entry_type', 'decimal', ($cInfo->entry_type=='decimal' ? true : false), '', $disabled) . '&nbsp;' . INV_LABEL_DECIMAL_FIELD . '</td>' . chr(10);
	$output .= '	<td>' . INV_LABEL_DECIMAL_RANGE;
	$output .= html_pull_down_menu('decimal_range', gen_build_pull_down($decimal_lengths), $cInfo->decimal_range, $disabled);
	$output .= '<br />' . INV_LABEL_DEFAULT_DISPLAY_VALUE . html_input_field('decimal_display', ($cInfo->decimal_display ? $cInfo->decimal_display : DEFAULT_REAL_DISPLAY_FORMAT), $readonly . 'size="6" maxlength="5"');
	$output .= '<br />' . INV_LABEL_DEFAULT_TEXT_VALUE . html_input_field('decimal_default', $cInfo->decimal_default, $readonly . 'size="16"');
	$output .= '	</td>' . chr(10);
	$output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '	<td>';
	$output .= html_radio_field('entry_type', 'multi_check_box', ($cInfo->entry_type=='multi_check_box' ? true : false),''). '&nbsp;' . INV_LABEL_MULTI_SELECT_FIELD . '<br />';	
	$output .= html_radio_field('entry_type', 'drop_down', ($cInfo->entry_type=='drop_down' ? true : false),'', $disabled) . '&nbsp;' . INV_LABEL_DROP_DOWN_FIELD . '<br />';
	$output .= html_radio_field('entry_type', 'radio',     ($cInfo->entry_type=='radio'     ? true : false),'', $disabled) . '&nbsp;' . INV_LABEL_RADIO_FIELD;
	$output .= '	</td>' . chr(10);
	$output .= '	<td>' . INV_LABEL_CHOICES . '<br />' . html_textarea_field('radio_default', 35, 6, $cInfo->radio_default, $readonly) . '</td>' . chr(10);
	$output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '	<td>' . html_radio_field('entry_type', 'check_box', ($cInfo->entry_type=='check_box' ? true : false), '', $disabled) . '&nbsp;' . INV_LABEL_CHECK_BOX_FIELD . '</td>' . chr(10);
	$output .= '	<td>' . INV_LABEL_DEFAULT_TEXT_VALUE . html_pull_down_menu('check_box_range', gen_build_pull_down($check_box_choices), $cInfo->check_box_range, $disabled) . '</td>' . chr(10);
	$output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '	<td>' . html_radio_field('entry_type', 'date', ($cInfo->entry_type=='date' ? true : false), '', $disabled) . '&nbsp;' . TEXT_DATE . '</td>' . chr(10);
	$output .= '	<td>&nbsp;</td>' . chr(10);
	$output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '	<td>' . html_radio_field('entry_type', 'time', ($cInfo->entry_type=='time' ? true : false), '', $disabled) . '&nbsp;' . TEXT_TIME . '</td>' . chr(10);
	$output .= '	<td>&nbsp;</td>' . chr(10);
	$output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '	<td>' . html_radio_field('entry_type', 'date_time', ($cInfo->entry_type=='date_time' ? true : false), '', $disabled) . '&nbsp;' . INV_LABEL_DATE_TIME_FIELD . '</td>' . chr(10);
	$output .= '	<td>&nbsp;</td>' . chr(10);
	$output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '	<td>' . html_radio_field('entry_type', 'time_stamp', ($cInfo->entry_type=='time_stamp' ? true : false), '', $disabled) . '&nbsp;' . INV_LABEL_TIME_STAMP_FIELD . '</td>' . chr(10);
	$output .= '	<td>' . INV_LABEL_TIME_STAMP_VALUE . '</td>' . chr(10);
	$output .= '  </tr>' . chr(10);
	$output .= '</table>' . chr(10);
    return $output;
  }
}
?>