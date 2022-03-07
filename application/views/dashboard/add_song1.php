	<div class="container padding-top-0">
      <div class="row">
        <div class="col-md-12 margin-top-30">
        	<form action="<?php echo base_url(); ?>dashboard/add_song" accept-charset="utf-8" class="form-horizontal be-dashboard-song-form" id="be-add-song-form1" method="post" enctype="multipart/form-data">
        		<div class="controls col-xs-12 col-md-12">
        			<div class="be-font-s26 be-font-dark-grey margin-bottom-30">ADD NEW SONG</div>
        		</div>
	          	<div class="form-group">
		              <div class="controls col-xs-12 col-md-6">
		                	<input type="file" name="link" class="form-control be-form-input-file-song be-form-input-song-link" placeholder="Upload a song" value="<?php echo (isset($_REQUEST['link'])) ? $_REQUEST['link'] : ''; ?>" data-filename-placement="inside">
		              </div>
	            </div>
		            
	            <div class="form-group">
		              <div class="col-xs-12 col-md-6 margin-top-30">
			              <label class="checkbox be-dashboard-song-form-checkbox">
			              		<input type="checkbox" name="terms_conditions" id="be-song-terms-confirm-val">
			              		<span></span>
			              		I agree that this song complies with the <a href="<?php echo base_url(); ?>pages/legal" target="_blank">Terms and Conditions</a>.
			              </label>
	            		  <a class="btn btn-orange submit-btn be-form-input width-100pc margin-top-20" id="be-song-terms-confirm">Submit</a>
              		  </div>
                </div>
                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
	          	<input type="hidden" name="tag" value="add_song2">
		        <?php if(isset($requests)) $this->be_model->set_hidden_tags($requests);	?>
			</form>
			<div class="modal fade be-song-terms-confirm-modal">
			    <div class="modal-dialog">
			        <div class="modal-content">
				        <!-- <div class="modal-header">
			                <h4>Confirm</h4>
			            </div> -->
			            <div class="modal-body">
			            	<div class="be-font-red be-font-s14 center margin-top-10">You cannot upload a song without confirming that it complies with our Terms and Conditions.</div>
			            </div>
			            <div class="modal-footer">
			                <button type="button" class="btn btn-orange" data-dismiss="modal">Close</button>
			            </div>
			        </div>
			    </div>
			</div>
			<script type="text/javascript">
				$(function() {
					$('#be-song-terms-confirm').click(function() {
						if($("#be-song-terms-confirm-val:checked").length == 0)
							$("div.be-song-terms-confirm-modal").modal('toggle');
						else
							$('#be-add-song-form1').submit();
					});
				});
			</script>
        </div>
      </div>
    </div>