	<div class="container padding-top-0">
      <div class="row">
        <div class="col-md-12 margin-top-30">
        	<form action="<?php echo base_url(); ?>dashboard/add_song" accept-charset="utf-8" class="form-horizontal be-dashboard-song-form" id="be-add-song-form" method="post">
        		<div class="controls col-xs-12 col-md-12">
        			<div class="be-font-s26 be-font-dark-grey margin-bottom-30">ADD NEW SONG</div>
        		</div>
	          	<div class="form-group">
		              <div class="controls col-xs-6 col-md-6 pull-left">
		                <input type="text" name="title" class="form-control" placeholder="Song Title *" value="<?php echo (isset($_REQUEST['title'])) ? $_REQUEST['title'] : ''; ?>">
		              </div><!-- end controls-->
		              <div class="col-xs-6 col-md-6 pull-left">
		              	<div href="#" class="be-dashboard-song-tip" data-toggle="tooltip" data-placement="right" title="Enter a title for your song."></div>
		              </div>
		              <div class="clear"></div>
	            </div><!-- end form-group -->
	            
	            <div class="form-group">
		              <div class="controls col-xs-6 col-md-6 pull-left">
		                <input type="text" name="guest" class="form-control" placeholder="Guest Appearance" value="<?php echo (isset($_REQUEST['guest'])) ? $_REQUEST['guest'] : ''; ?>">
		              </div><!-- end controls-->
		              <div class="col-xs-6 col-md-6 pull-left">
		              	<div href="#" class="be-dashboard-song-tip" data-toggle="tooltip" data-placement="right" title="Include any artist(s) you collaborated with to create this track."></div>
		              </div>
		              <div class="clear"></div>
	            </div><!-- end form-group -->
	            
	            <div class="form-group">
		              <div class="controls col-xs-6 col-md-6 pull-left">
		                <input type="text" name="type_beat" class="form-control" placeholder="Type Beat" value="<?php echo (isset($_REQUEST['type_beat'])) ? $_REQUEST['type_beat'] : ''; ?>">
		              </div><!-- end controls-->
		              <div class="col-xs-6 col-md-6 pull-left">
		              	<div href="#" class="be-dashboard-song-tip" data-toggle="tooltip" data-placement="right" title="Include any artist(s) you think your music sounds like."></div>
		              </div>
		              <div class="clear"></div>
	            </div><!-- end form-group -->
	            
	            <div class="form-group">
		              <div class="controls col-xs-6 col-md-6 pull-left be-dashboard-form-select">
		              		<select name="genre_ids[]" title="Genre(s) *" class="form-control selectpicker" multiple data-max-options="5" data-width="100%" data-style="btn-info">
			              	<?php
			              	$items = $this->be_model->get_genres_list();
			              	if(isset($_REQUEST['genre_ids'])) {
			              		foreach($items as $item) {
			              			echo '<option value="' . $item['id'] . '"';
			              			if(isset($_REQUEST['genre_ids']) && in_array($item['id'], $_REQUEST['genre_ids'])) echo ' selected="selected"';
			              			echo '>' . $item['name'] . '</option>';
			              		}
			              	} else {
			              		$genre_ids = explode(',', $user['genre_ids']);
			              		foreach($items as $item) {
			              			echo '<option value="' . $item['id'] . '"';
			              			if(isset($genre_ids) && in_array($item['id'], $genre_ids)) echo ' selected="selected"';
			              			echo '>' . $item['name'] . '</option>';
			              		}
			              	}
			              	?>
			              	</select>
		              </div><!-- end controls-->
		              <div class="col-xs-6 col-md-6 pull-left">
		              	<div href="#" class="be-dashboard-song-tip" data-toggle="tooltip" data-placement="right" title="Select between 1 and 5 genres to accurately categorize this track."></div>
		              </div>
		              <div class="clear"></div>
	            </div><!-- end form-group -->
	            
	            <div class="form-group">
		              <div class="controls col-xs-6 col-md-6 pull-left be-dashboard-form-select">
		              		<select name="languages[]" title="Language(s) *" class="form-control selectpicker" multiple data-max-options="3" data-width="100%" data-style="btn-info">
			              	<?php
			              		$items = config_item('languages');
			              		foreach($items as $item_key => $item_value) {
			              			echo '<option value="' . $item_key . '"';
			              			if(isset($_REQUEST['languages']) && in_array($item_key, $_REQUEST['languages'])) echo ' selected="selected"';
			              			echo '>' . $item_value . '</option>';
			              		}
			              	?>
			              	</select>
		              </div><!-- end controls-->
		              <div class="col-xs-6 col-md-6 pull-left">
		              	<div href="#" class="be-dashboard-song-tip" data-toggle="tooltip" data-placement="right" title="Select between 1 and 3 languages used in the song. Select instrumental if there are no words."></div>
		              </div>
		              <div class="clear"></div>
	            </div><!-- end form-group -->
	            <div class="form-group">
		              <div class="controls col-xs-6 col-md-6 pull-left">
		              	<div class="be-dashboard-form-radio">
		              		<span class="margin-right-10 be-font-s18 be-font-grey be-font-merriweather">*</span>
						    <input type="button" id="be-dashboard-form-live0" class="btn btn-white<?php echo (isset($_REQUEST['is_live']) && $_REQUEST['is_live'] == '0') ? ' active' : ''; ?>" onclick="onClickDashboardFormLive(0);" value="Studio">
						    <span class="margin-left-20 margin-right-20 be-font-s18 be-font-grey">or</span>
						    <input type="button" id="be-dashboard-form-live1" class="btn btn-white<?php echo (isset($_REQUEST['is_live']) && $_REQUEST['is_live'] == '1') ? ' active' : ''; ?>" onclick="onClickDashboardFormLive(1);" value="Live">
						    <input type="hidden" id="be-dashboard-form-live-val" name="is_live" value="<?php echo (isset($_REQUEST['is_live'])) ? $_REQUEST['is_live'] : ''; ?>">
						</div>
		              </div><!-- end controls-->
		              <div class="clear"></div>
	            </div><!-- end form-group -->
	            
	            <div class="form-group">
		              <div class="controls col-xs-6 col-md-6 pull-left">
		              	<textarea name="inspirations" class="form-control" rows="5" placeholder="Inspirations"><?php echo (isset($_REQUEST['inspirations'])) ? $_REQUEST['inspirations'] : ''; ?></textarea>
		              </div><!-- end controls-->
		              <div class="col-xs-6 col-md-6 pull-left">
		              	<div href="#" class="be-dashboard-song-tip" data-toggle="tooltip" data-placement="right" title="This will be shown on your song’s page. Limit: 300 words"></div>
		              </div>
		              <div class="clear"></div>
	            </div><!-- end form-group -->
	            
	            <div class="form-group">
		              <div class="controls col-xs-6 col-md-6 pull-left">
		                <textarea name="lyrics" class="form-control" rows="5" placeholder="Lyrics"><?php echo (isset($_REQUEST['lyrics'])) ? $_REQUEST['lyrics'] : ''; ?></textarea>
		              </div><!-- end controls-->
		              <div class="col-xs-6 col-md-6 pull-left">
		              	<div href="#" class="be-dashboard-song-tip" data-toggle="tooltip" data-placement="right" title="This will be shown on your song’s page. Limit: 1,500 words"></div>
		              </div>
		              <div class="clear"></div>
	            </div><!-- end form-group -->
	            
	            <div class="form-group">
		              <div class="col-xs-12 col-md-6">
		              		<div class="be-font-grey be-font-s16 margin-bottom-30 be-font-merriweather"><span>*</span> indicates required fields</div>
	            			<input class="btn btn-orange submit-btn be-form-input width-100pc" type="submit" value="Continue">
              		  </div>
                </div>
                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
	          	<input type="hidden" name="tag" value="add_song1">
			</form>
        </div>
      </div>
    </div>