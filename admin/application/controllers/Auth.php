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

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'auth');
        $this->config->load('auth', true);

        /** add error delimiters * */
        $this->form_validation->set_error_delimiters('<label class="error">', '</label>');
    }

    /**
     * Login
     * Get username and password if user authenticate it redirect in
     *  dashboard else it redirect to login page
     * @return Array
     * */
    public function login()
    {
        if ($this->session->userdata('logged_in') != false) {
            redirect('user');
        }
        if ($this->input->post()) {
            $this->config->load("form_validation");
            $this->form_validation->set_rules($this->config->item("loginForm"));
            if ($this->form_validation->run() != false) {
                $username = $this->input->post('username');
                $password = $this->input->post('password');
                $result = $this->auth->login($username, $password);
                $this->session->set_flashdata($result['status'], $result['msg']);
                if (!empty($result) && $result['status'] == 'success') {
                    // Add user data in session
                    $this->session->set_userdata('logged_in', $result['userdetail']);
                    redirect('user');
                } else {
                    redirect('auth');
                }
            }
        }
        $this->load->view('login');
    }

    /**
     * Logout
     * Delete user session and redirect to login page
     * @return Bool
     * */
    public function logout()
    {
        $this->session->sess_destroy();
        $this->session->set_flashdata('success', 'User logout successfully');
        redirect('auth');
    }

    /**
     * Forgot Password
     * Take user email and emailed user password reset link in email address
     * @return Bool
     * */
    public function forgot_password()
    {
        if ($this->input->post()) {
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_email_exists');
            if ($this->form_validation->run() != false) {
                $result = $this->auth->forgotten_password($this->input->post('email'));
                $this->session->set_flashdata($result['status'], $result['msg']);
                redirect('auth');
            }
        }
        $this->load->view('forgot_password');
    }

    /**
     * Reset password functionality
     */
    public function reset_password($code)
    {
        $this->data['heading'] = 'RESET PASSWORD';
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if ($code != '') {
                $profile = $this->auth->forgotten_code_detail($code);

                if (!empty($profile)) {
                    if ($this->config->item('forgot_password_expiration', 'auth') > 0) {
                        $interval = abs(time() - $profile->forgotten_password_time);
                        $minutes = round($interval / 60);
                        $expiration = $this->config->item('forgot_password_expiration', 'auth');
                        if ($minutes > $expiration * 60) {
                            $this->session->set_flashdata('error', $this->lang->line('forgot_password_link_expired'));
                            redirect('auth');
                        }
                    }
                    $this->data['code'] = $profile->forgotten_password_code;
                    $this->data['user_id'] = $profile->id;
                    $this->load->view('reset_password', $this->data);
                } else {
                    $this->session->set_flashdata('error', $this->lang->line('forgot_password_link_invalid'));
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line('forgot_password_link_invalid'));
                redirect('auth');
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->config->load("form_validation");
            $this->form_validation->set_rules($this->config->item("resetPasswordForm"));
            $code = $this->input->post('forgotten_code');
            $profile = $this->auth->forgotten_code_detail($code);
            $this->data['code'] = $profile->forgotten_password_code;
            $this->data['user_id'] = $profile->id;
            if ($this->form_validation->run() == false) {
                $this->load->view('reset_password', $this->data);
            } else {
                $id = $this->input->post('user_id');
                $password = $this->input->post('password');
                $res = $this->auth->reset_password($id, $password);
                $this->session->set_flashdata($res['status'], $res['msg']);
                redirect('auth');
            }
        }
    }

    public function index()
    {
        $this->login();
    }

    /**
     * Check Email
     * It is a callback function take user email to check if email exist in system or not
     * @return Bool
     * */
    public function check_email()
    {
        $email = $this->input->post('email');
        if ($email != '') {
            $count = $this->auth->email_check($email);
            if ($count) {
                $this->form_validation->set_message('check_email', $this->lang->line('email_exists'));
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * Check Email
     * It is a callback function take user email to check if email exist in system or not
     * @return Bool
     * */
    public function check_email_exists()
    {
        $email = $this->input->post('email');
        $count = false;
        $user_detail = $this->user->get_encrypted_user_detail(array('email'), $email);
        if ($email != '' && isset($user_detail['id'])) {
            $count = $this->auth->email_check($email);
        }
        if (!$count) {
            $this->form_validation->set_message('check_email_exists', $this->lang->line('email_not_exists'));
            return false;
        } else {
            return true;
        }
    }

    /**
     * Change Profile functionality
     */
    public function profile()
    {
        if ($this->session->userdata('logged_in') == false) {
            redirect('auth');
        }
        $data['login_user_detail'] = $this->session->userdata('logged_in');
        $this->breadcrumbs->push('Profile', 'profile');
        $data['breadcrumb'] = $this->breadcrumbs->show();
        if ($this->input->post()) {
            if ($this->input->post('action') == 'basic_information') {
                $this->form_validation->set_rules($this->config->item("basicInfoForm"));
                if ($this->form_validation->run() == false) {
                    $this->template->content->view('profile', $data);
                } else {
                    $user_data['first_name'] = $this->input->post('first_name');
                    $user_data['last_name'] = $this->input->post('last_name');
                    if ($this->input->post('email')) {
                        $user_data['email'] = $this->input->post('email');
                    }
                    $user_data['id'] = $data['login_user_detail']->id;

                    $result = $this->auth->update_profile($user_data);
                    $this->session->set_flashdata($result['status'], $result['msg']);
                    if (!empty($result) && $result['status'] == 'success') {
                        $this->session->unset_userdata('logged_in');
                        $session_data = $result['userdetail'];
                        $this->session->set_userdata('logged_in', $session_data);

                    }
                    redirect('auth/profile');
                }
            } else if ($this->input->post('action') == 'login_detail') {
                // Check Username
                if ($data['login_user_detail']->username != $this->input->post('username')) {
                    $this->form_validation->set_rules('username', 'Username', 'required');
                }

                // Check Email
                if ($data['login_user_detail']->email != $this->input->post('email')) {
                    $this->form_validation->set_rules('email', 'Email', 'required|callback_check_email|valid_email');
                }

                // Check Password
                if ($this->input->post('password') != '') {
                    $this->form_validation->set_rules('password', 'Password', 'matches[confirm_password]|is_valid_password');
                    $this->form_validation->set_rules('confirm_password', 'Confirm password', 'required');
                }

                if ($this->form_validation->run() == false) {
                    $this->template->content->view('profile', $data);
                } else {
                    $user_data['email'] = $this->input->post('email');
                    $user_data['username'] = $this->input->post('username');
                    $user_data['password'] = $this->input->post('password');
                    $user_data['id'] = $data['login_user_detail']->id;

                    $result = $this->auth->update_login_detail($user_data);
                    $this->session->set_flashdata($result['status'], $result['msg']);
                    if (!empty($result) && $result['status'] == 'success') {
                        $this->session->unset_userdata('logged_in');
                        $session_data = $result['userdetail'];
                        $this->session->set_userdata('logged_in', $session_data);
                    }
                    redirect('auth/profile');

                }
            }
        } else {
            $this->template->content->view('profile', $data);
        }
        $this->template->publish();
    }

    public function export()
    {
        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=\"user_details" . ".csv\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        if ($this->session->userdata('logged_in') == false) {
            redirect('auth');
        }

        $csv_header = array('Serial Number', 'Completion time', 'User type', 'Country', 'Age', 'Gender', 'Race', 'Working Status', 'Education Level', 'Atrial Fibrillation', 'Respiratory Failure', 'Pulmonary Hypertension', 'Hepatocellular Carcinoma', 'Hypertension', 'Diabetes', 'Heart Failure', 'Final Risk Score');

        $data = $this->user->get_users_data();
        $csvContent = array(implode('|', $csv_header));
        foreach ($data as $key => $value) {
            $csvContent[] = implode('|', array(
                $key + 1,
                isset($value['created_at']) ? $value['created_at'] : 'N/A',
                isset($value['user_type']) ? ($value['user_type'] == '1' ? 'Patient' : ($value['user_type'] == '2' ? 'Caregiver' : 'Healthcare')) : 'N/A',
                isset($value['country']) ? $value['country'] : 'United States',
                isset($value['age']) ? $value['age'] : 'N/A',
                isset($value['gender']) ? $value['gender'] : 'N/A',
                isset($value['race']) ? $value['race'] : 'N/A',
                isset($value['working_status']) ? $value['working_status'] : 'N/A',
                isset($value['education_level']) ? $value['education_level'] : 'N/A',
                isset($value['atrial_fibrillation']) ? $value['atrial_fibrillation'] : 'N/A',
                isset($value['respiratory_failure']) ? $value['respiratory_failure'] : 'N/A',
                isset($value['pulmonary_hypertension']) ? $value['pulmonary_hypertension'] : 'N/A',
                isset($value['hepatocellular_carcinoma']) ? $value['hepatocellular_carcinoma'] : 'N/A',
                isset($value['hypertension']) ? $value['hypertension'] : 'N/A',
                isset($value['diabetes']) ? $value['diabetes'] : 'N/A',
                isset($value['heart_failure']) ? $value['heart_failure'] : 'N/A',
                isset($value['final_risk_score']) ? $value['final_risk_score'] : 'N/A',
            ));
        }
        $csvContent = implode("\r\n", $csvContent);
        $handle = fopen('php://output', 'w');
        fputs($handle, $csvContent);
        fclose($handle);
        exit;
    }
}
