	<div class="container be-container-signup">
		<div class="col-xs-12">
			<div class="be-font-s36 margin-top-20">
          		SIGN UP NOW! AND START SHARING
			</div>
			<div class="be-home-signup-form margin-top-30">
				<form action="<?php echo base_url(); ?>user/signup" id="be-home-signup-form3" method="post" class="form-horizontal">
				
					<div class="form-group margin-top-50">
			              <div class="controls col-xs-12 col-md-6 col-md-offset-3">
			                <input type="text" name="website" class="form-control be-form-input be-form-input-grey" placeholder="Your Website (optional)" value="<?php echo (isset($_REQUEST['website'])) ? $_REQUEST['website'] : ''; ?>">
			              </div><!-- end controls-->
		            </div><!-- end form-group -->
		            
		          	<div class="form-group margin-top-30">
			              <div class="controls col-xs-12 col-md-6 col-md-offset-3">
			                <input type="text" name="facebook_link" class="form-control be-form-input be-form-input-grey" placeholder="Facebook Link (optional)" value="<?php echo (isset($_REQUEST['facebook_link'])) ? $_REQUEST['facebook_link'] : ''; ?>">
			              </div><!-- end controls-->
		            </div><!-- end form-group -->
		            
		            <div class="form-group margin-top-30">
			              <div class="controls col-xs-12 col-md-6 col-md-offset-3">
			                <input type="text" name="twitter_link" class="form-control be-form-input be-form-input-grey" placeholder="Twitter Link (optional)" value="<?php echo (isset($_REQUEST['twitter_link'])) ? $_REQUEST['twitter_link'] : ''; ?>">
			              </div><!-- end controls-->
		            </div><!-- end form-group -->
		            <div class="form-group margin-top-40">
			              <div class="controls col-xs-12 col-md-3 col-md-offset-3">
		            			<input class="btn btn-orange submit-btn be-form-input width-100pc" type="submit" value="Submit">
	              		  		<!-- <a class="btn btn-orange be-form-input width-100pc" id="be-signup-final-confirm">Submit</a> -->
	              		  </div>
	              		  <div class="controls col-xs-12 col-md-3 margin-bottom-30">
		            			<input class="btn btn-orange submit-btn be-form-input width-100pc" type="submit" value="Skip this">
	              		  </div>
	                </div>
		          	<input type="hidden" name="tag" value="signup4">
		          	<?php if(isset($requests)) $this->be_model->set_hidden_tags($requests);	?>
				</form>
				<!-- 
				<div class="modal fade be-signup-final-confirm-modal">
				    <div class="modal-dialog">
				        <div class="modal-content">
					        <div class="modal-header">
				                <h4>Confirm</h4>
				            </div>
				            <div class="modal-body">
				            	<div class="be-font-black be-font-s20">Are you sure to finalize signup form?</div>
				            </div>
				            <div class="modal-footer">
				                <button type="button" class="btn btn-light" data-dismiss="modal">No</button>
				                <button type="button" class="btn btn-orange" data-dismiss="modal" id="be-signup-final-confirm-yes">Yes</button>
				            </div>
				        </div>
				    </div>
				</div>
				<script type="text/javascript">
					$(function() {
						$('#be-signup-final-confirm').click(function() {
							$("div.be-signup-final-confirm-modal").modal('toggle');
						});
						$('#be-signup-final-confirm-yes').click(function() {
							$('#be-home-signup-form3').submit();
						});
					});
				</script>
				-->
			</div>
        </div>
		<div class="clear"></div>
	</div>