<?php
require 'vendor/autoload.php';
define('SECRET_LOC_TOK','.tokenDat/access_token.json');
define('SECRET_LOC_RTOK','.tokenDat/refreshToken.json');

session_start();
$client = new Google_Client();
$client->setAuthConfigFile('client_secrets.json');
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/project/test.php');
$client->setScopes(array("https://www.googleapis.com/auth/drive","profile","https://spreadsheets.google.com/feeds"));
$client->setAccessType('offline');

if(@$_GET['logout'] == 1)
	{
		unset($_SESSION['access_token']);
		unlink(SECRET_LOC_TOK);
		session_destroy();	
	}
	else
		{
		if(file_exists(SECRET_LOC_TOK) && !isset($_SESSION['access_token']) && isset($_GET['code']))
			{
				$_SESSION['access_token'] = file_get_contents(SECRET_LOC_TOK);
				$_SESSION['refreshToken'] = file_get_contents(SECRET_LOC_RTOK);
				$client->setAccessToken($_SESSION['access_token']);
				header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
			}
		}
if(!isset($_SESSION['access_token']))
		{
		if(isset($_GET['code']))
			{
				$_SESSION['access_token'] = $client->authenticate($_GET['code']);
				$_SESSION['refreshToken'] = json_decode($_SESSION['access_token'])->refresh_token;
				if(!file_exists(dirname(SECRET_LOC_TOK))) 
					{
      					mkdir(dirname(SECRET_LOC_TOK), 0700, true);
    				}
				file_put_contents(SECRET_LOC_TOK,$_SESSION['access_token']);
				file_put_contents(SECRET_LOC_RTOK,$_SESSION['refreshToken']);
				header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
			}
			else
				{
					$auth_url = $client->createAuthUrl();
				}
		
		?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Chart Wizard | Login</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</head>
<body>
 <div class="container" style="text-align:center">
  	<div class="row" style="margin-top:15%">
    	<div class="col-sm-4"></div>
  		<div class="col-sm-4"><pre style="border-radius:5px 5px 0 0">Login</pre></div>
        <div class="col-sm-4"></div>
  	</div>
 <form class="form-horizontal" role="form">
  <div class="form-group">
    <div class="col-sm-2"></div>
      <label class="control-label col-sm-2" for="email">Email:</label>
    <div class="col-sm-4">
      <input type="email" class="form-control" id="email" placeholder="Enter email">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-2"></div>
    <label class="control-label col-sm-2" for="pwd">Password:</label>
    <div class="col-sm-4">
      <input type="password" class="form-control" id="pwd" placeholder="Enter password">
    </div>
  </div>
  <div class="form-group">
  	<div class="col-sm-4"></div>
    <div class="col-sm-2">
      <div class="checkbox">
        <label><input type="checkbox">Remember me</label>
      </div>
    </div>
    <div class="col-sm-1">
      	<button type="submit" class="btn btn-default">Login</button>
    </div>
    <div class="col-sm-1">
      	<button type="submit" class="btn btn-default">SignUp</button>
    </div>
  </div>
  <div class="form-group">
  	<div class="col-sm-4"></div>
    <div class="col-sm-4">
      	<input type="button" class="btn btn-default"  onClick="<?php echo "window.location.href='" . $auth_url . "'"; ?>" value="Google Sign in">
    </div>
  </div>
</form>
 </div>
</body>
</html>
<?php 
		} 
		else
			{
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</head>
<body>
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="test.php">Chart Wizard</a>
			</div>
			<div style="float:right">
				<ul class="nav navbar-nav">
					<li class="active"><a href="test.php">Home</a></li>
					<li><a href="charts.php">Charts</a></li>
					<li><a href="details.html">Details</a></li>
				    <li><a href="http://localhost/project/test.php?logout=1">Exit Chart Wizard</a></li>
				</ul>
			</div>
		</div>
	</nav>
<?php 
				$client->setAccessToken($_SESSION['access_token']);
				if($client->isAccessTokenExpired())
					{
						unlink(SECRET_LOC_TOK);
						unset($_SESSION['access_token']);
					}
				$drive_service = new Google_Service_Drive($client);
				$plus_service = new Google_Service_Plus($client);
				$optParams = array('maxResults' => 2000);
				$mimeSet = 'application/vnd.google-apps.spreadsheet';
				try
					{
						$results = $drive_service->files->listFiles($optParams);
						$user = $plus_service->people->get('me');
					}
					catch(Google_Service_Exception $e)
						{
							if($e->getCode() == 401)
								{
									 ?>
									<div class="container" align="center" style="margin-top:45px">
										<div class="alert alert-danger fade in" style="width:50%">
											<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
											<strong>ERROR 401: Oops!</strong> You are Logged out. Please <a href="http://localhost/project/test.php?logout=1">Login</a> to continue.
										</div>
									</div>
									<iframe src="https://accounts.google.com/logout" style="display: none"></iframe>
									<?php
									unlink(SECRET_LOC_TOK);
								}
						}
						catch(Google_Exception $e)
							{
								?>
									<div class="container" align="center" style="margin-top:45px">
										<div class="alert alert-danger fade in" style="width:50%">
											<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
											<strong>ERROR <?php echo $e->getCode(); ?>: Oops!</strong> <?php  echo $e->message();?>. Please <a href="http://localhost/project/test.php?logout=1">Login</a> to continue.
										</div>
									</div>
									//<iframe src="https://accounts.google.com/logout" style="display: none"></iframe>
									<?php
							}

				$userName = $user['displayName'];
				$userImage = $user['image']['url'];						
				if(!@$e)
					{
					?>
						 <head><title>Chart Wizard | <?php echo $userName; ?> </title></head>
						 <div class="container" align="center">
							 <div class="row">
								 <div class="col-sm-12">
								 <img src="<?php echo $userImage; ?>" class="img-circle" alt="<?php echo $userName;?>" width="100" height="100">
								 </div>
								 <div class="col-sm-12" style="margin-top:20px">
									 <strong>Welcome!</strong><pre style="width:50%"><?php echo strtoupper($userName);?></pre>
								 </div>
							 </div>
						<?php
						echo "<div class='well well-sm' style='width:60%;margin-top:20px;'>CHOOSE A SPREAD SHEET TO CONTINUE IN CHARTS</div><br>";
						if ($results->getItems() == null) 
							{
								echo "There are no files in your Google Drive right now!";
							} 
							else 
								{
				  					$i = 0;
				  					foreach ($results->getItems() as $file) 
										{
				  							if($file->getMimeType() == $mimeSet)
												{
													$fileName[$i] = $file->getTitle();
													$fileUrl[$i] = "https://docs.google.com/spreadsheets/d/" . $file->getId();		
													$i++;
												}
				  						}
										if($i == 0)
										 {
											echo "There are no spreadsheets in your drive right now!";
										 }
							?>
							<div class="form-group" align="left" style="width:60%">
							<?php
							$arrlength = count(@$fileUrl);
							for($x = 0; $x < $arrlength; $x++)
								{?>
									
									<p style="margin-bottom:1px"><button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#myModal<?php echo $x;?>"><span class="glyphicon glyphicon-arrow-right"></span></button>&nbsp;<?php echo $fileName[$x]; ?></p><br>
									<div class="hidden" id="hidden<?php echo $x;?>"><?php echo $fileName[$x];?></div>
									<div id="myModal<?php echo $x;?>" class="modal fade" role="dialog">
										<div class="modal-dialog modal-lg">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title">One more step</h4>
												</div>
												<div class="modal-body">
													<p>You are being redirected to the Spreadsheet. Make sure you click on <kbd>Share</kbd> button and share it with the following email id. <br>email :<strong>1059197860223-knutj1qeb8f4gbbsek950r7ar738jk4o@developer.gserviceaccount.com</strong><br>Also publish file to the web. (<kbd>File</kbd> -> <kbd>Publish to the web</kbd>) If already done you are all set to use Charts!</p>
												</div>
												<div class="modal-footer">
													<div class="form-group"><div class="col-sm-9"></div><div class="col-sm-2"><button type="button" class="btn btn-default" onclick="window.open('<?php echo $fileUrl[$x];?>','_blank')">Spreadsheet</button></div><div class="col-sm-1" style="margin-left:-2%"><form method="post" action="charts.php"><button type="submit" class="btn btn-default" name="sheetName" value="<?php echo $fileName[$x]; ?>">Charts</button></form></div></div>
												</div>
											</div>
										</div>
									</div>
									
						<?php	} 
								}?>
							</div>
						
						
						<?php
					}
						?>
						</div>
</body>
</html>
<?php
			}
?>