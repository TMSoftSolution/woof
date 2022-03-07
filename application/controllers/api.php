<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Api extends CI_Controller {

    var $data;
    public function __construct() {
    	
    	parent::__construct();

    	$valid = !(
    			empty($_SERVER['CONTENT_TYPE']) ||
    			$_SERVER['CONTENT_TYPE'] != 'application/json; charset=UTF-8' ||
    			!(isset($_SERVER['HTTP_API_KEY']) && $_SERVER['HTTP_API_KEY'] == config_item('api_key')));

    	if($valid) {
    		$this->data = json_decode(file_get_contents('php://input'), TRUE);
    		$valid = !!count($this->data);
    	}
    	if(!$valid) {
    		echo "Invalid Request";
    		exit;
    	}
    }

    public function user_signup() {
    	$request_fields = array('signup_mode');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if (!isset($this->data[$request_field])) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->user_signup($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function user_login() {
    	$request_fields = array('user_email', 'user_password');
    	$request_form_success = true;
    	foreach($request_fields as $request_field) {
    		if(!(isset($this->data[$request_field]) && $this->data[$request_field] != '')) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if(!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->user_login($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function user_retrieve_password() {
    	$request_fields = array('user_email');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if(!(isset($this->data[$request_field]) && $this->data[$request_field] != '')) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->user_retrieve_password($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function user_logout() {
    	$request_fields = array('user_id', 'device_type');
    	$request_form_success = true;
    	foreach($request_fields as $request_field) {
    		if(!(isset($this->data[$request_field]) && $this->data[$request_field] != '')) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if(!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->user_logout($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function explore_barks() {
        $request_fields = array('id', 'location_latitude', 'location_longitude');
        $request_field_prefix = 'user_';
        $request_form_success = true;
        foreach ($request_fields as $request_field) {
            if (!isset($this->data[$request_field_prefix . $request_field])) {
                $request_form_success = false;
                break;
            }
        }
        if (!$request_form_success) {
            $response['status'] = 0;
            $response['msg'] = config_item('msg_fill_form');
        } else {
            $response = $this->api_model->explore_barks($this->data);
        }
        echo json_encode($response);
    }

    public function like_barks() {
        $request_fields = array('user_id', 'user_location_latitude', 'user_location_longitude', 'bark_id', 'like');
        $request_form_success = true;
        foreach ($request_fields as $request_field) {
            if (!isset($this->data[$request_field])) {
                $request_form_success = false;
                break;
            }
        }
        if (!$request_form_success) {
            $response['status'] = 0;
            $response['msg'] = config_item('msg_fill_form');
        } else {
            $response = $this->api_model->like_barks($this->data);
        }
        echo json_encode($response);
    }

    public function my_barks() {
        $request_fields = array('user_id', 'tag', 'bark_type');
        $request_form_success = true;
        foreach ($request_fields as $request_field) {
            if (!isset($this->data[$request_field])) {
                $request_form_success = false;
                break;
            }
        }
        if (!$request_form_success) {
            $response['status'] = 0;
            $response['msg'] = config_item('msg_fill_form');
        } else {
            $response = $this->api_model->my_barks($this->data);
        }
        echo json_encode($response);
    }

    public function remove_liked_barks() {
    	$request_fields = array('user_id', 'bark_id');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if (!isset($this->data[$request_field])) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->remove_liked_barks($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function bark_stat() {
    	$request_fields = array('user_id', 'bark_id', 'velocity_history_period');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if (!isset($this->data[$request_field])) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->bark_stat($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function like_user() {
    	$request_fields = array('user_id', 'ref_user_id');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if (!isset($this->data[$request_field])) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->like_user($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function favorite_barks() {
    	$request_fields = array('user_id', 'bark_id');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if (!isset($this->data[$request_field])) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->favorite_barks($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function profile_update() {
    	$request_fields = array('user_id', 'user_name');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if (!isset($this->data[$request_field])) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->profile_update($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function discovery_settings() {
    	$request_fields = array('user_id', 'user_settings_distance', 'user_settings_age_min', 'user_settings_age_max', 'user_settings_gender');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if (!isset($this->data[$request_field])) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->discovery_settings($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function location_settings() {
    	$request_fields = array('user_id', 'tag');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if (!isset($this->data[$request_field])) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->location_settings($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function user_settings_location_id() {
    	$request_fields = array('user_id', 'user_settings_location_id');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if (!isset($this->data[$request_field])) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->user_settings_location_id($this->data);
    	}
    	echo json_encode($response);
    }
}
