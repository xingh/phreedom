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
//  Path: /modules/phreebooks/dashboards/so_status/so_status.php
//
// Revision history
// 2011-07-01 - Added version number for revision control

class so_status extends ctl_panel {
	public $dashboard_id 		= 'so_status';
	public $description	 		= CP_SO_STATUS_DESCRIPTION;
	public $security_id  		= SECURITY_ID_SALES_ORDER;
	public $title		 		= CP_SO_STATUS_TITLE;
	public $version      		= 3.5;
	public $size_params			= 3;
	public $default_params 		= array('num_rows'=> 0, 'order'   => 'asc', 'limit'   => 1);

	function Output($params) {
		global $db, $currencies;
		if(count($params) != $this->size_params){ //upgrading
			$params = $this->Upgrade($params);
		}
		$contents = '';
		$control  = '';
		$list_length = array();
		for ($i = 0; $i <= $this->max_length; $i++) $list_length[] = array('id' => $i, 'text' => $i);
		$list_order = array(
		  array('id'=>'asc', 'text'=>TEXT_ASC),
		  array('id'=>'desc','text'=>TEXT_DESC),
		);
		$list_limit = array(
		  array('id'=>'0', 'text'=>TEXT_NO),
		  array('id'=>'1', 'text'=>TEXT_YES),
		);
		// Build control box form data
		$control  = '<div class="row">';
		$control .= '  <div style="white-space:nowrap">';
		$control .= TEXT_SHOW.TEXT_SHOW_NO_LIMIT.'&nbsp'.html_pull_down_menu('so_status_field_0', $list_length,$params['num_rows']).'<br />';
		$control .= CP_SO_STATUS_SORT_ORDER     .'&nbsp'.html_pull_down_menu('so_status_field_1', $list_order, $params['order']).'<br />';
		$control .= CP_SO_STATUS_HIDE_FUTURE    .'&nbsp'.html_pull_down_menu('so_status_field_2', $list_limit, $params['limit']);
		$control .= html_submit_field('sub_so_status', TEXT_SAVE);
		$control .= '  </div>';
		$control .= '</div>';
		// Build content box
		$sql = "select id, post_date, purchase_invoice_id, bill_primary_name, total_amount, currencies_code, currencies_value 
		  from " . TABLE_JOURNAL_MAIN . " where journal_id = 10 and closed = '0'";
		if ($params['limit']=='1')    $sql .= " and post_date <= '".date('Y-m-d')."'";
		if ($params['order']=='desc') $sql .= " order by post_date desc";
		if ($params['num_rows'])      $sql .= " limit " . $params['num_rows'];
		$result = $db->Execute($sql);
		if ($result->RecordCount() < 1) {
			$contents = ACT_NO_RESULTS;
		} else {
			while (!$result->EOF) {
				$contents .= '<div style="float:right">' . $currencies->format_full($result->fields['total_amount'], true, $result->fields['currencies_code'], $result->fields['currencies_value']) . '</div>';
				$contents .= '<div>';
				$contents .= '<a href="' . html_href_link(FILENAME_DEFAULT, 'module=phreebooks&amp;page=orders&amp;oID=' . $result->fields['id'] . '&amp;jID=10&amp;action=edit', 'SSL') . '">';
				$contents .= $result->fields['purchase_invoice_id'] . ' - ';
				$contents .= gen_locale_date($result->fields['post_date']);
				$name      = gen_trim_string($result->fields['bill_primary_name'], 20, true);
				$contents .= ' ' . htmlspecialchars($name);
				$contents .= '</a></div>' . chr(10);
				$result->MoveNext();
			}
	  	}
		return $this->build_div('', $contents, $control);
	}

	function Update() {
		if(count($this->params) == 0){
			$this->params = array(
			  'num_rows'=> db_prepare_input($_POST['so_status_field_0']),
			  'order'   => db_prepare_input($_POST['so_status_field_1']),
			  'limit'   => db_prepare_input($_POST['so_status_field_2']),
			);
		}
		parent::Update();
	}
}
?>