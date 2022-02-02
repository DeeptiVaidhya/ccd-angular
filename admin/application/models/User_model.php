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
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Name:    User Model
 *
 * Requirements: PHP5 or above
 *
 */
class User_model extends CI_Model
{

    public $tables = array();

    public function __construct()
    {
        parent::__construct();
        $this->tables = array('users' => 'users', 'participants' => 'patient_insurance_information');
    }

    /**
     * @desc Get user detail by encrypted field.
     */

    public function get_encrypted_user_detail($field_name, $str, $is_case_insensitive = true)
    {
        $str = trim($str);
        $userdata = $this->db->select('id,username as encrypt_username,username as username,email,first_name,last_name')
            ->get($this->tables['users'])->result_array();

        $user_detail = array();
        if (!empty($userdata)) {
            $flagFound = false;
            foreach ($userdata as $key => $val) {
                if ($flagFound) {
                    break;
                }
                foreach ($field_name as $field) {
                    //if (aes_256_decrypt($val[$field]) == $str) {
                    if (aes_256_decrypt($val[$field]) == $str || ($is_case_insensitive == true && strtolower(aes_256_decrypt($val[$field])) == strtolower($str))) {
                        $val['email'] = aes_256_decrypt($val['email']);
                        $val['username'] = aes_256_decrypt($val['username']);
                        $val['first_name'] = aes_256_decrypt($val['first_name']);
                        $val['last_name'] = aes_256_decrypt($val['last_name']);
                        $user_detail = $val;
                        $flagFound = true;
                        break;
                    }
                }
            }
        }
        return $user_detail;
    }

    /**
     * Get User Detail
     * @param  user_id
     * @return Array
     * */
    public function get_users_list($params = array())
    {
        extract($params);
        $user_type = isset($user_type) ? $user_type : false;
        $user_id = isset($user_id) ? $user_id : false;

        $col_sort = array("id", "patient_name", "insurance_type", "coverage_effective_date", "coverage_expiration_date", "co_insurance_percentage", "individual_deductible", "individual_deductible_paid", "individual_max_out_of_pocket", "individual_max_out_of_pocket_paid", "family_deductible", "family_deductible_paid", "family_max_out_of_pocket", "family_max_out_of_pocket_paid");

        $where = array();
        $order_by = "id";
        $order = 'DESC';

        if ($user_type) {
            $where['user_type'] = $user_type;
        }

        if ($user_id) {
            $where['id'] = $user_id;
        }

        if (isset($params['iSortCol_0'])) {
            $index = $params['iSortCol_0'];
            $order = $params['sSortDir_0'] === 'asc' ? 'asc' : 'desc';
            $order_by = $col_sort[$index];
        }

        if (isset($params['sSearch']) && $params['sSearch'] != "") {
            $words = $params['sSearch'];
            $search_array = array();
            foreach ($col_sort as $key => $value) {
                $search_array[$value] = $words;
            }

        }
        if (isset($params['iDisplayStart']) && $params['iDisplayLength'] != '-1') {
            $start = intval($params['iDisplayStart']);
            $limit = intval($params['iDisplayLength']);
        }
        if (isset($start) && isset($limit)) {
            $this->db->limit($limit, $start);
        }
        $total = $this->db->query('
            SELECT
                COUNT(*) as count
            FROM
            patient_insurance_information
        ')->row();

        $result = $this->db->select('*')->where($where)->order_by($order_by, $order)->get($this->tables['participants'])->result_array();
        return array('data' => $result, 'count' => $total);
    }

    

    /**
     * add patient
     * @param  array
     * @return patient_detials array
     * */

    public function add_patient_data($params = array())
    {
        extract($params);
        $info = array();
        $last_id = '';
        $id = isset($id) ? $id : FALSE;
        $info['patient_name'] = $patient_name;        
        $info['insurance_type'] = $insurance_type; 
        $info['insurance_name'] = $insurance_name;
        $info['company_address'] = $company_address;
        $info['identification_number'] = $identification_number;
        $info['phone'] = $phone;
        $info['group_number'] = $group_number;
        $info['insurance_plan'] = $insurance_plan;
        $info['coverage_effective_date'] = date("Y-m-d H:i:s", strtotime($coverage_effective_date)); 
        $info['coverage_expiration_date'] = date("Y-m-d H:i:s", strtotime($coverage_expiration_date)); 
        $info['co_insurance_percentage'] = floatval($co_insurance_percentage)/100; 
        $info['individual_deductible'] = $individual_deductible; 
        $info['individual_deductible_paid'] = $individual_deductible_paid; 
        $info['individual_max_out_of_pocket'] = $individual_max_out_of_pocket; 
        $info['individual_max_out_of_pocket_paid'] = $individual_max_out_of_pocket_paid; 
        $info['family_deductible'] = $family_deductible; 
        $info['family_deductible_paid'] = $family_deductible_paid; 
        $info['family_max_out_of_pocket'] = $family_max_out_of_pocket; 
        $info['family_max_out_of_pocket_paid'] = $family_max_out_of_pocket_paid;        
        $info['updated_at'] = date('Y-m-d H:i:s');
        $info['created_by'] = 1;
        
        $is_exist = $this->check_patient_exist($patient_name, $id);
        if($is_exist){
            return array('status' => 'error', 'msg' => 'Patient with this name already exist.');
        }
        if($id == ''){
            
            $info['created_at'] = date('Y-m-d H:i:s');
            $this->db->trans_start();
            $this->db->insert('patient_insurance_information', $info);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() !== false) {
                return array('status' => 'success', 'msg' => 'Patient added successfully.', 'patient_id' =>$last_id);
            }else{
                return array('status' => 'error', 'msg' => 'Error while adding patient');
            }
        }else{
            
            $this->db->update('patient_insurance_information', $info, array('id' => $id));
            $patient_data = $this->get_patient_details($id);
            return array('status' => 'success', 'patient_id' =>$id, 'data' => $patient_data, 'msg' => 'Patient details updated successfully.');
        }
        return array('status' => 'error', 'msg' =>'Error while updating participant.');
    }

    /**
     * get decrypted patient detials
     * @param patient_id
     * @return array
     */
    public function get_patient_details($id =''){
        $patient_data = array();
        if($id != ''){
            $patient_data = $this->db->select('*')
                                    ->where('id', $id)
                                    ->get('patient_insurance_information')
                                    ->row_array();
            $patient_data['coverage_effective_date'] = date("m/d/Y H:i:s", strtotime($patient_data['coverage_effective_date']));
            $patient_data['coverage_expiration_date'] = date("m/d/Y H:i:s", strtotime($patient_data['coverage_expiration_date']));
        }

        return $patient_data;
    }

    /**
     * get patient using search key
     * @param earch_key
     * @return array
     */
    public function search_patient_list($params = array()){
        extract($params);
        $patient_list = array();
        if($serach_key){
            $patient_list = $this->db->select('*')
                                    ->like('patient_name', $serach_key)
                                    ->get('patient_insurance_information')
                                    ->result_array();
        }
        
        return $patient_list;
    }

    /**
     * check patient exist or not using patient_name
     * @param name
     * @return boolean
     */
    public function check_patient_exist($name = '', $id = ''){
        $patient_found_flag = FALSE;
        $patients = $this->db->select('*')
                             ->get('patient_insurance_information')
                             ->result_array();
        foreach ($patients as $key => $value) {
            
            if(strtolower(trim($value['patient_name'])) == strtolower(trim($name)) && $id != $value['id']){
                
                $patient_found_flag = TRUE;
                break;
            }
        }
        if($patient_found_flag){
            return TRUE;
        }
        return FALSe;
    }


}
