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
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

if (!function_exists('generate_log')) {

    /**
     * @desc Used to add logs generated from APIs and user activity
     * @param string $msg
     * @return boolean
     */
    function generate_log($msg = '') {
        $CI = & get_instance();
        $message = '';
        $log_path = APPPATH . 'logs/';
        file_exists($log_path) OR mkdir($log_path, 0755, TRUE);
        if (!is_dir($log_path) OR ! is_really_writable($log_path)) {
            return FALSE;
        }
        $filepath = $log_path . 'system_log.php';

        if (!file_exists($filepath)) {
            $message .= "<" . "?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?" . ">\n\n";
        }
        
        if (!$fp = @fopen($filepath, FOPEN_WRITE_CREATE)) {
            return false;
        }
        
        $message .= date('Y-m-d H:i:s')." [".get_real_ip_addr()."] ".$msg."\n";
        flock($fp, LOCK_EX);
        fwrite($fp, $message);
        flock($fp, LOCK_UN);
        fclose($fp);

        @chmod($filepath, FILE_WRITE_MODE);
        return true;
    }

}
if (!function_exists('get_real_ip_addr')) {

    /**
     * @desc Used to get real ip for user activity
     * @return string
     */
    function get_real_ip_addr() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}



if (!function_exists('send_email')) {

    /**
     * @desc Send email function to use globally from Application
     * @param type $subject
     * @param type $to
     * @param type $msg
     * @param type $attachment
     * @return type
     */
    function send_email($subject = false, $to = false, $msg = false, $attachment = false) {
        $CI = & get_instance();
        $CI->load->library('email');
        $CI->email->clear();
        /* Add To Email */
        if ($to != false) {
            $CI->email->to($to);
        }
        /* Add From Email */
		$from = $CI->config->item('email_from_info');
		$CI->email->from($from, $CI->config->item('site_name'));

		$reply_to = $CI->config->item('email_reply_to_info');
		$reply_to && $CI->email->reply_to($reply_to, $CI->config->item('site_name'));
        /* Add From subject */
        $CI->email->subject($subject);
        /* Add message content */
        $CI->email->message($msg);
        /* Add attachment */
        if ($attachment != false) {
            if (is_array($attachment)) {
                foreach ($attachment as $val) {
                    $CI->email->attach($val, 'attachment');
                }
            } else {
                $CI->email->attach($attachment, 'attachment');
            }
        }
        /* Mail all data */
        $status = ($CI->email->send()) ? true : false;
        return $status;
    }

}

if (!function_exists('aes_256_encrypt')) {

    /**
     * @desc Used to encrypt a string in AES 256, to store in Database
     * @param string $str
     */
    function aes_256_encrypt($str = '') {
        $CI = & get_instance();
        $config = $CI->config->item('encryption');
        $CI->encryption->initialize($config);
        if ($str != '') {
            return $CI->encryption->encrypt($str);
        }
        return '';
    }

}
if (!function_exists('aes_256_decrypt')) {

    /**
     * @desc Used to decrypt a encrypted AES 256 string, fetched from Database
     * @param string $str
     */
    function aes_256_decrypt($str = '') {
        $CI = & get_instance();
        $config = $CI->config->item('encryption');
        $CI->encryption->initialize($config);
        if ($str != '') {
            return $CI->encryption->decrypt($str);
        }
        return '';
    }

}


if (!function_exists('assets_url')) {

    /**
     * @desc Function to get assets URL
     * @param type $uri
     * @return type
     */
    function assets_url($uri = '') {
        $CI = & get_instance();
        return $CI->config->item('base_url') . $CI->config->item('assets_url') . trim($uri, '/');
    }

}
if (!function_exists('re_arrange_files')) {

    function re_arrange_files($file_post = array(), $name = '') {
        $file_ary = array();
        $file_name = $file_post['name'];
        $file_keys = array_keys($file_post);

        foreach ($file_name as $i => $f_name) {
            foreach ($file_keys as $key) {
                $file_ary[$name . '_' . $i][$key] = $file_post[$key][$i];
            }
        }
        return $file_ary;
    }

}


if (!function_exists('get_plugins_in_template')) {

    /**
     * @desc Function to load JS and/or CSS for a plugin
     * @param type $plugin
     */
    function get_plugins_in_template($plugin = '') {
        $CI = & get_instance();
        switch ($plugin) {
            case 'datatable':
                $CI->template->javascript->add('assets/js/jquery.dataTables.min.js');
                $CI->template->javascript->add('assets/js/dataTables.bootstrap.min.js');
                $CI->template->javascript->add('assets/js/dataTables.responsive.min.js');
                // Dynamically add a css stylesheet
                $CI->template->stylesheet->add('assets/css/dataTables.bootstrap.min.css');
                $CI->template->stylesheet->add('assets/css/responsive.bootstrap.min.css');
                break;
            default:
                break;
        }
    }

}


/* End of file Site_helper.php */
/* Location: ./application/helpers/site_helper.php */
