<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model {

	public function __construct() {

	}
	function login($email, $password) {
		$this->db->select('id, email, user_role, full_name, photo_link, genre_ids, bio, facebook_link, twitter_link, country, zip, city, website');
		$query = $this->db->get_where('users', array('email' => $email, 'password' => sha1(md5($password)), 'is_verified' => 1));
		if($query -> num_rows() == 1) {
			return $query->row_array();
		} else {
			return false;
		}
	}
	function reset_password($email) {
		$query = $this->db->get_where('users', array('email' => $email, 'is_verified' => 1));
		if($query -> num_rows() == 1) {
			$row = $query->row_array();
			$password =  substr(md5($email . time()), 0, 8);
			$this->db->update('users', array('password' => sha1(md5($password))), array('id' => $row['id']));
			
			$this->load->library('email');
			$site_info = $this->be_model->get_site_info();
			$message = 'Dear ' . $row['full_name']. '.
Your password was reset upon your request.
Here are your login credentials:
		
Email Address : ' . $email . '
Password : ' . $password . '
		
This password is temporarily generated by our services, you can login to our site and change your account information including password.';
			
			$this->email->from($site_info['from_email'], $site_info['site_name']);
			$this->email->to($email);
			$this->email->subject('Thank you for registering to ' . $site_info['site_name']);
			$this->email->message($message);
			$this->email->reply_to($site_info['owner_email'], $site_info['site_name']);
			
			return $row['id'];
		} else {
			return false;
		}
	}
	function get_verify_token($user_id) {
		$query = $this->db->get_where('users', array('id' => $user_id));
		$row = $query->row_array();
		return md5(sha1($row['email']) . sha1($row['password']) . sha1($row['full_name']));
	}
	public function register($requests_param, $user_role = null)	{
		$requests = $requests_param;
		$requests['password'] = sha1(md5($requests['password']));
		$requests['date_created'] = date('Y-m-d h:i:s');
		$requests['user_role'] = 1;
		/* $query = $this->db->get_where('genres', array('name' => $requests['genre']));
		if($query->num_rows() > 0) {
			$requests['genre_id'] = element('id', $query->row_array());
		} else {
			$this->db->insert('genres', array('name' => $requests['genre']));
			$requests['genre_id'] = $this->db->insert_id();
		} */
		if(config_item('domain_sub_directory') != '') {
			$requests['photo_link'] = substr($requests['photo_link'], strlen(config_item('domain_sub_directory')) + 1);
		}
		$request_fields = array('full_name', 'email', 'password', 'bio', 'genre_ids', 'photo_link', 'facebook_link', 'twitter_link', 'date_created', 'user_role', 'country', 'zip', 'city', 'website');
		$this->db->insert('users', elements($request_fields, $requests));
		$id = $this->db->insert_id();
		if(isset($id) && $id > 0) {
			$this->load->library('email');
			$site_info = $this->be_model->get_site_info();
			
			$message = 'You just registered to our site - ' . $site_info['owner_site'] . '
				
Here are the information of your registered account:
			
Name : ' . $requests['full_name'] . '
Email : ' . $requests['email'] . '
Bio : ' . $requests['bio'] . '
Genre : ' . $requests['genre'] . '

Please verify your account by clicking this link below:
' . base_url() . 'user/verify/' . $this->get_verify_token($id) . '
			';
				
			$this->email->from($site_info['from_email'], $site_info['site_name']);
			$this->email->to($requests['email']);
			$this->email->subject('Thank you for registering to ' . $site_info['site_name']);
			$this->email->message($message);
			$this->email->reply_to($site_info['owner_email'], $site_info['site_name']);

			return $id;
			/*echo 'success';
			if (!$this->email->send()) {
				echo $this->email->print_debugger();
			} else {
				echo 'success';
			}
			return $id;*/
		} else {
			return FALSE;
		}
	}
}