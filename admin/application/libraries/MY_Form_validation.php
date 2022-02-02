<?php  /*

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
 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {
    protected $CI;

    public function __construct($rules = array()) {
	    parent::__construct($rules);
        // reference to the CodeIgniter super object
        $this->CI =& get_instance();
    }
    
    /**
	 * Convert PHP tags to entities
	 *
	 * @param	string
	 * @return	string
	 */
	public function is_valid_password($str)
	{
	    $this->set_message('is_valid_password', 'The password must contain minimum 8 characters, at least 1 uppercase letter, 1 lowercase letter, 1 number and 1 special character.');
		return ( ! preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,}/', $str)) ? FALSE : TRUE;
	}

	// valid_url Function OverRided
	function valid_url($str){
        $pattern = "|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i";
        if (!preg_match($pattern, $str)){
            $this->set_message('valid_url', 'The %s field must contain a valid URL.');
            return FALSE;
        }

        return TRUE;
    }       

}
