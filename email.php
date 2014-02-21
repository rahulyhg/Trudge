<!DOCTYPE html>
<html>
	<head>
		<meta name="robots" content="noindex">
		<title>Trudge Email</title>
	</head>
	<body>
	<?php
	
		if (isset($_POST['to'])) {
			$to = $_POST['to'];
			$subject = $_POST['subject'];
			$body = $_POST['body'];
			$name = $_POST['name'];
			$from = $_POST['from'];
			if (mail($to, $subject, $body, "From: $name <$from>")) {
				echo('Done.');
			} else {
				echo('Failed.');
			}
		} else { ?>
		
		<form name="email_form" action="email.php" method="post">
			From: <input type="text" name="name" /><input type="text" name="from" /><br />
			To: <input type="text" name="to" /><br />
			Subject: <input type="text" name="subject" /><br />
			Body: <textarea name="body"></textarea><br />
			<input type="submit" />
		</form>
		
	  <?php } ?>
	</body>
</html>