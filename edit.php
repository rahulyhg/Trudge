<!DOCTYPE html>
<html>
	<head>
		<meta name="robots" content="noindex">
		<title>Trudge Edit</title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<style>
			body { padding:0; margin:0; }
			span { display:block; padding:5px; border-bottom:1px solid #333333; }
			.options { text-align:center; }
			#contents {
				width:100%;
				height:700px;
			}
		</style>
		<script language="Javascript" type="text/javascript" src="edit_area/edit_area_full.js"></script>
		<script>
			editAreaLoader.init({
				id: "contents",
				start_highlight: true,
				allow_resize: "none",
				allow_toggle: false,
				language: "en",
				syntax: "php"
			});
		</script>
	</head>
	<body>
		<?php
			$current_file = $_GET['path'];
			echo('<span class="current">' . $current_file . '</span>');
			echo('<span class="options">');
			echo('<input id="raw_button" type="button" value="Raw" />');
			echo('<input id="save_button" type="button" value="Save" />');
			echo('</span>');
			echo('<textarea id="contents">');
			echo(htmlentities(file_get_contents($current_file)));
			echo('</textarea>');
		?>
        <script language="javascript">
            $('#save_button').click(function() {
                $.ajax({
                    type:'POST',
                     url:'do.php',
                    data:{do:'save',path:"<?php echo(str_replace('\\', "\\\\", $_GET['path'])) ?>",value:editAreaLoader.getValue('contents')}
                });
            });
            
            $('#raw_button').click(function() {
                window.open('raw.php?path=<?php echo(str_replace('\\', "\\\\", $_GET['path'])) ?>');
            });
        </script>
	</body>
</html>