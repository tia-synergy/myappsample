<?php
	include_once "../db_connect.php";
	include "functions.php";
	
	session_start();
	if( !isset($_SESSION['id']) )
	{
		header("location:index.php");
	}
	
	$id = $_SESSION['id'];
	
	$selectHeartbeat = "select * from tb_heartbeat";
	$resHeartbeat = mysql_query($selectHeartbeat);
	$selectquestion = "select * from tb_questions";
	$resselectquestion = mysql_query($selectquestion);
	
	// pagination
							$perpage = 3;
							if(isset($_GET["page"]))
							{
								$page = intval($_GET["page"]);
							}
							else 
							{
								$page = 1;
							}
							$calc = $perpage * $page;
							$start = $calc - $perpage;
							
							
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Survey Ballot Admin | Dashboard</title>
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
		<!-- Bootstrap 3.3.4 -->
		<link href="../_layout/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<!-- Font Awesome Icons -->
		<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
		<!-- Ionicons -->
		<link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
		<!-- Theme style -->
		<link href="css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
		<!-- AdminLTE Skins. Choose a skin from the css/skins 
			 folder instead of downloading all of them to reduce the load. -->
		<link href="../_layout/css/_all-skins.min.css" rel="stylesheet" type="text/css" />

		<link href="../_layout/css/jquery-ui.css" rel="stylesheet" type="text/css" />
	
	<link href="../_layout/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="../_layout/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
	<link href="../_layout/css/_all-skins.min.css" rel="stylesheet" type="text/css" />
	
	<!-- /// Favicons ////////  -->
    <link rel="shortcut icon" href="favicon.png">
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="apple-touch-icon-144-precomposed.png">

	<!-- /// Google Fonts ////////  -->
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800">
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic">
    
    <!-- /// FontAwesome Icons 4.2.0 ////////  -->
	<link rel="stylesheet" href="../_layout/css/fontawesome/font-awesome.min.css">
    
    <!-- /// Custom Icon Font ////////  -->
    <link rel="stylesheet" href="../_layout/css/iconfontcustom/icon-font-custom.css">  
    
	<!-- /// Template CSS ////////  -->
    <link rel="stylesheet" href="../_layout/css/base.css">
    <link rel="stylesheet" href="../_layout/css/grid.css">
    <link rel="stylesheet" href="../_layout/css/elements.css">
    <link rel="stylesheet" href="../_layout/css/layout.css">


    
	<!-- /// JS Plugins CSS ////////  -->
	<link rel="stylesheet" href="../_layout/js/revolutionslider/css/settings.css">
	<link rel="stylesheet" href="../_layout/js/revolutionslider/css/custom.css">
    <link rel="stylesheet" href="../_layout/js/bxslider/jquery.bxslider.css">
    <link rel="stylesheet" href="../_layout/js/magnificpopup/magnific-popup.css">
    <link rel="stylesheet" href="../_layout/js/animations/animate.min.css">
	<link rel="stylesheet" href="../_layout/js/itplayer/css/YTPlayer.css">
	<style>
		input[type=text] {
    //width: 130px;
    box-sizing: border-box;
    border: 2px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
    background-color: white;
    background-image: url('searchicon.png');
    background-position: 10px 10px; 
    background-repeat: no-repeat;
    padding: 12px -1px 12px 40px;
    -webkit-transition: width 0.4s ease-in-out;
    transition: width 0.4s ease-in-out;
}
	</style>
		
	</head>
	<body class="skin-blue sidebar-mini">
		<!-- Site wrapper -->
		<div class="wrapper">
			<?php
			top();
			?>
			<!-- Left side column. contains the sidebar -->
			<?php
			left_nav();
			?>
			<!-- =============================================== -->
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<section class="content-header">
					<h1>All Survey</h1>
					<ol class="breadcrumb">
						<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
						<li class=""><a href="dashboard.php">Dashboard</a></li>
						<li class="active">All Survey</li>
					</ol>
				</section>
				<!-- Main content -->
				<section class="content">
					<div>
				<?php
				if(mysql_num_rows($resHeartbeat)==0)
				{
				?>
					<div>
						No Survey sent yet. Send your first survey to all active customers.
						<div style="float:right;">
							<form class="fixed" name="heartbeat-form" name="heartbeat-form" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
								<input id="heartbeat-submit" name="heartbeat-submit" type="submit" class="btn" value="Send First Survey" />
							</form>
						</div>
					</div>
				<?php
				}
				else
				{
				?>
					<div>
						<?php
						$selectCusHis = "select * from tb_customerhistory where operation='heartbeatsent'";
						$resSelectCusHis = mysql_query($selectCusHis);
						
						$selectResponse = "select * from tb_customerheartbeat where status='responsereceived'";
						$resResponse = mysql_query($selectResponse);
						if(mysql_num_rows($resResponse)==0)
						{
						?>
							<div align="center">
								<h4 style="color:red;">Your survey has been sent out. Waiting for first Response.</h4>
							</div>
						<?php
						}
						else
						{
							$heartbeatSent = mysql_num_rows($resSelectCusHis);
							$heartbeatReceived = mysql_num_rows($resResponse);
							$heartbeatPending = $heartbeatSent - $heartbeatReceived;
							
							$selectMaxResponseDate = "select max(opdate) as lastdate from tb_customerhistory where  operation='responsereceived'";
							$resMaxResponseDate = mysql_query($selectMaxResponseDate);
							$rowMaxResponseDate = mysql_fetch_array($resMaxResponseDate);
							
							$lastResponseDate = $rowMaxResponseDate['lastdate'];
							
							$selectdatediff = "select DATEDIFF(CURDATE(),'$lastResponseDate') AS DiffDate, TIMESTAMPDIFF(HOUR,'$lastResponseDate',NOW()) AS DiffHour, TIMESTAMPDIFF(MINUTE,'$lastResponseDate',NOW()) AS DiffMin";
							$resdatediff = mysql_query($selectdatediff);
							$rowdatediff = mysql_fetch_array($resdatediff);
							// echo "DiffDate: ".$rowdatediff['DiffDate'];
							// echo "DiffHour: ".$rowdatediff['DiffHour'];
							
							$diffstring = "";
							if($rowdatediff['DiffDate']>0)
							{
								$diffstring = $rowdatediff['DiffDate']." days ago";
							}
							elseif($rowdatediff['DiffHour']>0)
							{
								$diffstring = $rowdatediff['DiffHour']." hours ago";
							}
							elseif($rowdatediff['DiffMin']>0)
							{
								$diffstring = $rowdatediff['DiffMin']." minutes ago";
							}
							
							//$selectHeartbeatData = "select * from tb_customerheartbeat where client_id='$client_id' and status='responsereceived'";
							$selectHeartbeatData = "select * from tb_customerheartbeat where  status='responsereceived'";
							$resHeartbeatData = mysql_query($selectHeartbeatData);
							$totalResponse = 0;
							$question1_total = 0;
							$question2_total = 0;
							$question3_total = 0;
							$question4_total = 0;
							$question5_total = 0;
							$question6_total = 0;
							$question7_total = 0;
							$question8_total = 0;
							$question9_total = 0;
							$question10_total = 0;
							
							$loveyou_total = 0;
							$likeyou_total = 0;
							$notsatisfied_total = 0;
							
							$loveyou_percent = 0;
							$likeyou_percent = 0;
							$notsatisfied_percent = 0;
							
							$excellent = 0;
							$satisfied = 0 ;
							$not_satisfied = 0;
							while($rowHeartbeatData=mysql_fetch_array($resHeartbeatData))
							{
								$totalResponse = $totalResponse + 1;								
								$question1_total = $question1_total + $rowHeartbeatData['question1_response'];
								$question2_total = $question2_total + $rowHeartbeatData['question2_response'];
								$question3_total = $question3_total + $rowHeartbeatData['question3_response'];
								$question4_total = $question4_total + $rowHeartbeatData['question4_response'];
								$question5_total = $question5_total + $rowHeartbeatData['question5_response'];
								$question6_total = $question6_total + $rowHeartbeatData['question6_response'];
								$question7_total = $question7_total + $rowHeartbeatData['question7_response'];
								$question8_total = $question8_total + $rowHeartbeatData['question8_response'];
								$question9_total = $question9_total + $rowHeartbeatData['question9_response'];
								$question10_total = $question10_total + $rowHeartbeatData['question10_response'];
								
								
								
								if($rowHeartbeatData["overall_response"] >= 9)
								{
									$excellent = $excellent + 1;
								}
								elseif($rowHeartbeatData["overall_response"] < 9 && $rowHeartbeatData["overall_response"] >= 6)
								{
									$satisfied = $satisfied + 1;
								}
								elseif($rowHeartbeatData["overall_response"] < 6)
								{
									$not_satisfied = $not_satisfied + 1;
								}
							}
							
							for($j=1; $j<=10; $j++)
							{
								$quename = "question".$j."_response";
								$total = "total".$j;
								$selectCount = "select count(*) as $total from tb_customerheartbeat where  $quename!=''";
								$resSelectCount = mysql_query($selectCount);
								$recSelectCount = mysql_fetch_array($resSelectCount);
							
								$$total = $recSelectCount["$total"];
							}
							
							$question1_value = $question1_total/$total1;
							$question2_value = $question2_total/$total2;
							$question3_value = $question3_total/$total3;
							$question4_value = $question4_total/$total4;
							$question5_value = $question5_total/$total5;
							$question6_value = $question6_total/$total6;
							$question7_value = $question7_total/$total7;
							$question8_value = $question8_total/$total8;
							$question9_value = $question9_total/$total9;
							$question10_value = $question10_total/$total10;
							//$date = '2018-05-11 14:31:39';
							//echo date('h:i:s a m/d/Y', strtotime($date));
							
							
						?>
							<div>
								<h3 style="margin-bottom:-5px;"><?php echo $heartbeatReceived;?> of <?php echo $heartbeatSent;?> Survey Received</h3>Last received about <?php echo $diffstring;?> <?php if($heartbeatPending>0){ echo ', '.$heartbeatPending.' Pending'; } ?>
							</div>
							<div class="row">
								<!--<div class="span4" style="margin-top:20px;" align="center">-->
								<div class="" style="margin-top:20px;" align="center">
									<div>
									
										<?php 
										$i=1;
										
										while($rowquestion = mysql_fetch_array($resselectquestion))
										{
											$divid = "sub".$i;
											$value = 5;
											if($i==1)
											{
												$color = "#00c0ef";
												$value = number_format($question1_value,2);
											}
											elseif($i==2)
											{
												$color = "#00a65a";
												$value = number_format($question2_value,2);
											}
											elseif($i==3)
											{
												$color = "#f39c12";
												$value = number_format($question3_value,2);
											}
											elseif($i==4)
											{
												$color = "#dd4b39";
												$value = number_format($question4_value,2);
											}
											elseif($i==5)
											{
												$color = "#1F618D";
												$value = number_format($question5_value,2);
											}
											elseif($i==6)
											{
												$color = "#A569BD";
												$value = number_format($question6_value,2);
											}
											elseif($i==7)
											{
												$color = "#17A589";
												$value = number_format($question7_value,2);
											}
											elseif($i==8)
											{
												$color = "#5D6D7E";
												$value = number_format($question8_value,2);
											}
											elseif($i==9)
											{
												$color = "#EC7063";
												$value = number_format($question9_value,2);
											}
											elseif($i==10)
											{
												$color = "#7E5109";
												$value = number_format($question10_value,2);
											}
											
											if($rowquestion['question_name']!='')
											{
												
												//if($i%2 != 0)
												{
												?>
													<!--<div class="span6" style="margin-left:0px;">-->
												<?php
												}
											?>
												<div class="span2" style="margin-top:20px;float:left;margin-left:0px;margin-right:45px;">
													<a href="#" title="<?php echo $rowquestion['question'];?>" style="text-decoration:none;">
														<input type="text" class="knob" style="display:inline-block;" value="<?php echo $value;?>" data-width="100" data-height="100" data-fgColor="<?php echo $color;?>" data-readonly="true" data-min="0" data-max="10" data-angleOffset="180"/>
														<div class="knob-label">
														<?php 
																echo $rowquestion['question_name']."<br>";
																$last_received = 0;
																$count1 = 0;
																	$selectQue2 = "select * from tb_selectedquestions";
																	$resSelectQue2 = mysql_query($selectQue2);
																	while($recSelectQue2 = mysql_fetch_array($resSelectQue2))
																	{
																			
																			if($recSelectQue2["question_id"] == $rowquestion["id"])
																			{
																				
																				$selectCust2 = "select * from tb_customerheartbeat where email_token='".$recSelectQue2['token']."' && status='responsereceived' " ;
																				
																				$resSelectCust2 = mysql_query($selectCust2);
																				$recSelectQue2= mysql_fetch_array($resSelectCust2);
																				$cust_count = mysql_num_rows($resSelectCust2);
																				if($cust_count == 1)
																				{
																					$last_received = $recSelectQue2['response_date'];
																					$count1 = $count1 + 1;
																				}
																				
																			}
																		
																
																	}
																echo "<label style='color:black;'>".$count1." customers responded</label>";
																if($last_received != 0)
																	echo "<label style='color:black;'>Last received:<br>".$last_received."</label>";
														?>
														</div>
														
													</a>
													
												</div>
												
												<input type="hidden"  name="que_title_<?php echo $i; ?>" id="que_title_<?php echo $i; ?>" value="<?php echo $rowquestion['question_name'];?>"/>
												<div style="display:none;">
													<div id="<?php echo $divid;?>" style="width:250px;">
														<?php 
																echo $rowquestion['question'];
														?>      
													</div>
												</div>
												
												<?php
												
												//if($i%2 == 0)
												{
												?>
												<!--	</div>-->
												<?php
												}
												if($i == 5)
												{
													?>
													<div style="clear:both;"></div>
													<?php
												}
												$i++;
											}
										}
										
										?>
										
									</div>									
								</div>
								
								<div style="clear:both;margin-bottom:25px;"></div>
								<div class="" align="center">
									<div class="rate">
									<a href="customers.php?recommend=1"><h4 style=""><?php echo $excellent;?></h4></a>
									<p>EXCELLENT</p>
									</div>
									<!--<div class="divider single-line"></div>-->
									<div class="rate">
									<a href="customers.php?recommend=2"><h4 style=""><?php echo $satisfied;?></h4></a>
									<p>SATISFIED</p>
									</div>
									<!--<div class="divider single-line"></div>-->
									<div class="rate">
									<a href="customers.php?recommend=3"><h4 style=""><?php echo $not_satisfied;?></h4></a>
									<p>NOT SATISFIED</p>
									</div>
								</div>
								<div style="clear:both;margin-bottom:25px;"></div>
							</div>
						<?php
						}
						?>
					</div>
				<?php
				}
				?>
				
				<div class="span6" style="margin-top:20px;margin-bottom:20px;">
						<?php 
						if (!empty($_REQUEST['term'])) {
							$term=$_REQUEST['term'];
							$selectHeartbeatData = "select a.* from tb_customerheartbeat a, tb_customer b where a.customer_id=b.id and (b.customer_firstname LIKE '%".$term."%' or b.customer_lastname LIKE '%".$term."%') && a.overall_response < 6 && a.overall_response != 0 Limit $start, $perpage";
						}else{
							
							$selectHeartbeatData = "select * from tb_customerheartbeat where  status='responsereceived' ORDER BY send_date DESC Limit $start, $perpage";
						}
						$resHeartbeatData = mysql_query($selectHeartbeatData);
						$customeratrisk = 0; 
						while($rowHeartbeatData=mysql_fetch_array($resHeartbeatData))
						{
							//if($rowHeartbeatData['question1_response']<7 || $rowHeartbeatData['question2_response']<7 || $rowHeartbeatData['question3_response']<7 || $rowHeartbeatData['question4_response']<7 || $rowHeartbeatData['question5_response']<7 || $rowHeartbeatData['question6_response']<7 || $rowHeartbeatData['question7_response']<7 || $rowHeartbeatData['question8_response']<7 || $rowHeartbeatData['question9_response']<7 || $rowHeartbeatData['question10_response']<7)
							if($rowHeartbeatData['overall_response'] < 6)		
							{
								$customeratrisk = $customeratrisk + 1;
							}
						}
						?>
						<p><a href="customers.php?type=atrisk">You have <?php echo $customeratrisk;?> customer at risk.</a></p>
						<form action="" method="post">
						<input type="text" name="term" value="" placeholder="Search name here..." style="float:left;margin-right: 12px;" >
						<input type="submit" value="Submit" style="border: 2px solid #ccc;font-size: 16px;" />
						
						</form>
						
						<?php 
						$selectHeartbeatData1 = "select * from tb_customerheartbeat where  status='responsereceived' and overall_response < 7 ORDER BY send_date DESC";
						$resHeartbeatData1 = mysql_query($selectHeartbeatData1);
						$total_row1 = mysql_num_rows($resHeartbeatData1);
						$resHeartbeatData = mysql_query($selectHeartbeatData);
						while($rowHeartbeatData=mysql_fetch_array($resHeartbeatData))
						{
							//if($rowHeartbeatData['question1_response']<7 || $rowHeartbeatData['question2_response']<7 || $rowHeartbeatData['question3_response']<7 || $rowHeartbeatData['question4_response']<7 || $rowHeartbeatData['question5_response']<7 || $rowHeartbeatData['question6_response']<7 || $rowHeartbeatData['question7_response']<7 || $rowHeartbeatData['question8_response']<7 || $rowHeartbeatData['question9_response']<7 || $rowHeartbeatData['question10_response']<7)
							if($rowHeartbeatData['overall_response'] < 6)	
							{
								$selectCustomer = "select * from tb_customer where id=".$rowHeartbeatData['customer_id'];
								$resCustomer = mysql_query($selectCustomer);
								$rowCustomer = mysql_fetch_array($resCustomer);
								
								$dateSubmitted = date_parse($rowHeartbeatData['response_date']);
								
								$question1_percent = ($rowHeartbeatData['question1_response']*100)/10;
								$question2_percent = ($rowHeartbeatData['question2_response']*100)/10;
								$question3_percent = ($rowHeartbeatData['question3_response']*100)/10;
								$question4_percent = ($rowHeartbeatData['question4_response']*100)/10;
								$question5_percent = ($rowHeartbeatData['question5_response']*100)/10;
								$question6_percent = ($rowHeartbeatData['question6_response']*100)/10;
								$question7_percent = ($rowHeartbeatData['question7_response']*100)/10;
								$question8_percent = ($rowHeartbeatData['question8_response']*100)/10;
								$question9_percent = ($rowHeartbeatData['question9_response']*100)/10;
								$question10_percent = ($rowHeartbeatData['question10_response']*100)/10;
							?>
								<div class="span2" style="margin-left:0px;">
									<a href="customer-detail.php?id=<?php echo $rowCustomer['id'];?>"><?php echo $rowCustomer['customer_firstname'].' - '.$rowCustomer['customer_company']; ?></a>
									<br /><small>
									<?php echo "Submitted ".$dateSubmitted['day'].' '.get_month($dateSubmitted['month']).', '.$dateSubmitted['year'];
									?></small>
								</div>
								<div class="span3" style="margin-left:50px;">
									<div class="span1" style="margin-left:0px;">
										<?php echo $rowHeartbeatData['overall_response']; ?>
									</div>
									<div class="span2">
										<div class="progress xs">
											<div class="progress-bar progress-bar-blue" style="width: <?php echo $question1_percent.'%';?>;"></div>
										</div>
										<div class="progress xs">
											<div class="progress-bar progress-bar-green" style="width: <?php echo $question2_percent.'%';?>;"></div>
										</div>
										<div class="progress xs">
											<div class="progress-bar progress-bar-yellow" style="width: <?php echo $question3_percent.'%';?>;"></div>
										</div>
										<div class="progress xs">
											<div class="progress-bar progress-bar-red" style="width: <?php echo $question4_percent.'%';?>;"></div>
										</div>
										<div class="progress xs">
											<div class="progress-bar progress-bar-grey" style="width: <?php echo $question5_percent.'%';?>;background-color:#1F618D;"></div>
										</div>
										<div class="progress xs">
											<div class="progress-bar progress-bar-purple" style="width: <?php echo $question6_percent.'%';?>;background-color:#A569BD;"></div>
										</div>
										<div class="progress xs">
											<div class="progress-bar progress-bar-orange" style="width: <?php echo $question7_percent.'%';?>;background-color:#17A589;"></div>
										</div>
										<div class="progress xs">
											<div class="progress-bar progress-bar-lightgreen" style="width: <?php echo $question8_percent.'%';?>;background-color:#5D6D7E;"></div>
										</div>
										<div class="progress xs">
											<div class="progress-bar progress-bar-darkblue" style="width: <?php echo $question9_percent.'%';?>;background-color:#EC7063;"></div>
										</div>
										<div class="progress xs">
											<div class="progress-bar progress-bar-red" style="width: <?php echo $question10_percent.'%';?>;background-color:#7E5109;"></div>
										</div>
									</div>
								</div>
								<div class="divider single-line" style="margin-top:160px;"></div>
								
						<?php
							}
						}
						?>
						
					</div><!-- end .span6 -->
				</div>
				</section><!-- /.content -->
			</div><!-- /.content-wrapper -->
			<footer class="main-footer">
				<strong>Copyright &copy; 2015 <a href="http://www.synergyit.ca/">Synergy IT</a>.</strong> All rights reserved.
			</footer>
            <!-- Add the sidebar's background. This div must be placed immediately after the control sidebar -->
			<div class='control-sidebar-bg'></div>
		</div><!-- ./wrapper -->

		<!-- jQuery 2.1.4 -->
		<script src="../_layout/js/vendor/fastclick.js"></script>

	<!-- /// jQuery ////////  -->
	<script src="../_layout/js/jQuery-2.1.3.min.js"></script>
	<script src="../_layout/bootstrap/js/bootstrap.min.js"></script>
	<script src="../_layout/js/knob/jquery.knob.js" type="text/javascript"></script>
	
	<script src="../_layout/js/foundation.min.js"></script>
    
	<!-- /// ViewPort ////////  -->
	<script src="../_layout/js/viewport/jquery.viewport.js"></script>    
    <!-- /// Easing ////////  -->
	<script src="../_layout/js/easing/jquery.easing.1.3.js"></script>
    <!-- /// SimplePlaceholder ////////  -->
	<script src="../_layout/js/simpleplaceholder/jquery.simpleplaceholder.js"></script>
    <!-- /// Fitvids ////////  -->
    <script src="../_layout/js/fitvids/jquery.fitvids.js"></script>
    <!-- /// Animations ////////  -->
    <script src="../_layout/js/animations/animate.js"></script> 
    <!-- /// Superfish Menu ////////  -->
	<script src="../_layout/js/superfish/hoverIntent.js"></script>
    <script src="../_layout/js/superfish/superfish.js"></script>
    <!-- /// Revolution Slider ////////  -->
    <script src="../_layout/js/revolutionslider/js/jquery.themepunch.tools.min.js"></script>
    <script src="../_layout/js/revolutionslider/js/jquery.themepunch.revolution.min.js"></script> 
    <!-- /// bxSlider ////////  -->
	<script src="../_layout/js/bxslider/jquery.bxslider.min.js"></script>
   	<!-- /// Magnific Popup ////////  -->
	<script src="../_layout/js/magnificpopup/jquery.magnific-popup.min.js"></script>
    <!-- /// Isotope ////////  -->
	<script src="../_layout/js/isotope/imagesloaded.pkgd.min.js"></script>
	<script src="../_layout/js/isotope/isotope.pkgd.min.js"></script>
    <!-- /// Parallax ////////  -->
	<script src="../_layout/js/parallax/jquery.parallax.min.js"></script>
	<!-- /// EasyPieChart ////////  -->
	<script src="../_layout/js/easypiechart/jquery.easypiechart.min.js"></script>
	<!-- /// YTPlayer ////////  -->
	<script src="../_layout/js/itplayer/jquery.mb.YTPlayer.js"></script>
	
    <!-- /// Easy Tabs ////////  -->
    <script src="../_layout/js/easytabs/jquery.easytabs.min.js"></script>	
    
    <!-- /// Form validate ////////  -->
    <script src="../_layout/js/jqueryvalidate/jquery.validate.min.js"></script>
    
	<!-- /// Form submit ////////  -->
    <script src="../_layout/js/jqueryform/jquery.form.min.js"></script>
    
    <!-- /// Twitter ////////  -->
	<script src="../_layout/js/twitter/twitterfetcher.js"></script>
	
	<!-- /// Custom JS ////////  -->
	<script src="../_layout/js/plugins.js"></script>	
	<script src="../_layout/js/scripts.js"></script>
<!-- Bootstrap 3.3.2 JS -->
		<script src="../_layout/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
		<!-- SlimScroll -->
		<script src="../_layout/js/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
		<!-- FastClick -->
		<script src='../_layout/js/fastclick/fastclick.min.js'></script>
		<!-- AdminLTE App -->
		<script src="../_layout/js/app.min.js" type="text/javascript"></script>
		
		<!-- Demo -->
		<script src="../_layout/js/demo.js" type="text/javascript"></script>	
		<script type="text/javascript">
// Get the modal
var modal = document.getElementById('myModal');
var lightbox = document.getElementsByClassName('lightbox');
//alert(lightbox[0]);
//alert(modal);
// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
	//alert(event.target);
	//alert(lightbox[0]);
    if (event.target == modal) {
        modal.style.display = "none";
    }
	
	 /*if (event.target == lightbox[0]) {
		 lightbox[0].style.display = "none";
		 //alert("2q3");
    }*/
}
	

$(document).ready(function(){
			
			$(window).click(function(e) {
				//alert(e.target.className);
				if(e.target.className == "lightbox")
				{
					$('.lightbox').css( "display", "none" );
					$(location).attr('href','dashboard.php');
				}
				
			});
			
		
			
			$( document ).tooltip({
			  show: null,
			  position: {
				my: "center bottom",
				at: "left+30 top-80"
			  },
			  open: function( event, ui ) {
				ui.tooltip.animate({ top: ui.tooltip.position().top + 10 }, "fast" );
			  }
			});
			
			var tabload = '<?php echo $tabload;?>';
			$('#tabs-container').easytabs({
				animationSpeed: 300,
				defaultTab: "#"+tabload
			});
			
			// $(".question").click(function (e) {
				// e.preventDefault();
				// $('#questionFormScreen').modal();
			// });
			
			$(".knob").knob({
			  /*change : function (value) {
			   //console.log("change : " + value);
			   },
			   release : function (value) {
			   console.log("release : " + value);
			   },
			   cancel : function () {
			   console.log("cancel : " + this.value);
			   },*/
			  draw: function () {

				// "tron" case
				if (this.$.data('skin') == 'tron') {

				  var a = this.angle(this.cv)  // Angle
						  , sa = this.startAngle          // Previous start angle
						  , sat = this.startAngle         // Start angle
						  , ea                            // Previous end angle
						  , eat = sat + a                 // End angle
						  , r = true;

				  this.g.lineWidth = this.lineWidth;

				  this.o.cursor
						  && (sat = eat - 0.3)
						  && (eat = eat + 0.3);

				  if (this.o.displayPrevious) {
					ea = this.startAngle + this.angle(this.value);
					this.o.cursor
							&& (sa = ea - 0.3)
							&& (ea = ea + 0.3);
					this.g.beginPath();
					this.g.strokeStyle = this.previousColor;
					this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false);
					this.g.stroke();
				  }

				  this.g.beginPath();
				  this.g.strokeStyle = r ? this.o.fgColor : this.fgColor;
				  this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false);
				  this.g.stroke();

				  this.g.lineWidth = 2;
				  this.g.beginPath();
				  this.g.strokeStyle = this.o.fgColor;
				  this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false);
				  this.g.stroke();

				  return false;
				}
			  }
			});
			
			
	});	
			
			
	
		</script>
	</body>
</html>