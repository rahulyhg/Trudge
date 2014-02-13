<!DOCTYPE html>
<html>
	<head>
		<meta name="robots" content="noindex">
		<title>Trudge</title>
	</head>
	<body>
		<?php
            $output = shell_exec('ls');
            echo('<pre>' . $output . '</pre>');
		?>
	</body>
</html>