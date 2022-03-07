	<div class="container-wrapper container-top container-wrapper-faqs">
      <div class="container">
        <div class="row">
          <div class="col-md-12 summary center">
            <h1>Salon Owners</h1>
          </div><!-- end col -->
        </div><!-- end row -->
      </div> <!-- end container -->
    </div> <!-- end container wrapper -->
    
	<div class="container">
	
      <div class="row">
        <div class="col-md-12">
          <!-- <h2>SALON OWNERS</h2> -->
          	<h2 class="pink margin-bottom-10">WHY NAILSPOLISHED?</h2>
          	
          	<p>
	          WE ARE EXCLUSIVLY FOR THE NAIL SALON INDUSTRY, AND YOUR CUSTOMERS HAVE BEEN ASKING FOR IT!
	          WE WANT YOU TO STAY POLISHED!
          	</p>
          <div class="alert alert-error">
          <?php
          $owner_features = array(
          		'CLIENTS CAN BOOK SERVICES 24/7 USING OUR SITE AND MOBILE APPS.',
          		'SENDS REMINDERS OF APPOINTMENTS TO REDUCE CUSTOMER NO-SHOWS.',
          		'CUSTOMIZED NAILSPOLISHED PAGE TO SHOWCASE YOUR NAIL SALON’S MENU OF SERVICES.',
          		'ISSUE ELECTRONIC GIFT CARDS FOR YOUR NAIL SALON ONLY.',
          		'ACCEPT THE NAILSPOLISHED GIFT CARD FROM ANYONE WHO HAS ONE.',
          		'ACCEPT NAILSPOLISHED POINTS AND LET US PAY YOU FOR THAT FREE SERIVCE.',
          		'HELPS YOU FIND AND SERVICE EVEN MORE CUSTOMERS, WHICH MEANS MORE $$$$.',
          		'WE BUILD YOUR NAILSPOLISHED PAGE WHICH LETS PEOPLE EASILY ORDER SERVICES FROM YOU ONLINE.',
          		'NO MORE PROCESSING OF CREDIT CARDS FOR NAILSPOLISHED CUSTOMERS. WITH SMUDGE FREE PAYMENTS POWERED BY POLISHEDPAY, YOUR MONEY WILL BE DEPOSITED NEXT DAY.',
          		'WE CONNECT CUSTOMERS TO NAIL SALONS NATIONWIDE. DON’T BE LEFT OUT OF THE NETWORK!'
          	);
          foreach($owner_features as $item) {
          	?>
          	<p class="space-5"></p>
          	<p><i class="fa fa-circle-o be-font-s12 margin-right-10"></i><?php echo $item; ?></p>
          	<p class="space-5"></p>
          	<?php
          }
          ?>
          </div>
        </div><!-- end col -->
       </div>
       
      <div class="row">
        <div class="col-md-12">
          <h2 class="pink">STATISTICS</h2>
          <p>EVERYONE HAS GONE MOBILE</p>
          <div class="alert alert-error">
          <?php
          $owner_statistics = array(
          		'6 OUT OF 7 OF YOUR CUSTOMERS WANT TO ',
          		'ON AVERAGE YOUR CONSUMER SPENDS 3.5 HOURS EACH DAY USING THEIR SMARTPHONES.  WHEN AREN’T YOU ON YOURS?',
          		'CUSTOMERS LOOK AT THEIR PHONES OVER 160 TIMES A DAY.  CONSTANTLY IN HAND WHILE AT NAIL SALONS.',
          		'5 OUT OF 7 CUSTOMERS PREFER TO NOT HAVE TO CALL FOR APPOINTMENTS.',
          		'85% OF TIME ON SMARTPHONES ARE SPENT ON APPS.'
          	);
          foreach($owner_statistics as $item) {
          	?>
          	<p class="space-5"></p>
          	<p><i class="fa fa-circle-o be-font-s12 margin-right-10"></i><?php echo $item; ?></p>
          	<p class="space-5"></p>
          	<?php
          }
          ?>
          </div>
        </div><!-- end col -->
       </div>
       
      </div>