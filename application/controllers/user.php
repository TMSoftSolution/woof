<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('User_model');
	}
	public function index() {
		
	}
	function login() {
		$requests = $this->input->post();
		$page_param = 'user/login';
		$response = array();
		$data_param = array('page_style' => 3);
		if(isset($requests['tag'])) {
			if($requests['tag'] == 'login') {
				$request_fields = array('email', 'password');
				$input_total_data = array(
					array('name' => 'email', 'label' => 'Email', 'maxlength' => 100, 'required' => true, 'type' => 'email', 'valid_email' => true),
					array('name' => 'password', 'label' => 'Password', 'maxlength' => 60, 'required' => true, 'type' => 'password'),
				);
				// validate form input
				foreach($input_total_data as $item) {
					$this->form_validation->set_rules($item['name'], $item['label'], $this->be_model->get_form_validation_rule($item));
				}
				if($this->form_validation->run() == true) {
					if(!$this->login_check($requests['email'], $requests['password'])) {
						$response['result'] = 0;
						$response['report']['status'] = 2;
						$response['report']['msg'] = 'Invalid Email or Password';
					} else {
						redirect('/dashboard/manage');
					}
				} else {
					$response['result'] = 0;
					$response['report']['status'] = 0;
					$response['report']['msg'] = validation_errors();
				}
				//$data_param['requests'] = elements($request_fields, $requests);
			} else {
				$response['result'] = 0;
				$response['report']['status'] = 0;
				$response['report']['msg'] = 'Invalid Request';
			}
		}
		$this->be_page->generate(false, $page_param, $data_param, $response);
		return;
	}
	function login_check($email, $password) {
		$result = $this->User_model->login($email, $password);
		if($result == false) {
			return false;
		} else {
			$result['user_role_name'] = element('name', element($result['user_role'], config_item('user_role')));
			$this->session->set_userdata('logged_in', $result);
			return true;
		}
	}
	function reset_password() {
		$requests = $this->input->post();
		$page_param = 'user/reset_password';
		$response = array();
		$data_param = array('page_style' => 3);
		if(isset($requests['tag'])) {
			if($requests['tag'] == 'reset_password') {
				$request_fields = array('email');
				$input_total_data = array(
						array('name' => 'email', 'label' => 'Email', 'maxlength' => 100, 'required' => true, 'type' => 'email', 'valid_email' => true)
				);
				// validate form input
				foreach($input_total_data as $item) {
					$this->form_validation->set_rules($item['name'], $item['label'], $this->be_model->get_form_validation_rule($item));
				}
				if($this->form_validation->run() == true) {
					$result = $this->User_model->reset_password($requests['email']);
					if($result == false) {
						$response['result'] = 0;
						$response['report']['status'] = 2;
						$response['report']['msg'] = 'Your email is not registered.';
					} else {
						$data_param['reset'] = 1;
					}
				} else {
					$response['result'] = 0;
					$response['report']['status'] = 0;
					$response['report']['msg'] = validation_errors();
				}
				//$data_param['requests'] = elements($request_fields, $requests);
			} else {
				$response['result'] = 0;
				$response['report']['status'] = 0;
				$response['report']['msg'] = 'Invalid Request';
			}
		}
		$this->be_page->generate(false, $page_param, $data_param, $response);
		return;
	}
 	public function logout() {
 		$this->session->unset_userdata('logged_in');
  		redirect('/', 'refresh');
 	}
	public function signup_success() {
		$this->be_page->generate(false, 'user/signup_success', array('page_style' => 3));
	}
	public function signup() {
		$requests = $this->input->post();
		$page_param = '';
		$response = array();
		$data_param = array('page_style' => 1);
		if(!isset($requests['tag'])) {
			redirect('/');
		} else if($requests['tag'] == 'signup1') {
			$request_fields = array('full_name', 'email', 'password', 'password_confirm');
			$input_total_data = array(
					array('name' => 'full_name', 'label' => 'First Name', 'maxlength' => 100, 'required' => true),
					array('name' => 'email', 'label' => 'Email', 'maxlength' => 100, 'required' => true, 'type' => 'email', 'valid_email' => true, 'is_unique' => 'users.email'),
					array('name' => 'password', 'label' => 'Password', 'minlength' => 5,  'maxlength' => 60, 'type' => 'password', 'required' => true, 'password_confirm' => true),
					array('name' => 'password_confirm', 'label' => 'Password Confirm','minlength' => 5,  'maxlength' => 60, 'type' => 'password', 'form_only' => true, 'required' => true)
				);
			// validate form input
			foreach($input_total_data as $item) {
				$this->form_validation->set_rules($item['name'], $item['label'], $this->be_model->get_form_validation_rule($item));
			}
			$this->form_validation->set_message('is_unique', 'This Email address is already associated with an Artist account.');
			if($this->form_validation->run() == true) {
				$page_param = 'user/signup1';
				$data_param['page_style'] = 3;
				$cookie = array(
						'name' => 'be_signup_middle_data',
						'value' => json_encode(elements(array('full_name', 'email'), $requests)),
						'expire' => 3600*24*100
				);
				$this->input->set_cookie($cookie);
			} else {
				$response['result'] = 0;
				$response['report']['status'] = 0;
				$response['report']['msg'] = validation_errors();
			}
			$data_param['requests'] = elements($request_fields, $requests);
		} else if($requests['tag'] == 'signup2') {
			$request_fields = array('full_name', 'email', 'password', 'password_confirm');
			$input_total_data = array(
					array('name' => 'country', 'label' => 'Country', 'maxlength' => 100, 'required' => true),
					array('name' => 'zip', 'label' => 'Zip', 'minlength' => 3, 'maxlength' => 6, 'required' => true, 'numeric' => true),
					array('name' => 'city', 'label' => 'City', 'maxlength' => 100, 'required' => true),
					array('name' => 'genre_ids', 'label' => 'Genres', 'required' => true),
					array('name' => 'bio', 'label' => 'Bio', 'maxlength' => 200, 'required' => true)
				);
			// validate form input
			foreach($input_total_data as $item) {
				$this->form_validation->set_rules($item['name'], $item['label'], $this->be_model->get_form_validation_rule($item));
			}
			if($this->form_validation->run() == true) {
				$page_param = 'user/signup2';
				$data_param['page_style'] = 3;
				if(isset($requests['genre_ids']) && count($requests['genre_ids']) > 0) {
					$requests['genre_ids'] = $this->be_model->convert_array_to_string($requests['genre_ids']);
				}
				$request_fields = array_merge($request_fields, array('bio', 'genre_ids', 'country', 'zip', 'city'));
			} else {
				$response['result'] = 0;
				$response['report']['status'] = 0;
				$response['report']['msg'] = validation_errors();
				$page_param = 'user/signup1';
				$data_param['page_style'] = 3;
			}
			$data_param['requests'] = elements($request_fields, $requests);
		} else if($requests['tag'] == 'signup3') {
			$request_fields = array('full_name', 'email', 'password', 'password_confirm', 'bio', 'genre_ids', 'country', 'zip', 'city');
			$input_total_data = array(
					array('name' => 'photo_link', 'label' => 'Photo', 'required' => true)
				);
			// validate form input
			foreach($input_total_data as $item) {
				$this->form_validation->set_rules($item['name'], $item['label'], $this->be_model->get_form_validation_rule($item));
			}
			$this->form_validation->set_message('photo_link', 'You should upload your profile photo.');
			if($this->form_validation->run() == true) {
				$page_param = 'user/signup3';
				$data_param['page_style'] = 3;
				$request_fields = array_merge($request_fields, array('photo_link'));
			} else {
				$response['result'] = 0;
				$response['report']['status'] = 0;
				$response['report']['msg'] = validation_errors();
				$page_param = 'user/signup2';
				$data_param['page_style'] = 3;
			}
			$data_param['requests'] = elements($request_fields, $requests);
		} else if($requests['tag'] == 'signup4') {
			$request_fields = array('full_name', 'email', 'password', 'password_confirm', 'bio', 'genre_ids', 'photo_link');
			$input_total_data = array(
					array('name' => 'website', 'label' => 'Website Link'),
					array('name' => 'facebook_link', 'label' => 'Facebook Link'),
					array('name' => 'twitter_link', 'label' => 'Twitter Link')
				);
			// validate form input
			foreach($input_total_data as $item) {
				$this->form_validation->set_rules($item['name'], $item['label'], $this->be_model->get_form_validation_rule($item));
			}
			if($this->form_validation->run() == true) {
				$result = $this->User_model->register($requests);
				if ($result == false) {
					$response['result'] = 0;
					$response['report']['status'] = 2;
					$response['report']['msg'] = 'Signup action failed.';
					$page_param = 'user/signup3';
					$data_param['page_style'] = 3;
				} else {
					delete_cookie('be_signup_middle_data');
					redirect('/user/signup_success');
				}
			} else {
				$response['result'] = 0;
				$response['report']['status'] = 0;
				$response['report']['msg'] = validation_errors();
				$page_param = 'user/signup3';
				$data_param['page_style'] = 3;
			}
			if($result !== false) $data_param['requests'] = elements($request_fields, $requests);
		} else {
			$response['result'] = 0;
			$response['report']['status'] = 0;
			$response['report']['msg'] = 'Invalid Request';
		}
		$this->be_page->generate(false, $page_param, $data_param, $response);
		return;
	}
	function verify($param = null) {
		if(isset($param)) {
			$query = $this->db->select('id', 'full_name')->get_where('users', array('is_verified' => 0));
			foreach($query->result_array() as $row) {
				if($param == $this->User_model->get_verify_token($row['id'])) {
					$this->db->update('users', array('is_verified' => 1), array('id' => $row['id']));
					$page_param = 'user/verify';
					$data_param['user_full_name'] = $row['full_name'];
					$data_param['page_style'] = 3;
					$this->be_page->generate(false, $page_param, $data_param);
					return;
				}
			}
			
		}
		echo 'invalid request';
	}
}
?>