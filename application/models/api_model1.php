<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Api_model extends CI_Model {

    public function __construct() {
        
    }

    public function user_signup($params) {
        $result = array();
        $data = array();
        $status = 0;
        $msg = '';
        $request_field_prefix = 'user_';
        
        if((int)$params['signup_mode'] == 2) {
	        $request_fields = array('facebook_id', 'email', 'first_name', 'last_name', 'age', 'gender', 'address', 'photo_url', 'location_address', 'location_latitude', 'location_longitude');

	        $query = $this->db->get_where('wf_users', array('user_facebook_id' => $params['user_facebook_id']));
	        if ($query->num_rows() > 0) {
	        	
	        	$request_fields_update = array('facebook_id', 'email', 'first_name', 'last_name', 'age', 'gender', 'address', 'location_address', 'location_latitude', 'location_longitude');
	        	foreach ($request_fields_update as $request_field) {
	        		$request_field_new = $request_field_prefix . $request_field;
	        		if(isset($params[$request_field_new])) {
	        			$data[$request_field_new] = $params[$request_field_new];
	        		}
	        	}
	        	
	        	$data['user_full_name'] = $data['user_first_name'];
	        	if($data['user_last_name'] != '') $data['user_full_name'] .= (' ' . $data['user_last_name']);
	        	
	        	$update_date = date('Y-m-d h:i:s');
	        	$data['user_last_updated_date'] = $update_date;
	        	
	        	$this->db->update('wf_users', $data, array('user_facebook_id' => $data['user_facebook_id']));
	        	
	        	// Get Current Address from Lat, Long
	        	$data_geo = array();
	        	$location_address = false;
	        	if ($data['user_location_latitude'] != '' && $data['user_location_longitude'] != '' && $data['user_location_latitude'] != 0 && $data['user_location_longitude'] != 0) {
	        		$location_address = $this->get_address($data['user_location_latitude'], $data['user_location_longitude']);
	        	}
	        	if ($location_address) {
	        		$data_geo['user_location_address'] = $location_address;
	        		$this->db->update('wf_users', $data_geo, array('user_facebook_id' => $data['user_facebook_id']));
	        	}
	        	
	            $status = 1;
	            $result = $this->get_user_info(element('user_id', $query->row_array()));
	        } else {
	        	
	        	foreach ($request_fields as $request_field) {
	        		$request_field_new = $request_field_prefix . $request_field;
	        		if(isset($params[$request_field_new])) {
	        			$data[$request_field_new] = $params[$request_field_new];
	        		}
	        	}
	        	
	            //$data['user_salt'] = $this->get_user_salt($data);
	            $data['user_full_name'] = $data['user_first_name'];
	            if($data['user_last_name'] != '') $data['user_full_name'] .= (' ' . $data['user_last_name']);
	            
	            if(isset($data['user_full_name']) && $data['user_full_name'] != '') {
	            	$data['user_name'] = strtolower($this->remove_characters(array('\n', '\r', ' ', ')', '('), $data['user_full_name']));
	            } else {
	            	$data['user_name'] = time();
	            }
	            
	            $data['user_settings_distance'] = config_item('user_settings_distance_default');
	            $data['user_settings_age_min'] = config_item('user_settings_age_min_default');
	            $data['user_settings_age_max'] = config_item('user_settings_age_max_default');
	            
	            $signup_date = date('Y-m-d h:i:s');
	            $data['user_signup_date'] = $signup_date;
	            $data['user_last_updated_date'] = $signup_date;
	            
	            $this->db->insert('wf_users', $data);
	            $insert_id = $this->db->insert_id();
	            
	            if (isset($insert_id) && $insert_id > 0) {
	            	
	            	// Check if settled username already exists in the table and change it.
	            	$query = $this->db->get_where('wf_users', array('user_name' => $data['user_name']));
	            	if($query->num_rows() > 1) {
	            		$data['user_name'] .= $insert_id;
	            		$this->db->update('wf_users', array('user_name' => $data['user_name']), array('user_id' => $insert_id));
	            	}
	            	
	            	/*
	            	//{"0":{"photo_link":"http://cdn.sheknows.com/filter/l/gallery/top_10_hottest_reality_men_nigel_barker.jpg", "photo_display_order":"0"}}
	            	if(isset($params['user_photos']) && $params['user_photos'] != '') {
		            	$photos_arr = json_decode($params['user_photos'], true);
		            	foreach ($photos_arr as $key => $photo) {
		            		$this->db->insert(
		            				'wf_photos', array_merge(array('photo_user_id' => $insert_id), $photo)
		            			);
		            	}
	            	}
	            	
	            	$data_geo = array();
	            	
	            	// Get Lat, Long from Home Town
	            	$lat = '';
	            	$lng = '';
	            	$distance_miles = 0;
	            	$current_address = '';
	            	$is_traveller = 1;
	            	
	            	$address = $data['user_home_town'];
	            	if ($address != '') {
	            	
	            		// create the context
	            		$arContext['http']['timeout'] = config_item('http_timeout_default');
	            		$context = stream_context_create($arContext);
	            		$geocode_stats = @file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&sensor=false");
	            	
	            		$output_deals = json_decode($geocode_stats);
	            	
	            		//                 echo $output_deals;
	            		//                 exit;
	            	
	            		if(isset($output_deals->results[0])) {
	            			$latLng = $output_deals->results[0]->geometry->location;
	            	
	            			$lat = $latLng->lat;
	            			$lng = $latLng->lng;
	            	
	            			$distance_miles = $this->distance($lat, $lng, $data['user_latitude'], $data['user_longitude'], "M");
	            			$is_traveller = (($distance_miles > config_item('user_is_traveller_distance_default')) ? 1 : 0);
	            		}
	            	}
	            	
	            	// Update to Table
	            	$data_geo['user_home_town_latitude'] = $lat;
	            	$data_geo['user_home_town_longitude'] = $lng;
	            	
	            	$data_geo['user_is_traveller'] = $is_traveller;
					*/
	            	
	            	$this->db->insert('wf_user_likes', array('like_user_id' => $insert_id));
	            
	            	// Get Current Address from Lat, Long
	            	$data_geo = array();
	            	$location_address = false;
	            	if ($data['user_location_latitude'] != '' && $data['user_location_longitude'] != '' && $data['user_location_latitude'] != 0 && $data['user_location_longitude'] != 0) {
	            		$location_address = $this->get_address($data['user_location_latitude'], $data['user_location_longitude']);
	            	}
	            	if ($location_address) {
	            		$data_geo['user_location_address'] = $location_address;
	            		$this->db->update('wf_users', $data_geo, array('user_id' => $insert_id));
	            	}
	            	
	            	
	//             	$result['latitude'] = $lat;
	//             	$result['longitude'] = $lng;
	//             	$result['distance'] = $distance_miles;
	//             	$result['address'] = $current_address;
	            	
	            	$result = $this->get_user_info($insert_id);
	            	$status = 1;
	            } else {
	            	$status = 3;
	            	$msg = 'User Sign up Failed';
	            }
	        }
        } else {
        	// Manual Sign Up
        	$request_fields = array('email', 'password', 'first_name', 'last_name', 'age', 'gender', 'address', 'photo_url', 'location_address', 'location_latitude', 'location_longitude');
        	
        	foreach ($request_fields_update as $request_field) {
        		$request_field_new = $request_field_prefix . $request_field;
        		if(isset($params[$request_field_new])) {
        			$data[$request_field_new] = $params[$request_field_new];
        		}
        	}

        	$query_user_email = $this->db->get_where('wf_users', array('user_email' => $data['user_email']));
        	if($query_user_email->num_rows() > 0) {
        		$status = 3;
        		$msg = 'Same email address is already registered. Try with different email.';
        	} else {
        	
        		$data['user_full_name'] = $data['user_first_name'];
        		if($data['user_last_name'] != '') $data['user_full_name'] .= (' ' . $data['user_last_name']);
        		 
        		if(isset($data['user_full_name']) && $data['user_full_name'] != '') {
        			$data['user_name'] = strtolower($this->remove_characters(array('\n', '\r', ' ', ')', '('), $data['user_full_name']));
        		} else {
        			$data['user_name'] = time();
        		}
        	
        		$data['user_password'] = $this->get_user_auth_salt($data['user_password']);
        	
        		$avatar_num = rand(1, config_item('user_photo_avatar_count'));
        		if($avatar_num < 10) {
        			$avatar_num = '0' . $avatar_num;
        		} else {
        			$avatar_num = '' . $avatar_num;
        		}
        		$data['user_photo_url'] = config_item('media_user_photo_avatar_prefix') . $avatar_num;
        		
        		$data['user_settings_distance'] = config_item('user_settings_distance_default');
        		$data['user_settings_age_min'] = config_item('user_settings_age_min_default');
        		$data['user_settings_age_max'] = config_item('user_settings_age_max_default');
        		 
        		$signup_date = date('Y-m-d h:i:s');
        		$data['user_signup_date'] = $signup_date;
        		$data['user_last_updated_date'] = $signup_date;
        		 
        		$this->db->insert('wf_users', $data);
        		$insert_id = $this->db->insert_id();
        	
        		if (isset($insert_id) && $insert_id > 0) {
        			 
        			// Check if settled username already exists in the table and change it.
        			$query = $this->db->get_where('wf_users', array('user_name' => $data['user_name']));
        			if($query->num_rows() > 1) {
        				$data['user_name'] .= $insert_id;
        				$this->db->update('wf_users', array('user_name' => $data['user_name']), array('user_id' => $insert_id));
        			}
        			
        			$this->db->insert('wf_user_likes', array('like_user_id' => $insert_id));
        			 
        			// Get Current Address from Lat, Long
        			$data_geo = array();
        			$location_address = false;
        			if ($data['user_location_latitude'] != '' && $data['user_location_longitude'] != '' && $data['user_location_latitude'] != 0 && $data['user_location_longitude'] != 0) {
        				$location_address = $this->get_address($data['user_location_latitude'], $data['user_location_longitude']);
        			}
        			if ($location_address) {
        				$data_geo['user_location_address'] = $location_address;
        				$this->db->update('wf_users', $data_geo, array('user_id' => $insert_id));
        			}
        			
        			$result = $this->get_user_info($insert_id);
        			$status = 1;
        		} else {
        			$status = 3;
        			$msg = 'User sign up Failed';
        		}
        	}
        	
        }
        $result['status'] = $status;
        $result['msg'] = $msg;
        return $result;
    }

    public function get_user_auth_salt($password) {
    	return sha1(config_item('user_auth_salt') . md5($password));
    }
    
    public function user_login($params) {
    	$result = array();
    	$status = 0;
    	$msg = '';
    	$query = $this->db->select('user_id')->get_where('wf_users', array('user_email' => $params['user_email'], 'user_password' => $this->get_user_auth_salt($params['user_password'])));
    	if($query->num_rows() > 0) {
    		$result = $this->get_user_info(element('user_id', $query->row_array()));
    		$status = 1;
    	} else {
    		$status = 2;
    		$msg = 'Email and password do not match.';
    	}
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function user_retrieve_password($params) {
    	$result = array();
    	$status = 0;
    	$msg = '';
    	$query = $this->db->select('user_id')->get_where('wf_users', array('user_email' => $params['user_email']));
    	if($query->num_rows() > 0) {
    		
    		$digits = config_item('user_new_password_length');
			$new_password = rand(pow(10, $digits-1), pow(10, $digits)-1);
			
			// Send Mail
			$to = $params['user_email'];
		
			$subject = 'Woof Social - Password Reset';
			
			$message = '
			You have recently requested to reset a new password for your account through <b>Woof Social</b> application.<br>
			<br><br>
			Your new password is:<br>
			' . $new_password . '<br>
			<br>
			Thank you for using WOOF Social.
			<br>
			';

			// Always set content-type when sending HTML email
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			
			// More headers
			$headers .= 'From: <no-reply@woofsocial.com>' . "\r\n";
			//$headers .= 'Cc: cc@example.com' . "\r\n";
			
			if(mail($to, $subject, $message, $headers)) {
				$status = 1;
			} else {
				$status = 3;
				$msg = 'An error occurred while resetting a new password.';
			}
			
			if($status == 1) {
				$this->db->update('wf_users', array('user_password' => $this->get_user_auth_salt(md5($new_password))), array('user_email' => $params['user_email']));
			}
    		
    	} else {
    		$status = 2;
    		$msg = 'Your requested email is not registered to our system.';
    	}
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function explore_barks($params) {
        $result = array();
        $status = 0;
        $msg = '';
        
        // Update Latitude, Longitude, Address of Current User
        $request_fields = array('user_location_latitude', 'user_location_longitude');
        $data = $params;

        if ($data['user_location_latitude'] != '' && $data['user_location_longitude'] != '' && $data['user_location_latitude'] != 0 && $data['user_location_longitude'] != 0) {

        	$where = array('user_id' => $params['user_id']);
        	$this->db->update('wf_users', array_merge(elements($request_fields, $data), array('user_last_updated_date' => date('Y-m-d h:i:s'))), $where);

        	$data_geo = array();
        	$location_address = false;
        	$location_address = $this->get_address($data['user_location_latitude'], $data['user_location_longitude']);

        	if ($location_address) {
        		$data_geo['user_location_address'] = $location_address;
        		$this->db->update('wf_users', $data_geo, $where);
        	}
        }
        
        // Get Explore Barks
        $result = $this->get_user_all($params);
        $status = 1;

        $result['status'] = $status;
        $result['msg'] = $msg;
        return $result;
    }

    public function like_barks($params) {
    	$result = array();
    	$status = 0;
    	$msg = '';
    	//$request_fields = array('user_id', 'user_location_latitude', 'user_location_longitude', 'bark_id', 'like');
    	
    	// Update Latitude, Longitude, Address of Current User
        $request_fields = array('user_location_latitude', 'user_location_longitude');
        $data = $params;
        if ($data['user_location_latitude'] != '' && $data['user_location_longitude'] != '' && $data['user_location_latitude'] != 0 && $data['user_location_longitude'] != 0) {
        	$where = array('user_id' => $params['user_id']);
        	$this->db->update('wf_users', array_merge(elements($request_fields, $data), array('user_last_updated_date' => date('Y-m-d h:i:s'))), $where);
        	
        	$data_geo = array();
        	$location_address = false;
        	$location_address = $this->get_address($data['user_location_latitude'], $data['user_location_longitude']);
        	if ($location_address) {
        		$data_geo['user_location_address'] = $location_address;
        	}
        	$this->db->update('wf_users', $data_geo, $where);
        }
    	
    	$query = $this->db->get_where('wf_user_likes', array('like_user_id' => $params['user_id']));
    	$user_like = $query->row_array();
    	$like_bark_ids = explode(',', $user_like['like_bark_ids']);
    	$like_total_ids = explode(',', $user_like['like_total_ids']);
    	
	    // Update User Likes
    	if($params['like'] == '1') {
    		foreach($like_total_ids as $like_total_id) {
    			if($like_total_id == $params['bark_id']) {
    				$status = 3;
    				break;
    			}
    		}
    		if($status != 3) {
    			$new_like_bark_ids_str = '';
    			if(count($like_bark_ids) >= config_item('limit_like_barks_per_user')) {
    				$favorites_count = 0;
    				$i = 0;
    				foreach($like_bark_ids as $like_bark_id_str) {
    					if($i > 1) $new_like_bark_ids_str .= ',';
    					if($i > 0) $new_like_bark_ids_str .= $like_bark_id_str;
    					$i++;
    			
    					$like_bark_id_arr = explode(':', $like_bark_id_str);
    					if($like_bark_id_arr[0] == $params['bark_id']) {
    						$status = 3;
    						break;
    					}
    					if($like_bark_id_arr[1] == 1) $favorites_count++;
    				}
    				if($favorites_count == count($like_bark_ids)) {
    					$status = 2;
    					$msg = 'Operation failed.\nPlease remove at least one of your favorite barks on the liked barks page.';
    				}
    			} else {
    				if($user_like['like_bark_ids'] != '') {
    					$i = 0;
    					foreach($like_bark_ids as $like_bark_id_str) {
    						$like_bark_id_arr = explode(':', $like_bark_id_str);
    						if($like_bark_id_arr[0] == $params['bark_id']) {
    							$status = 3;
    							break;
    						}
    						if($i > 0) $new_like_bark_ids_str .= ',';
    						$new_like_bark_ids_str .= $like_bark_id_str;
    						$i++;
    					}
    				}
    				 
    			}
    		}
    		if($status < 2) {
    			if($user_like['like_bark_ids'] != '') $new_like_bark_ids_str .= ',';
    			$new_like_bark_ids_str .= $params['bark_id'] . ':0';
    			// Update Like ids
    			$this->db->update('wf_user_likes', array('like_bark_ids' => $new_like_bark_ids_str), array('like_user_id' => $params['user_id']));
    		}
    	}
    	
    	// Update User Likes - Total Ids
    	if($status < 2) {
	    	$new_like_total_ids = $user_like['like_total_ids'];
	    	if($new_like_total_ids != '') $new_like_total_ids .= ',';
	    	$new_like_total_ids .= $params['bark_id'];
    		$this->db->update('wf_user_likes', array('like_total_ids' => $new_like_total_ids), array('like_user_id' => $params['user_id']));
    		$status = 1;
    		
    	}
    	
    	// Update BroadCasts
    	if($params['like'] == '1' && $status == 1) {
    		$query = $this->db->get_where('wf_broadcasts', array('bc_bark_id' => $params['bark_id']));
    		$bc_locations = element('bc_locations', $query->row_array());
    		if($bc_locations != '') $bc_locations .= ',';
    		$bc_locations .= $params['user_location_latitude'] . ':' . $params['user_location_longitude'] . ';1';
    		$this->db->update('wf_broadcasts', array('bc_locations' => $bc_locations), array('bc_bark_id' => $params['bark_id']));
    	}
    	
    	if($status == 3) $msg = 'You already liked this bark';
    	$result = $this->get_user_detail($params['user_id']);
    	
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function my_barks($params) {
        $result = array();
        $data = array();
        $status = 0;
        $msg = '';
        
        switch ($params['tag']) {
            case 'add':
            	$query = $this->db->get_where('wf_barks', array('bark_user_id' => $params['user_id']));
            	if($query->num_rows() > config_item('limit_post_barks_per_user')) {
            		$msg = 'You exceeded the limit of barks you can post.';
            		$status = 2;
            	} else if(!(isset($params['bark_photo']) && $params['bark_photo'] != '')) {
            		$msg = 'Please upload your bark photo';
            		$status = 0;
            	} else {
            		$image_path = config_item('path_media_barks');
            		$created_time = time();
            		$image_name = $params['user_id'] . '_' . $created_time . '.jpg';
            		$image_url = $image_path . $image_name;
            		
            		$binary = base64_decode($params['bark_photo']);
            		header('Content-Type: bitmap; charset=utf-8');
            		$file = fopen($image_url, 'w');
            		if($file) {
            			fwrite($file, $binary);
            		}
            		fclose($file);
            		
            		if (!$file) {
            			$status = 2;
            			$msg = 'File Upload failed';
            		} else {
            			$current_user = element('current_user', $this->get_user_info($params['user_id']));
            			$data = array(
            					'bark_user_id' => $params['user_id'],
            					'bark_user_photo_url' => $current_user['user_photo_url'],
            					'bark_user_name' => $current_user['user_name'],
            					'bark_type' => 1,
            					'bark_url' => config_item('media_bark_self_domain_prefix').$image_name,
            					'bark_location_address' => $current_user['user_location_address'],
            					'bark_location_latitude' => $current_user['user_location_latitude'],
            					'bark_location_longitude' => $current_user['user_location_longitude'],
            					'bark_created_date' => date('Y-m-d h:i:s')
            			);
            			$this->db->insert('wf_barks', $data);
            			$insert_id = $this->db->insert_id();
            			if (isset($insert_id) && $insert_id > 0) {
            				$this->db->insert('wf_broadcasts', array(
            						'bc_bark_id' => $insert_id,
            						'bc_locations' => $data['bark_location_latitude'] . ':' . $data['bark_location_longitude'] . ';1'
            				));
            			}
            		}
    				if($status != 2) {
    					$result = $this->get_user_detail($params['user_id']);
    					$status = 1;
    				}
            	}
                break;
            case 'delete':
            	$query = $this->db->get_where('wf_barks', array('bark_id' => $params['bark_id']));
            	$bark = $query->row_array();
            	$bark_media = config_item('path_media_barks') . $this->get_bark_prefix_removed_media_name($bark['bark_url']);
            	if(file_exists($bark_media)) unlink($bark_media);
            	
                $this->db->delete('wf_barks', array('bark_user_id' => $params['user_id'], 'bark_id' => $params['bark_id']));
                $this->db->delete('wf_broadcasts', array('bc_bark_id' => $params['bark_id']));
                
                $result = $this->get_user_detail($params['user_id']);
                $status = 1;
                break;
            default:
                $status = 0;
                $msg = config_item('msg_fill_form');
                break;
        }

        
        $result['status'] = $status;
        $result['msg'] = $msg;
        return $result;
        
    }

    public function remove_liked_barks($params) {
    	$result = array();
    	$status = 0;
    	$msg = '';
    	
    	$where = array('like_user_id' => $params['user_id']);
    	$query = $this->db->get_where('wf_user_likes', $where);
    	$like_bark_ids_arr = explode(',', element('like_bark_ids', $query->row_array()));
    	
    	$new_like_bark_ids_str = '';
    	$i = 0;
    	foreach($like_bark_ids_arr as $like_bark_id_str) {
    		$like_bark_id_arr = explode(':', $like_bark_id_str);
    		if($like_bark_id_arr[0] != $params['bark_id']) {
	    		if($i > 0) $new_like_bark_ids_str .= ',';
	    		$new_like_bark_ids_str .= $like_bark_id_str;
	    		$i++;
    		} else {
    			if($like_bark_id_arr[1] == '1') {
    				$msg = 'You cannot unlike the favorite bark';
    				$status = 2;
    			}
    		}
    	}
    	
    	if($status != 2) {
    		$this->db->update('wf_user_likes', array('like_bark_ids' => $new_like_bark_ids_str), $where);
    		
    		// Update Broadcasts
    		$current_user = element('current_user', $this->get_user_info($params['user_id']));
    		$where_bc = array('bc_bark_id' => $params['bark_id']);
    		$query = $this->db->get_where('wf_broadcasts', $where_bc);
    		$bc_locations_arr = explode(',', element('bc_locations', $query->row_array()));
    		$new_bc_locations_str = '';
    		$i = 0;
    		foreach($bc_locations_arr as $bc_location_str) {
    			$bc_location_arr = explode(';', $bc_location_str);
    			$bc_location_point_arr = explode(':', $bc_location_arr[0]);
    			if($bc_location_point_arr[0] == $current_user['user_location_latitude'] && $bc_location_point_arr[1] == $current_user['user_location_longitude']) {
    				$new_bc_location_str = $bc_location_arr[0] . ';0';
    			} else {
    				$new_bc_location_str = $bc_location_str;
    			}
    			if($i > 0) $new_bc_locations_str .= ',';
    			$new_bc_locations_str .= $new_bc_location_str;
    			$i++;
    		}
    		$this->db->update('wf_broadcasts', array('bc_locations' => $new_bc_locations_str), $where_bc);
    		
    		$result = $this->get_user_detail($params['user_id']);
    		$status = 1;
    	}
    	
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function favorite_barks($params) {
    	$result = array();
    	$status = 0;
    	$msg = '';
    	 
    	$where = array('like_user_id' => $params['user_id']);
    	$query = $this->db->get_where('wf_user_likes', $where);
    	$like_bark_ids_arr = explode(',', element('like_bark_ids', $query->row_array()));
    	
    	$new_like_bark_ids_str = '';
    	$i = 0;
    	foreach($like_bark_ids_arr as $like_bark_id_str) {
    		$like_bark_id_arr = explode(':', $like_bark_id_str);
    		if($like_bark_id_arr[0] == $params['bark_id']) {
    			if($like_bark_id_arr[1] == '1') {
    				$new_like_bark_id_str = $like_bark_id_arr[0] . ':0';
    			} else {
    				$count_favorite = 0;
    				foreach($like_bark_ids_arr as $like_bark_id_str1) {
			    		$like_bark_id_arr1 = explode(':', $like_bark_id_str1);
			    		if($like_bark_id_arr1[1] == '1') {
			    			$count_favorite++;
			    		}
    				}
    				if($count_favorite >= config_item('limit_favorite_barks_per_user')) {
    					$status = 2;
    					break;	
    				} else {
    					$new_like_bark_id_str = $like_bark_id_arr[0] . ':1';
    				}
    			}
    		} else {
    			$new_like_bark_id_str = $like_bark_id_str;
    		}
    		if($i > 0) $new_like_bark_ids_str .= ',';
    		$new_like_bark_ids_str .= $new_like_bark_id_str;
    		$i++;
    	}
    	if($status == 2) {
    		$msg = 'You will not be able to explore more barks with this many favorites';
    	} else { 
    		$this->db->update('wf_user_likes', array('like_bark_ids' => $new_like_bark_ids_str), $where);
    		$result = $this->get_user_detail($params['user_id']);
    		$status = 1;
    	}

    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function profile_update($params) {
    	$result = array();
    	$status = 0;
    	$msg = '';
    
    	$where = array('user_id' => $params['user_id']);
    	
    	$query = $this->db->get_where('wf_users', array('user_name' => $params['user_name'], 'user_id !=' => $params['user_id']));
    	if($query->num_rows() > 0) {
    		$status = 3;
    		$msg = 'Same username already exists. Choose different username';
    	} else {
	    	if(isset($params['user_photo']) && $params['user_photo'] != '') {
	    		$image_path = config_item('path_media_users');
	    		$created_time = time();
	    		$image_name = $params['user_id'] . '_' . $created_time . '.jpg';
	    		$image_url = $image_path . $image_name;
	    	
	    		$binary = base64_decode($params['user_photo']);
	    		header('Content-Type: bitmap; charset=utf-8');
	    		$file = fopen($image_url, 'w');
	    		if($file) {
	    			fwrite($file, $binary);
	    		}
	    		fclose($file);
	    	
	    		if (!$file) {
	    			$status = 2;
	    			$msg = 'File Upload failed';
	    		} else {
	    			$query = $this->db->get_where('wf_users', $where);
	    			$user_media = config_item('path_media_users') . $this->get_user_prefix_removed_media_name(element('user_photo_url', $query->row_array()));
	    			if(file_exists($user_media)) unlink($user_media);
	    			 
	    			$this->db->update('wf_users', array('user_name' => $params['user_name'], 'user_photo_url' => config_item('media_user_self_domain_prefix').$image_name), $where);
	    			$this->db->update('wf_barks', array('bark_user_name' => $params['user_name'], 'bark_user_photo_url' => config_item('media_user_self_domain_prefix').$image_name), array('bark_user_id' => $params['user_id']));
	    		}
	    	} else {
	    		$this->db->update('wf_users', array('user_name' => $params['user_name']), $where);
	    		$this->db->update('wf_barks', array('bark_user_name' => $params['user_name']), array('bark_user_id' => $params['user_id']));
	    	}				
	    	if($status < 2) {
	    		$result = $this->get_user_info($params['user_id']);
	    		$status = 1;
	    	}
    	}
    	
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function discovery_settings($params) {
    	$result = array();
    	$status = 0;
    	$msg = '';
    
    	$where = array('user_id' => $params['user_id']);
    	$request_fields = array('user_settings_distance', 'user_settings_age_min', 'user_settings_age_max', 'user_settings_gender');
    	$this->db->update('wf_users', elements($request_fields, $params), $where);
    
    	//$result = $this->get_user_detail($params['user_id']);
    	$result = $this->get_user_all($params);
    	
    	$status = 1;
    
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function location_settings($params) {
    	$result = array();
    	$status = 0;
    	$msg = '';
    
    	switch ($params['tag']) {
    		case 'add':
    			if((isset($params['location_address']) && $params['location_address'] != '')) {
	    			$this->db->insert('wf_user_settings_locations', array('location_user_id' => $params['user_id'], 'location_address' => $params['location_address']));
	    			$insert_id = $this->db->insert_id();
	            	if (isset($insert_id) && $insert_id > 0) {
	            		if(isset($params['location_latitude']) && isset($params['location_longitude'])) {
	            			if($params['location_latitude'] != '' && $params['location_longitude'] != '' && $params['location_latitude'] != 0 && $params['location_longitude'] != 0) {
	            				$this->db->update('wf_user_settings_locations', elements(array('location_latitude', 'location_longitude'), $params), array('location_id' => $insert_id));
	            				$status = 1;
	            			}
	            		}
	            		
	            		if($status != 1) {
	            			// Get Lat, Long from Location Address
			            	$lat = '';
			            	$lng = '';
			            	$address = $params['location_address'];
			            	if ($address != '') {
			            		$arContext['http']['timeout'] = config_item('http_timeout_default');
			            		$context = stream_context_create($arContext);
			            		$geocode_stats = @file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&sensor=false");
			            		$output_deals = json_decode($geocode_stats);
			            		//                 echo $output_deals;
			            		//                 exit;
			            		if(isset($output_deals->results[0])) {
			            			$latLng = $output_deals->results[0]->geometry->location;
			            			$lat = $latLng->lat;
			            			$lng = $latLng->lng;
			            			$this->db->update('wf_user_settings_locations', array('location_latitude' => $lat, 'location_longitude' => $lng), array('location_id' => $insert_id));
			            			$status = 1;
			            		}
			            	}
	            		}
	            	} else {
	            		$status = 0;
	            	}
    			} else {
    				$status = 0;
    			}
    			break;
    		case 'delete':
    			$this->db->delete('wf_user_settings_locations', array('location_user_id' => $params['user_id'], 'location_id' => $params['location_id']));
    			
    			$status = 1;
    			break;
    	}
    	
    	if($status == 1) {
    		//$result = $this->get_user_detail($params['user_id']);
    		$result = $this->get_user_all($params);
    	}
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function user_settings_location_id($params) {
    	$result = array();
    	$status = 0;
    	$msg = '';
    
    	$this->db->update('wf_users', array('user_settings_location_id' => $params['user_settings_location_id']), array('user_id' => $params['user_id']));

    	$status = 1;
    	$result = $this->get_user_all($params);
    	
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    // Utility Functions
    public function get_user_info($param_id) {
    	// Get Current User Detail
        $result = array();
        $user_id = $param_id;

        $current_user = array();
        $query = $this->db->select(array('user_id', 'user_name', 'user_age', 'user_gender', 'user_address', 'user_photo_url', 'user_location_address', 'user_location_latitude', 'user_location_longitude', 'user_settings_distance', 'user_settings_age_min', 'user_settings_age_max', 'user_settings_gender', 'user_settings_location_id'))
        	->get_where('wf_users', array('user_id' => $user_id, 'user_closed' => 0));
        $current_user = $query->row_array();
      
//         $current_location = array(
//         		'location_id' => 0,
//         		'location_address' => $current_user['user_location_address'],
//         		'location_latitude' => $current_user['user_location_latitude'],
//         		'location_longitude' => $current_user['user_location_longitude']
//         	);
        $query = $this->db->select(array('location_id', 'location_address', 'location_latitude', 'location_longitude'))
        	->get_where('wf_user_settings_locations', array('location_user_id' => $user_id));
        $current_user['user_settings_locations'] =  $query->result_array();
//         $current_user['user_settings_locations'][] = $current_location;
        
        $result['current_user'] = $current_user;
        return $result;
    }

    public function get_user_detail($param_id) {
    	// Get My Barks and Liked Barks
    	$result = array();
    	$user_id = $param_id;
    	
    	// Get Current User Detail
    	$result = $this->get_user_info($user_id);
    	
    	// My Barks
    	$query = $this->db->select(array('bark_id', 'bc_locations', 'bark_user_id', 'bark_user_name', 'bark_user_photo_url', 'bark_type', 'bark_url', 'bark_location_address', 'bark_location_latitude', 'bark_location_longitude'))
    		->order_by('bark_id', 'desc')
    		->where(array('wf_barks.bark_user_id' => $user_id))
    		->join('wf_broadcasts', 'wf_broadcasts.bc_bark_id = wf_barks.bark_id')
    		->get('wf_barks');
    	$result['my_barks'] = $query->result_array();
    		
    		
    	// Liked Barks
    	$liked_barks = array();
    	$query = $this->db->get_where('wf_user_likes', array('like_user_id' => $user_id));
    	if($query->num_rows() > 0) {
    		
    		$like_bark_ids_arr = array();
    		$like_bark_ids_arr_fav = array();
    		$like_bark_ids_arr_order = array();
    		if(element('like_bark_ids', $query->row_array()) != '') {
	    		$like_bark_ids = explode(',', element('like_bark_ids', $query->row_array()));
	    		
	    		$i = 0;
	    		foreach($like_bark_ids as $item) {
	    			$item_exp = explode(':', $item);
	    			$like_bark_ids_arr[] = $item_exp[0];
	    			$like_bark_ids_arr_fav[$item_exp[0]] = $item_exp[1];
	    			$like_bark_ids_arr_order[$item_exp[0]] = $i;
	    			$i++;
	    		}
	    		$like_bark_ids_str = '(' . implode(',', $like_bark_ids_arr) . ')';
    		}
    		if(count($like_bark_ids_arr) > 0) {

	    		$this->db->select(array('bark_id', 'bc_locations', 'bark_user_id', 'bark_user_name', 'bark_user_photo_url', 'bark_type', 'bark_url', 'bark_location_address', 'bark_location_latitude', 'bark_location_longitude'));
	    		$this->db->where('wf_barks.bark_id IN ' . $like_bark_ids_str);
	    		$this->db->join('wf_broadcasts', 'wf_broadcasts.bc_bark_id = wf_barks.bark_id');
	    		$this->db->order_by('bark_id', 'desc');
		    	$query_barks = $this->db->get('wf_barks');
		    	

		    	
	    		$liked_barks_temp = $query_barks->result_array();
	    		foreach($liked_barks_temp as $row) {
		    		$distance_miles = $this->distance($result['current_user']['user_location_latitude'], $result['current_user']['user_location_longitude'], $row['bark_location_latitude'], $row['bark_location_longitude'], "M");
	    			$liked_barks[] = array_merge($row, array('is_favorite' => $like_bark_ids_arr_fav[$row['bark_id']], 'order_id' => $like_bark_ids_arr_order[$row['bark_id']], 'bark_distance' => $distance_miles));
	    		}
	    		function sort_by_order_id($a, $b) {
	    			return $b['order_id'] - $a['order_id'];
	    		}
	    		usort($liked_barks, 'sort_by_order_id');
    		}
    	}
    	$result['liked_barks'] = $liked_barks;
    	
    	return $result;
    }
    
    
    public function get_user_all($params1) {
    	
    	$result = array();
    	$params = $params1;
    	$result = $this->get_user_detail($params['user_id']);

    	//// Get Explore Barks
    	if(!isset($params['barks_count'])) {
    		$params['barks_count'] = config_item('explore_barks_count_default');
    	}
    	$barks = array();
    	
    	// Get Liked barks - ids
    	$query_likes = $this->db->select('like_total_ids')->get_where('wf_user_likes', array('like_user_id' => $params['user_id']));
    	$like_total_ids = element('like_total_ids', $query_likes->row_array());

    	// Get Limit
    	$barks_count = config_item('explore_barks_count_default');
    	if(isset($params['barks_count']) && $params['barks_count'] > 0) {
    		$barks_count = $params['barks_count'];
    	}

    	// Total Query
    	$current_user = $result['current_user'];
    	$limit_offset = 0;
    	$limit_per_query = config_item('explore_barks_limit_per_query');
    	$count = 0;
    	$total_count = $this->db->count_all('wf_barks');
    	
    	while($count < $barks_count) {
    		$query = '';
    		if(strlen($like_total_ids) > 0) {
	    		$query = $this->db->select(array('bark_id', 'user_age', 'user_gender', 'bc_locations', 'bark_user_id', 'bark_user_name', 'bark_user_photo_url', 'bark_type', 'bark_url', 'bark_location_address', 'bark_location_latitude', 'bark_location_longitude'))
	    		->where(array('bark_user_id !=' => $params['user_id'])) // Constraint : exclude my barks
	    		->where('bark_id NOT IN (' . $like_total_ids . ')')
	    		->join('wf_broadcasts', 'wf_broadcasts.bc_bark_id = wf_barks.bark_id')
	    		->join('wf_users', 'wf_users.user_id = wf_barks.bark_user_id')
	    		->order_by('bark_created_date', 'desc')
	    		->limit($limit_per_query, $limit_offset)
	    		->get('wf_barks');
    		} else {
    			$query = $this->db->select(array('bark_id', 'user_age', 'user_gender', 'bc_locations', 'bark_user_id', 'bark_user_name', 'bark_user_photo_url', 'bark_type', 'bark_url', 'bark_location_address', 'bark_location_latitude', 'bark_location_longitude'))
    			->where(array('bark_user_id !=' => $params['user_id'])) // Constraint : exclude my barks
    			->join('wf_broadcasts', 'wf_broadcasts.bc_bark_id = wf_barks.bark_id')
    			->join('wf_users', 'wf_users.user_id = wf_barks.bark_user_id')
    			->order_by('bark_created_date', 'desc')
    			->limit($limit_per_query, $limit_offset)
    			->get('wf_barks');
    		}
    		$limit_offset += $limit_per_query;


    		foreach($query->result_array() as $bark) {
    			
    			$match = true;
    			
    			// Check Settings Age
    			if($bark['user_age'] > 0) {
    				if($bark['user_age'] < $current_user['user_settings_age_min'] || (($current_user['user_settings_age_max'] < config_item('user_settings_age_max_default')) && ($bark['user_age'] > $current_user['user_settings_age_max']))) {
    					$match = false;
    				}
    			}
    			
    			// Check Settings Gender
    			if($bark['user_gender'] > 0) {
    				if($current_user['user_settings_gender'] > 0 && $current_user['user_settings_gender'] != $bark['user_gender']) {
    					$match = false;
    				}
    			}
    			
    			// Check Settings Locations with Distance
    			$distance_match = false;
    			foreach(explode(',', $bark['bc_locations']) as $bc_location_str) {
    				$bc_location_arr = explode(';', $bc_location_str);
    				if($bc_location_arr[1] == '1') {
    					$bc_location_point_arr = explode(':', $bc_location_arr[0]);
    					
    					if($current_user['user_settings_location_id'] == 0) {
	    					// Check with Current Position
	    					$distance_miles = $this->distance($bc_location_point_arr[0], $bc_location_point_arr[1], $current_user['user_location_latitude'], $current_user['user_location_longitude'], "M");
	    					if($distance_miles <= $current_user['user_settings_distance']) {
	    						$distance_match = true;
	    						break;
	    					}
	    					if($distance_match) break;
    					} else {
	    					// Check with Location Settings
	    					foreach($current_user['user_settings_locations'] as $user_settings_location) {
	    						if($user_settings_location['location_id'] == $current_user['user_settings_distance']) {
		    						$distance_miles = $this->distance($bc_location_point_arr[0], $bc_location_point_arr[1], $user_settings_location['location_latitude'], $user_settings_location['location_longitude'], "M");
		    						if($distance_miles <= $current_user['user_settings_distance']) {
		    							$distance_match = true;
		    							break;
		    						}
		    						break;
	    						}
	    					}
    					}
    				}
    				if($distance_match) break;
    			}
    			
    			if($match && $distance_match) {
    				$barks[$count] = array_merge(elements(array('bark_id', 'bark_user_id', 'bark_user_name', 'bark_user_photo_url', 'bark_type', 'bark_url'), $bark), array('bark_distance' => $distance_miles));
    				$count++;
    				if($count >= $barks_count) {
    					$limit_offset = $total_count;
    					break;
    				}
    			}
    		}
    		
    		if($limit_offset >= $total_count) {
    			break;
    		}
    	}
    	
    	$result['barks'] = $barks;
    	return $result;
    }
    
    public function get_bark_prefix_removed_media_name($image_name) {
    	$prefix_length = strlen(config_item('media_bark_self_domain_prefix'));
    	if(substr($image_name, 0, $prefix_length) == config_item('media_bark_self_domain_prefix')) {
    		return substr($image_name, $prefix_length);
    	} else {
			return $image_name;
		}
    }
    
    public function get_user_prefix_removed_media_name($image_name) {
    	$prefix_length = strlen(config_item('media_user_self_domain_prefix'));
    	if(substr($image_name, 0, $prefix_length) == config_item('media_user_self_domain_prefix')) {
    		return substr($image_name, $prefix_length);
    	} else {
    		return $image_name;
    	}
    }
    
    public function get_user_salt($user) {
        return md5(config_item('user_salt') . md5($user['user_email']));
    }

    public function get_user_authentication($user, $password) {
        return !!($user['user_password'] == md5($password . $user['user_salt']));
    }

    /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
    /* ::                                                                         : */
    /* ::  This routine calculates the distance between two points (given the     : */
    /* ::  latitude/longitude of those points). It is being used to calculate     : */
    /* ::  the distance between two locations using GeoDataSource(TM) Products    : */
    /* ::                                                                         : */
    /* ::  Definitions:                                                           : */
    /* ::    South latitudes are negative, east longitudes are positive           : */
    /* ::                                                                         : */
    /* ::  Passed to function:                                                    : */
    /* ::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  : */
    /* ::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  : */
    /* ::    unit = the unit you desire for results                               : */
    /* ::           where: 'M' is statute miles (default)                         : */
    /* ::                  'K' is kilometers                                      : */
    /* ::                  'N' is nautical miles                                  : */
    /* ::  Worldwide cities and other features databases with latitude longitude  : */
    /* ::  are available at http://www.geodatasource.com                          : */
    /* ::                                                                         : */
    /* ::  For enquiries, please contact sales@geodatasource.com                  : */
    /* ::                                                                         : */
    /* ::  Official Web site: http://www.geodatasource.com                        : */
    /* ::                                                                         : */
    /* ::         GeoDataSource.com (C) All Rights Reserved 2015		   		     : */
    /* ::                                                                         : */
    /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

    public function distance($lat1, $lon1, $lat2, $lon2, $unit = "M") {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    /*
      echo distance(32.9697, -96.80322, 29.46786, -98.53506, "M") . " Miles<br>";
      echo distance(32.9697, -96.80322, 29.46786, -98.53506, "K") . " Kilometers<br>";
      echo distance(32.9697, -96.80322, 29.46786, -98.53506, "N") . " Nautical Miles<br>";
     */

    public function get_address($lat, $lng, $timeoutParam = 0) {
    	
    	$timeout = (($timeoutParam == 0) ? config_item('http_timeout_default') : $timeoutParam);
    	$arContext['http']['timeout'] = $timeout;
    	$context = stream_context_create($arContext);
        $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($lat) . ',' . trim($lng) . '&sensor=false';
        $json = @file_get_contents($url);
        $data = json_decode($json);
        $status = $data->status;
        if ($status == "OK")
            return $data->results[0]->formatted_address;
        else
            return false;
    }

    
    public function remove_characters($needles, $str) {
    	$s = $str;
    	foreach($needles as $needle) {
    		$s = str_replace($needle, '', $s);
    	}
    	//$s = $this->clean($s);
    	return $s;
    }
    public function clean($text) {
    	$text = trim( preg_replace( '/\s+/', ' ', $text ) );
    	$text = preg_replace( "/\r|\n/", "", $text);
    	return $text;
    }
    
}
