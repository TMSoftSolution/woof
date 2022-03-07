	<div class="container padding-top-0">
      <div class="row">
        <div class="col-md-12">
        	<div class="be-dashboard-block col-sm-12 col-md-4">
        		<div class="be-dashboard-block-title be-dashboard-block-title-orange">
        			<div class="be-dashboard-block-title-inner be-dashboard-block-title-icon-list">
        				Manage Artists
        			</div>
        		</div>
        		<div class="be-dashboard-block-content">
        			<?php
        			$users = $this->be_model->get_users();
        			foreach($users as $iuser) {
        			?>
	        		<div class="be-dashboard-block-artist margin-bottom-10">
	        			<div class="be-dashboard-block-artist-photo">
        					<img class="be-dashboard-block-photo-img" src="<?php
	        					$sub_directory = ((config_item('domain_sub_directory') != '') ? '/' . config_item('domain_sub_directory') : '');
	        					if(isset($iuser['photo_link']) && $iuser['photo_link'] != '') {
	        						echo $sub_directory . $iuser['photo_link'];
	        					} else {
	        						echo $sub_directory . config_item('default_artist_photo_link');
	        					}
	        				?>">
        					<div class="be-dashboard-block-photo-img-cover"></div>
	        			</div>
	        			<div class="be-dashboard-block-artist-title">
	        				<div class="margin-bottom-10 be-font-black"><?php echo $iuser['full_name']; ?></div>
	        				<div class=""><?php echo $iuser['bio']; ?></div>
	        			</div>
	        			<div class="clear"></div>
	        		</div>
	        		<?php } ?>
        		</div>
        	</div>
        	<div class="be-dashboard-block col-sm-12 col-md-4">
        		<div class="be-dashboard-block-title be-dashboard-block-title-green">
        			<div class="be-dashboard-block-title-inner be-dashboard-block-title-icon-music">
        				Listner Issues
        			</div>
        		</div>
        		<div class="be-dashboard-block-content">
        			<div class="margin-bottom-10 be-font-black">This is a Ticket Example from Listner</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Ticket Title from Listner Goes Here</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Another ticket example</div>
        			<div class="margin-bottom-10 be-font-black">This is a Ticket Example from Listner</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Ticket Title from Listner Goes Here</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Another ticket example</div>
        			<div class="margin-bottom-10 be-font-black">This is a Ticket Example from Listner</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Ticket Title from Listner Goes Here</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Another ticket example</div>
        		</div>
        	</div>
        	<div class="be-dashboard-block col-sm-12 col-md-4">
        		<div class="be-dashboard-block-title be-dashboard-block-title-yellow">
        			<div class="be-dashboard-block-title-inner be-dashboard-block-title-icon-bubble">
        				Suspension
        			</div>
        		</div>
        		<div class="be-dashboard-block-content">
        			<div class="margin-bottom-10 be-font-black">This is a Ticket Example from Listner</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Ticket Title from Listner Goes Here</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Another ticket example</div>
        			<div class="margin-bottom-10 be-font-black">This is a Ticket Example from Listner</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Ticket Title from Listner Goes Here</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Another ticket example</div>
        			<div class="margin-bottom-10 be-font-black">This is a Ticket Example from Listner</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Ticket Title from Listner Goes Here</div>
        			<div class="margin-bottom-20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ullamcorper elit massa, eu pretium tortor tempor id.</div>
        			<div class="margin-bottom-10 be-font-black">Another ticket example</div>
        		</div>
        	</div>
        </div>
      </div>
    </div>