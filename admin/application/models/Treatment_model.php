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
 * Name:    Treatment Model
 *
 * Requirements: PHP5 or above
 *
 */
class Treatment_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'user');
    }

    /**
     * Add billing code for patient
     * @param array
     * @return array
     */
    public function add_billing_code($params = array()){
        extract($params);
        $status = 'error';
        $billing_code_list = array();
        $billing_info = array();
        $id = isset($id) ? $id : FALSE;
        $billing_info['co_pay'] = $co_pay;
        $billing_info['ccs_provider'] = $ccs_provider;
        $billing_info['frequent_referral'] = $frequent_referral;
        $billing_info['provider'] = $provider;
        $billing_info['cpt_code'] = $cpt_code;
        $billing_info['no_of_units'] = $no_of_units;
        $billing_info['covered'] = $covered;
        $billing_info['patient_id'] = $patient_id;
        $billing_info['est_unit_allow'] = $mode_allowed;
        $billing_info['est_allow'] = floatval($mode_allowed)*floatval($no_of_units);
        $billing_info['covered_amount'] = $covered == '1' ? $billing_info['est_allow'] : 0;
        $billing_info['non_covered_amount'] = $covered == '0' ? $billing_info['est_allow'] : 0;
        $billing_info['rvu_reimbursement'] = $billing_info['covered_amount']*0.5;
        $msg = $id ? 'Error While updating billing code details.' :'Error while adding billing code.';

        if(!empty($billing_info) && isset($billing_info['patient_id'])){
            
            $this->db->trans_start();
            if(!$id){
                $this->db->insert('treatment_information', $billing_info);
            }else{
                $this->db->update('treatment_information', $billing_info, array('id' => $id));
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() !== false) {
                $status = 'success';
                $msg = $id ? 'Billing code details updated successfully' :'Billing code added successfully.';
            }
            $billing_code_list = $this->get_patient_billing_code_list($patient_id);
            
        }
        return array('status' => $status, 'data' => $billing_code_list, 'msg' => $msg);
    }

    /**
     * Add prescription for patient
     * @param array
     * @return array
     */
    public function add_prescription($params = array()){
        extract($params);
        $status = 'error';
        $prescription_list = array();
        $prescription_info = array();
        $id = isset($id) ? $id : FALSE;
        $prescription_info['co_pay'] = $co_pay;
        $prescription_info['unit_price'] = $unit_price;
        $prescription_info['quantity'] = $quantity;
        $prescription_info['drug_name'] = $drug_name;
        $prescription_info['patient_id'] = $patient_id;
        $prescription_info['covered'] = $covered;

        if($covered == '1'){
            $prescription_info['patient_pay'] = $co_pay;
        }else{
            $prescription_info['patient_pay'] = floatval($unit_price)*floatval($quantity);
        }

        $msg = $id ? 'Error While updating prescription details.' :'Error while adding prescription.';
        if(!empty($prescription_info) && isset($prescription_info['patient_id'])){
            
            $this->db->trans_start();
            if(!$id){
                $this->db->insert('patient_prescriptions', $prescription_info);
            }else{
                $this->db->update('patient_prescriptions', $prescription_info, array('id' => $id));
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() !== false) {
                $status = 'success';
                $msg = $id ? 'Prescription details updated successfully' :'Prescription added successfully.';
            }
            $prescription_list = $this->get_prescription_list($patient_id);
            
        }
        return array('status' => $status, 'data' => $prescription_list, 'msg' => $msg);
    }

    /**
     * Update patient payment information
     * @param patient_id
     * @return boolean
     */
    public function update_payment_data($patient_id = ''){
        $status = 'error';
        $msg = "Error while saving billing code.";
        $coverd_amt = ''; 
        $non_coverd_amt = ''; 
        $co_pays = '';
        $patient_payment_info = array();
        $patient_info = array();
        $payment_info = array();
        
        if($patient_id != ''){
            $allBillingCode = $this->get_patient_billing_code_list($patient_id);
            $patient_info = $this->user->get_patient_details($patient_id);
            foreach ($allBillingCode as $key => $value) {
                $coverd_amt = floatval($coverd_amt)+floatval($value['covered_amount']);
                $co_pays = floatval($co_pays)+floatval($value['co_pay']);
                $non_coverd_amt = floatval($non_coverd_amt)+floatval($value['non_covered_amount']);
            }

            $this->db->delete('estimated_patient_payment', array('patient_id' => $patient_id));

            $payment_info['covered_services'] = floatval($coverd_amt);
            
            $payment_info['non_covered_services'] = floatval($non_coverd_amt);
            
            $payment_info['individual_deductible'] = min($payment_info['covered_services'], (floatval($patient_info['individual_deductible'])-floatval($patient_info['individual_deductible_paid'])));
            
            $payment_info['co_insurance'] = (($payment_info['covered_services']-$payment_info['individual_deductible'])*floatval($patient_info['co_insurance_percentage']));
            
            $payment_info['individual_max_out_of_pocket'] = min((floatval($patient_info['individual_max_out_of_pocket'])-floatval($patient_info['individual_max_out_of_pocket_paid'])),$payment_info['co_insurance']);
            
            $payment_info['family_deductible'] = max($payment_info['individual_deductible'],(floatval($patient_info['family_deductible'])-floatval($patient_info['family_deductible_paid'])));
            
            $payment_info['family_max_out_of_pocket'] = max($patient_info['family_deductible'], (floatval($patient_info['family_max_out_of_pocket'])-floatval($patient_info['family_max_out_of_pocket_paid'])));
            
            $payment_info['individual_plan'] = $payment_info['non_covered_services']+max($payment_info['individual_max_out_of_pocket'], $payment_info['family_max_out_of_pocket']);
            
            $payment_info['family_plan'] = $payment_info['non_covered_services']+min($payment_info['individual_max_out_of_pocket'], $payment_info['family_max_out_of_pocket']);
            
            $payment_info['co_pays'] = floatval($co_pays);

            $payment_info['patient_id'] = $patient_id;
            
            $family_pay = $payment_info['family_deductible']+$payment_info['family_max_out_of_pocket'];
            if($family_pay == 0){
                $payment_info['estimated_patient_payment'] = $payment_info['co_pays']+$payment_info['individual_plan'];
            }else{
                $payment_info['estimated_patient_payment'] = $payment_info['co_pays']+$payment_info['family_plan'];
            }
            $this->db->trans_start();
                $this->db->insert('estimated_patient_payment', $payment_info);
            $this->db->trans_complete();
            if ($this->db->trans_status() !== false) {
                $status = 'success';
                $msg = "Billing code data save successfully.";
            }
        }

        return array('status' => $status, 'msg' => $msg);
    }
    
    /**
     * Get patient payment info
     * @param patient_id
     * @return array
     */
    public function patient_payment_info($patient_id = ''){
        $patient_payment_info = array();
        if($patient_id != ''){
            $patient_payment_info = $this->db->select('*')
                                            ->where('patient_id', $patient_id)
                                            ->get('estimated_patient_payment')
                                            ->row_array();
        }
        return $patient_payment_info;
    }

    /**
     * Get list of added billing code of patient
     * @param patient_id
     * @return list of billing codes
     */

    public function get_patient_billing_code_list($id = ''){
        $billing_code_list = array();
        if($id != ''){
            $billing_code_list = $this->db->select('*')
                                         ->where('patient_id', $id)
                                         ->get('treatment_information')
                                         ->result_array();

        }
        return $billing_code_list;
    }

    /**
     * Get list of added billing code of patient
     * @param patient_id
     * @return list of billing codes
     */

    public function get_patient_billing_code_details($id = ''){
        $billing_code_data = array();
        if($id != ''){
            $billing_code_data = $this->db->select('id, co_pay, ccs_provider, frequent_referral, est_date_of_service, provider, cpt_code, no_of_units, covered, patient_id')
                                         ->where('id', $id)
                                         ->get('treatment_information')
                                         ->row_array();
        }
        return $billing_code_data;
    }

    /**
     * Get list of added billing code of patient
     * @param patient_id
     * @return list of billing codes
     */

    public function get_prescription_list($id = ''){
        $prescription_list = array();
        if($id != ''){
            
            $prescription_list = $this->db->select('*')
                                         ->where('patient_id', $id)
                                         ->get('patient_prescriptions')
                                         ->result_array();
        }
        return $prescription_list;
    }

    /**
     * Get patient prescription data
     * @param prescription_id
     * @return array
     */

    public function get_prescription_data($id = ''){
        $prescription_data = array();
        if($id != ''){
            
            $prescription_data = $this->db->select('*')
                                         ->where('id', $id)
                                         ->get('patient_prescriptions')
                                         ->row_array();
        }
        return array('status' => 'success','data' =>$prescription_data);
    }


    /**
     * Get patient prescription data
     * @param prescription_id
     * @return array
     */

    public function get_patient_prescription_data($id = ''){
        $prescription_data = array();
        $copay_covered_prescriptions = 0;
        $covered_prescriptions = 0;
        $non_covered_prescriptions = 0;
        $insurance_pays = 0;
        if($id != ''){
            
            $prescription_data = $this->db->select('*')
                                         ->where('patient_id', $id)
                                         ->get('patient_prescriptions')
                                         ->result_array();
            foreach ($prescription_data as $key => $value) {
                if($value['covered'] == '1'){
                    $copay_covered_prescriptions = floatval($copay_covered_prescriptions)+floatval($value['co_pay']);
                    $covered_prescriptions = (floatval($copay_covered_prescriptions)+(floatval($value['unit_price'])*floatval($value['quantity'])));
                    $pays = ((floatval($value['unit_price'])*floatval($value['quantity'])))-floatval($value['co_pay']);
                    $insurance_pays = floatval($insurance_pays)+floatval($pays);
                }else{
                    $non_covered_prescriptions = floatval($non_covered_prescriptions)+(floatval($value['unit_price'])*floatval($value['quantity']));
                }
            }
        }
        return array('copay_covered_prescriptions'=>$copay_covered_prescriptions, 'non_covered_prescriptions' =>$non_covered_prescriptions, 'insurance_pays' =>$insurance_pays, 'covered_prescriptions' =>$covered_prescriptions);
    }

    /**
     * Get providers list
     * @param CCS Referral and InsType
     * @return array
     */

    public function get_provider_list($params = array()){
        extract($params);
        $type = '';
        $providers = array();
        if(isset($ins_type)){
            if(isset($ccs_provider) && $ccs_provider == '1' && $frequent_referral == '0'){
                switch ($ins_type) {
                    case 'HMO':
                        $type = 'HMOY';
                        break;
                    case 'Medicare':
                        $type = 'MedicareY';
                        break;
                    case 'PPO':
                        $type = 'PPOY';
                        break;
                }
            }else if(isset($ccs_provider) && $ccs_provider == '0' && $frequent_referral == '1'){
                
                switch ($ins_type) {
                    case 'HMO':
                        $type = 'HMON_Y_';
                        break;
                    case 'Medicare':
                        $type = 'Senior_Care_PlusN_Y_';
                        break;
                    case 'PPO':
                        $type = 'PPON_Y_';
                        break;
                }
            }else if(isset($ccs_provider) && $ccs_provider == '0' && $frequent_referral == '0'){
                
                switch ($ins_type) {
                    case 'HMO':
                        $type = 'HMO_N_CCS_PROV_N_FREQUENT_REF';
                        break;
                    case 'Medicare':
                        $type = 'MedicareN';
                        break;
                    case 'PPO':
                        $type = 'PPON';
                        break;
                }
            }
            
            if($type != ''){
                $providers = $this->db->select('*')
                                      ->where('insurance_provider_type', $type)
                                      ->get('name_manager')
                                      ->result_array();
            }
            return $providers;
        }
        
        return array('status' => 'error', 'msg' => 'Insurance type not selected for this patient.');

    }

    /**
     * Get CPT code for patient billing code
     * @param InsType, Provider
     */
    public function get_cpt_code($params = array()){
        extract($params);
        $cpt_code = array();
        if(isset($ins_type) && isset($provider)){
            $cpt_code = $this->db->select('id, key, rendering_provider, claim_type, cpt_proc_desc_concat, unique_id, mode_allowed')
                                 ->where(array('rendering_provider' => $provider, 'claim_type' => $ins_type))
                                 ->get('providers')
                                 ->result_array();
        }
        
        return $cpt_code;
    }

    /**
     * Get CPT code for patient billing code
     * @param InsType, Provider
     */
    public function search_prescription($key =''){
        $prescription_data = array();
        if($key != ''){
            $prescription_data = $this->db->select('*')
                                         ->like('drug_full_name', $key)
                                         ->get('prescription')
                                         ->result_array();
        }
        return $prescription_data;
    }

    /**
     * Get and update treatment paln details
     * @param array
     */
    public function update_treatment_plan($params = array()){
        extract($params);
        $treatment_data = array();
        $treatment_data['treatment_plan_details'] = $treatment_plan_details;
        $patient_id = $patient_id;
        if($patient_id != '' && $treatment_data['treatment_plan_details'] != ''){
            $this->db->update('estimated_patient_payment', $treatment_data, array('patient_id' => $patient_id));
            $treatment_data = $this->get_treatment_plan($patient_id);
        }
        return $treatment_data;
    }

    /**
     * Get treatment paln details
     * @param patient_id
     */
    public function get_treatment_plan($id =''){
        $treatment_data = array();
        if($id != ''){
            $treatment_data = $this->db->select('id, treatment_plan_details, patient_id')
                                         ->where('patient_id', $id)
                                         ->get('estimated_patient_payment')
                                         ->row_array();
        }
        return $treatment_data;
    }


    /**
     * Get assistance list
     * @param patient_id
     */
    public function get_assistance_data($id =''){
        $assistance_data = array();
        $applied_assistance = array();
        $approved_assistance = array();
        $recommended_assistance = array();
        $assistance_list = array();
        $patient_data = array();
        $patient_assistance_data = array();
        if($id != ''){
            $assistance_data = $this->db->select('pro.*, pass.program_type, pass.applied_date, pass.recommend_date, pass.approved_date, pass.id as patient_program_id')
                                         ->where('pass.patient_id', $id)
                                         ->join('program pro', 'pro.id = pass.program_id')
                                         ->get('patient_program pass')
                                         ->result_array();
            
            foreach ($assistance_data as $value) {
                $program_assistance_data = $this->db->select('ass.*, phass.program_id')
                                         ->where('phass.program_id', $value['id'])
                                         ->join('assistance ass', 'ass.id = phass.assistance_id')
                                         ->get('program_has_assistance phass')
                                         ->result_array();
                
                $ass_string = '';
                $assistance = array();
                
                foreach ($program_assistance_data as $key => $svalue) {
                    $ass_string = $svalue['title'].', '.$ass_string;
                    array_push($assistance, $svalue);
                }
                $ass_list = array('assistance' => $assistance);
                $ass_name = array('service' => $ass_string);
                $value = array_merge($value, $ass_name);
                $value = array_merge($value, $ass_list);
                switch ($value['program_type']) {
                    case 'applied':
                        array_push($applied_assistance, $value);
                        break;
                    case 'approved':
                        array_push($approved_assistance, $value);
                        break;
                    case 'recommended':
                        array_push($recommended_assistance, $value);
                        break;
                }
            }
            $assistance_list = $this->get_assistance();
            $patient_data = $this->user->get_patient_details($id);
            $patient_assistance_data = $this->get_patient_assistance($id);

            $all_assistance = array_merge($recommended_assistance, $applied_assistance);
            $all_assistance = array_merge($all_assistance, $approved_assistance);
        }
        return array('all_assistance' =>$all_assistance,'patient_assistance'=>$patient_assistance_data,'data'=>$patient_data,'assistance_data'=>$assistance_list,'applied' => $applied_assistance, 'approved' => $approved_assistance, 'recommended' => $recommended_assistance);
    }


    /**
     * Add program for patient
     * @param array
     * @return array
     */
    public function add_program($params = array()){
        extract($params);
        $status = 'error';
        $program_list = array();
        $program_info = array();
        $id = isset($id) ? $id : FALSE;
        $program_id = '';
        $program_info['program'] = trim($program);
        $program_info['website'] = trim($website);
        $program_info['phone'] = trim($phone);
        $program_info['description'] = trim($description);
        $program_info['cancer_type'] = trim($cancer_type);
        $program_info['created_at'] = date('Y-m-d H:i:s');
        $assistance_list = count($assistance)>0 ? $assistance : FALSE;
        if(!$assistance_list){
            return array('status' => "Error", 'data' => array(), 'msg' => "Error while adding program");
        }
        $msg = $id ? 'Error While updating program details.' :'Error while adding program.';
        if(!empty($program_info)){
            $this->db->trans_start();
            if(!$id){
                $this->db->insert('program', $program_info);
                $program_id = $this->db->insert_id();
            }else{
                $this->db->update('program', $program_info, array('id' => $id));
            }

            $data = array();
            foreach ($assistance_list as $value) {
                $program_data = array();
                $program_data['program_id'] = $program_id;
                $program_data['assistance_id'] = $value;
                array_push($data, $program_data);
            }
            $this->db->insert_batch('program_has_assistance', $data); 

            $this->db->trans_complete();
            if ($this->db->trans_status() !== false) {
                $status = 'success';
                $msg = $id ? 'Program details updated successfully' :'Program added successfully.';
            }
            
        }
        return array('status' => $status, 'data' => array(), 'msg' => $msg);
    }


    /**
     * update assistance type
     * @param assistance_id
     */
    public function update_assistance_type($id ='', $type = ''){
        $program_info = array();
        $program_list = array();
        if($id != '' && $type != ''){
            $program_info['assistance_type'] = $assistance_type;
            $this->db->update('patient_has_assistance', $program_info, array('id' => $id));
            $program_list = $this->get_assistance_data($patient_id);
        }
        return array('data' => $program_list, 'msg' => 'Assistance updated successfully.', 'status' => 'success');
    }

    /**
     * add program for patient
     * @param program_id
     */
    public function patient_has_program($params = array()){
        $data = array();
        $to_updatye_data = array();
        //$assId = array();
        $patient_id = '';
        $program_list = array();
        
        foreach ($params as $key => $value) {
            $update_patient_program_list = array();
            $program_info = array();

            if($value['patient_program_id'] != ''){
                $update_patient_program_list['program_id'] = $value['id'];
                $update_patient_program_list['id'] = $value['patient_program_id'];
                $update_patient_program_list['patient_id'] = $value['patient_id'];
                $update_patient_program_list['program_type'] = $value['assistance_type'];
                
                if($value['assistance_type'] == 'applied'){
                    $update_patient_program_list['applied_date'] = date('Y-m-d H:i:s');
                    
                }
                if($value['assistance_type'] == 'approved'){
                    $update_patient_program_list['approved_date'] = date('Y-m-d H:i:s');
                }
                if($value['assistance_type'] == 'recommended'){
                    $update_patient_program_list['recommend_date'] = date('Y-m-d H:i:s');
                }
                array_push($to_updatye_data, $update_patient_program_list);
                
            }else{
                $program_info['program_id'] = $value['id'];
                $program_info['patient_id'] = $value['patient_id'];
                $program_info['program_type'] = $value['assistance_type'];
                $program_info['recommend_date'] = date('Y-m-d H:i:s');
                //array_push($assId, $value['id']);
                array_push($data, $program_info);
                
            }
            $patient_id = $value['patient_id'];
        }
        if($value['patient_program_id'] != ''){
            $this->db->update_batch('patient_program', $to_updatye_data, 'id'); 
        }else{
            $this->db->insert_batch('patient_program', $data); 
        }
        
        $program_list = $this->get_assistance_data($patient_id);
       
        return array('data' => $program_list, 'msg' => 'Assistance updated successfully.', 'status' => 'success');
    }

    

    /**
     * get patient using search key
     * @param earch_key
     * @return array
     */
    public function search_program($params = array()){
        
        $programList = array();
        if(!empty($params['assistance'])){
            $existingPatient = array();
            $patient_data = $this->db->select('program_id')
                                    ->where('patient_id', $params['patient_id'])
                                    ->get('patient_program')
                                    ->result_array();
            foreach ($patient_data as $value) {
                array_push($existingPatient, $value['program_id']);
            }
            $programs = array();
            $programsId = $this->db->select('program_id')
                                   ->where_in('assistance_id', $params['assistance'])
                                   ->get('program_has_assistance')
                                   ->result_array();
            foreach ($programsId as $key => $value) {
                array_push($programs, $value['program_id']);
            }
            $programs = $this->db->select('*')
                                 ->where_in('id', $programs)
                                 ->get('program')
                                 ->result_array();
            foreach ($programs as $key => $value) {
                if(($value['cancer_type'] == $params['cancer_type'] || $value['cancer_type'] == 'All' || $params['cancer_type'] = '') && !in_array($value['id'], $existingPatient)){
                    array_push($programList,$value);
                }
            }
        }
        
        return array('data'=>$programList, 'status'=>'success');
    }

    /**
     * get assistanceList
     * @param 
     * @return array
     */
    public function get_assistance(){
        $assistance_list = array();
       
            $assistance_list = $this->db->select('*')
                                    ->get('assistance')
                                    ->result_array();
        return $assistance_list;
    }

    /**
     * get patient assistance
     * @param 
     * @return array
     */
    public function get_patient_assistance($patient_id = ''){
        $patient_assistance_data = array();
        if($patient_id != ''){
            $patient_assistance_data = $this->db->select('ass.*')
                                    ->where('p_ass.patient_id', $patient_id)
                                    ->join('assistance ass', 'ass.id = p_ass.assistance_id')
                                    ->get('patient_has_assistance p_ass')
                                    ->result_array();
        }

        return $patient_assistance_data;
        
    }

    /**
     * update patient profile
     * @param patient_data
     * @return array
     */
    public function update_patient_profile($params = array()){
        extract($params);
        $patient_info = array();
        $patient_list = array();
        $patient_info['age_group'] = $age_group;
        $patient_info['zip_code'] = $zip_code;
        $patient_info['cancer_type'] = $cancer_type;
        if(!empty($patient_info)){
            $this->db->update('patient_insurance_information', $patient_info, array('id' => $id));
            $patient_list = $this->user->get_patient_details($id);
        }
        return array('data' => $patient_list, 'msg' => 'Patient profile updated successfully.', 'status' => 'success');
    }


    /**
     * update patient profile
     * @param patient_data
     * @return array
     */
    public function add_needed_patient_assistance_data($params = array()){
        extract($params);
        $assistance_list = array();
        $patientList = $this->db->select('id')
                                ->where('patient_id', $id)
                                ->get('patient_has_assistance')
                                ->result_array();
        if(!empty($patientList)){
            $this->db->delete('patient_has_assistance', array('patient_id' => $id));
        }
        foreach ($assistance as $value) {
            $ass_data = array();
            $ass_data['assistance_id'] = $value;
            $ass_data['patient_id'] = $id;
            $this->db->insert('patient_has_assistance', $ass_data);
        }
        $assistance_list = $this->get_patient_assistance($id);
        return array('data' => $assistance_list, 'msg' => 'Assistance updated successfully.', 'status' => 'success');
    }

}
