<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Data_model extends CI_Model {

    var $table;

    function __construct() {
        parent::__construct();
    }

    function set_table($table) {
        $this->table = $table;
    }

    private function set_where($getwhere) {
        if (is_array($getwhere)) {
            foreach ($getwhere as $key => $where) {
                if (is_array($where)) {
                    $this->db->where_in($key, $where);
                } else {
                    $this->db->where($key, $where);
                }
            }
        } else {
            $this->db->where($getwhere);
        }
    }

    function add_data($data, $table = '') {
        $table = $table == '' ? $this->table : $table;
        if ($data) {
            $this->db->insert($table, $data);
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    function edit_data($getwhere, $data, $table = '') {
        $table = $table == '' ? $this->table : $table;
        if (is_array($getwhere)) {
            foreach ($getwhere as $key => $where) {
                if ($key == 'findinset') {
                    $this->db->where("1", "1 AND FIND_IN_SET($where)", FALSE);
                    continue;
                }
                if (is_array($where)) {
                    $this->db->where_in($key, $where);
                } else {
                    $this->db->where($key, $where);
                }
            }
        } else {
            $this->db->where($getwhere);
        }
        $this->db->update($table, $data);
        return $this->db->affected_rows();
    }

    function del_data($ids, $table = '') {
        $table = $table == '' ? $this->table : $table;
        if (is_array($ids)) {
            $this->db->where_in('id', $ids);
        } else {
            $this->db->where('id', $ids);
        }
        return $this->db->delete($table);
    }


    function get_data_num($getwhere = '', $table = '') {
        $table = $table == '' ? $this->table : $table;
        if ($getwhere) {
            $this->set_where($getwhere);
        }
        return $this->db->count_all_results($table);
    }

    function get_row($fields = '*', $getwhere = '', $table = '') {
        $table = $table == '' ? $this->table : $table;
        if ($getwhere) {
            $this->set_where($getwhere);
        }
        $this->db->select($fields);
        $row = $this->db->get($table)->row_array();
        return $row;
    }

    function get_data($fields = '*', $getwhere = "", $order = '', $pagenum = "0", $exnum = "0", $table = '') {
        $table = $table == '' ? $this->table : $table;
        if ($getwhere) {
            $this->set_where($getwhere);
        }
        if ($order) {
            $this->db->order_by($order);
        }
        $this->db->select($fields);
        if ($pagenum > 0) {
            $this->db->limit($pagenum, $exnum);
        }
        $data = $this->db->get($table)->result_array();
        return $data;
    }

    function get_data_join($fields = '*', $getwhere = "", $order = '', $pagenum = "0", $exnum = "0", $table = '', $table2 = '', $condition = '') {
        $table = $table == '' ? $this->table : $table;
        if ($getwhere) {
            $this->set_where($getwhere);
        }
        if ($order) {
            $this->db->order_by($order);
        }
        $this->db->select($fields);
        if ($pagenum > 0) {
            $this->db->limit($pagenum, $exnum);
        }
        $this->db->join($table2, $condition);
        $data = $this->db->get($table)->result_array();
        return $data;
    }


    function del_data($where, $table = '') {
        $table = $table == '' ? $this->table : $table;
        if (is_array($where)) {
            foreach ($where as $key => $val) {
                if (is_array($val)) {
                    $this->db->where_in($key, $val);
                } else {
                    $this->db->where($key, $val);
                }
            }
        } else {
            $this->db->where($where, null, false);
        }
        return $this->db->delete($table);
    }

    function page_list($where = '', $fields = '*', $order = '', $page = 1, $pagesize = 20, $table = '') {
        $table = $table == '' ? $this->table : $table;
        $count = $this->getDataNum($where, $table);
        $totalPages = (int) ceil($count / $pagesize);
        $page = max(intval($page), 1);
        $offset = $pagesize * ($page - 1);
        $array = array();
        if ($count > 0) {
            $this->db->select($fields);
            if ($where !== '') {
                $this->db->where($where);
            }
            if ($order !== '') {
                if (is_array($order)) {
                    foreach ($order as $k => $v) {
                        $this->db->order_by($k, $v);
                    }
                } else {
                    $this->db->order_by($order);
                }
            }

            $this->db->limit($pagesize, $offset);
            $array = $this->db->get($table)->result_array();
        }
        $result = array(
            "totalCount" => $count,
            "pageNo" => $page,
            "totalPages" => $totalPages,
            "pageSize" => $pagesize,
            "datas" => $array
        );
        return $result;
    }

    
}
