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
	        $request_fields = array('facebook_id', 'email', 'first_name', 'last_name', 'age', 'gender', 'address', 'apns_id', 'gcm_id', 'photo_url', 'location_address', 'location_latitude', 'location_longitude');

	        $query = $this->db->get_where('wf_users', array('user_facebook_id' => $params['user_facebook_id']));
	        if ($query->num_rows() > 0) {
	        	
	        	$request_fields_update = array('facebook_id', 'email', 'first_name', 'last_name', 'age', 'gender', 'address', 'apns_id', 'gcm_id', 'location_address', 'location_latitude', 'location_longitude');
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
	        	if(isset($data['user_location_latitude']) && isset($data['user_location_longitude'])) {
		        	if ($data['user_location_latitude'] != '' && $data['user_location_longitude'] != '' && $data['user_location_latitude'] != 0 && $data['user_location_longitude'] != 0) {
		        		$location_address = $this->get_address($data['user_location_latitude'], $data['user_location_longitude']);
		        	}
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
	            	$this->db->insert('wf_user_favorites', array('fav_user_id' => $insert_id));
	            	
	            	// Get Current Address from Lat, Long
	            	$data_geo = array();
	            	$location_address = false;
	            	if(isset($data['user_location_latitude']) && isset($data['user_location_longitude'])) {
		            	if ($data['user_location_latitude'] != '' && $data['user_location_longitude'] != '' && $data['user_location_latitude'] != 0 && $data['user_location_longitude'] != 0) {
		            		$location_address = $this->get_address($data['user_location_latitude'], $data['user_location_longitude']);
		            	}
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
        } else if((int)$params['signup_mode'] == 1) {
        	// Manual Sign Up
        	$request_fields = array('email', 'password', 'first_name', 'last_name', 'age', 'gender', 'address', 'apns_id', 'gcm_id', 'photo_url', 'location_address', 'location_latitude', 'location_longitude');
        	
        	foreach ($request_fields as $request_field) {
        		$request_field_new = $request_field_prefix . $request_field;
        		if(isset($params[$request_field_new])) {
        			$data[$request_field_new] = $params[$request_field_new];
        		}
        	}

        	$query_user_email = $this->db->get_where('wf_users', array('user_email' => $data['user_email']));
        	if($query_user_email->num_rows() > 0) {
        		$status = 3;
        		$msg = 'This email is already registered';
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
        			$this->db->insert('wf_user_favorites', array('fav_user_id' => $insert_id));
        			
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
        			$status = 2;
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
    		if(isset($params['user_gcm_id']) && $params['user_gcm_id'] != '') {
    			$this->db->update('wf_users', array('user_gcm_id' => $params['user_gcm_id']), array('user_email' => $params['user_email']));
    		}
    		if(isset($params['user_apns_id']) && $params['user_apns_id'] != '') {
    			$this->db->update('wf_users', array('user_apns_id' => $params['user_apns_id']), array('user_email' => $params['user_email']));
    		}
    		$status = 1;
    	} else {
    		$status = 2;
    		$msg = 'Email and password do not match';
    	}
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function user_logout($params) {
    	$result = array();
    	$status = 0;
    	$msg = '';
    	
    	if((int)$params['device_type'] == 1) {
    		$this->db->update('wf_users', array('user_apns_id' => ''), array('user_id' => $params['user_id']));
    	} else if((int)$params['device_type'] == 2) {
    		$this->db->update('wf_users', array('user_gcm_id' => ''), array('user_id' => $params['user_id']));
    	}
    	
    	$status = 1;
    	
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function user_retrieve_password($params) {
    	$result = array();
    	$status = 0;
    	$msg = '';
    	$query = $this->db->get_where('wf_users', array('user_email' => $params['user_email']));
    	if($query->num_rows() > 0) {
    		
    		$current_user = $query->row_array();
    		
    		$digits = config_item('user_new_password_length');
			$new_password = rand(pow(10, $digits-1), pow(10, $digits)-1);
			
			// Send Mail
			$to = $params['user_email'];
		
			$subject = 'Woof Social - Password Reset';
			
			$message = '
			Thanks for trying Woof ' . (($current_user['user_first_name'] != '') ? $current_user['user_first_name'] : $current_user['user_name']) . '!<br>
			<br>
			You can now login using the credentials below:<br>
			<br>
			<b>Email Address:</b><br>
			' . $current_user['user_email'] . '<br>
			<br>
			<b>Password:</b><br>
			' . $new_password . '<br>
			<br><br>
			Happy barking!<br>
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
        		$this->db->update('wf_users', $data_geo, $where);
        	}
        	
        }
    	
    	$query = $this->db->get_where('wf_user_likes', array('like_user_id' => $params['user_id']));
    	$user_like = $query->row_array();
    	$like_bark_ids = explode(',', $user_like['like_bark_ids']);
    	$like_total_ids = explode(',', $user_like['like_total_ids']);
    	
	    // Update User Likes
    	if((int)$params['like'] == 1) {
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
    					$msg = 'Make room for more barks! Unfavorite a bark from your Liked Barks feed to discover more content';
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

    	
    	if(((int)$params['like'] != 1) || ((int)$params['like'] == 1 && $status < 2)) {
    		
    		// Update User Likes - Total Ids
    		
	    	$new_like_total_ids = $user_like['like_total_ids'];
	    	if($new_like_total_ids != '') $new_like_total_ids .= ',';
	    	$new_like_total_ids .= $params['bark_id'];
    		$this->db->update('wf_user_likes', array('like_total_ids' => $new_like_total_ids), array('like_user_id' => $params['user_id']));

    	
			// Update Stats

    		$where_st = array('st_bark_id' => $params['bark_id']);
    		$query_current_st = $this->db->get_where('wf_stats', $where_st);
    		$current_st = $query_current_st->row_array();
    		
    		$current_st['st_total_count']++;
    		
    		if(((int)$params['like'] == 1 && $status < 2)) {
    			
    			// Update BroadCasts Start
    			$query = $this->db->get_where('wf_broadcasts', array('bc_bark_id' => $params['bark_id']));
    			$bc_locations_old = element('bc_locations', $query->row_array());
    			if($bc_locations_old != '') $bc_locations_old .= ',';
    			$bc_locations_new = $bc_locations_old . $params['user_location_latitude'] . ':' . $params['user_location_longitude'] . ';1';
    			$this->db->update('wf_broadcasts', array('bc_locations' => $bc_locations_new), array('bc_bark_id' => $params['bark_id']));
    			// Update BroadCasts End
    			
    			
	    		$current_st['st_liked_count']++;
	    		
	    		$query_liked_bark = $this->db->get_where('wf_barks', array('bark_id' => $params['bark_id']));
	    		$liked_bark = $query_liked_bark->row_array();
	    		
	    		$distance_miles = $this->distance($liked_bark['bark_location_latitude'], $liked_bark['bark_location_longitude'], $params['user_location_latitude'], $params['user_location_longitude'], "M");
	    		if($distance_miles > (double)$current_st['st_furthest_distance']) {
	    			$current_st['st_furthest_distance'] = $distance_miles;
	    		}
	    		
	    		// Update Reach
	    		$old_points = array(); // array of array('lat' => 23.5234432, 'lng' => 45.2342356)
	    		foreach(explode(',', $bc_locations_old) as $bc_locations_old_item) {
	    			if($bc_locations_old_item != '') {
	    				$bc_locations_old_item_arr = explode(';', $bc_locations_old_item);
	    				$bc_locations_old_point_arr = explode(':', $bc_locations_old_item_arr[0]);
	    				$old_points[] = array('lat' => $bc_locations_old_point_arr[0], 'lng' => $bc_locations_old_point_arr[1]); 
	    			}
	    		}
	    		$new_point = array('lat' => $params['user_location_latitude'], 'lng' => $params['user_location_longitude']);
	    		$current_st['st_reach'] += $this->get_new_reach_added($old_points, $new_point);
	    		
	    		// Update Velocity History Start
	    		$hour = floor((int)date('H') / config_item('velocity_history_duration_unit')) * config_item('velocity_history_duration_unit');
	    		$seek_date = date('Y-m-d ') . ($hour < 10 ? '0' : '') . $hour . ':00:00';
	    		
	    		$vc_content_new = ',';
	    		$query_vc = $this->db->get_where('wf_velocity_history', array('vc_time' => $seek_date));
	    		if($query_vc->num_rows() > 0) {
	    			$vc = $query_vc->row_array();
	    			$vc_content = $vc['vc_content'];
	    			$exist = false;
	    			foreach(explode(',', $vc_content) as $vc_content_item_str) {
	    				if($vc_content_item_str != '') {
	    					$vc_content_item_arr = explode(':', $vc_content_item_str);
	    					$vc_bark_id = $vc_content_item_arr[0];
	    					if($vc_bark_id == $params['bark_id']) {
	    						$exist = true;
	    						$vc_content_new .= $params['bark_id'] . ':' . round($current_st['st_reach']) . ',';
	    					} else {
	    						$vc_content_new .= $vc_content_item_str . ',';
	    					}
	    				}
	    			}
	    			if(!$exist) {
	    				$vc_content_new .= $params['bark_id'] . ':' . round($current_st['st_reach']) . ',';
	    			}
	    			$this->db->update('wf_velocity_history', array('vc_content' => $vc_content_new), array('vc_id' => $vc['vc_id']));
	    		} else {
	    			$vc_content_new .= $params['bark_id'] . ':' . round($current_st['st_reach']) . ',';
	    			$this->db->insert('wf_velocity_history', array('vc_time' => $seek_date, 'vc_content' => $vc_content_new));
	    		}
	    		// Update Velocity History End
    		}
    		
    		$current_st['st_updated_date'] = date('Y-m-d H:i:s');
    		$this->db->update('wf_stats', $current_st, $where_st);
    		
    		$status = 1;
    	}
    	
    	
    	
    	if($status == 3) $msg = 'You already liked this bark';
    	if($status == 1) {
    		$result = $this->get_user_detail($params['user_id']);
    		
    		// Push Notification
    		$users_notify = array();
    		
    		$where_st = array('st_bark_id' => $params['bark_id']);
    		$query_stat = $this->db->get_where('wf_stats', $where_st);
    		$stat = $query_stat->row_array();
    		
    		foreach(config_item('pn_liked_counts') as $pn_liked_count) {
    			if((int)$stat['st_liked_count'] >= $pn_liked_count && (int)$stat['st_pn_liked_count'] != $pn_liked_count) {
    				$stat['st_pn_liked_count'] = $pn_liked_count;
    				$users_notify[] = array('user_id' => $stat['st_bark_user_id'], 'msg' => $this->get_pn_message_filtered(config_item('pn_message_liked_counts'), $pn_liked_count));
    				break;
    			}
    		}
    		foreach(config_item('pn_furthest_distances') as $pn_furthest_distance) {
    			if((int)$stat['st_furthest_distance'] >= $pn_furthest_distance && (int)$stat['st_pn_furthest_distance'] != $pn_furthest_distance) {
    				$stat['st_pn_furthest_distance'] = $pn_furthest_distance;
    				$users_notify[] = array('user_id' => $stat['st_bark_user_id'], 'msg' => $this->get_pn_message_filtered(config_item('pn_message_furthest_distances'), $pn_furthest_distance));
    				break;
    			}
    		}
    		foreach(config_item('pn_reaches') as $pn_reach) {
    			if((int)$stat['st_reach'] >= $pn_reach && (int)$stat['st_pn_reach'] != $pn_reach) {
    				$stat['st_pn_reach'] = $pn_reach;
    				$users_notify[] = array('user_id' => $stat['st_bark_user_id'], 'msg' => $this->get_pn_message_filtered(config_item('pn_message_reaches'), $pn_reach));
    				break;
    			}
    		}
    		$diff_hours = ceil((time() - strtotime($stat['st_bark_created_date'])) / 3600);
    		foreach(config_item('pn_velocities') as $pn_velocity) {
    			if(round($stat['st_furthest_distance'] / $diff_hours) >= $pn_velocity && (int)$stat['st_pn_velocity'] != $pn_velocity) {
    				$stat['st_pn_velocity'] = $pn_velocity;
    				$users_notify[] = array('user_id' => $stat['st_bark_user_id'], 'msg' => $this->get_pn_message_filtered(config_item('pn_message_velocities'), $pn_velocity));
    				break;
    			}
    		}
    		
    		if(count($users_notify) > 0) {
    			$this->db->update('wf_stats', $stat, $where_st);
    			
	    		$user_ids = array();
	    		foreach($users_notify as $user) {
	    			$user_ids[] = $user['user_id'];
	    		}
	    		$query_users_notify = $this->db->where_in('user_id', $user_ids)->get('wf_users');
	    		$users_pn = array();
	    		foreach($query_users_notify->result_array() as $user) {
	    			$users_pn[$user['user_id']] = array('user_gcm_id' => $user['user_gcm_id'], 'user_apns_id' => $user['user_apns_id']);
	    		}
	    		
	    		$msgs_gcm_pn = array();
	    		$msgs_apns_pn = array();
	    		foreach($users_notify as $user) {
	    			if(isset($users_pn[$user['user_id']]['user_gcm_id']) && $users_pn[$user['user_id']]['user_gcm_id'] != '') {
	    				$msgs_gcm_pn[] = array(
	    						'gcm_id' => $users_pn[$user['user_id']]['user_gcm_id'],
	    						'msg' => array('msg_type' => 1, 'msg_content' => $user['msg'])
	    					);
	    			}
	    			if(isset($users_pn[$user['user_id']]['user_apns_id']) && $users_pn[$user['user_id']]['user_apns_id'] != '') {
	    				$msgs_apns_pn[] = array(
	    						'apns_id' => $users_pn[$user['user_id']]['user_apns_id'],
	    						'msg' => array('msg_type' => 1, 'msg_content' => $user['msg'])
	    					);
	    			}
	    		}
	    		/* if(count($msgs_gcm_pn) > 0) {
	    			$this->send_GCM($msgs_gcm_pn);
	    		} */
	    		if(count($msgs_apns_pn) > 0) {
	    			$this->send_APNS($msgs_apns_pn);
	    		}
    		}
    	}
    	
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function get_pn_message_filtered($msg, $placeholder) {
    	return str_replace(config_item('pn_message_placeholder'), number_format((int)$placeholder, 0, '.', ','), $msg);
    }
    
    public function get_new_reach_added($old_points, $new_point) {
    	$reach_add = pow(config_item('user_settings_distance_default'), 2) * M_PI;
    	
    	for($i = 0; $i < count($old_points); $i++) {
    		$old_point = $old_points[$i];
    		$distance = $this->distance($old_point['lat'], $old_point['lng'], $new_point['lat'], $new_point['lng'], 'M');
    		if($distance < config_item('user_settings_distance_default') * 2) {
    			$diff = config_item('user_settings_distance_default') * 2 - $distance;
    			$reach_add -= pow($diff / 2, 2);
    			if($i > 0) {
	    			for($j = 0; $j < $i; $j++) {
	    				$distance_prev = $this->distance($old_points[$j]['lat'], $old_points[$j]['lng'], $old_point['lat'], $old_point['lng'], 'M');
			    		if($distance_prev < config_item('user_settings_distance_default') * 2) {
			    			$diff_prev = config_item('user_settings_distance_default') * 2 - $distance_prev;
			    			$reach_add += pow(($diff_prev / 4), 2);
			    		}
	    			}
    			}
    		}
    	}
    	
    	return $reach_add;
    }
    
    
    public function bark_stat($params) {
    	
    	$result1 = array();
    	$result = array();
    	$status = 0;
    	$msg = '';
    	
    	$query = $this->db->get_where('wf_stats', array('st_bark_id' => $params['bark_id']));
    	if($query->num_rows() > 0) {
    		$stat = $query->row_array();
    		
    		$diff_hours = ceil((time() - strtotime($stat['st_bark_created_date'])) / 3600);
    		$result['velocity'] = round($stat['st_furthest_distance'] / $diff_hours);
    		if((int)$stat['st_total_count'] == 0) {
    			$result['likes'] = 0;
    		} else {
    			$result['likes'] = round(100 * $stat['st_liked_count'] / $stat['st_total_count']);
    		}
    		$result['liked_count'] = $stat['st_liked_count'];
    		$result['reach'] = round($stat['st_reach']);
    		$result['furthest_distance'] = round($stat['st_furthest_distance']);
    		
    		
    		$history = array();
    		$history_count = (int)$params['velocity_history_period'];
    		if($history_count <= 7) {
    			$history_count *= 4;
    		}
    		
    		$query_history = $this->db->like('vc_content', ',' . $params['bark_id'] . ':')
    			->get('wf_velocity_history');
    		if($query_history->num_rows() > 0) {
    			$vc_history = $query_history->result_array();
    			$vc_records = array();
    			foreach($vc_history as $vc) {
    				$vc_velocity = 0;
    				foreach(explode(',', $vc['vc_content']) as $vc_content_item_str) {
    					if($vc_content_item_str != '') {
    						$vc_content_item_arr = explode(':', $vc_content_item_str);
    						if($vc_content_item_arr[0] == $params['bark_id']) {
    							$vc_velocity = $vc_content_item_arr[1];
    							break;
    						}
    					}
    				}
    				$vc_records[] = array('timestamp' => strtotime($vc['vc_time']), 'reach' => $vc_velocity);
    			}
    			$days_ago = strtotime(date('Y-m-d H:i:s', strtotime('-' . $params['velocity_history_period'] . ' days', time())));
    			$history_unit = round((time() - $days_ago) / $history_count);
    			for($i = 0; $i < $history_count; $i++) {
    				$history_time = $days_ago + ($history_unit * $i);
    				$start_reach = 0;
    				$end_reach = 0;
    				for($j = 0; $j < count($vc_records); $j++) {
    					if($vc_records[$j]['timestamp'] < $history_time) {
    						$start_reach = $vc_records[$j]['reach'];
    					} else {
    						$end_reach = $vc_records[$j]['reach'];
    						break;
    					}
    				}
    				if($end_reach < $start_reach) {
    					$end_reach = $start_reach;
    				}
    				$history[$i] = ($end_reach - $start_reach) / ($history_unit / 3600);
    			}
    			if($history[count($history) - 1] == 0 && $history[count($history) - 2] > 0) {
    				$history[count($history) - 1] = $history[count($history) - 2];
    			}
    		} else {
	    		for($i = 0; $i < $history_count; $i++) {
	    			$history[] = 0;
	    		}
    		}
    		$result['velocity_history'] = $history;
    		
    		$result1['bark_stat'] = $result;
    		$status = 1;
    	} else {
    		$status = 2;
    		$msg = 'Status is not available for this bark';
    	} 
    	
    	
    	$result1['status'] = $status;
    	$result1['msg'] = $msg;
    	return $result1;
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
            	} else {
            		if((int)$params['bark_type'] == 1) {
	            		if(!(isset($params['bark_photo']) && $params['bark_photo'] != '')) {
		            		$msg = 'Please upload your bark photo';
		            		$status = 0;
		            	} else {
		            		$image_path = config_item('path_media_bark_photos');
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
		            			$bark_created_date = date('Y-m-d h:i:s');
		            			$data = array(
		            					'bark_user_id' => $params['user_id'],
		            					'bark_user_photo_url' => $current_user['user_photo_url'],
		            					'bark_user_name' => $current_user['user_name'],
		            					'bark_type' => 1,
		            					'bark_url' => config_item('media_bark_photo_self_domain_prefix').$image_name,
		            					'bark_location_address' => $current_user['user_location_address'],
		            					'bark_location_latitude' => $current_user['user_location_latitude'],
		            					'bark_location_longitude' => $current_user['user_location_longitude'],
		            					'bark_created_date' => $bark_created_date
		            			);
		            			$this->db->insert('wf_barks', $data);
		            			$insert_id = $this->db->insert_id();
		            			if (isset($insert_id) && $insert_id > 0) {
		            				$this->db->insert('wf_broadcasts', array(
		            						'bc_bark_id' => $insert_id,
		            						'bc_locations' => $data['bark_location_latitude'] . ':' . $data['bark_location_longitude'] . ';1'
		            				));
		            				$reach = pow(config_item('user_settings_distance_default'), 2) * M_PI;  
		            				$this->db->insert('wf_stats', array(
		            						'st_bark_id' => $insert_id,
		            						'st_bark_user_id' => $params['user_id'],
		            						'st_bark_created_date' => $bark_created_date,
		            						'st_reach' => $reach,
		            						'st_updated_date' => $bark_created_date
		            				));
		            			}
		            		}
		            	}
            		} else if((int)$params['bark_type'] == 2) {
            			if(!(isset($params['bark_video']) && $params['bark_video'] != '' && isset($params['bark_video_thumb']) && $params['bark_video_thumb'] != '')) {
            				$msg = 'Please upload your bark video';
            				$status = 0;
            			} else {
            				$video_path = config_item('path_media_bark_videos');
            				$created_time = time();
            				$video_name = $params['user_id'] . '_' . $created_time . '.mp4';
            				$video_url = $video_path . $video_name;
            				$binary = base64_decode($params['bark_video']);
            				header('Content-Type: bitmap; charset=utf-8');
            				$file = fopen($video_url, 'w');
            				if($file) {
            					fwrite($file, $binary);
            				} else {
            					$status = 3;
            					$msg = 'File upload failed';
            				}
            				
            				fclose($file);
            				if($status < 2) {
            					if(isset($params['bark_video_thumb']) && $params['bark_video_thumb'] != '') {
            						$image_path = config_item('path_media_bark_video_thumbs');
            						$created_time = time();
            						$image_name = $params['user_id'] . '_' . $created_time . '.jpg';
            						$image_url = $image_path . $image_name;
            						$binary = base64_decode($params['bark_video_thumb']);
            						header('Content-Type: bitmap; charset=utf-8');
            						$file1 = fopen($image_url, 'w');
            						if($file1) {
            							fwrite($file1, $binary);
            						} else {
            							$status = 3;
            							$msg = 'File upload failed';
            						}
            						fclose($file1);
            					}
            				
            					if($status < 2) {
            						
            						$bark_created_date = date('Y-m-d h:i:s');
	            					$current_user = element('current_user', $this->get_user_info($params['user_id']));
	            					$data = array(
	            							'bark_user_id' => $params['user_id'],
	            							'bark_user_photo_url' => $current_user['user_photo_url'],
	            							'bark_user_name' => $current_user['user_name'],
	            							'bark_type' => 2,
	            							'bark_url' => config_item('media_bark_video_domain_prefix') . $video_name,
	            							'bark_thumb_url' => config_item('media_bark_video_thumb_self_domain_prefix') . $image_name,
	            							'bark_location_address' => $current_user['user_location_address'],
	            							'bark_location_latitude' => $current_user['user_location_latitude'],
	            							'bark_location_longitude' => $current_user['user_location_longitude'],
	            							'bark_created_date' => $bark_created_date
	            					);
	            					$this->db->insert('wf_barks', $data);
	            					$insert_id = $this->db->insert_id();
	            					if (isset($insert_id) && $insert_id > 0) {
	            						$this->db->insert('wf_broadcasts', array(
	            								'bc_bark_id' => $insert_id,
	            								'bc_locations' => $data['bark_location_latitude'] . ':' . $data['bark_location_longitude'] . ';1'
	            						));
	            						$reach = pow(config_item('user_settings_distance_default'), 2) * M_PI;
	            						$this->db->insert('wf_stats', array(
	            								'st_bark_id' => $insert_id,
	            								'st_bark_user_id' => $params['user_id'],
	            								'st_bark_created_date' => $bark_created_date,
	            								'st_reach' => $reach,
	            								'st_updated_date' => $bark_created_date
	            						));
	            					}
            					}
            				}
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
            	if((int)$bark['bark_type'] == 1) {
            		$bark_media = config_item('path_media_bark_photos') . $this->get_bark_photo_prefix_removed_media_name($bark['bark_url']);
            		if(file_exists($bark_media)) unlink($bark_media);
            	} else {
            		$bark_media = config_item('path_media_bark_videos') . $this->get_bark_video_prefix_removed_media_name($bark['bark_url']);
            		$bark_thumb_media = config_item('path_media_bark_video_thumbs') . $this->get_bark_video_thumb_prefix_removed_media_name($bark['bark_thumb_url']);
            		if(file_exists($bark_media)) unlink($bark_media);
            		if(file_exists($bark_thumb_media)) unlink($bark_thumb_media);
            	}
            	
                $this->db->delete('wf_barks', array('bark_user_id' => $params['user_id'], 'bark_id' => $params['bark_id']));
                $this->db->delete('wf_broadcasts', array('bc_bark_id' => $params['bark_id']));
                $this->db->delete('wf_stats', array('st_bark_id' => $params['bark_id']));
                
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
    
    public function like_user($params) {
    	$result = array();
    	$status = 0;
    	$msg = '';
    	 
    	$where = array('fav_user_id' => $params['user_id']);
    	$query = $this->db->get_where('wf_user_favorites', $where);
    	$ref_user_ids_str = element('fav_ref_user_ids', $query->row_array());
    	$is_exist = false;
    	foreach(explode(',', $ref_user_ids_str) as $ref_id) {
    		if($ref_id == $params['ref_user_id']) {
    			$is_exist = true;
    			break;
    		}
    	}
    	
    	if(!$is_exist && count(explode(',', $ref_user_ids_str)) > config_item('limit_like_user')) {
    		$status = 2;
    		$msg = 'Oops! You can only have ' . config_item('limit_like_user') . ' favorite barkers';
    	} else {
    		$new_ref_user_ids_str = '';
    		if(!$is_exist) {
    			if($ref_user_ids_str == '') {
    				$new_ref_user_ids_str = $params['ref_user_id'];
    			} else {
    				$new_ref_user_ids_str = $ref_user_ids_str . ',' . $params['ref_user_id'];
    			}
    		} else {
    			foreach(explode(',', $ref_user_ids_str) as $ref_id) {
    				if($ref_id != $params['ref_user_id']) {
    					if($new_ref_user_ids_str != '') $new_ref_user_ids_str .= ',';
    					$new_ref_user_ids_str .= $ref_id;
    				}
    			}
    		}
    		
    		$this->db->update('wf_user_favorites', array('fav_ref_user_ids' => $new_ref_user_ids_str), $where);
    		$result = $this->get_user_detail($params['user_id']);
    		
    		// Push Notification
    		if(!$is_exist) {
	    		$query_user_notify = $this->db->get_where('wf_users', array('user_id' => $params['ref_user_id']));
	    		$user_pn = $query_user_notify->row_array();
	    		
	    		$msgs_gcm_pn = array();
	    		$msgs_apns_pn = array();
	    		
	    		if(isset($user_pn['user_gcm_id']) && $user_pn['user_gcm_id'] != '') {
	    			$msgs_gcm_pn[] = array(
	    					'gcm_id' => $user_pn['user_gcm_id'],
	    					'msg' => array('msg_type' => 1, 'msg_content' => config_item('pn_message_get_favorited'))
	    			);
	    		}
	    		if(isset($user_pn['user_apns_id']) && $user_pn['user_apns_id'] != '') {
	    			$msgs_apns_pn[] = array(
	    					'apns_id' => $user_pn['user_apns_id'],
	    					'msg' => array('msg_type' => 1, 'msg_content' => config_item('pn_message_get_favorited'))
	    			);
	    		}
	    		/* if(count($msgs_gcm_pn) > 0) {
	    		 $this->send_GCM($msgs_gcm_pn);
	    		} */
	    		if(count($msgs_apns_pn) > 0) {
	    			$this->send_APNS($msgs_apns_pn);
	    		}
    		}
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
    		if(isset($params['user_photo_avatar']) && $params['user_photo_avatar'] != '') {
    			$this->db->update('wf_users', array('user_name' => $params['user_name'], 'user_photo_url' => config_item('media_user_photo_avatar_prefix').$params['user_photo_avatar']), $where);
	    		$this->db->update('wf_barks', array('bark_user_name' => $params['user_name'], 'bark_user_photo_url' => config_item('media_user_photo_avatar_prefix').$params['user_photo_avatar']), array('bark_user_id' => $params['user_id']));
    		} else if(isset($params['user_photo']) && $params['user_photo'] != '') {
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
    	$query = $this->db->select(array('bark_id', 'bc_locations', 'bark_user_id', 'bark_user_name', 'bark_user_photo_url', 'bark_type', 'bark_url', 'bark_thumb_url', 'bark_location_address', 'bark_location_latitude', 'bark_location_longitude'))
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
    		
    		$where = array('fav_user_id' => $user_id);
    		$query_fav_user = $this->db->get_where('wf_user_favorites', $where);
    		$fav_ref_user_ids_arr = explode(',', element('fav_ref_user_ids', $query_fav_user->row_array()));
    		
    		if(count($like_bark_ids_arr) > 0) {

	    		$this->db->select(array('bark_id', 'bc_locations', 'bark_user_id', 'bark_user_name', 'bark_user_photo_url', 'bark_type', 'bark_url', 'bark_thumb_url', 'bark_location_address', 'bark_location_latitude', 'bark_location_longitude'));
	    		$this->db->where('wf_barks.bark_id IN ' . $like_bark_ids_str);
	    		$this->db->join('wf_broadcasts', 'wf_broadcasts.bc_bark_id = wf_barks.bark_id');
	    		$this->db->order_by('bark_id', 'desc');
		    	$query_barks = $this->db->get('wf_barks');
		    	
	    		$liked_barks_temp = $query_barks->result_array();
	    		foreach($liked_barks_temp as $row) {
	    			$is_user_favorite = (in_array($row['bark_user_id'], $fav_ref_user_ids_arr)) ? 1 : 0;
		    		$distance_miles = $this->distance($result['current_user']['user_location_latitude'], $result['current_user']['user_location_longitude'], $row['bark_location_latitude'], $row['bark_location_longitude'], "M");
	    			$liked_barks[] = array_merge($row, array('is_user_favorite' => $is_user_favorite), array('is_favorite' => $like_bark_ids_arr_fav[$row['bark_id']], 'order_id' => $like_bark_ids_arr_order[$row['bark_id']], 'bark_distance' => $distance_miles));
	    		}
	    		function sort_by_order_id($a, $b) {
	    			return $b['order_id'] - $a['order_id'];
	    		}
	    		usort($liked_barks, 'sort_by_order_id');
    		}
    	}
    	$result['liked_barks'] = $liked_barks;
    	
    	
    	// My Favorite Barkers
    	$favorite_users = array();
    	$where = array('fav_user_id' => $user_id);
    	$query_fav_user = $this->db->get_where('wf_user_favorites', $where);
    	$ref_user_ids_str = element('fav_ref_user_ids', $query_fav_user->row_array());
    	if($ref_user_ids_str != '') {
	    	$query = $this->db->select(array('user_id', 'user_name', 'user_age', 'user_gender', 'user_address', 'user_photo_url', 'user_location_address', 'user_location_latitude', 'user_location_longitude', 'user_settings_distance', 'user_settings_age_min', 'user_settings_age_max', 'user_settings_gender', 'user_settings_location_id'))
	    		->where_in('user_id', explode(',', $ref_user_ids_str))
	    		->get('wf_users');
	    	$favorite_users = $query->result_array();
    	}
    	$result['favorite_users'] = $favorite_users;
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
	    		$query = $this->db->select(array('bark_id', 'user_age', 'user_gender', 'bc_locations', 'bark_user_id', 'bark_user_name', 'bark_user_photo_url', 'bark_type', 'bark_url', 'bark_thumb_url', 'bark_location_address', 'bark_location_latitude', 'bark_location_longitude'))
	    		->where(array('bark_user_id !=' => $params['user_id'])) // Constraint : exclude my barks
	    		->where('bark_id NOT IN (' . $like_total_ids . ')')
	    		->join('wf_broadcasts', 'wf_broadcasts.bc_bark_id = wf_barks.bark_id')
	    		->join('wf_users', 'wf_users.user_id = wf_barks.bark_user_id')
	    		->order_by('bark_created_date', 'desc')
	    		->limit($limit_per_query, $limit_offset)
	    		->get('wf_barks');
    		} else {
    			$query = $this->db->select(array('bark_id', 'user_age', 'user_gender', 'bc_locations', 'bark_user_id', 'bark_user_name', 'bark_user_photo_url', 'bark_type', 'bark_url', 'bark_thumb_url', 'bark_location_address', 'bark_location_latitude', 'bark_location_longitude'))
    			->where(array('bark_user_id !=' => $params['user_id'])) // Constraint : exclude my barks
    			->join('wf_broadcasts', 'wf_broadcasts.bc_bark_id = wf_barks.bark_id')
    			->join('wf_users', 'wf_users.user_id = wf_barks.bark_user_id')
    			->order_by('bark_created_date', 'desc')
    			->limit($limit_per_query, $limit_offset)
    			->get('wf_barks');
    		}
    		$limit_offset += $limit_per_query;


    		foreach($query->result_array() as $bark) {
    			
    			$distance_miles = 0;
    			
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
    			
    			// Check if user liked the bark's owner
    			$is_fav_user_bark = false;
    			$where = array('fav_user_id' => $params['user_id']);
    			$query_fav_user = $this->db->get_where('wf_user_favorites', $where);
    			$ref_user_ids_str = element('fav_ref_user_ids', $query_fav_user->row_array());
    			if(in_array($bark['bark_user_id'], explode(',', $ref_user_ids_str))) {
    				$is_fav_user_bark = true;
    			}
    			if($is_fav_user_bark) {
    				$distance_miles = $this->distance($current_user['user_location_latitude'], $current_user['user_location_longitude'], $bark['bark_location_latitude'], $bark['bark_location_longitude'], "M");
    			}
    			
    			if(($match && $distance_match) || $is_fav_user_bark) {
    				$barks[$count] = array_merge(elements(array('bark_id', 'bark_user_id', 'bark_user_name', 'bark_user_photo_url', 'bark_type', 'bark_url', 'bark_thumb_url'), $bark), array('bark_distance' => $distance_miles));
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
    
    public function get_bark_photo_prefix_removed_media_name($image_name) {
    	$prefix_length = strlen(config_item('media_bark_photo_self_domain_prefix'));
    	if(substr($image_name, 0, $prefix_length) == config_item('media_bark_photo_self_domain_prefix')) {
    		return substr($image_name, $prefix_length);
    	} else {
			return $image_name;
		}
    }
    public function get_bark_video_prefix_removed_media_name($image_name) {
    	$prefix_length = strlen(config_item('media_bark_video_domain_prefix'));
    	if(substr($image_name, 0, $prefix_length) == config_item('media_bark_video_domain_prefix')) {
    		return substr($image_name, $prefix_length);
    	} else {
    		return $image_name;
    	}
    }
    public function get_bark_video_thumb_prefix_removed_media_name($image_name) {
    	$prefix_length = strlen(config_item('media_bark_video_thumb_self_domain_prefix'));
    	if(substr($image_name, 0, $prefix_length) == config_item('media_bark_video_thumb_self_domain_prefix')) {
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
    
    /*
    echo distance(32.9697, -96.80322, 29.46786, -98.53506, "M") . " Miles<br>";
    echo distance(32.9697, -96.80322, 29.46786, -98.53506, "K") . " Kilometers<br>";
    echo distance(32.9697, -96.80322, 29.46786, -98.53506, "N") . " Nautical Miles<br>";
    */
    
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
    
    
    // Send Push Notification (GCM + APNS)
    
    public function send_GCM($gcm_ids, $msg_params) {
    	$status = false;
    	require_once('GCM.php');
    
    	$api_key = 'AIzaSyBe-7uPHny-2kg1aT6-df8JgoIqFcGlU01';
    	$sender = '855717861923';
    	$receiver = 'com.tiick.app';
    	 
    	$gcm = new GCM();
    
    	$ret = $gcm->send_notification($gcm_ids, $msg_params, $api_key);
    	if(!$ret) {
    		$status = false;
    	} else {
    		$status = true;
    	}
    	return $status;
    }
    
    public function send_APNS($msg_params) {
    	$status = false;

    	$passphrase = 'silver';

    	////////////////////////////////////////////////////////////////////////////////
    
    	$ctx = stream_context_create();
    	stream_context_set_option($ctx, 'ssl', 'local_cert', './application/models/ck.pem');
    	stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

    	// Open a connection to the APNS server
    	$fp = stream_socket_client(
    			'ssl://gateway.push.apple.com:2195', $err,
    			$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
    
    	//     	$fp = stream_socket_client(
    	//     	 'ssl://gateway.sandbox.push.apple.com:2195', $err,
    	//     			$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
    	
    	if(!$fp) exit("Failed to connect: $err $errstr" . PHP_EOL);
    	//echo 'Connected to APNS' . PHP_EOL;
    	// Create the payload body

    	foreach($msg_params as $param) {
    		
    		$message_info = array('message' => $param['msg']['msg_content']);
    		
    		// Create the payload body
    		$body['aps'] = array(
    				'alert' => $param['msg']['msg_content'],
    				'sound' => 'default',
    				'badgecount' => 1,
    				'info'=> $message_info,
    				'notify' => 'notification',
    		);
    		// Encode the payload as JSON
    		$payload = json_encode($body);
    		// Build the binary notification
    		$msg1 = chr(0) . pack('n', 32) . pack('H*', $param['apns_id']) . pack('n', strlen($payload)) . $payload;
    		// Send it to the server
    		$result_apns = fwrite($fp, $msg1, strlen($msg1));
    		if (!$result_apns) {
    			$status = false;
    		} else {
    			$status = true;
    		}
    	}
    	// Close the connection to the server
    	fclose($fp);
    
    	return $status;
    }
    
}
