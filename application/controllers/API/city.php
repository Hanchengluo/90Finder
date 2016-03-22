<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class City extends My_Controller {
        
    function __construct(){
        parent::__construct();
		$this->load->model("City_model", "C_mdl");
    }

    public function index($method = ''){
        switch($method){
			case "get_province":
				$this->get_province();
				break;
			case "get_city":
				$this->get_city();
				break;
			case "get_recommend":
				$this->get_recommend();
				break;
        }
    }
	
	private function get_province(){
		$parm = $this->input->post();
        $this->check_sign($parm);
		$where = "rank = 1 or rank = 2";
		$field = "id, name, pinyin, code";
		$result = $this->C_mdl->get_data($field, $where);
		if(!$result){
			$this->return_error(11);
		}
		$output = array();
		foreach($result as $v){
			$output[] = array(
				"id" => $v["id"],
				"name" => $v["name"],
				"pinyin" => $v["pinyin"],
				"code" => $v["code"]
			);
		}
		$this->return_right($output);
	}
	
	private function get_city(){
		$parm = $this->input->post();
        $this->check_sign($parm);
		if(!isset($parm["city_id"])){
			$this->return_error(3);
		}
		$where = array(
			"parentid" => $parm["city_id"]
		);
		$field = "id, name, pinyin, code";
		$result = $this->C_mdl->get_data($field, $where);
		if(!$result){
			$this->return_error(11);
		}
		$output = array();
		foreach($result as $v){
			$output[] = array(
				"id" => $v["id"],
				"name" => $v["name"],
				"pinyin" => $v["pinyin"],
				"code" => $v["code"]
			);
		}
		$this->return_right($output);
	}
	
	private function get_recommend(){
		//返回推荐的常用城市
	}
    
}
