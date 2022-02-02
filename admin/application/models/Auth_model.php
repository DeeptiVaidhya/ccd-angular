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

/**
 * Name:    Auth Model
 *
 * Requirements: PHP5 or above
 *
 */
class Auth_model extends CI_Model
{

    public $expire_time;

    public function __construct()
    {
        parent::__construct();
        $this->expire_time = $this->config->item('lockout_time');
        $this->config->load('auth', true);
        $this->load->helper('date');
        $this->tables = array('users' => 'users');
        $this->load->model('user_model', 'user');
        // initialize db tables data
        //initialize data
        $this->store_salt = $this->config->item('store_salt', 'auth');
        $this->salt_length = $this->config->item('salt_length', 'auth');
        // initialize hash method options (Bcrypt)
        $this->hash_method = $this->config->item('hash_method', 'auth');
        $this->default_rounds = $this->config->item('default_rounds', 'auth');
        $this->random_rounds = $this->config->item('random_rounds', 'auth');
        $this->min_rounds = $this->config->item('min_rounds', 'auth');
        $this->max_rounds = $this->config->item('max_rounds', 'auth');
        // load the bcrypt class if needed
        if ($this->hash_method == 'bcrypt') {
            if ($this->random_rounds) {
                $rand = rand($this->min_rounds, $this->max_rounds);
                $params = array('rounds' => $rand);
            } else {
                $params = array('rounds' => $this->default_rounds);
            }

            $params['salt_prefix'] = $this->config->item('salt_prefix', 'auth');
            $this->load->library('bcrypt', $params);
        }
    }

    /**
     * @desc Login to the Web Application
     * @param type $username
     * @param type $password
     * @return type
     */
    public function login($username, $password)
    {
        $user = $this->user->get_encrypted_user_detail(array('username'), $username);
        if (!empty($user)) {
            $user = (object) $user;
            if ($this->is_max_login_attempts_exceeded($user->id)) {
                // Hash something anyway, just to take up time
                generate_log("User [$user->id] account is deactivated due to many failed login attempts");
                return array('status' => 'error', 'msg' => sprintf($this->lang->line('login_attempts_exceed'), $this->expire_time)); //'This account is inactive due to many failed login attempts. Please try again after ' . $this->expire_time . ' minute(s)'
            }
            $password = $this->hash_password_db($user->id, $password);
            if ($password === true) {
                $this->update_login($user->id);
                $status = 'success';
                $msg = $this->lang->line('account_logged_successfully');
                return array('status' => $status, 'msg' => $msg, 'userdetail' => $user);
            } else {
                $status = 'error';
                $msg = 'Incorrect password';
                $this->increase_login_attempts($user->encrypt_username);
                generate_log("User [$user->id] $msg");
                return array('status' => $status, 'msg' => $msg);
            }
        }
        $status = 'error';
        $msg = $this->lang->line('not_valid');
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * @desc Registration for users
     * @param type $data
     * @return type
     */
    public function signup($params = array())
    {
        $status = 'error';
        $msg = 'Server in adding responses.';
        $log = 'Error while submitting responses';
        extract($params);
        $gender = isset($gender) ? $gender : null;
        // Users table.
        $user_data = array(
            'user_type' => $user_type,
            'gender' => $gender,
        );
        $this->db->trans_start();
        $this->db->insert('participants', $user_data);
        $id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() !== false) {
            $status = 'success';
            $msg = 'Responses added successfully.';
            $log = "Participant[$id] responses added.";
        }
        generate_log($log);
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * @desc Insert a forgotten password key.
     * @param type $email
     * @return type
     */
    public function forgotten_password($email)
    {
        // All some more randomness
        $log = '';
        $activation_code_part = "";
        if (function_exists("openssl_random_pseudo_bytes")) {
            $activation_code_part = openssl_random_pseudo_bytes(128);
        }

        for ($i = 0; $i < 1024; $i++) {
            $activation_code_part = sha1($activation_code_part . mt_rand() . microtime());
        }

        $key = $this->hash_code($activation_code_part . $email);

        // If enable query strings is set, then we need to replace any unsafe characters so that the code can still work
        if ($key != '' && $this->config->item('permitted_uri_chars') != '' && $this->config->item('enable_query_strings') == false) {
            // preg_quote() in PHP 5.3 escapes -, so the str_replace() and addition of - to preg_quote() is to maintain backwards
            // compatibility as many are unaware of how characters in the permitted_uri_chars will be parsed as a regex pattern
            if (!preg_match("|^[" . str_replace(array('\\-', '\-'), '-', preg_quote($this->config->item('permitted_uri_chars'), '-')) . "]+$|i", $key)) {
                $key = preg_replace("/[^" . $this->config->item('permitted_uri_chars') . "]+/i", "-", $key);
            }
        }

        // Limit to 40 characters since that's how our DB field is setup
        $expire_time = $this->config->item('forgot_password_expiration', 'auth');
        $link_expires_at = time() + $expire_time * 60 * 60;
        $update = array(
            'forgotten_password_code' => $key,
            'forgotten_password_time' => $link_expires_at,
        );
        $user_detail = $this->user->get_encrypted_user_detail(array('email'), $email);
        $status = 'error';
        $msg = $this->lang->line('unable_make_request');
        if (!empty($user_detail)) {
            $this->db->update('users', $update, array('id' => $user_detail['id']));
            if ($this->db->affected_rows() == 1) {
                $content['note'] = sprintf($this->config->item('expiration_note'), $expire_time);

                $content['link'] = base_url() . 'auth/reset-password/' . $key;

                $content['btntitle'] = $this->config->item('reset_password_btn_titte');
                $content['message'] = sprintf($this->config->item('reset_password_message'), ucfirst($user_detail['first_name']));
                $content['heading'] = $this->config->item('reset_password_heading');
                $subject = $this->config->item('reset_password_subject');
                $log = "User [$user_detail[id]] request for forgot password";

                $message = $this->load->view('email_template', $content, true);
                if (send_email($subject, $email, $message)) {
                    $status = "success";
                    $msg = $this->lang->line('password_reset_link');
                } else {
                    $status = 'error';
                    $msg = $this->lang->line('unable_make_request');
                }
            }
        }
        generate_log($log);
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * Misc functions
     *
     * Hash password : Hashes the password to be stored in the database.
     * Hash password db : This function takes a password and validates it
     * against an entry in the users table.
     * Salt : Generates a random salt value.
     *
     */

    /**
     * @desc Hashes the password to be stored in the database.
     * @param type $password
     * @param type $salt
     * @param type $use_sha1_override
     * @return boolean
     */
    public function hash_password($password, $salt = false, $use_sha1_override = false)
    {
        if (empty($password)) {
            return false;
        }
        // bcrypt
        if ($use_sha1_override === false && $this->hash_method == 'bcrypt') {
            return $this->bcrypt->hash($password);
        }

        if ($this->store_salt && $salt) {
            return sha1($password . $salt);
        } else {
            $salt = $this->salt();
            return $salt . substr(sha1($salt . $password), 0, -$this->salt_length);
        }
    }

    /**
     * This function takes a password and validates it
     * against an entry in the users table.
     *
     * @return void
     * ''
     * */
    public function hash_password_db($id, $password, $use_sha1_override = false)
    {
        if (empty($id) || empty($password)) {
            return false;
        }

        $query = $this->db->select('password, salt')
            ->where('id', $id)
            ->limit(1)
            ->order_by('id', 'desc')
            ->get('users');

        $hash_password_db = $query->row();

        if ($query->num_rows() !== 1) {
            return false;
        }

        // bcrypt
        if ($use_sha1_override === false && $this->hash_method == 'bcrypt') {
            if ($hash_password_db->password != null && $this->bcrypt->verify($password, $hash_password_db->password)) {
                return true;
            }

            return false;
        }

        // sha1
        if ($this->store_salt) {
            $db_password = sha1($password . $hash_password_db->salt);
        } else {
            $salt = substr($hash_password_db->password, 0, $this->salt_length);

            $db_password = $salt . substr(sha1($salt . $password), 0, -$this->salt_length);
        }

        if ($db_password == $hash_password_db->password) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Generates a random salt value for forgotten passwords or any other keys. Uses SHA1.
     *
     * @return void
     * ''
     * */
    public function hash_code($password)
    {
        return $this->hash_password($password, false, true);
    }

    /**
     * Generates a random salt value.
     *
     * Salt generation code taken from https://github.com/ircmaxell/password_compat/blob/master/lib/password.php
     *
     * @return void

     * */
    public function salt()
    {

        $raw_salt_len = 16;

        $buffer = '';
        $buffer_valid = false;

        if (function_exists('random_bytes')) {
            $buffer = random_bytes($raw_salt_len);
            if ($buffer) {
                $buffer_valid = true;
            }
        }

        if (!$buffer_valid && function_exists('mcrypt_create_iv') && !defined('PHALANGER')) {
            $buffer = mcrypt_create_iv($raw_salt_len, MCRYPT_DEV_URANDOM);
            if ($buffer) {
                $buffer_valid = true;
            }
        }

        if (!$buffer_valid && function_exists('openssl_random_pseudo_bytes')) {
            $buffer = openssl_random_pseudo_bytes($raw_salt_len);
            if ($buffer) {
                $buffer_valid = true;
            }
        }

        if (!$buffer_valid && @is_readable('/dev/urandom')) {
            $f = fopen('/dev/urandom', 'r');
            $read = strlen($buffer);
            while ($read < $raw_salt_len) {
                $buffer .= fread($f, $raw_salt_len - $read);
                $read = strlen($buffer);
            }
            fclose($f);
            if ($read >= $raw_salt_len) {
                $buffer_valid = true;
            }
        }

        if (!$buffer_valid || strlen($buffer) < $raw_salt_len) {
            $bl = strlen($buffer);
            for ($i = 0; $i < $raw_salt_len; $i++) {
                if ($i < $bl) {
                    $buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
                } else {
                    $buffer .= chr(mt_rand(0, 255));
                }
            }
        }

        $salt = $buffer;

        // encode string with the Base64 variant used by crypt
        $base64_digits = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
        $bcrypt64_digits = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $base64_string = base64_encode($salt);
        $salt = strtr(rtrim($base64_string, '='), $base64_digits, $bcrypt64_digits);

        $salt = substr($salt, 0, $this->salt_length);

        return $salt;
    }

    /**
     * Get User Detail Using Email
     *
     * @return Array
     * */
    public function user_detail($email = '')
    {
        if (empty($email)) {
            return false;
        }

        $query = $this->db->select('*')
            ->where('email', $email)
            ->limit(1)
            ->order_by('id', 'desc')
            ->get('users');

        $user_detail = $query->row();

        if ($query->num_rows() > 0) {
            return $user_detail;
        } else {
            return false;
        }
    }

    /**
     * @param string $identity: user's identity
     * */
    public function increase_login_attempts($username)
    {
        if ($this->config->item('track_login_attempts', 'auth')) {

            $this->db->select('login_attempts,id');
            $this->db->where('username', $username);
            $this->db->or_where('email', $username);
            $qres = $this->db->get('users');
            if ($qres->num_rows() > 0) {
                $user = $qres->row();
                $data = array('login_attempts' => $user->login_attempts + 1);
                $data['updated_at'] = date('Y-m-d H:i:s');
                return $this->db->update('users', $data, array('id' => $user->id));
            }
        }
        return false;
    }

    /**
     * @param string $identity: user's identity
     * @return boolean
     * */
    public function is_max_login_attempts_exceeded($id)
    {
        if ($this->config->item('track_login_attempts', 'auth')) {
            $max_attempts = $this->config->item('maximum_login_attempts', 'auth');
            if ($max_attempts > 0) {
                $attempts = $this->get_attempts_num($id);
                return $attempts >= $max_attempts;
            }
        }
        return false;
    }

    /**
     * @param string $identity: user's identity
     * @return int
     */
    public function get_attempts_num($id)
    {
        if ($this->config->item('track_login_attempts', 'auth')) {
            $this->db->select('login_attempts');
            $this->db->where('id', $id);
            $qres = $this->db->get('users');
            if ($qres->num_rows() > 0) {
                $user = $qres->row();
                return $user->login_attempts;
            }
        }
        return 0;
    }

    /**
     * clear_login_attempts
     * Based on code from Tank Auth, by Ilya Konyukhov (https://github.com/ilkon/Tank-Auth)
     *
     * @param string $identity: user's identity
     * @param int $old_attempts_expire_period: in seconds, any attempts older than this value will be removed.
     *                                         It is used for regularly purging the attempts table.
     *                                         (for security reason, minimum value is lockout_time config value)

     * */
    public function update_login($user_id)
    {
        if ($this->config->item('track_login_attempts', 'auth')) {
            $this->db->where('id', $user_id);

            return $this->db->update('users', array('login_attempts' => 0, 'last_login' => date('Y-m-d H:i:s')));
        }
    }

    /**
     * Get forgotten code detail
     *
     * @return array
     * ''
     * */
    public function forgotten_code_detail($code = '', $is_access_code = false)
    {
        if (empty($code)) {
            return false;
        }
        $this->db->select('id,email,forgotten_password_code,forgotten_password_time');
        $this->db->where('forgotten_password_code', $code);
        $query = $this->db->limit(1)
            ->order_by('id', 'desc')
            ->get('users');
        // echo $this->db->last_query();
        $user_detail = $query->row();
        return $query->num_rows() > 0 ? $user_detail : false;
    }

    /**
     * reset password
     *
     * @return bool
     *
     * */
    public function reset_password($id, $password)
    {
        $is_exist = $this->db->where('id', $id)->limit(1)->count_all_results('users') > 0;
        $status = 'error';
        $msg = 'Password link is invalid';
        $log = "User [$id ] 'password link is invalid.";
        if ($is_exist) {
            $query = $this->db->select('id, password, salt')
                ->where('id', $id)
                ->limit(1)
                ->order_by('id', 'desc')
                ->get('users');

            if ($query->num_rows() == 1) {
                if (!$is_password_exist) {
                    $result = $query->row();
                    $salt = $this->store_salt ? $this->salt() : false;
                    $new = $this->hash_password($password, $salt);

                    $data = array(
                        'password' => $new,
                        'salt' => $salt,
                    );

                    $this->db->update('users', $data, array('id' => $id));
                    $return = $this->db->affected_rows() == 1;
                    if ($return) {
                        $status = 'success';
                        $msg = $this->lang->line('password_reset_successfully');
                        $log = "User [$id] password reset successfully.";
                    }
                } else {
                    $status = 'error';
                    $msg = $this->lang->line('different_previous_password');
                    $log = "User [$id] your password must be different from the previous 6 passwords.";
                }
            }
        }
        generate_log($log);
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * Update Profile
     *
     * @return array
     *
     * */
    public function update_profile($params = array())
    {
        extract($params);
        $status = 'error';
        $msg = 'User not found.';
        $log = '';
        $user = array();
        $gender = isset($gender) ? $gender : null;
        $first_name = isset($first_name) ? $first_name : null;
        $last_name = isset($last_name) ? $last_name : null;
        $email = isset($email) ? $email : null;
        $id = isset($id) ? $id : false;
        if ($id) {
            $data = array();
            !is_null($gender) && ($data['gender'] = $gender);
            !is_null($first_name) && ($data['first_name'] = aes_256_encrypt($first_name));
            !is_null($last_name) && ($data['last_name'] = aes_256_encrypt($last_name));
            !is_null($email) && ($data['email'] = aes_256_encrypt($email));

            $is_exist = $this->db->where('id', $id)->limit(1)->count_all_results('users') > 0;
            if ($is_exist && !empty($data)) {
                $this->db->update($this->tables['users'], $data, array('id' => $id));
                $query = $this->db->select('*')
                    ->where(array('id' => $id))
                    ->limit(1)->get('users');
                if ($query->num_rows() > 0) {
                    $user = $query->row();
                    $user->username = aes_256_decrypt($user->username);
                    $user->first_name = aes_256_decrypt($user->first_name);
                    $user->last_name = aes_256_decrypt($user->last_name);
                    $user->email = $user->email ? aes_256_decrypt($user->email) : '';
                }
                $log = "User [$id] profile detail updated successfully";
                $status = 'success';
                $msg = $this->lang->line('profile_update_success');
            }
        }
        generate_log($log);
        return array('status' => $status, 'msg' => $msg, 'userdetail' => $user);
    }

    public function email_check($email = '')
    {
        if ($email != '') {
            $user_detail = $this->user->get_encrypted_user_detail(array('email'), $email);
            return count($user_detail) > 0;
        }
        return false;
    }

    /**
     * Update login detail
     *
     * @return array
     *
     * */
    public function update_login_detail($params = array())
    {
        extract($params);
        $is_password = $password != '';

        $log = '';
        $user = array();
        $data = array("username" => aes_256_encrypt($username), 'email' => aes_256_encrypt($email));
        $query = $this->db->where('id', $id)->limit(1)->get('users');
        if ($query->num_rows() > 0) {
            if ($is_password) {
                $salt = $this->store_salt ? $this->salt() : false;
                $data['password'] = $this->hash_password($password, $salt);
                $data['salt'] = $salt;
            }
            $this->db->update($this->tables['users'], $data, array('id' => $id));
            $query = $this->db->select('*')->where(array('id' => $id))->limit(1)->get('users');
            if ($query->num_rows() > 0) {
                $user = $query->row();
                $user->username = aes_256_decrypt($user->username);
                $user->first_name = aes_256_decrypt($user->first_name);
                $user->last_name = aes_256_decrypt($user->last_name);
                $user->email = aes_256_decrypt($user->email);
            }
            $status = 'success';
            $log = "User [$id] login detail updated successfully";
            $msg = $this->lang->line('user_detail_update_success');
        } else {
            $status = 'error';
            $msg = $this->lang->line('user_not_found');
        }
        generate_log($log);
        return array('status' => $status, 'msg' => $msg, 'userdetail' => $user);
    }

    /**
     * Check Token
     * It is a callback function take user token to check if token exist in system or not
     * @return Bool
     * */
    public function check_token($token = false)
    {
        if ($token) {
            $query = $this->db->where(array('token' => $token, 'logout_time' => null))->limit(1)->get('user_tokens');
            if ($query->num_rows() > 0) {
                // checks for last login and last access time,
				// if it exceeds with default session time then make a user log out forcefully.
				$tokenRow = $query->row();
				// get user details
				$user = $this->db->select('id,last_login,last_access_date')
								->where('id',$tokenRow->users_id)->get($this->tables['users'])->row();
				if(isset($user->last_access_date) && !is_null($user->last_access_date) && $user->last_access_date){
					// default session time
					$session_out_time = get_site_config('APP_SESSION_TIMEOUT'); // get it from DB
					$diff = strtotime("now")-strtotime($user->last_access_date); // difference in seconds
					if($diff > $session_out_time){ // check for the difference
						$this->db->update('user_tokens', array('logout_time'=>$user->last_access_date), array('token' => $tokenRow->token));
						generate_log("User[$user->id] logged out forcefully because of session time out.");
						return FALSE;
					}
				}
				$this->db->update($this->tables['users'], array('last_access_date' => date('Y-m-d H:i:s')), array('id' => $user->id));
                return TRUE;
            } 
        }
        return FALSE;
    }

    /**
     * Delete Token
     * @return Bool
     * */
    public function update_token($token = false)
    {
        $flag = false;
        $data = '';
        if ($token) {
            $id = $this->get_user($token);
            $log = "User [$id] could not be logout.";
            $this->db->update('user_tokens', array('logout_time' => date('Y-m-d H:i:s')), array('token' => $token));
            $return = $this->db->affected_rows() == 1;
            if ($return) {
                $flag = true;
                $log = "User [$id] logged out successfully.";

            } else {
                $flag = false;
                $log = "User [$id] could not be logged out.";
            }
        }
        generate_log($log);
        return $flag;
    }

    /**
     * Check Token
     * It is a callback function take user token to check if token exist in system or not
     * @return Bool
     * */
    public function get_user($token = false)
    {
        if ($token) {
            $query = $this->db->where('token', $token)->limit(1)->get('user_tokens');
            if ($query->num_rows() > 0) {
                $userdata = $query->row();
                return $userdata->users_id;
            } else {
                return false;
            }
        }
        return false;
    }
}
