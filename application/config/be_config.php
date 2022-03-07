<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// API Auth
$config['api_key'] = 'j05wd2ae49d212578ef13cb607cef64b';

// Messages
$config['msg_fill_form'] = 'Please fill the form correctly';

// Paths
$config['path_media_users'] = 'assets/media/users/';
$config['path_media_bark_photos'] = 'assets/media/bark_photos/';
$config['path_media_bark_videos'] = 'assets/media/bark_videos/';
$config['path_media_bark_video_thumbs'] = 'assets/media/bark_video_thumbs/';


$config['media_user_photo_avatar_prefix'] = 'wf_avatar_';

$config['media_user_self_domain_prefix'] = 'wf_media_user_';
$config['media_bark_photo_self_domain_prefix'] = 'wf_media_bark_photo_';
$config['media_bark_video_domain_prefix'] = 'wf_media_bark_video_';
$config['media_bark_video_thumb_self_domain_prefix'] = 'wf_media_bark_video_thumb_';

// Settings
$config['user_auth_salt'] = 'j05wd2ae49d212578ef13cb607cef64b';
$config['user_new_password_length'] = 7;

$config['user_photo_avatar_count'] = 12;

$config['user_settings_distance_default'] = 99; // miles
$config['user_settings_age_min_default'] = 18;
$config['user_settings_age_max_default'] = 55;

$config['velocity_history_duration_unit'] = 6; // hours

$config['explore_barks_count_default'] = 100;
$config['explore_barks_limit_per_query'] = 100;

$config['limit_post_barks_per_user'] = 30;
$config['limit_like_barks_per_user'] = 50;
$config['limit_favorite_barks_per_user'] = 20;
$config['limit_like_user'] = 20;

$config['http_timeout_default'] = 5;

// Push notification
$config['pn_message_placeholder'] = 'WF_PLACEHOLDER';

$config['pn_liked_counts'] = array(100, 500, 1000, 2000, 3000, 4000, 5000, 10000, 20000, 30000, 40000, 50000,
		100000, 200000, 300000, 400000, 500000, 600000, 700000, 800000, 900000);
$config['pn_message_liked_counts'] = 'Congratulations! ' . $config['pn_message_placeholder'] . ' people are rebroadcasting your bark!';

$config['pn_furthest_distances'] = array(100, 250, 500, 1000, 2000, 5000, 10000, 15000, 20000, 25000);
$config['pn_message_furthest_distances'] = 'Get Outta Town! Your bark has traveled ' . $config['pn_message_placeholder'] . ' miles!';

$config['pn_reaches'] = array(270000);
$config['pn_message_reaches'] = 'Bark Like a Big Dog. Your bark is now reaching an area the size of Texas!';

$config['pn_velocities'] = array(100);
$config['pn_message_velocities'] = 'Don\'t get pulled over! Your bark is travelling over ' . $config['pn_message_placeholder'] . ' mph!';

$config['pn_message_get_favorited'] = 'You\'re popular so keep barking! Another user has added you to his or her favorite barkers.';

