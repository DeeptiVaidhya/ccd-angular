<?php
 /**

 * Copyright (c) 2003-2019 BrightOutcome Inc.  All rights reserved.
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

$CI = & get_instance();

$config = array(
    'loginForm' => array(
        array(
            'field' => 'username',
            'label' => 'username',
            'rules' => 'required'
        ),
        array(
            'field' => 'password',
            'label' => 'password',
            'rules' => 'required'
        ),
    ),

    'resetPasswordForm' => array(
        array(
            'field' => 'password',
            'label' => 'password',
            'rules' => 'required|matches[confirm_password]|is_valid_password'
        ),
        array(
            'field' => 'confirm_password',
            'label' => 'confirm password',
            'rules' => 'required'
        ),
    ),
   
    'basicInfoForm' => array(
        array(
            'field' => 'first_name',
            'label' => 'first name',
            'rules' => 'required'
        ),
        array(
            'field' => 'last_name',
            'label' => 'last name',
            'rules' => 'required'
        ),
	)
);
