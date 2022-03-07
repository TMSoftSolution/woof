<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Be_model extends CI_Model {
	
	public function logged_in() {
		if($this->session->userdata('logged_in')) {
			return $this->session->userdata('logged_in');
		} else {
			return false;
		}
	}
	
	public function is_valid_email($email) {
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	
	public function set_hidden_tags($requests = array()) {
		if(count($requests) > 0) {
			foreach($requests as $request_field => $request_value) {
				echo '<input type="hidden" name="' . $request_field . '" value="' . $request_value . '">';
			}
		}
	}
	public function get_site_info() {
		$site_info = array();
		$query = $this->db->get('site_info');
		foreach($query->result_array() as $row) {
			$site_info[$row['name']] = $row['value'];
		}
		return $site_info;
	}
	public function get_form_validation_rule($item) {
		$rule = 'xss_clean';
		if(isset($item['minlength'])) $rule .= '|min_length['.$item['minlength'].']';
		if(isset($item['maxlength'])) $rule .= '|max_length['.$item['maxlength'].']';
		if(isset($item['required'])) $rule .= '|required';
		if(isset($item['valid_email'])) $rule .= '|valid_email';
		if(isset($item['password_confirm'])) $rule .= '|matches[password_confirm]';
		if(isset($item['numeric'])) $rule .= '|numeric';
		if(isset($item['exact_length'])) $rule .= '|exact_length['.$item['exact_length'].']';
		if(isset($item['is_unique'])) $rule .= '|is_unique['.$item['is_unique'].']';
		return $rule;
	}
	
	public function get_users() {
		$query = $this->db->select('id, email, user_role, full_name, photo_link, genre_ids, bio, facebook_link, twitter_link', 'country', 'zip', 'city', 'website')
			->get_where('users', array('user_role' => 1));
		return $query->result_array();
	}
	
	public function get_zip_info($zip) {
		$city = "N/A";
		$state = "N/A";
		$query = $this->db->select('city')->limit(1)->get_where('zip_code', array('zip_code' => $zip));
		if($query->num_rows() == 1) {
			$city = element('city', $query->row_array());
		}
		$zipinfo['zip']   = $zip;
		$zipinfo['city']  = $city;
		$zipinfo['state'] = $state;
		return $zipinfo;
	}
	
	public function get_zip_info_auto($zip) {
		//Function to retrieve the contents of a webpage and put it into $pgdata
		$pgdata =""; //initialize $pgdata
		// Open the url based on the user input and put the data into $fd:
		$fd = fopen("http://zipinfo.com/cgi-local/zipsrch.exe?zip=$zip","r");
		while(!feof($fd)) {//while loop to keep reading data into $pgdata till its all gone
			$pgdata .= fread($fd, 1024); //read 1024 bytes at a time
		}
		fclose($fd); //close the connection
		
		
		if (preg_match("/is not currently assigned/", $pgdata)) {
			$city = "N/A";
			$state = "N/A";
		} else {
			$citystart = strpos($pgdata, "Code</th></tr><tr><td align=center>");
			$citystart = $citystart + 35;
			$pgdata    = substr($pgdata, $citystart);
			$cityend   = strpos($pgdata, "</font></td><td align=center>");
			$city      = substr($pgdata, 0, $cityend);
	
			$statestart = strpos($pgdata, "</font></td><td align=center>");
			$statestart = $statestart + 29;
			$pgdata     = substr($pgdata, $statestart);
			$stateend   = strpos($pgdata, "</font></td><td align=center>");
			$state      = substr($pgdata, 0, $stateend);
		}
		$zipinfo['zip']   = $zip;
		$zipinfo['city']  = $city;
		$zipinfo['state'] = $state;
		
		return $zipinfo;
	}
	
	public function get_countries_list() {
		$countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");
		return $countries;
	}
	
	public function get_genres_list() {
		$query = $this->db->get('genres');
		return $query->result_array();
	}
	
	public function convert_array_to_string($arr) {
		return implode(',', $arr);
	}
}