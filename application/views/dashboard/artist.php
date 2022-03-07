	<div class="container padding-top-0">
      <div class="row">
      	<div class="col-md-12">
      		<a href="<?php echo base_url(); ?>dashboard/add_song/" class="btn btn-orange be-no-round-border pull-right be-dashboard-add-song-button">Add New Song</a>
      	</div>
      	<div class="clear"></div>
        <div class="col-md-12">
        	<div class="be-dashboard-block col-sm-12 col-md-4">
        		<div class="be-dashboard-block-title be-dashboard-block-title-orange">
        			<div class="be-dashboard-block-title-inner be-dashboard-block-title-icon-profile">
        				Profile Information
        			</div>
        		</div>
        		<div class="be-dashboard-block-content">
        			<div class="be-dashboard-block-edit-button-area">
        				<a href="#" class="be-dashboard-block-edit-button be-font-s24" title="Edit Profile"><i class="fa fa-edit"></i></a>
        			</div>
	        		<div class="be-dashboard-block-profile margin-top-10">
	        			<div class="be-dashboard-block-photo margin-bottom-20">
	        				<img class="be-dashboard-block-photo-img" src="<?php
	        					$sub_directory = ((config_item('domain_sub_directory') != '') ? '/' . config_item('domain_sub_directory') : '');
	        					if(isset($user['photo_link']) && $user['photo_link'] != '') {
	        						echo $sub_directory . $user['photo_link'];
	        					} else {
	        						echo $sub_directory . config_item('default_artist_photo_link');
	        					}
	        				?>">
        					<div class="be-dashboard-block-photo-img-cover"></div>
	        			</div>
	        			<div class="margin-bottom-10 be-font-black"><?php echo $user['full_name']; ?></div>
	        			<div class="margin-bottom-10"><?php echo $user['bio']; ?></div>
	        		</div>
	        		<div class="margin-top-30">
	        			<div class="margin-bottom-10">Total Music Added: 112</div>
	        			<?php if(isset($user['website']) && $user['website'] != '') { ?>
	        			<div class="margin-bottom-10">Website: <a href="<?php echo $user['website']; ?>" target="_blank"><?php echo $user['website']; ?></a></div>
	        			<?php } ?>
	        			<div class="margin-bottom-10">Follower: 1102</div>
	        			<div class="margin-bottom-10">Loved by others: 12000</div>
	        			<div class="margin-bottom-10">Location: <?php echo $user['city'] . ', ' . $user['country']; ?></div>
        			</div>
        		</div>
        	</div>
        	<div class="be-dashboard-block col-sm-12 col-md-4">
        		<div class="be-dashboard-block-title be-dashboard-block-title-green">
        			<div class="be-dashboard-block-title-inner be-dashboard-block-title-icon-music">
        				My Songs
        			</div>
        		</div>
        		<div class="be-dashboard-block-content">
        			<div class="margin-bottom-10 be-font-black">Song Title Goes Here</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Song Title Goes Here</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Song Title Goes Here</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Song Title Goes Here</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Song Title Goes Here</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Song Title Goes Here</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        		</div>
        	</div>
        	<div class="be-dashboard-block col-sm-12 col-md-4">
        		<div class="be-dashboard-block-title be-dashboard-block-title-yellow">
        			<div class="be-dashboard-block-title-inner be-dashboard-block-title-icon-bubble">
        				Comments
        			</div>
        		</div>
        		<div class="be-dashboard-block-content">
        			<div class="margin-bottom-10 be-font-black">This is Comment Title</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Another Comment Title</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">This is Comment Title</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Another Comment Title</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Another Comment Title</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Another Comment Title</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        		</div>
        	</div>
        </div>
      </div>
    </div>