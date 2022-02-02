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

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . '/libraries/REST_Controller.php';

class User extends REST_Controller {

    /**
     * @desc Class Constructor
     */
    public function __construct() {

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Arm, Authorization, Token, Content-Type, X-XSRF-TOKEN");


        parent::__construct();
        $this->load->model('User_model', 'user');
        $this->load->model('Auth_model', 'auth');
        $this->load->model('Treatment_model', 'treatment');
        //$this->check_token();
    }



    /**
     * Change Profile functionality
     */
    public function user_post() {
        $register_data = json_decode(file_get_contents('php://input'), true);

        if (!empty($register_data)) {
            $this->config->load("form_validation");
            $this->form_validation->set_data($register_data);
            $this->form_validation->set_rules($this->config->item("registerForm"));
            $this->form_validation->set_rules('email', 'Email', 'required|is_unique[users.email]|valid_email');
            if ($this->form_validation->run() == false) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array()
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $register_data['users_id'] = $this->get_user();
                $res = $this->auth->signup($register_data);
                $this->response($res, REST_Controller::HTTP_CREATED);
            }
        } else {
            $data = array(
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    /**
     * Change Profile functionality
     */
    public function user_put() {
        $update_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($update_data)) {
            $this->config->load("form_validation");
            $this->form_validation->set_data($update_data);
            $this->form_validation->set_rules($this->config->item("registerForm"));
            if ($update_data['previous_email'] != $update_data['email']) {
                $this->form_validation->set_rules('email', 'Email', 'required|is_unique[users.email]|valid_email');
            }
            if ($this->form_validation->run() == false) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array()
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $res = $this->user->update_user($update_data);
                $this->response($res, REST_Controller::HTTP_CREATED);
            }
        } else {
            $data = array(
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    public function user_detail_post() {
        $status = 'error';
        $msg = "User not found";
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $user_id = $post_data['user_id'];
            $user_type = $post_data['user_type'];
            $result = $this->user->get_users_list(array('user_type' => $user_type, 'user_id' => $user_id,'is_active'=>true));
            if (!empty($result) && isset($result['result'])) {
                $status = 'success';
                $data = $result['result'];
                $msg = '';
            }
        }
        $this->response(array('status' => $status, 'data' => $data, 'msg' => $msg), REST_Controller::HTTP_OK);
    }

    /**
     * Method: Post
     * Add patient and save information in USER Table
     */
    public function add_patient_post(){
       
        $result = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $result = $this->user->add_patient_data($post_data);
        }
        $this->response($result, REST_Controller::HTTP_OK);
    }


    /**
     * Method: Post
     * Add patient and save information in USER Table
     */
    public function get_patient_post(){
       
        $result = array();
        $payment_info = array();
        $prescription_info = array();
        $treatment_plan_info = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $result = $this->user->get_patient_details($post_data['patientId']);
            $payment_info = $this->treatment->patient_payment_info($post_data['patientId']);
            $insurance_plan = $this->treatment->get_patient_billing_code_list($post_data['patientId']);
            $treatment_plan_info = $this->treatment->get_treatment_plan($post_data['patientId']);
            $prescription_info = $this->treatment->get_patient_prescription_data($post_data['patientId']);
            $assistance_data = $this->treatment->get_assistance_data($post_data['patientId']);
        }
        $this->response(array('status'=>'success','data'=>$result, 'prescriptions' => $prescription_info, 'treatment_plan'=>$treatment_plan_info, 'payment_data'=>$payment_info, 'insurance_plans' => $insurance_plan, 'all_assistance' =>$assistance_data['all_assistance']), REST_Controller::HTTP_OK);
    }

    /**
     * Method: Post
     * Update Participant 
     */

    public function update_patient_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->user->add_patient_data($post_data);
        }
        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * Method: Post
     * get patient search list
     */

    public function search_patient_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->user->search_patient_list($post_data);
        }
        $this->response(array('status' => 'success', 'data' => $data), REST_Controller::HTTP_OK);
    }
}
