<div class="container-wrapper container-wrapper-nails container-top">
      <div class="container container-top">
		<div class="row">
			<div class="col-md-12 center">
				<h1>Test</h1>
			</div>
		</div><!-- end row -->
	</div><!-- end container -->
</div><!-- end container wrapper -->

<div class="container">
	<?php
	if(isset($result)) {
		if(count($result) > 0) {
			foreach($result as $key => $value) {
				echo '<pre>' . $key . ' -> ';
				print_r($value);
				echo '</pre><br><br>';
			}
		} else {
			echo $result;
		}
		echo '<br><br>';
	}
	
	echo form_open("test");
	foreach($params as $param) {
		echo $param . ' <input type="text" name="'.$param.'" value="'.$this->input->post($param).'" /><br><br>';
	}
	?>
		<input type="submit" name="submit" value="Submit" />
	<?php
	echo form_close();
	?>
</div>

<div class="clear"></div>