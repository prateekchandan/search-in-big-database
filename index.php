<html>
  <head>  
   <title>Voter Search</title>
	 <!-- Latest compiled and minified CSS -->
	 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
	 <link rel="stylesheet" href="//cdn.datatables.net/1.10.5/css/jquery.dataTables.min.css">
  </head>
	<body>
		<div class="container">
	   <h1>Welocme to Voter Search Engine</h1>
	   <hr>

<?php 
$host = "localhost";
$user = "root";
$passd = "Tucansam2112!565Fire";
$db = "voters";
$con = mysqli_connect($host,$user,$passd,$db) or die("error");

$counties = mysqli_query($con,"select * from county");
$county = array();

while($row = mysqli_fetch_assoc($counties))
{
	$county[$row['code']] = $row['name'];
}

$mailstates = mysqli_query($con,"select * from mailstate");
$mailstate = array(''=>'');

while($row = mysqli_fetch_assoc($mailstates))
{
	$mailstate[$row['code']] = $row['text'];
}

foreach($_POST as $key => $value)
{
	$_POST[$key]=mysqli_real_escape_string($con,$value);
}
function table_print($data){
	$limit = 2000;
	global $county;
	global $mailstate;

	echo '<table class="table table-bordered" id="example">';
	echo '<thead><tr><th>State Voter Id</th>';
	echo '<th>Country Voter Id</th>';
	echo '<th>First Name</th>';
	echo '<th>Last Name</th>';
	echo '<th>BirthDate (m/d/y)</th>';
	echo '<th>Gender</th>';
	echo '<th>Zip Code</th>';
	echo '<th>Address</th>';
	echo '<th>City</th>';
	echo '<th>State</th>';
	echo '<th>County</th>';
	echo '</tr></thead><tbody>';

	$i = 0;

	while($row = mysqli_fetch_assoc($data)){
		$i++;
		$address = '';
		for ($j=1; $j <5 ; $j++) { 
			if($row['Mail'.$j]!="")
				$address.=$row['Mail'.$j].'<br>';
		}
		echo '<tr><td>'.$row['StateVoterId'].'</td>';
		echo '<td>'.$row['CountryVoterId'].'</td>';
		echo '<td>'.$row['FName'].'</td>';
		echo '<td>'.$row['LName'].'</td>';
	  	echo '<td>'.$row['BirthDate'].'</td>';
	  	echo '<td>'.$row['Gender'].'</td>';
	  	echo '<td>'.$row['MailZip'].'</td>';
	  	echo '<td>'.$address.'</td>';
	  	echo '<td>'.$row['MailCity'].'</td>';
	  	echo '<td>'.$mailstate[$row['MailState']].'</td>';
	  	echo '<td>'.$county[$row['CountyCode']].'</td>';
	  	echo '</tr>';
	  	if($i>$limit)
	  		break;
	}
	echo '</tbody></table>';
}
if(isset($_POST['type']))
{
	if($_POST['type'] == 'name'){
		$name = trim($_POST['name']);
		$names = explode(" ", $name);
		if(sizeof($names)==2){
			$data = mysqli_query($con , 'select * from active_voters where Fname = "'.$names[0].'"  && Lname = "'.$names[1].'" ');
		}
		else{
			$data = mysqli_query($con , 'select * from active_voters where Fname = "'.$name.'"  || Lname = "'.$name.'" ');
		}
		table_print($data);
	}
	if($_POST['type'] == 'dob'){
		$date = date('m/d/Y',strtotime($_POST['date']));
		$data = mysqli_query($con , 'select * from active_voters where BirthDate = "'.$date.'" ');
		table_print($data);
	}
	if($_POST['type'] == 'county'){
		$countys = $_POST['county'];
		$data = mysqli_query($con , 'select * from active_voters where CountyCode = "'.$countys.'" ');
		table_print($data);
	}
	if($_POST['type'] == 'zip'){
		$zip = $_POST['zip'];
		$data = mysqli_query($con , 'select * from active_voters where RegZipCode = "'.$zip.'" ');
		table_print($data);
	}
	if($_POST['type'] == 'address'){
		$mail1 = $_POST['mail1'];
		$city = $_POST['city'];
		$state = $_POST['state'];
		$coun = $_POST['county'];
		$data = mysqli_query($con , 'select * from active_voters where 
				 CountyCode like "%'.$coun.'%" 
				&& MailState like "%'.$state.'%"
				&& MailCity like "%'.$city.'%"
				&& Mail1 like "%'.$mail1.'%"
				limit 2000' );
		table_print($data);
	}
	echo '
	<hr>
	<b><a href="/">Search Again</a></b>
	<hr>
	';
}
else{
?>
	<div class="row">
		<div class="col-md-4">
			<h4>Search Voters By Name</h4>
			<form method="post" class="form">
				<input type="hidden" name="type" value="name">
				<label>Input Name to search : </label>
				<input name="name" class="form-control" required>
				<p>Input first name , last name or full name</p><br>
				<button class="btn btn-info"> Submit</button>

			</form>
		</div>
		<div class="col-md-4">
			<h4>Search Voters By Date of Birth</h4>
			<form method="post" class="form">
				<input type="hidden" name="type" value="dob">
				<label>Chose DOB : </label>
				<input name="date" class="form-control" type="date" required>
				<p>Format : d-m-y</p>
				<br>
				<button class="btn btn-info"> Submit</button>

			</form>
		</div>
		<div class="col-md-4">
			<h4>Search Voters By County</h4>
			<form method="post" class="form">
				<input type="hidden" name="type" value="county">
				<label>Chose county : </label>
				<select name="county" class="form-control" required>
					<option value=""> Select County </option>
					<?php
					foreach ($county as $key => $value) {
						echo "<option value='".$key."'>".$value."</option>";
					}
					?>
				</select>
				<br>
				<button class="btn btn-info"> Submit</button>

			</form>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<h4>Search Voters By Zip Code</h4>
			<form method="post" class="form">
				<input type="hidden" name="type" value="zip">
				<label>Enter Zip Code : </label>
				<input name="zip" class="form-control"  required>
				<br>
				<button class="btn btn-info"> Submit</button>

			</form>
		</div>
		<div class="col-md-8">
			<h4>Search Voters By Address</h4>
			<form method="post" class="form">
				<input type="hidden" name="type" value="address">
				<label class="col-md-4">Enter Address Line 1 : </label>
				<div class="col-md-8"><input name="mail1" class="form-control" ></div>
				<label class="col-md-4">City : </label>
				<div class="col-md-8"><input name="city" class="form-control" ></div>
				<label class="col-md-4">County : </label>
				<div class="col-md-8">
				<select name="county" class="form-control">
					<option value=""> Select County </option>
					<?php
					foreach ($county as $key => $value) {
						echo "<option value='".$key."'>".$value."</option>";
					}
					?>
				</select>
				</div>
				<label class="col-md-4">state : </label>
				<div class="col-md-8">
					<select name="state" class="form-control">
						<option value=""></option>
						<?php
							$states = mysqli_query($con,"select * from mailstate");
							while($row = mysqli_fetch_assoc($states)){
								echo'
								<option value="'.$row['code'].'">'.$row['text'].'</option>';
							}
						?>
					</select>
				</div>
				
				<p>Note : if you are not sure about some fields , leave them empty</p>
				<br>
				<button class="btn btn-info"> Submit</button>

			</form>
		</div>
		
	</div>
	
<?php } ?>
</div>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
	    $('#example').dataTable();
} );
</script>
</body>
</html> 
