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
//  Path: /modules/phreebooks/pages/admin/template_tab_tax_auths.php
//
$tax_auths_toolbar = new toolbar;
$tax_auths_toolbar->icon_list['cancel']['show'] = false;
$tax_auths_toolbar->icon_list['open']['show']   = false;
$tax_auths_toolbar->icon_list['save']['show']   = false;
$tax_auths_toolbar->icon_list['delete']['show'] = false;
$tax_auths_toolbar->icon_list['print']['show']  = false;
if ($security_level > 1) $tax_auths_toolbar->add_icon('new', 'onclick="loadPopUp(\'tax_auths_new\', 0)"', $order = 10);

?>
<div id="tax_auths" class="tabset_content">
  <h2 class="tabset_label"><?php echo TEXT_COUNTRIES_TABS; ?></h2>
  <?php echo $tax_auths_toolbar->build_toolbar(); ?>
  <div class="pageHeading"><?php echo $tax_auths->title; ?></div>
  <div id="tax_auths_content"><?php echo $tax_auths->build_main_html(); ?></div>
</div>
