<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package mattys
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-content">
		<?php the_content(); ?>
	</div><!-- .entry-content -->
	<div class="entry-content">
		<div id="mainproductcontainer" class="pagecontent">
			<form action="" name="mattyscartforminfo" id="mattyscartforminfo" class="mattyscartforminfo" method="get">
				<a href="<?php echo home_url(); ?>" class="btn btn-back">Back</a>
				<div id="mainproductcontent" class="mainproductcontent">
					<div id="mainprodlistitems">
						<div id="maincaritems" class="maincaritems ">
							<div id="mainmattyscartformwrap" class="mainmattyscartformwrap bg-white">
								<h3 class="text-green">Fill in your details</h3>
								<div class="form-group">
									<div class="row">
										<div class="col-12 col-md-6">
											<label for="fullname">Full Name</label>
											<input type="text" class="form-control" name="fullname" id="fullname" aria-describedby="fullname">
										</div>
										<div class="col-12 col-md-6">
											<label for="phonenumber">Phone number</label>
											<input type="text" class="form-control" name="phonenumber" id="phonenumber" aria-describedby="phonenumber">
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-12 col-md-6">
											<label for="emailaddress">Email address</label>
											<input type="email" class="form-control" name="emailaddress" id="emailaddress" aria-describedby="emailaddress">
										</div>
										<div class="col-12 col-md-6">
											<label for="companyorganization">Company / Organization</label>
											<input type="text" class="form-control" name="companyorganization" id="companyorganization" aria-describedby="companyorganization">
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-12 col-md-6">
											<label for="totalpeoplecatering">How many people are we catering for?</label>
											<input type="text" class="form-control" name="totalpeoplecatering" id="totalpeoplecatering" aria-describedby="totalpeoplecatering">
										</div>
										<div class="col-12 col-md-6">
											<label class="labeldatetime" for="deliverydate">Date & Time of Pick-up / Delivery</label>
											<div class="datetimewrap">
												<div class="w-100">
													<input type="date" class="form-control" name="deliverydate" id="deliverydate" aria-describedby="deliverydate">
												</div>
												<div class="w-100">
													<div class="datetimeselect">
														<select class="form-select" name="deliverytime" id="deliverytime" aria-describedby="deliverytime">
															<option value="">Select Time</option>
														</select>
													</div>	
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-12 col-md-6">
											<label for="totalpeoplecatering">Pick-up or Delivery</label>
											<div class="radioboxwrap">
												<label class="radiolabel">
													Pick-up
													<input type="radio" class="form-control pickupdelivery" name="pickupdelivery" id="pickup" aria-describedby="pickup" value="Pick-up" checked>
													<span class="radiocheckmark"></span>
												</label>
											</div>
											<div class="radioboxwrap">
												<label class="radiolabel">
													Delivery
													<input type="radio" class="form-control pickupdelivery" name="pickupdelivery" id="delivery" value="Delivery"  aria-describedby="delivery">
													<span class="radiocheckmark"></span>
												</label>
											</div>
										</div>
										<div class="col-12 col-md-6 ">
											<fieldset id="deliveryaddresswrap" disabled>
												<label for="deliveryaddress">Delivery Address</label>
												<textarea name="deliveryaddress" id="deliveryaddress" class="form-control deliveryaddress"></textarea>
											</fieldset>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-12 col-md-6">
											<label for="dietaryrequirements">Dietary Requirements</label>
											<textarea name="dietaryrequirements" id="dietaryrequirements" class="form-control dietaryrequirements"></textarea>
										</div>
										<div class="col-12 col-md-6 ">
											<label for="specialrequest">Special Request</label>
											<textarea name="specialrequest" id="specialrequest" class="form-control specialrequest"></textarea>
										</div>
									</div>
								</div>
								<button type="submit" name="submit" class="btn btnsubmitquery btnmobdisplay" id="btnsubmitquery">SUBMIT ENQUIRY</button>
								<div id="errormessage" class="pt-4"></div>
							</div>
						</div>
					</div>
					<div id="mainsidecartlist">
						<aside id="sidebar" class="sidebar">
							<button type="button" class="btn btn-default btn-closesidebar">
								<img src="<?php echo get_template_directory_uri(); ?>/assets/images/closeicon.svg" alt="">
							</button>
							<div id="sidebarwrapper" class="sidebarwrapper">	
								<div id="sidebarcontent" class="sidebarcontent">
									<h4 class="text-green">YOUR ORDER</h4>
									<div id="sidebarcartitemlist" class="sidebarcartitemlist"></div>
									<div id="maindrinkswrap" class="maindrinkswrap">
										<h4 id="addondrinkstitle" class="addondrinkstitle"></h4>   
										<div id="sidebarcartitemlistdrinks" class="sidebarcartitemlist"></div>  
									</div>  
									<div class="formcartinfo">Additional delivery fee will be charged based on your location, our team will reach out for confirmation.</div>
									<div id="carttotalwrap" class="carttotalwrap">
										<div class="carttotaltext">TOTAL</div>
										<div class="carttotalprice">$0.00</div>
									</div>
								</div>
								<button type="submit" name="submit" class="btn btnsubmitquery" id="btnsubmitquery">SUBMIT ENQUIRY</button>
							</div>
							
							<div id="errormessage" class="pt-4"></div>
							
						</aside>
						<div id="sidebartrigger" class="sidebartrigger"></div>
					</div>

					
					
				</div>
			</form>
		</div>
	</div>
	
	<div class="boxseparator my-0"></div>

</article><!-- #post-<?php the_ID(); ?> -->
