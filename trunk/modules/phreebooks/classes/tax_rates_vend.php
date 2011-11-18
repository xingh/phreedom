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
//  Path: /modules/phreebooks/classes/tax_rates_vend.php
//

class tax_rates_vend {

  function tax_rates_vend() {
  	$this->security_id = $_SESSION['admin_security'][SECURITY_ID_CONFIGURATION];
	$this->db_table    = TABLE_TAX_RATES;
	$this->help_path   = '07.08.03.02';
	$this->type        = 'v'; // choices are c for customers and v for vendors
  }

  function btn_save($id = '') {
  	global $db, $messageStack;
	if ($this->security_id < 2) {
		$messageStack->add_session(ERROR_NO_PERMISSION,'error');
		return false;
	}
    $description_short  = db_prepare_input($_POST['description_short']);
	$rate_accounts      = db_prepare_input($_POST['rate_accounts']);
	$tax_auth_id_add    = db_prepare_input($_POST['tax_auth_id_add']);
	$tax_auth_id_delete = db_prepare_input($_POST['tax_auth_id_delete']);
	$rate_accounts = $this->combine_rates($rate_accounts, $tax_auth_id_add, $tax_auth_id_delete);
	$sql_data_array = array(
		'type'              => $this->type,
		'description_short' => $description_short,
		'description_long'  => db_prepare_input($_POST['description_long']),
		'rate_accounts'     => $rate_accounts,
		'freight_taxable'   => db_prepare_input($_POST['freight_taxable']),
	);
    if ($id) {
	  db_perform($this->db_table, $sql_data_array, 'update', "tax_rate_id = '" . (int)$id . "'");
	  gen_add_audit_log(SETUP_TAX_RATES_LOG . TEXT_UPDATE, $description_short);
	} else  {
      db_perform($this->db_table, $sql_data_array);
	  gen_add_audit_log(SETUP_TAX_RATES_LOG . TEXT_ADD, $description_short);
	}
	return true;
  }

  function btn_delete($id = 0) {
  	global $db, $messageStack;
	if ($this->security_id < 4) {
	  $messageStack->add_session(ERROR_NO_PERMISSION,'error');
	  return false;
	}
	// Check for this rate as part of a journal entry, if so do not delete
	// Since tax rates are not used explicitly, they can be deleted at any time.
	$result = $db->Execute("select description_short from " . $this->db_table . " where tax_rate_id = '" . $id . "'");
    $db->Execute("delete from " . $this->db_table . " where tax_rate_id = '" . $id . "'");
	gen_add_audit_log(SETUP_TAX_RATES_LOG . TEXT_DELETE, $result->fields['description_short']);
	return true;
  }

  function build_main_html() {
  	global $db, $messageStack;
    $tax_authorities_array = gen_build_tax_auth_array();
    $content = array();
	$content['thead'] = array(
	  'value' => array(SETUP_TAX_DESC_SHORT, TEXT_DESCRIPTION, SETUP_HEADING_TOTAL_TAX, SETUP_HEADING_TAX_FREIGHT, TEXT_ACTION),
	  'params'=> 'width="100%" cellspacing="0" cellpadding="1"',
	);
    $result = $db->Execute("select tax_rate_id, description_short, description_long, rate_accounts, freight_taxable 
		from " . $this->db_table . " where type = '" . $this->type . "'");
    $rowCnt = 0;
	while (!$result->EOF) {
	  $actions = '';
	  if ($this->security_id > 1) $actions .= html_icon('actions/edit-find-replace.png', TEXT_EDIT, 'small', 'onclick="loadPopUp(\'tax_rates_vend_edit\', ' . $result->fields['tax_rate_id'] . ')"') . chr(10);
	  if ($this->security_id > 3) $actions .= html_icon('emblems/emblem-unreadable.png', TEXT_DELETE, 'small', 'onclick="if (confirm(\'' . SETUP_TAX_DELETE_INTRO . '\')) subjectDelete(\'tax_rates_auth\', ' . $result->fields['tax_rate_id'] . ')"') . chr(10);
	  $content['tbody'][$rowCnt] = array(
	    array('value' => htmlspecialchars($result->fields['description_short']),
			  'params'=> 'style="cursor:pointer" onclick="loadPopUp(\'tax_rates_vend_edit\',\''.$result->fields['tax_rate_id'].'\')"'),
		array('value' => htmlspecialchars($result->fields['description_long']), 
			  'params'=> 'style="cursor:pointer" onclick="loadPopUp(\'tax_rates_vend_edit\',\''.$result->fields['tax_rate_id'].'\')"'),
		array('value' => gen_calculate_tax_rate($result->fields['rate_accounts'], $tax_authorities_array),
			  'params'=> 'style="cursor:pointer" onclick="loadPopUp(\'tax_rates_vend_edit\',\''.$result->fields['tax_rate_id'].'\')"'),
		array('value' => ($result->fields['freight_taxable'] ? TEXT_YES : TEXT_NO),
			  'params'=> 'style="cursor:pointer" onclick="loadPopUp(\'tax_rates_vend_edit\',\''.$result->fields['tax_rate_id'].'\')"'),
		array('value' => $actions,
			  'params'=> 'align="right"'),
	  );
      $result->MoveNext();
	  $rowCnt++;
    }
    return html_datatable('tax_v_table', $content);
  }

  function build_form_html($action, $id = '') {
    global $db;
	require_once(DIR_FS_MODULES . 'phreebooks/functions/phreebooks.php');
    $tax_authorities_array = gen_build_tax_auth_array();
    $sql = "select description_short, description_long, rate_accounts, freight_taxable 
	    from " . $this->db_table . " where tax_rate_id = " . $id;
    $result = $db->Execute($sql);
	if ($action == 'new') {
	  $cInfo = '';
	} else {
      $cInfo = new objectInfo($result->fields);
	}

	$output  = '<table style="border-collapse:collapse;margin-left:auto; margin-right:auto;">' . chr(10);
	$output .= '  <thead class="ui-widget-header">' . "\n";
	$output .= '  <tr>' . chr(10);
	$output .= '    <th colspan="2">' . ($action=='new' ? SETUP_HEADING_NEW_TAX_RATE : SETUP_HEADING_EDIT_TAX_RATE) . '</th>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  </thead>' . "\n";
	$output .= '  <tbody class="ui-widget-content">' . "\n";
	$output .= '  <tr>' . chr(10);
	$output .= '    <td colspan="2">' . ($action=='new' ? SETUP_TAX_INSERT_INTRO : SETUP_TAX_EDIT_INTRO) . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '    <td>' . SETUP_INFO_DESC_SHORT . '</td>' . chr(10);
	$output .= '    <td>' . html_input_field('description_short', $cInfo->description_short, 'size="16" maxlength="15"') . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '    <td>' . SETUP_INFO_DESC_LONG . '</td>' . chr(10);
	$output .= '    <td>' . html_input_field('description_long', $cInfo->description_long, 'size="33" maxlength="64"') . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '    <td>' . SETUP_INFO_TAX_AUTHORITIES . '</td>' . chr(10);
	$output .= '    <td>' . html_hidden_field('rate_accounts', $cInfo->rate_accounts) . $this->draw_tax_auths($cInfo->rate_accounts, $tax_authorities_array) . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '    <td>' . SETUP_INFO_TAX_AUTH_ADD . '</td>' . chr(10);
	$output .= '    <td>' . html_pull_down_menu('tax_auth_id_add', $this->get_tax_auths()) . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '    <td>' . SETUP_INFO_TAX_AUTH_DELETE . '</td>' . chr(10);
	$output .= '    <td>' . html_pull_down_menu('tax_auth_id_delete', $this->get_selected_tax_auths($cInfo->rate_accounts, $tax_authorities_array)) . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '    <td>' . SETUP_INFO_FREIGHT_TAXABLE . '</td>' . chr(10);
	$output .= '    <td>' . html_radio_field('freight_taxable', '0', !$cInfo->freight_taxable) . TEXT_NO . html_radio_field('freight_taxable', '1', $cInfo->freight_taxable) . TEXT_YES . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  </tbody>' . "\n";
    $output .= '</table>' . chr(10);
    return $output;
  }

  function draw_tax_auths($tax_authorities_chosen, $tax_auth_array) {
	$field = '<table border="1" width="100%">';
	$chosen_auth_array = explode(':', $tax_authorities_chosen);
	$total_tax_rate = 0;
	while ($chosen_auth = array_shift($chosen_auth_array)) {
	  $field .= '<tr><td>' . $tax_auth_array[$chosen_auth]['description_short'] . '</td><td align="right">' . $tax_auth_array[$chosen_auth]['tax_rate'] . '</td></tr>';
	  $total_tax_rate += $tax_auth_array[$chosen_auth]['tax_rate'];
	}
	$field .= '<tr><td align="right">' . TEXT_TOTAL . '</td><td align="right">' . $total_tax_rate . '</td></tr>';
	$field .= '</table>';
	return $field;
  }

////
// Get list of tax_auth for pull down
  function get_tax_auths() {
    global $db;
    $tax_auth_values = $db->Execute("select tax_auth_id, description_short
		from " . TABLE_TAX_AUTH . " where type = '" . $this->type . "' order by description_short");
    $tax_auth_array = array();
    $tax_auth_array[] = array('id' => '', 'text' => TEXT_NONE);
    while (!$tax_auth_values->EOF) {
      $tax_auth_array[] = array(
	  	'id'   => $tax_auth_values->fields['tax_auth_id'],
        'text' => $tax_auth_values->fields['description_short'],
	  );
      $tax_auth_values->MoveNext();
    }
    return $tax_auth_array;
  }

////
// Get list of tax_auth for for a specific tax rate code to build remove drop down
  function get_selected_tax_auths($tax_authorities_chosen, $tax_auth_choices) {
	$chosen_auth_array = explode(':', $tax_authorities_chosen);
    $tax_auth_array = array();
    $tax_auth_array[] = array('id' => '', 'text' => TEXT_NONE);
	while ($chosen_auth = array_shift($chosen_auth_array)) {
      $tax_auth_array[] = array(
	    'id'   => $chosen_auth, 
	  	'text' => $tax_auth_choices[$chosen_auth]['description_short'],
	  );
	}
    return $tax_auth_array;
  }

////
// regenerate listing string for tax authorities 
  function combine_rates($rate_accounts, $tax_auth_id_add = '', $tax_auth_id_delete = '') {
	$tax_auth_array = explode(':', $rate_accounts);
	$new_tax_auth_array = array();
	while ($tax_auth = array_shift($tax_auth_array)) {
	  if ($tax_auth <> $tax_auth_id_delete) $new_tax_auth_array[] = $tax_auth;
	}
	if (gen_not_null($tax_auth_id_add)) $new_tax_auth_array[] = $tax_auth_id_add;
	return implode(':', $new_tax_auth_array);
  }

}
?>