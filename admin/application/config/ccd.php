<?php
 /**

 * Copyright (c) 2003-2020 BrightOutcome Inc.  All rights reserved.
 * 
 * This software is the confidential and proprietary information of
 * BrightOutcome Inc. ("Confidential Information").  You shall not
 * disclose such Confidential Information and shall use it only
 * in accordance with the terms of the license agreement you
 * entered into with BrightOutcome.
 * 
 * BRIGHTOUTCOME MAKES NO REPRESENTATIONS OR WARRANTIES ABOUT THE
 * SUITABILITY OF THE SOFTWARE, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT 
 * NOT LIMITED TO THE IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
 * PARTICULAR PURPOSE, OR NON-INFRINGEMENT. BRIGHTOUTCOME SHALL NOT BE LIABLE
 * FOR ANY DAMAGES SUFFERED BY LICENSEE AS A RESULT OF USING, MODIFYING OR
 * DISTRIBUTING THIS SOFTWARE OR ITS DERIVATIVES.
 */
defined('BASEPATH') OR exit('No direct script access allowed');
// Application URL to call from Emails
$config['app_url'] = 'http://pub1.brightoutcome-dev.com/cancercostdetox/build/';
$config['assets_url'] = 'assets/';

// AES-256 Encryption/Decryption
$config['encryption'] = array('cipher' => 'aes-256', 'mode' => 'CBC', 'driver' => 'openssl', 'key' => 'Bp$o68T^v8zjWP!q9kwIPkprA1kiugN');

// Records Per page in Pagination
$config['pager_limit'] = 10;

$config['site_name']= 'Cancer Cost Detox';
// Auto Lock Out time for Locked Users on Wrong Password Entered.
$config['lockout_time'] = 10; // In minutes


$config['expiration_note'] = 'Please note that the link will expire after %s hours. If you did not make this request, then you can safely ignore this email.';
$config['reset_password_subject'] = 'Password Change Request';
$config['reset_password_btn_titte'] = 'Reset password';
$config['reset_password_heading'] = 'Password Reset Request';
$config['reset_password_message'] = "<h2>Hi %s,</h2><p>We received a request to reset the password associated with this e-mail address. Please click the link below to start the password reset process.</p>";

