<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Libraries
 * @author      EllisLab Dev Team
 * @link        https://codeigniter.com/user_guide/general/controllers.html
 */
abstract class MY_Controller extends CI_Controller {

    var $error_code = array();
    
    function __construct(){
        parent::__construct();
        $this->load->config("return_code");
        $this->return_code = $this->config->item("return_code");
    }
    
    function return_right($data){
        $result = array(
            "status" => 0,
            "data" => $data,
            "msg" => $this->return_code[0]
        );
        $this->return_json($result);
    }
    
    function return_error($error_code){
        $result = array(
            "status" => $error_code,
            "data" => array(),
            "msg" => $this->return_code[$error_code]
        );
        $this->return_json($result);
    }
    
    private function return_json($result){
        $output = json_encode($result);
        ob_clean();
        header("Content-type: application/json");
        echo $output;
        exit();
    }
    
    function check_sign($parm){
        $this->load->config("app_config");
        $parm["app_secret"] = $this->config->item("app_secret");
        $sign = $parm["sign"];
        unset($parm["sign"]);
        ksort($parm);
        $str = "";
        foreach($parm as $k => $v){
            if($v){
                $str .= $k.$v;
            }
        }    
        if(md5($str) != $sign){
            $this->return_error(1);
        }
        return true;
    }
    
    function check_session($session_key){
        $this->load->model("Member_model", "M_mdl");
        $field = "expire_time";
        $where = array(
            "session_key" => $session_key
        );
        $result = $this->M_mdl->get_row($field, $where);
        if(!$result){
            $this->return_error(8);
        }
        if($result['expire_time'] < time()){
            $this->return_error(9)
        }
        return true;
    }
}
