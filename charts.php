<!DOCTYPE html>
<html lang="en">
<head>
	<title>Chart Wizard</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <!--Highcharts-->
    <script src="http://code.highcharts.com/highcharts.js"></script>
    <script src="http://code.highcharts.com/modules/data.src.js"></script>
    <script src="http://code.highcharts.com/modules/exporting.js"></script>
</head>
<body>
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="test.php">Chart Wizard</a>
			</div>
			<div style="float:right">
				<ul class="nav navbar-nav">
					<li><a href="test.php">Choose another sheet</a></li>
					<li class="active"><a href="#" class="disabled">Charts</a></li>
					<li><a href="details.html">Details</a></li>
				    <li><a href="http://localhost/project/test.php?logout=1">Exit Chart Wizard</a></li>
				</ul>
			</div>
		</div>
	</nav>
<?php
require 'spreadsheets/vendor/autoload.php';
require 'vendor/autoload.php';
define('GOOGLE_CLIENT_ID','1059197860223-knutj1qeb8f4gbbsek950r7ar738jk4o.apps.googleusercontent.com');
define('GOOGLE_CLIENT_EMAIL','1059197860223-knutj1qeb8f4gbbsek950r7ar738jk4o@developer.gserviceaccount.com');
define('GOOGLE_SPREADSHEETS_SCOPE','https://spreadsheets.google.com/feeds');
define('GOOGLE_APPLICATION_NAME','Chart Wizard');
define('GOOGLE_KEY_FILE','.servAcc/Chart Wizard-0bc59cb3fc77.p12');

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

session_start();

if(!isset($_SESSION['access_token']))
	{
	 ?>
		<div class="container" align="center" style="margin-top:45px">
			<div class="alert alert-danger fade in" style="width:50%">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					<strong>ERROR 401: Oops!</strong> You are Logged out. Please <a href="http://localhost/project/test.php?logout=1">Login</a> to continue.
			</div>
        </div>
        <!--<iframe src="https://accounts.google.com/logout" style="display: none"></iframe>-->
<?php
	}
	else
		{
		if(!isset($_POST['sheetName']))
			{
				?>
                <div class="container" align="center" style="margin-top:45px">
                    <div class="alert alert-danger fade in" style="width:50%">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <strong>ERROR :</strong> You need to select a sheet to view charts. Please <a href="http://localhost/project/test.php">Go Back</a> to continue.
                    </div>
                </div>
                <!--<iframe src="https://accounts.google.com/logout" style="display: none"></iframe>-->
				<?php	
			}
		else
			{
				function getToken()
					{
						$client = new Google_Client();
						$client->setApplicationName(GOOGLE_APPLICATION_NAME);
						$client->setClientId(GOOGLE_CLIENT_ID);
						$key = file_get_contents(GOOGLE_KEY_FILE);
						$cred = new Google_Auth_AssertionCredentials(GOOGLE_CLIENT_EMAIL,array(GOOGLE_SPREADSHEETS_SCOPE),$key);
						$client->setAssertionCredentials($cred);
						if($client->getAuth()->isAccessTokenExpired()) 
							{
								$client->getAuth()->refreshTokenWithAssertion($cred);
							}
						$service_token = json_decode($client->getAccessToken());
						return $service_token->access_token;
					}
				$_SESSION['token'] = getToken();
				$serviceRequest = new DefaultServiceRequest($_SESSION['token']);
				ServiceRequestFactory::setInstance($serviceRequest);
				$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
				$spreadsheetFeed = $spreadsheetService->getSpreadsheets();
			}
	if(!$spreadsheet = $spreadsheetFeed->getByTitle($_POST['sheetName']))
		{?>
			<div class="container" align="center" style="margin-top:45px">
			<div class="alert alert-danger fade in" style="width:50%">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					<strong>ERROR! :</strong> You have to share the sheet with the given email-id.<a href="http://localhost/project/test.php">Click</a> to continue.
			</div>
        </div>
	<?php	}
	else
		{
			//$worksheetFeed = $spreadsheet->getWorksheets();
			//$worksheet = $worksheetFeed->getByTitle('Sheet1');
			//$listFeed = $worksheet->getListFeed();	
			$url = $spreadsheet->getId();
			$key =  str_replace("https://spreadsheets.google.com/feeds/spreadsheets/private/full/","",$url);
			if(isset($_POST['chartType']))
				{ ?>
					<script>
						var key = "<?php echo $key; ?>";
						var chartType = "<?php echo $_POST['chartType']; ?>";
						var yaxisLabel = "<?php echo $_POST['yaxisLabel']; ?>";
						$.getScript('js/hc.js');
					</script>
			<?php	}
					else
						{ ?>
						<script>
							var key = "<?php echo $key; ?>";
							var chartType = "line";
							var yaxisLabel = "";
							$.getScript('js/hc.js');
						</script>
				<?php }
				
				?>
					<!--HTML START-->

					<div id="chart" style="margin-top:5%">
						<div class="loading">
							<span class="glyphicon glyphicon-repeat spinning"></span> Loading data from Google Spreadsheets...
						</div>
					</div>
					<div class="form-group" align="center" style="margin-top:5%">
						<form action="charts.php" method="post">
						<label>Set chart type : &nbsp;</label>
						<select name="chartType">
						  <option value="bar">bar</option>
						  <option value="area">area</option>
						  <option value="areaspline">areaspline</option>
						  <option value="arearange">arearange</option>
						  <option value="column">column</option>
						  <option value="pie">pie</option>
						  <option value="bubble">bubble</option>
						  <option value="waterfall">waterfall</option>
						</select>
						<label>Set Y-axis label : &nbsp;</label><input type="text" name="yaxisLabel">
						<button type="submit" name="sheetName" value="<?php echo $_POST['sheetName'];?>">Set</button>
						</form>
					</div>
	<?php 
		}
	}	
		 ?>
		  </div>
</body>
</html>