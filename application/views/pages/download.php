	<div class="container be-container-download">
		<div class="col-xs-12">
          	<div class="col-xs-12 col-sm-5 be-container-left-hidden text-left">
          		<div class="be-container-download-left"></div>
          	</div>
          	<div class="col-xs-12 col-sm-7">
          		<div class="be-container-download-right">
	          		<div class="be-font-s48">
	          			Launching Soon
					</div>
					<div class="be-font-s20 margin-top-30">Stay in the know by entering your email address below</div>
					<div class="margin-top-20">
						<div class="be-home-form be-home-subscribe-form be-download-subscribe-form">
			            	<form novalidate="novalidate" id="be-home-subscribe-form" method="post" action="<?php echo base_url(); ?>api/add_subscriber/">
								<div class="input-group">
					              <input type="text" name="email" class="required email form-control be-form-input be-form-input-orange" placeholder="Enter your email" value="" />
					              <span class="input-group-btn">
					                <input class="btn btn-green submit-btn be-form-input" value="Submit" type="submit">
					              </span>
					              <input type="hidden" name="from_link" value="<?php echo $page; ?>">
					              <input type="hidden" name="tag" value="add_subscriber">
					            </div>
							</form>
						</div>
					</div>
				</div>
          	</div>
        </div>
		<div class="clear"></div>
	</div>