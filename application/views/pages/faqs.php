	<div class="container-wrapper container-wrapper-faqs container-top">
      <div class="container">
        <div class="row">
          <div class="col-md-12 summary center">
            <h1><?php echo $title; ?></h1>
            <p class="lead">WE LOVE, LOVE, LOVE NAILS!</p>
          </div><!-- end col -->
        </div><!-- end row -->
      </div> <!-- end container -->
    </div> <!-- end container wrapper -->
    
	<div class="container">
	
      <div class="row">
        <div class="col-md-12">
        <div id="accordion" class="panel-group">
            <div class="panel panel-default">
            <?php
            $faqs = array(
            		array('question' => 'Will I be charged to use this service?', 'answer' => 'NailsPolished does <b>not</b> charge a service fee for booking appointments or using the app, it’s FREE!'),
            		array('question' => 'Will my nail salon charge me a fee for using NailsPolished?', 'answer' => 'Nope! And if they do please let us know at help@nailspolished.com'),
            		array('question' => 'PolishedPay, is it safe?', 'answer' => 'YES!!! It is actually safer than presenting your card at your favorite salon. We use the most robust solution in the industry which provides an inline frame with a transparent redirect and token processing. PCI Compliant servers capture and store the credit card data in our secure encrypted card vault.'),
            		array('question' => 'How do I find out how many Polished Points I have?', 'answer' => 'Simply log in to your account online or through our mobile app and click on My Polished Points.'),
            		array('question' => 'What happens if I no show an appointment?', 'answer' => 'No-shows are quite disruptive to a nail salon business, so if any user accumulates five (5) no-shows reservations within the same twelve-month period their NailsPolished account will automatically be deactivated and your Polished Points will go… bye bye. To prevent no-shows on your account, make sure that you always <span class="be-font-underline">modify or cancel your reservation</span> if your plans change.'),
            		array('question' => 'Can I use my Polished Points anywhere?', 'answer' => 'Yes! As long as your nail salon is part of the NailsPolished Network.'),
            		array('question' => 'Do my Gift Cards Expire?', 'answer' => 'NailsPolished gift cards never expire. Yay!!! '),
            		array('question' => 'Why isn’t my nail salon in the NailsPolished Network?', 'answer' => 'Ask them to call us!!! We have a simple and easy membership process, they can be set up in minutes!'),
            		array('question' => 'My questions have not been answered?', 'answer' => 'Feel free to contact us anytime at help@nailspolished.com')
            	);
            $i = 0;
            foreach($faqs as $faq) {
            	$i++;
            ?>
              <div class="panel-heading">
                <a href="#collapse<?php echo $i; ?>" data-parent="#accordion" data-toggle="collapse" class="panel-toggle">
                  <span class="be-font-s20 pink">Q. <?php echo $faq['question']; ?></span>
                </a>
              </div><!-- end panel-heading -->
              <div class="panel-collapse collapse in" id="collapse<?php echo $i; ?>" style="height: auto;">
                <div class="panel-body">
                  <p class="be-font-s14">A: <?php echo $faq['answer']; ?></p>
                </div><!-- end panel-body -->
              </div><!-- end panel-collapse -->
            <?php
            }
            ?>
            </div><!-- end panel -->
          </div><!-- end panel-group -->
        </div><!-- end col -->
       </div>
       
      </div>
      
      <script type="text/javascript">
      $(document).ready(function() {
        $('.dropdown-toggle').dropdown();
        $('[data-toggle=tooltip]').tooltip();
        $('[data-toggle=popover]').popover({html:true});
        $('#myTab a').click(function (e) {
          e.preventdefault();
          $(this).tab('show');
        });
      });
    </script>