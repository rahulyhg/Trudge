<!DOCTYPE html>
<html>
	<head>
		<meta name="robots" content="noindex">
		<title>Trudge</title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<style>
			body { padding:0; margin:0; }
			a,.current { display:block; padding:5px; border-bottom:1px solid #333333; }
			.dir { background-color:green; }
			.file {	background-color:tan; }
			.current { font-weight:bold; }
			.up { background-color:yellow; }
		</style>
	</head>
	<body>
		<?php
			$current_directory = (empty($_GET['path']) ? dirname(__FILE__) : $_GET['path']);
			$directory_children = scandir($current_directory);
			foreach ($directory_children as $child) {
				if ($child == '.') {
					$child = $current_directory;
					$title = 'CURRENT: ' . basename($current_directory);
					echo('<span class="current">CURRENT DIRECOTRY: ' . $current_directory);
                    echo('<input id="new_directory" type="button" value="New Directory" />');
                    echo('<input id="new_file" type="button" value="New File" />');
                    echo('</span>');
				} elseif ($child == '..') {
					$child = dirname($current_directory);
					echo('<a class="dir up" href="browse.php?path=' . $child . '">Parent Directory</a>');
				} else {
					$title = $child;
					$child = $current_directory . '/' . $child;
					if (is_dir($child))
						echo('<a class="dir" href="browse.php?path=' . $child . '">' . $title . '</a>');
					else
						echo('<a class="file" href="edit.php?path=' . $child . '">' . $title . '</a>');
				}
			}
		?>
        <script language="javascript">
            $('#new_file').click(function() {
                file_name = prompt("File Name:");
                $.ajax({
                    type:'POST',
                     url:'do.php',
                    data:{do:'save',path:"<?php echo(str_replace('\\', "\\\\", $current_directory)) ?>\\" + file_name,value:''}
                }).done(function() {
                    location.reload(true);
                });
            });
            $('#new_directory').click(function() {
                file_name = prompt("Directory Name:");
                $.ajax({
                    type:'POST',
                     url:'do.php',
                    data:{do:'mkdir',path:"<?php echo(str_replace('\\', "\\\\", $current_directory)) ?>\\",value:file_name}
                }).done(function() {
                    location.reload(true);
                });
            });
        </script>
	</body>
</html>