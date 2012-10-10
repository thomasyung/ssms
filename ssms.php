<?php 

// Variable declarations
$thisscript = "ssms.php";
$thisurl = "https://servername/".$thisscript; // Change this to your server that this script is running on
$uuid = uniqid(); // Generate the unique message identifier
$messageid = Null; // Hold the message id from the GET request
$message = Null; // The actual message itself
$viewed = Null; // 0=unviewed, 1=viewed
$ipaddress = Null; // Track IP address of computer used to view message
$timestamp = Null; // Track timestamp of when message was viewed

// Connect to MySQL database
$con = mysql_connect('localhost','root','password'); // Change the MySql connection information here
if (!$con) {
	die('Could not connect: ' . mysql_error());
}
mysql_select_db("ssms", $con); // connect to the database table "ssms"

// Custom function to retrieve IP address of viewer
function get_ip_address() {
	foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
		if (array_key_exists($key, $_SERVER) === true) {
			foreach (explode(',', $_SERVER[$key]) as $ip) {
				if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
					return $ip;
				}
			}
		}
	}
}

// Retrieve the message from the database based on the messageid passed to us in the GET request
if (isset($_GET['messageid'])) {
	$messageid = $_GET['messageid'];
	if (strlen($messageid) != 13) {
		echo('Invalid message id. Must be 13 chars only.');
		mysql_close($con);
		exit;
	}
	$result = mysql_query("select * from messages where id='".$messageid."'");
	while ($row = mysql_fetch_array($result)) {
		if ($row['viewed'] == 0) {
			$message = $row['message'];
			// zero out the message once it's been read
			mysql_query("update messages set viewed=1, timestamp='".date(DATE_RFC822)."', ipaddress='".get_ip_address()."', message='0' where id='".$messageid."'");
		}
		else {
			$ipaddress = $row['ipaddress'];
			$timestamp = $row['timestamp'];
		}
	}
}

// Save the message that was posted from the form
if (isset($_POST['msg'])) {
	if (base64_decode($_POST['msg'], true)) {
		mysql_query("insert into messages (id, message, viewed) values ('".$uuid."','".$_POST['msg']."','0')");
	}
}

// clean up MySQL connection
mysql_close($con);
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width">
	<title>Secure single-use message system</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script src="jquery.base64.min.js"></script>
	<script>
	$(function(){
		<?php if (isset($messageid)) { ?>
			<?php if (isset($message)) { ?>
				var msg = "<?php echo $message; ?>";
				var sentences = $.base64.decode(msg).match(/[^\.!\?]+[\.!\?]+/g);
				$("#play").click(function(e){
					e.preventDefault();
					var queue = $.Deferred();
					queue.resolve();
					$.each(sentences, function(i, sentence){
						queue = queue.pipe(function(){
							return $("#playmsg").show().html(sentence).fadeOut(5000);
						});
					});
				});
			<?php } ?>
		<?php } else { ?>
		$("#secureform").submit(function(){
			if ($("#msg").val() != "") {
				$("#msg").val($.base64.encode($("#msg").val()));
			} else {
				alert("Message is empty. Please fill out your message.");
				$("#msg").focus();
				return false;
			}
		});
		<?php } ?>
	});
	</script>
	<style>
	body {
		background: #fff;
		font-family: Georgia, serif;
		font-size: 0.75em;
	}
	h1, h2, h3 {
		font-family: Roboto, sans-serif;
	}
	label {
		font-family: Roboto, sans-serif;
		font-weight: bold;
	}
	#container {
		max-width: 800px;
		margin: 0 auto;
		padding: 1em;
	}
	#msg {
		width: 100%;
		background: #f0ffff;
	}
	#playmsg {
		margin: 1em 0;
		font-size: 1.2em;
		background: #f0ffff;
	}
	.highlight {
		background: yellow;
	}
	input[type=submit] {
		margin-top: 1em;
		padding: 0.8em;
		background: #000;
		color: #fff;
	}
	</style>
</head>
<body>
<div id="container">
	<h1>SSMS</h1>
	<h3>Secure single-use message system</h3>
	<hr>
	<?php if (isset($_POST['msg'])) { ?>
		<p>Copy and paste this URL.</p>
		<span class="highlight"><?php echo $thisurl; ?>?messageid=<?php echo $uuid; ?></span>
		<p><b>Note:</b> Do not go to this URL in your browser or else the message will no longer be viewable by your recipient.</p>
		<p>To create another single-use message, click <a href="<?php echo $thisscript; ?>">here</a>.</p>
	<?php } else { ?>
		<p> 
		<?php if (isset($messageid)) { ?>
			<b>Note:</b> The contents of this message can only be viewed once. If you came here, and the message is not viewable, then this message has already been read. Contents are destroyed on the server as soon as the message has been read.
		<?php } else { ?>
			<b>Note:</b> Use this form to generate a secure single-use message that can only be read ONCE by the recipient. There is no identifying information other than what what you put into the text box. When you are done composing your message, click the button to generate a unique URL that you can send to your intended recipient. Contents of the message are destroyed on the server as soon as the message has been retrieved.
		<?php } ?>
		</p>
		<?php if (isset($timestamp) && isset($ipaddress)) { ?>
			<p class="highlight">This message was read on <?php echo $timestamp; ?> from computer ip address <?php echo $ipaddress; ?>.</p>
		<?php } ?>
		<form id="secureform" action="<?php echo $thisscript; ?>" method="post">
			<label for="msg">Your message:</label>
			<?php if (isset($messageid)) { ?>
				<?php if (isset($message)) { ?>
					<button id="play">Play message</button>
					<div id="playmsg"></div>
				<?php } ?>
				<p>To create your own single-use message, click <a href="<?php echo $thisscript; ?>">here</a>.</p>
			<?php } else { ?>
				<textarea id="msg" name="msg" rows="15"></textarea>
				<input type="submit" value="Save message and generate link">
			<?php } ?>
		</form>
	<?php } ?>
</div>
</body>
</html>
