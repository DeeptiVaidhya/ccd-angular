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
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

/**
 * This is Auth API Controller used to interact with application's common functionality like checking & managing
 * Tokens for logged in users, Login, Logout, etc.
 *
 * @subpackage      Rest Server
 * @category        Controller
 * @license         MIT
 */
class Auth extends REST_Controller
{

    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Arm, Authorization, Token, Content-Type, X-XSRF-TOKEN");

        parent::__construct();
        $this->load->model('Auth_model', 'auth');
        $this->load->model('User_model', 'users');
    }

    /**
     * Method: POST
     * Header Key: Authorization
     * Value: Auth token generated in GET call
     */
    public function login_post()
    {

        $login_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($login_data)) {
            $this->config->load("form_validation");
            $this->form_validation->set_rules($this->config->item("loginForm"));
            $this->form_validation->set_data($login_data);
            if ($this->form_validation->run() == false) {
                $data = array(
                    'status' => 'error',
                    'msg' => "Username or password not valid.",
                );

                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $username = $login_data['username'];
                $password = $login_data['password'];
                $result = $this->auth->login($username, $password, 3);
                if($result['status'] == 'success')
                {
                    $arm  = $this->get_arm();
                    if($result['arm'] == $arm){
                        $this->set_response($result);
                    }
                    else{
                        $data = array(
                            'status' => 'error',
                            'msg'=>'You are unauthorised to see the content'
                        );
                        $this->response($data, REST_Controller::HTTP_OK);
                    }
                }
                $this->set_response($result);
            }
        } else {
            $data = array(
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    /**
     * Checking user is logged in and token is valid or not
     */
    public function check_login_post()
    {
        $this->check_token(true);
    }

    /** Forgot Password */
    public function forgot_password_post()
    {
        $insert_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($insert_data)) {
            $this->form_validation->set_data($insert_data);
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_email');
            if ($this->form_validation->run() == false) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array(),
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $res = $this->auth->forgotten_password($insert_data['email'], true);
                $this->response($res, REST_Controller::HTTP_CREATED);
            }
        }
    }
    public function generate_access_code_post()
    {
        $insert_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($insert_data)) {
            $this->form_validation->set_data($insert_data);
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_email');
            if ($this->form_validation->run() == false) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array(),
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $access_code = $this->auth->generate_access_code($insert_data['email'], true);
                $data = array(
                    'status' => 'success',
                    'access_code' => $access_code,
                );
                $this->response($data, REST_Controller::HTTP_CREATED);
            }
        }
    }

    /** change Activate status */
    public function change_active_status_post()
    {
        $params = json_decode(file_get_contents('php://input'), true);
        if (!empty($params['user_id'])) {
            $result = $this->user->update_status($params);
            if ($result > 0) {
                $res['status'] = "success";
                $res['msg'] = 'User updated successfull';
                $this->response($res, REST_Controller::HTTP_OK);
            }
        }
    }

    /**
     * Method: Get
     * Header Key: Authorization
     */
    public function profile_get()
    {
        $users_id = $this->get_user();
        $data = array();
        $data = $this->users->get_detail($users_id);
        $this->response(array('status' => 'success', 'data' => $data), REST_Controller::HTTP_OK);
    }

    public function check_email($email)
    {
        if ($email != '') {
            $count = $this->auth->email_check($email);
            if (!$count) {
                $this->form_validation->set_message('check_email', 'Email address does not exist in system.');
                return false;
            } else {
                return true;
            }
        }
    }

    

    public function check_email_post()
    {
        $insert_data = json_decode(file_get_contents('php://input'), true);
        $current_email = (isset($insert_data['current_email'])) ? $insert_data['current_email'] : false;
        $previous_email = (isset($insert_data['previous_email'])) ? $insert_data['previous_email'] : false;
        $result = false;
        if ($current_email != $previous_email) {
            $result = $this->auth->email_check($current_email);
        }
        if ($result) {
            $this->response(array('status' => 'success'), REST_Controller::HTTP_OK);
        } else {
            $this->response(array('status' => 'error'), REST_Controller::HTTP_OK);
        }
    }

    /** Callback function to check username exist or not in system */
    public function check_username_post()
    {
        $insert_data = json_decode(file_get_contents('php://input'), true);
        $current_username = (isset($insert_data['current_username'])) ? $insert_data['current_username'] : false;
        $previous_username = (isset($insert_data['previous_username'])) ? $insert_data['previous_username'] : false;
        $result = false;
        if ($current_username != $previous_username) {
            $result = $this->auth->username_check($current_username);
        }
        if ($result) {
            $this->response(array('status' => 'success'), REST_Controller::HTTP_OK);
        } else {
            $this->response(array('status' => 'error'), REST_Controller::HTTP_OK);
        }
    }

    /** User logout */
    public function logout_get()
    {
        $token = $this->input->server('HTTP_TOKEN');
        if ($token) {
            if ($this->auth->update_token($token)) {
                $this->response(array('status' => 'success', 'msg' => "Logged out successfully"), REST_Controller::HTTP_OK);
            } else {
                $this->response(array('status' => 'error'), REST_Controller::HTTP_OK);
            }
        }
    }

    public function profile_post()
    {
        $this->check_token();
        $insert_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($insert_data)) {
            $this->form_validation->set_data($insert_data);
            $this->form_validation->set_rules('first_name', 'first_name', 'required');

            // Check Email
            if ($insert_data['previous_email'] != $insert_data['email']) {
                $this->form_validation->set_rules('email', 'Email', 'required|is_unique[users.email]|valid_email');
            }
            // Check Password
            if ($insert_data['password'] != '') {
                $this->form_validation->set_rules('current_password', 'Current Password', 'required');
                $this->form_validation->set_rules('password', 'Password', 'matches[confirm_password]');
                $this->form_validation->set_rules('confirm_password', 'Confirm password', 'required');
            }
            if ($this->form_validation->run() == false) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array(),
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $users_id = $this->get_user();
                $user_data['first_name'] = $insert_data['first_name'];
                $user_data['id'] = $users_id;
                $this->auth->update_profile($user_data);
                $update_login_detail['email'] = $insert_data['email'];
                $update_login_detail['username'] = $insert_data['email'];
                $update_login_detail['password'] = $insert_data['password'];
                $update_login_detail['id'] = $users_id;
                $result = $this->auth->update_login_detail($update_login_detail);
                $this->response(array('status' => $result['status'], 'msg' => $result['msg']), REST_Controller::HTTP_OK);
            }
        } else {
            $this->response(array('status' => 'error'), REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }

    }

    /** Callback function to check username exist or not in system */
    public function check_password_post()
    {
        $insert_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($insert_data)) {
            $users_id = $this->get_user();
            $result = $this->auth->hash_password_db($users_id, $insert_data['password']);
            if (!$result) {
                $this->response(array('status' => 'success'), REST_Controller::HTTP_OK);
            } else {
                $this->response(array('status' => 'error'), REST_Controller::HTTP_OK);
            }
        }
    }

    /** Callback function to check username exist or not in system */
    public function check_previous_password_post()
    {
        $insert_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($insert_data)) {
            $users_id = $this->get_user();
            $result = $this->auth->check_password_history($users_id, $insert_data['password']);
            if ($result) {
                $this->response(array('status' => 'success'), REST_Controller::HTTP_OK);
            } else {
                $this->response(array('status' => 'error'), REST_Controller::HTTP_OK);
            }
        }
    }

    public function base64_to_image($profile_image = array())
    {
        if (!empty($profile_image)) {
            $config = $this->config->item('assets_images');
            $upload_path = check_directory_exists($config['path']);
            $path = $profile_image['filename'];
            $file_name = pathinfo($path, PATHINFO_FILENAME) . '-' . uniqid() . '.png';
            $img = $profile_image['value'];
            $data = base64_decode($img);
            $file_path = $upload_path . '/' . $file_name;
            $success = file_put_contents($file_path, $data);
            return $success ? $file_name : false;
        }
    }

    public function reset_password_post()
    {
        $res['status'] = 'error';
        $res['msg'] = 'Unable to make a request please try again later';
        $input = json_decode(file_get_contents('php://input'), true);
        // var_dump($input);die();
        if (!empty($input)) {
            $code = !empty($input['code']) != '' ? $input['code'] : (!empty($input['access_code']) != '' ? $input['access_code'] : '');

            if ($code != '') {
                $profile = $this->auth->forgotten_code_detail($code, !empty($input['access_code']));
                // print_r($profile);die();
                if (!empty($profile)) {
                    $this->form_validation->set_data($input);
                    $this->form_validation->set_rules($this->config->item("resetPasswordForm"));
                    if ($this->form_validation->run() == false) {
                        $data = array(
                            'status' => 'error',
                            'data' => $this->form_validation->error_array(),
                        );
                        $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
                    } else {
                        $id = $profile->id;
                        $password = $input['password'];
                        $res = $this->auth->reset_password($id, $password);
                    }
                } else {
                    $res['msg'] = 'Access code or link is invalid';
                }
            } else {
                $res['status'] = "error";
                $res['msg'] = 'Password link is invalid';
            }
        }
        $this->response($res, REST_Controller::HTTP_OK);
    }

    public function reset_password_code_post()
    {
        $res['status'] = 'error';
        $res['msg'] = 'Unable to make a request please try again later';
        $input = json_decode(file_get_contents('php://input'), true);
        if (!empty($input)) {
            $code = $input['code'];
            if ($code != '') {
                $profile = $this->auth->forgotten_code_detail($code);
                if (!empty($profile)) {
                    if (time() > $profile->forgotten_password_time) {
                        $res['status'] = "error";
                        $res['msg'] = 'Forgot password link has been expired';
                    } else {
                        $res['status'] = "success";
                        $res['msg'] = 'Forgot password link is verified';
                    }
                } else {
                    $res['status'] = "error";
                    $res['msg'] = 'Forgot password link is invalid';
                }
            }
        }
        $this->response($res, REST_Controller::HTTP_OK);
    }


}

