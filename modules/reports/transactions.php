<?php
	if($access->level < 1){
		alert("/nbc", "No access");
	}
?>
<style type="text/css">
	@media print {
		body{
			background-color: white;
		}
		#reportg, #reportg * {
			visibility: visible;
		}
		#fees label{
			font-size: 12px !important;
		}
		label{
			font-size: 12px !important;
		}
		#fees div{		
			margin-top: -10px !important;
		}
		p{
			position: absolute;
			z-index: 999;
			left: 2%;
			font-size: 12px !important;
			top: 0%;
			color: black !important;
		}
		#imgx, #pbot{
			position: absolute;
			z-index: 999;
			right: 0%;
			font-size: 12px !important;
			bottom: 0%;
			color: black !important;
		}
		table th, table td{
			font-size: 10px !important;
			padding: 3px !important;
		}
		#header{
			font-size: 12px !important;
		}
		.card{
			width: 99%;
		}
		#head{
			margin-top: -140px;
		}
	}
</style>
<div class="breadcrumbs">
	<div class="col-sm-4">
		<div class="page-header float-left">
			<div class="page-title">
				<h1><?php echo $xx . $title2; ?></h1>
			</div>
		</div>
	</div>
	<div class="col-sm-8">
		<div class="page-header float-right">
			<div class="page-title">
				<ol class="breadcrumb text-right">
					<li><a href="./">Dashboard</a></li>
					<li><a href="<?php echo $_GET['module'];?>"><?php echo $xx; ?></a></li>
					<li class="active"><?php echo $title;?></li>
				</ol>
			</div>
		</div>
	</div>
</div>
<div class="content mt-3">
	<div class="animated fadeIn">
		<div class="row">
			<div class="col-lg-12">
				<form action="" method="get">
					<div class="card">
						<div class="card-header">
							<strong class="card-title"><?php echo $title; ?></strong>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-3">
									<label>Date From <font color = "red"> * </font></label>
									<input required <?php if(isset($_GET['date_fr'])){ echo ' value = "' . $_GET['date_fr'] . '" '; } ?> autocomplete = "off" name = "date_fr" type = "date" class = "input-sm form-control-sm form-control" aria-required = "true" aria-invalid = "false">
								</div>
								<div class="col-3">
									<label>Date To <font color = "red"> * </font></label>
									<input required <?php if(isset($_GET['date_to'])){ echo ' value = "' . $_GET['date_to'] . '" '; } ?> autocomplete = "off" name = "date_to" type = "date" class = "input-sm form-control-sm form-control" aria-required = "true" aria-invalid = "false">
								</div>
								<div class="col-3">
									<label>Shift <font color = "red"> * </font></label>
									<select class="form-control form-control-sm input-sm" name = "shift">
										<option value=""> All </option>
										<option <?php if(isset($_GET['shift']) && $_GET['shift'] == 'Day'){ echo ' selected '; } ?> value="Day">Day</option>
										<option <?php if(isset($_GET['shift']) && $_GET['shift'] == 'Night'){ echo ' selected '; } ?> value="Night">Night</option>
									</select>
								</div>
								<div class="col-3">
									<label>Product</label>
									<select class="form-control form-control-sm input-sm" name = "product">
										<option value=""> All </option>
										<option <?php if(isset($_GET['product']) && $_GET['product'] == '1,2'){ echo ' selected '; } ?> value="1,2"> Free Meal </option>
										<?php
											$products = "SELECT * FROM products ORDER BY name";
											$products = $conn->query($products);
											if($products->num_rows > 0){
												while ($rows = $products->fetch_object()) {
													$select = "";
													if(isset($_GET['product']) && $_GET['product'] == $rows->product_id){
														$select = ' selected ';
													}
													echo '<option ' . $select . ' value = "' . $rows->product_id . '"> ' . $rows->name . ' </option>';
												}
											}
										?>
									</select>
								</div>
							</div>
						</div>
						<div class="card-footer">
							<button type = "submit" onclick = "return confirm('Are you sure?');" name = "submit" class = "btn btn-info btn-block btn-sm">
								<i class = "fa fa-dot-circle-o"></i>&nbsp;<span>Generate</span>
							</button>
						</div>
					</div>
				</form>
				<?php
					$close = "";
					if(isset($_GET['date_fr']) && isset($_GET['date_to'])){
						$where = "";
						$shift_fr = " 05:00:00"; $shift_to = " 04:59:59";
						if(isset($_GET['shift']) && $_GET['shift'] != ""){
							if($_GET['shift'] == 'Day'){
								$shift_fr = " 05:00:00";
								$shift_to = " 16:59:59";
							}else{
								$shift_fr = " 17:00:00";
								$shift_to = " 04:59:59";
								if($_GET['date_fr'] == $_GET['date_to']){
									$shift_to = " 23:59:59";
								}
							}							
						}else{
							$_GET['date_to'] = date("Y-m-d", strtotime("+1day", strtotime($_GET['date_to'])));
						}
						if(isset($_GET['product']) && $_GET['product'] != ""){
							$where = " and product_id in (" . mysqli_real_escape_string($conn, $_GET['product']) . ") ";
						}
						$prod = "SELECT * FROM transactions as a left join members as b ON a.rfid_no = b.rfid_no WHERE a.dttm BETWEEN '" . mysqli_real_escape_string($conn, $_GET['date_fr']) . $shift_fr . "' and '" . mysqli_real_escape_string($conn, $_GET['date_to']) . $shift_to . "' and a.active = 2 " . $where . " GROUP BY a.transaction_id  ORDER BY a.dttm";
						//$prod = "SELECT * FROM transactions as a WHERE a.dttm BETWEEN '" . mysqli_real_escape_string($conn, $_GET['date_fr']) . $shift_fr . "' and '" . mysqli_real_escape_string($conn, $_GET['date_to']) . $shift_to . "' and a.active = 2 " . $where . " ORDER BY dttm";

						$prod = $conn->query($prod);
						if($prod->num_rows > 0){
							$close = "window.close();";				?>
					<div <?php if(isset($_GET['print'])){ ?> id="reportg" <?php } ?>>
						<img <?php if(isset($_GET['print'])){ ?> style = "position: absolute; margin-top: -140px; margin-left: 100px;" src="<?php echo "http://$_SERVER[HTTP_HOST]/".$pagename;?>/images/logo.jpg?<?php echo rand(1,100).rand(1,1000);?>" height = "100px" width = "100px" <?php } ?>>
						<div align="center" id = "head">
							<b style="font-size: 18px;">NBC (Philippines) Car Technology</b><br>
							<b style="font-size: 14px;">Lot 9-B FPIP II Special Economic Zone</b><br>
							<b style="font-size: 14px;">Sto. Tomas, Batangas 4234</b><br><br>
						</div>
						<br>
						<div class="card">			
							<div class="card-header">
								<h4 class="text-CENTER spacing"><center><b>TRANSACTIONS REPORT From <?php echo ddate($_GET['date_fr']) . ' ' . $shift_fr . ' - ' . ddate($_GET['date_to']) . ' ' . $shift_to; ?></b></center></h4>
								<a id = "backs" class="btn btn-success btn-sm pull-right" target = "_blank" href="reports/transaction_rep?print&date_fr=<?php echo $_GET['date_fr'];?>&date_to=<?php echo $_GET['date_to'];?>"  style="margin-right: 5px;"><span class="fa fa-print"></span> Print </a>
								<a id = "backs" class="btn btn-warning btn-sm pull-right" target = "_blank" href="export.php?date_fr=<?php echo $_GET['date_fr'];?>&date_to=<?php echo $_GET['date_to'];?>&trans2_export&product=<?php echo $_GET['product'];?>&shift=<?php echo $_GET['shift'];?>"  style="margin-right: 5px;"><span class="fa fa-save"></span> Export </a>
							</div>
							<div class="card-body">
								<table class="table">
									<thead  <?php if(!isset($_GET['print'])){ ?> class="thead-dark" <?php } ?>>
										<tr>
											<th scope="col" width="20%">Date</th>
											<th scope="col" width="15%">Receipt</th>
											<th scope="col" width="15%">Emp. No.</th>
											<th scope="col" width="30%">Name</th>
											<th scope="col" width="15%">Emp. Type</th>
											<th scope="col" width="20%">Department</th>
											<th scope="col" width="20%">Type</th>	
											<th scope="col" width="20%">Trans. Type</th>
											<th scope="col" width="10%">Qty</th>
											<th scope="col" width="20%">Amount</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$total = 0;
											while ($row = $prod->fetch_object()) {
												$trans_type = "";
												if($row->product_id <= 2){
													$trans_type = "FREE MEAL";
												}elseif($row->product_id > 2 && $row->type == 'Direct' && $row->isload == 0){
													$trans_type = "SD";
												}elseif($row->isload == 1 && $row->type == 'Direct'){
													$trans_type = "NBCLoad";
												}elseif($row->isload == 1 && $row->type == 'Agency'){
													$trans_type = "AgencyLoad";
												}
												echo '<tr>';
													echo '<td>' . date("m/d/Y h:i A", strtotime($row->dttm)) . '</td>';
													echo '<td>' . $row->receipt . '</td>';
													echo '<td>' . $row->emp_no . '</td>';
													echo '<td>' . $row->member_name . '</td>';
													echo '<td>' . $row->type . '</td>';
													echo '<td>' . $row->department . '</td>';
													echo '<td>' . $row->product_name . '</td>';
													echo '<td>' . $trans_type . '</td>';
													echo '<td>' . $row->qty . '</td>';
													echo '<td>' . number_format($row->amount,2) . '</td>';
												echo '</tr>';
											}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				<?php }else{ echo '<h4 align = "center"><i><b> No record found </i></b></h4>'; } }?>
			</div>
		</div>
	</div>
</div>
<?php
	if(isset($_GET['print'])){
		echo '
		<script type = "text/javascript"> 
			window.onload = function() {						 
				setTimeout(function() { window.print(); '.$close.' window.location.href = "reports"; }, 500); 
			};
		</script>';			 
	}

?>