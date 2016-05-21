<!DOCTYPE html>
<html lang="en">
<head>
  <title>Choose Google Spreadsheet</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <!--Bootstrap-->
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <!--Highcharts-->
  <script src="http://code.highcharts.com/highcharts.js"></script>
  <script src="http://code.highcharts.com/modules/data.src.js"></script>
  <script src="http://code.highcharts.com/modules/exporting.js"></script>
  <!--Google Picker API-->
  <script type="text/javascript" src="js/picker.js"></script>
  <script type="text/javascript" src="https://apis.google.com/js/api.js?onload=loadPicker"></script>
  </body> 
</head>
  <body onLoad="">
  	<div class="container">
    	<nav class="navbar navbar-inverse">
        	<div class="container-fluid">
    			<div class="navbar-header">
      				<a class="navbar-brand" href="index.php">Chart Wizard</a>
    			</div>
    			<div style="float:right">
      				<ul class="nav navbar-nav">
        				<li class="active"><a href="index.php">Home</a></li>
        				<li><a href="choose.php">Reload another sheet</a></li>
       					<li><a href="" onClick="logout()">Exit Chart Wizard</a></li>
        				<li><a href="details.html" target="new">Details</a></li>
      				</ul>
    			</div>
  			</div>
		</nav>
    </div>
	<div id="chart">
		<div class="loading">
			Loading data from Google Spreadsheets...
		</div>
	</div>
    <div>
    	
    </div> 
    
</html>