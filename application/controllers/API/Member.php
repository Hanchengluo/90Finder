<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends My_Controller {
    
    var $session_expire_time = 0;
    
    function __construct(){
        parent::__construct();
        $this->load->model('Member_model', 'M_mdl');
        $this->load->config('app_config');
        $this->session_expire_time = $this->config->item('session_expire_time');
    }

    public function index($method = ''){
        switch($method){
            case 'login':
                $this->login();
                break;
            case 'register':
                $this->register();
                break;
            case 'logout':
                $this->logout();
                break;
            default:
                $this->return_error(2);
        }
    }
    
    private function login(){
        $parm = $this->input->post();
        $this->check_sign($parm);
        if(!isset($parm['mobile']) || !isset($parm['password'])){
            $this->return_error(3);
        }
        $field = 'ID, nick_name, password';
        $where = array(
            'mobile' => $parm['mobile'],
            'enabled' => 1
        );
        $result = $this->M_mdl->get_row($field, $where);
        if(!$result){
            $this->return_error(4);
        }
        if($result['password'] != $parm['password']){
            $this->return_error(5);
        }
        $now = time();
        $expire_time = $now + $this->session_expire_time;
        $session_key = md5($now);
        $data_update = array(
            'session_key' => $session_key,
            'expire_time' => $expire_time,
            'login_time' => $now
        );
        $where_update = array(
            'ID' => $result['ID']
        );
        $this->M_mdl->edit_data($where_update, $data_update);
        $data_output = array(
            'uid' => $result['ID'],
            'session_key' => $session_key,
            'nick_name' => $result['nick_name']
        );
        $this->return_right($data_output);
    }
    
    private function register(){
        $parm = $this->input->post();
        $this->check_sign($parm);
        if(!isset($parm['mobile']) || !isset($parm['password'])){
            $this->return_error(3);
        }
        $field = 'ID';
        $where = array(
            'mobile' => $parm['mobile']
        );
        $result = $this->M_mdl->get_row($field, $where);
        if($result){
            $this->return_error(6);
        }
        $now = time();
        $expire_time = $now + $this->session_expire_time;
        $session_key = md5($now);
        $data_insert = array(
            'mobile' => $parm['mobile'],
            'password' => $parm['password'],
            'session_key' => $session_key,
            'expire_time' => $expire_time,
            'add_time' => $now,
            'login_time' => $now
        );
        $id = $this->M_mdl->add_data($data_insert);
        if(!$id){
            $this->return_error(7);
        }
        $data_output = array(
            'uid' => $id,
            'session_key' => $session_key
        );
        $this->return_right($data_output);
    }
    
    private function logout(){
        $parm = $this->input->post();
        $this->check_sign($parm);
        if(!$parm['uid'] || !$parm['session_key']){
            $this->return_error(3);
        }
        $where = array(
            'id' => $parm['uid'],
            'session_key' => $parm['session_key']
        );
        $data = array(
            'session_key' => '',
            'expire_time' => 0
        );
        $result = $this->M_mdl->edit_data($where, $data);
        if(!$result_update){
            $this->return_error(10);
        }
        $this->return_right(array());
    }
    
}
