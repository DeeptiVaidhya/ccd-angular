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
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{

    /**
     * @desc Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') == false) {
            redirect('auth');
        }
        $this->load->model('User_model', 'user');
        $this->load->model('Auth_model', 'auth');
        $this->form_validation->set_error_delimiters('<label class="error">', '</label>');
    }

    // Call list users function by default
    public function index()
    {
        $this->list_users();
    }

    /**
     * @desc Showing list of all users
     *
     */
    public function list_users($arm_type = '')
    {
        get_plugins_in_template('datatable');

        $this->template->title = 'List User';

        $this->template->content->view('list_users');
        // Publish the template
        $this->template->publish();
    }

    /**
     * Get list of users in Datatable
     */

    public function get_users_data()
    {
        $data = $this->user->get_users_list($this->input->get());

        $rowCount = $data['count'];
        $output = array(
            "sEcho" => intval($this->input->get('sEcho')),
            "iTotalRecords" => $rowCount,
            "iTotalDisplayRecords" => $rowCount,
            "aaData" => []
        );
        $i = $this->input->get('iDisplayStart') + 1;
        foreach ($data['data'] as $val) {
            $output['aaData'][] = array(
                "DT_RowId" => $val['id'],
                $i++,
                $val['patient_name'], 
                $val['insurance_type'], 
                $val['coverage_effective_date'], 
                $val['coverage_expiration_date'], 
                $val['co_insurance_percentage'], 
                $val['individual_deductible'], 
                $val['individual_deductible_paid'], 
                $val['individual_max_out_of_pocket'], 
                $val['individual_max_out_of_pocket_paid'], 
                $val['family_deductible'], 
                $val['family_deductible_paid'], 
                $val['family_max_out_of_pocket'], 
                $val['family_max_out_of_pocket_paid'],
                '',
            );
        }
        echo json_encode($output);
        die;
    }

    /**
     * Function to add participant/researcher
     */
    public function add_user()
    {
        
        $this->breadcrumbs->push('User', 'Add Professional User');
        $data['breadcrumb'] = $this->breadcrumbs->show();
        if ($this->input->post()) {
            $this->form_validation->set_rules($this->config->item("registerForm"));
            $this->form_validation->set_rules('email', 'Email', 'required|callback_check_email|valid_email');
            if ($this->input->post('user_type') == 3 && trim($this->input->post('subject_id'))) {
                $this->form_validation->set_rules('subject_id', 'Subject Id', array('regex_match[/^[a-zA-Z0-9--]+$/], required'));
            }
            if ($this->form_validation->run() != false) {
                $request = $this->input->post();

                if ($request['user_type'] == 3) {
                    $request['arm_alloted'] = $data['arm_type'];
                    $request['users_id'] = $this->session->userdata('logged_in')->id;
                }
                $result = $this->auth->signup($request);
                $this->session->set_flashdata($result['status'], $result['msg']);
                redirect('user/list-users/' . strtolower($data['arm_type']));
            }
        }
        $this->template->content->view('add_user', $data);
        $this->template->publish();
    }

    public function get_detail()
    {
        $data = array('status' => 'error', 'msg' => "User not found");
        if ($this->input->post()) {
            $user_id = $this->input->post('user_id');
            $result = $this->user->get_detail($user_id);
            if (!empty($result)) {
                $data = array('status' => 'success', 'data' => $result);
            }
        }
        echo json_encode($data);
        exit;
    }
}
