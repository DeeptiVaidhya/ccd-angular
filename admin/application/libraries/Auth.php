<?php
 /*

 * Copyright (c) 2003-2017 BrightOutcome Inc.  All rights reserved.
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
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Name:  Ion Auth
 *
 * Author: Ben Edmunds
 * 		  ben.edmunds@gmail.com
 *         @benedmunds
 *
 * Added Awesomeness: Phil Sturgeon
 *
 * Location: http://github.com/benedmunds/CodeIgniter-Ion-Auth
 *
 * Created:  10.01.2009
 *
 * Description:  Modified auth system based on redux_auth with extensive customization.  This is basically what Redux Auth 2 should be.
 * Original Author name has been kept but that does not mean that the method has not been modified.
 *
 * Requirements: PHP5 or above
 *
 */
class Auth_middleware {

    public function __construct() {
        $this->load->model('auth_model', 'Auth');
    }

    /**
     * Login Method
     *
     * @param $data array
     * @return array
     */
    public function login_user($login_data = FALSE) {
        if (!empty($login_data)) {
            $this->config->load("form_validation");
            $this->form_validation->set_rules($this->config->item("loginForm"));
            $this->form_validation->set_data($login_data);
            if ($this->form_validation->run() == FALSE) {
                $data = array(
                    'success' => FALSE,
                    'data' => $this->form_validation->error_array()
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $username = $login_data['username'];
                $password = $login_data['password'];
                $result = $this->auth->login($username, $password);

                $this->set_response($result);
            }
        } else {
            $data = array(
                'success' => FALSE,
            );
            $this->response($data, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

}
