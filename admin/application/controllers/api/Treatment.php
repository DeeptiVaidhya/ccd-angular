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

class Treatment extends REST_Controller {

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
    }

    /**
     * Method :Post
     * add billing code information for patient
     */
    public function add_billing_code_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->treatment->add_billing_code($post_data);
        }
        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * Method :Post
     * update billing code information for patient
     */
    public function update_billing_code_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->treatment->add_billing_code($post_data);
        }
        $this->response($data, REST_Controller::HTTP_OK);
    }
    
    /**
     * Method : Post
     * get billing code list
     */
    public function get_billing_code_list_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->treatment->get_patient_billing_code_list($post_data['patientId']);
        }
        $this->response(array('status'=>'success','data'=>$data), REST_Controller::HTTP_OK);
    }


    /**
     * Method : Post
     * get cpt code for billing code
     */
    public function get_cpt_code_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->treatment->get_cpt_code($post_data);
        }
        $this->response(array('status'=>'success','data'=>$data), REST_Controller::HTTP_OK);
    }

    /**
     * Method : POST
     * Get patient payment information
     */
    public function get_patient_payment_info_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->treatment->patient_payment_info($post_data['patient_id']);
        }
        $this->response(array('status' =>'success','data'=>$data), REST_Controller::HTTP_OK);
    }
    
    /**
     * Method : POST
     * Get patient prescription list
     */
    public function get_patient_prescription_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
           
            $data = $this->treatment->get_prescription_list($post_data['patient_id']);
        }
        $this->response(array('status' =>'success','data'=>$data), REST_Controller::HTTP_OK);
    }

    /**
     * Method : POST
     * Add patient prescription
     */
    public function add_prescription_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->treatment->add_prescription($post_data);
        }
        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * Method :Post
     * update prescription information for patient
     */
    public function update_prescription_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->treatment->add_prescription($post_data);
        }
        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * Method :Post
     * update prescription information for patient
     */
    public function get_prescription_details_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->treatment->get_prescription_data($post_data['prescriptionId']);
        }
        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * Method :Post
     * update prescription information for patient
     */
    public function get_billing_code_details_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->treatment->get_patient_billing_code_details($post_data['id']);
        }
        $this->response(array('status'=>'success','data'=>$data), REST_Controller::HTTP_OK);
    }

    /**
     * Method :Post
     * get provider for patient billing code
     */
    public function get_provider_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->treatment->get_provider_list($post_data);
        }
        $this->response(array('status'=>'success','data'=>$data), REST_Controller::HTTP_OK);
    }

    /**
     * Method :Post
     * get provider for patient billing code
     */
    public function search_prescription_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->treatment->search_prescription($post_data['key']);
        }
        $this->response(array('status'=>'success','data'=>$data), REST_Controller::HTTP_OK);
    }

    /**
     * Method :Post
     * update patient treatment plan details
     */
    public function edit_treatment_plan_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->treatment->update_treatment_plan($post_data);
        }
        $this->response(array('status'=>'success','data'=>$data), REST_Controller::HTTP_OK);
    }


    /**
     * Method :Post
     * update patient payment data
     */
    public function update_payment_data_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->treatment->update_payment_data($post_data['patient_id']);
        }
        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * Method :Post
     * get patient assistance data
     */
    public function get_patient_assistance_data_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->treatment->get_assistance_data($post_data['id']);
        }
        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * Method : POST
     * Add patient assistance
     */
    public function add_program_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->treatment->add_program($post_data);
        }
        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * Method : POST
     * Add patient program
     */
    public function add_program_to_patient_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->treatment->patient_has_program($post_data);
        }
        $this->response($data, REST_Controller::HTTP_OK);
    }
    
    /**
     * Method : POST
     * Add patient assistance
     */
    public function add_needed_patient_assistance_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->treatment->add_needed_patient_assistance_data($post_data);
        }
        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * Method : POST
     * Add assistance
     */
    public function update_assistance_type_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->treatment->update_assistance_type($post_data['id'], $post_data['type']);
        }
        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * Method : POST
     * update patient
     */
    public function update_patient_profile_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->treatment->update_patient_profile($post_data);
        }
        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * Method : POST
     * Search assistance
     */
    public function search_program_post(){
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $data = $this->treatment->search_program($post_data);
        }
        $this->response($data, REST_Controller::HTTP_OK);
    }

}
