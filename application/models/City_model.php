<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class City_model extends Data_model {

    function __construct() {
        parent::__construct();
        $this->set_table('city');
    }
    
}
