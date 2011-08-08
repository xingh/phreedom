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
//  Path: /modules/shipping/methods/fedex_v7/language/en_us/language.php
//

define('MODULE_SHIPPING_FEDEX_V7_TEXT_TITLE', 'Federal Express');
define('MODULE_SHIPPING_FEDEX_V7_TITLE_SHORT', 'FedEx');
define('MODULE_SHIPPING_FEDEX_V7_TEXT_DESCRIPTION', 'Federal Express');
define('MODULE_SHIPPING_FEDEX_V7_TEXT_INTRODUCTION', '<h3>Step 1. Develop and Test Web Services Enabled Application</h3>
This step has been completed by PhreeSoft. The remaining steps must be followed to enable label generation.
<h3>Step 2. Register for Move to Production</h3>
Start the certification process by applying for a FedEx Production Meter Number at the FedEx Developer Resource Center. <a href="http://www.fedex.com/developer">http://www.fedex.com/developer</a>
<h3>Step 3. Obtain Production Credentials</h3>
Obtain your production credentials (production meter number, production authentication key and production security key) online during the registration process. Your password was sent via a separate email communication.
Note: A production meter number is required for each of your locations. It is acceptable to use
the same authentication key among multiple locations. Additional meter numbers can be obtained using the Subscribe (Advanced) Web Service.
<br />Important Note: Due to the sensitivity of this information, the production authentication key is not provided in the confirmation email (Step 4). Please retain this information for your records.
<h3>Step 4. Receive Confirmation E-mail</h3>
You will receive confirmation of your registration by e-mail.
<h3>Step 5. Contact the Regional Web Integration Solutions (WIS) Support Team</h3>
Contact the Web Integration Solutions Team for your region with your application information and production credentials. Request that your application be enabled using advanced Web Services with shipping labels. (US &amp; Canada - 1.877.339.2774)
<h3>Step 6. Regional WIS Team Provides Instructions for Submitting Labels</h3>
The support team of your region sends an e-mail with instructions for certifying labels generated by your application.
<h3>Step 7. Generate and Submit Test Labels</h3>
Generate test labels associated with the application and submit the test labels to the FedEx label evaluation teams for approval. The sample data is locted in /modules/shipping/methods/fedex_v7/sample_data.php. <a href="index.php?module=shipping&amp;page=fedex_v7_qualify" target="_blank">Download Sample Labels</a>
<br />Note: The FedEx label evaluation teams require a five-day turn-around time for label evaluation.
<h3>Step 8. Label Evaluation</h3>
The FedEx label evaluation teams evaluate the submitted test labels and approves or rejects the labels. The support team member in your region contacts you regarding the approval or rejection of the submitted labels. If the labels are approved, continue to the next step. If the labels are rejected, correct the labels as instructed and resubmit them for approval.
<h3>Step 9. Regional WIS Team Enables the Application</h3>
Once the test labels are approved for production status by the label evaluation teams, the support team member for your region authorizes your profile to transmit the identified shipping label transaction and notifies you of successful certification.
<h3>Step 10. Replace URL and Credentials</h3>
Replace the test URL and test credentials with the production URL and production credentials.');
// key descriptions
define('MODULE_SHIPPING_FEDEX_V7_TITLE_DESC','Title to use for display purposes on shipping with FedEx.');
define('MODULE_SHIPPING_FEDEX_V7_ACCOUNT_NUMBER_DESC','Enter the FedEx account number to use for rate estimates');
define('MODULE_SHIPPING_FEDEX_V7_LTL_ACCOUNT_NUMBER_DESC','Enter the FedEx Freight account number to use for rate estimates. Leave this field blank if no LTL Freight will be used.');
define('MODULE_SHIPPING_FEDEX_V7_NAT_ACCOUNT_NUMBER_DESC','Enter the FedEx National LTL account number to use for rate estimates. Leave this field blank if no LTL Freight will be used.');
define('MODULE_SHIPPING_FEDEX_V7_AUTH_KEY_DESC','Enter the FedEx developer key provided by FedEx.');
define('MODULE_SHIPPING_FEDEX_V7_AUTH_PWD_DESC','Enter the FedEx developer password provided by FedEx.');
define('MODULE_SHIPPING_FEDEX_V7_METER_NUMBER_DESC','Enter the meter number supplied to you from FedEx.');
define('MODULE_SHIPPING_FEDEX_V7_TEST_MODE_DESC','Test/Production mode used for testing shipping labels');
define('MODULE_SHIPPING_FEDEX_V7_PRINTER_TYPE_DESC','Type of printer to use for printing labels. PDF for plain paper, Thermal for FedEx 2844 Thermal Label Printer (See Help file before selecting Thermal printer)');
define('MODULE_SHIPPING_FEDEX_V7_PRINTER_NAME_DESC', 'Sets then name of the printer to use for printing labels as defined in the printer preferences for the local workstation.');
define('MODULE_SHIPPING_FEDEX_V7_TYPES_DESC','Select the FEDEX services to be offered by default.');
define('MODULE_SHIPPING_FEDEX_V7_SORT_ORDER_DESC','Sort order of display. Lowest is displayed first.');
// Delivery Methods
define('MODULE_SHIPPING_FEDEX_V7_GND','Ground');
define('MODULE_SHIPPING_FEDEX_V7_GDR','Home Delivery');
define('MODULE_SHIPPING_FEDEX_V7_1DM','First Overnight');
define('MODULE_SHIPPING_FEDEX_V7_1DA','Priority Overnight');
define('MODULE_SHIPPING_FEDEX_V7_1DP','Standard Overnight');
define('MODULE_SHIPPING_FEDEX_V7_2DP','Express 2 Day');
define('MODULE_SHIPPING_FEDEX_V7_3DS','Express Saver');
define('MODULE_SHIPPING_FEDEX_V7_XDM','Int. First');
define('MODULE_SHIPPING_FEDEX_V7_XPR','Int. Priority');
define('MODULE_SHIPPING_FEDEX_V7_XPD','Int. Economy');
define('MODULE_SHIPPING_FEDEX_V7_1DF','1 Day Freight');
define('MODULE_SHIPPING_FEDEX_V7_2DF','2 Day Freight');
define('MODULE_SHIPPING_FEDEX_V7_3DF','3 Day Freight');
define('MODULE_SHIPPING_FEDEX_V7_GDF','Gnd Priority Freight');
define('MODULE_SHIPPING_FEDEX_V7_ECF','Gnd Economy Freight');
// General defines
define('SHIPPING_FEDEX_V7_VIEW_REPORTS','View Reports for ');
define('SHIPPING_FEDEX_V7_CLOSE_REPORTS','Closing Report');
define('SHIPPING_FEDEX_V7_MULTIWGHT_REPORTS','Multiweight Report');
define('SHIPPING_FEDEX_V7_HAZMAT_REPORTS','Hazmat Report');
define('SHIPPING_FEDEX_V7_SHIPMENTS_ON','FedEx Shipments on ');
define('SHIPPING_FEDEX_CURL_ERROR','There was an error communicating with the FedEx server:');
define('SHIPPING_FEDEX_V7_RATE_ERROR','FedEx rate response error: ');
define('SHIPPING_FEDEX_V7_RATE_CITY_MATCH','City doesn\'t match zip code.');
define('SHIPPING_FEDEX_V7_RATE_TRANSIT',' Day(s) Transit, arrives ');
define('SHIPPING_FEDEX_V7_TNT_ERROR',' FedEx Time in Transit Error # ');
define('SHIPPING_FEDEX_V7_DEL_ERROR','FedEx Delete Label Error: ');
define('SHIPPING_FEDEX_V7_DEL_SUCCESS','Successfully deleted the FedEx shipping label. Tracking # ');
define('SHIPPING_FEDEX_V7_TRACK_ERROR','FedEx Package Tracking Error: ');
define('SHIPPING_FEDEX_V7_TRACK_SUCCESS','Successfully Tracked Package Reference # ');
define('SHIPPING_FEDEX_V7_TRACK_STATUS','The package reference: %s is not delivered, the status is: (Code %s) %s.');
define('SHIPPING_FEDEX_V7_TRACK_FAIL','The following package reference number was deliverd after the expected date/time: ');
define('SHIPPING_FEDEX_V7_CLOSE_SUCCESS','Successfully closed the FedEx shipments for today.');
// Ship manager Defines
define('SRV_TRACK_FEDEX_V7','Track Today\'s FedEx Shipments');
// FedEx ground delivery days definitions
define('ONE_DAY','One Day Transit');
define('TWO_DAYS','Two Days Transit');
define('THREE_DAYS','Three Days Transit');
define('FOUR_DAYS','Four Days Transit');
define('FIVE_DAYS','Five Days Transit');
define('SIX_DAYS','Six Days Transit');
define('SEVEN_DAYS','Seven Days Transit');
define('EIGHT_DAYS','Eight Days Transit');
define('NINE_DAYS','Nine Days Transit');
define('TEN_DAYS','Ten Days Transit');
define('ELEVEN_DAYS','Eleven Days Transit');
define('TWELVE_DAYS','Twelve Days Transit');
define('THIRTEEN_DAYS','Thirteen Days Transit');
define('FOURTEEN_DAYS','Fourteen Days Transit');
define('FIFTEEN_DAYS','Fifteen Days Transit');
define('SIXTEEN_DAYS','Sixteen Days Transit');
define('SEVENTEEN_DAYS','Seventeen Days Transit');
define('EIGHTEEN_DAYS','Eighteen Days Transit');
define('NINETEEN_DAYS','Nineteen Days Transit');
define('TWENTY_DAYS','Twenty Days Transit');
define('UNKNOWN','Unknown Transit Time');
// Label Manager
define('SHIPPING_FEDEX_ERROR_POSTAL_CODE','Postal Code is required to use the FedEx module');
define('SHIPPING_FEDEX_RECON_TITLE','FedEx Reconciliation Report generated: ');
define('SHIPPING_FEDEX_RECON_INTRO','Invoice Number: %s dated %s');
define('SHIPPING_FEDEX_RECON_NO_RECORDS','Ship Date: %s Reference %s, tracking # %s - No records were found, shipment from %s to %s');
define('SHIPPING_FEDEX_RECON_TOO_MANY','Ship Date: %s Reference %s, tracking # %s - Too many references were found, shipment from %s to %s');
define('SHIPPING_FEDEX_RECON_COST_OVER','Ship Date: %s Reference %s, tracking # %s - The billed cost: (%s) quoted cost: (%s)');
define('SHIPPING_FEDEX_RECON_SUMMARY','Total number of records reconcilied: %s');

?>