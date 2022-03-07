	<div class="container be-container-signup">
		<div class="col-xs-12">
			<div class="be-font-s36 margin-top-20">
          		SIGN UP NOW! AND START SHARING
			</div>
			<div class="be-home-signup-form margin-top-30">
				<form action="<?php echo base_url(); ?>user/signup" id="be-home-signup-form2" method="post" class="form-horizontal">
		          	<!-- 
		          	<div class="form-group margin-top-50">
			              <div class="controls col-xs-12 col-md-6 col-md-offset-3">
			                <input type="file" name="photo_link" class="form-control be-form-input be-form-input-file-photo be-form-input-photo-link" placeholder="Upload a photo" value="<?php echo (isset($_REQUEST['photo_link'])) ? $_REQUEST['photo_link'] : ''; ?>" data-filename-placement="inside">
			              </div>
		            </div>
		            -->
		            <div class="be-font-s24 margin-top-20">
		          		-&nbsp;&nbsp;&nbsp;Upload your photo&nbsp;&nbsp;&nbsp;-
					</div>
		            <div id="be-signup-photo-upload" class="be-signup-photo-upload"></div>
		            <script>
			            $(function() {
			            	$("#be-signup-photo-upload").PictureCut({
				                InputOfImageDirectory       : "photo_link",
				                PluginFolderOnServer        : "/<?php echo (config_item('domain_sub_directory') != '') ? config_item('domain_sub_directory') . '/' : ''; ?><?php echo config_item('jquery_picture_cut_temp_path'); ?>",
				                FolderOnServer              : "/<?php echo (config_item('domain_sub_directory') != '') ? config_item('domain_sub_directory') . '/' : ''; ?><?php echo config_item('upload_path_photos'); ?>",
				                EnableCrop                  : true,
				                CropWindowStyle             : "Bootstrap",
				                CropOrientation				: false,
				                CropModes					: {widescreen: false, letterbox: false, free: true}
				            });
			            });
		            </script>
		            
		            <div class="form-group margin-top-40">
			              <div class="controls col-xs-12 col-md-6 col-md-offset-3">
		            			<input class="btn btn-orange submit-btn be-form-input width-100pc" type="submit" value="Go to Next Step">
	              		  </div>
	                </div>
		          	<input type="hidden" name="tag" value="signup3">
		          	<?php if(isset($requests)) $this->be_model->set_hidden_tags($requests);	?>
				</form>
			</div>
        </div>
		<div class="clear"></div>
	</div>