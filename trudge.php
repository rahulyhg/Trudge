<?php

	define('VERSION', '0.13');
	define('REALM_NAME', 'Trudge ' . VERSION);

	function get_auth() {
		header('WWW-Authenticate: Basic realm="' . REALM_NAME . '"');
		header('HTTP/1.0 401 Unauthorized');
	}
	
	function check_auth($user, $pass) {
		if (empty($_SERVER['PHP_AUTH_USER']) || ($_SERVER['PHP_AUTH_USER'] !== $user || $_SERVER['PHP_AUTH_PW'] !== $pass)) {
			get_auth();
			exit;
		}
	}
	
	check_auth('trudge', 'Duc@ti748');
	
	function unifyURL($url) {
		return str_replace('\\', '/', $url);
	}
	
	function delTree($dir) { 
		$files = array_diff(scandir($dir), array('.','..')); 
		foreach ($files as $file) { 
			(is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file"); 
		} 
		return rmdir($dir); 
	}
	
	function get_mime_type($file_extension) {
	
		$mime_types = array(
			"pdf"=>"application/pdf"
			,"exe"=>"application/octet-stream"
			,"zip"=>"application/zip"
			,"docx"=>"application/msword"
			,"doc"=>"application/msword"
			,"xls"=>"application/vnd.ms-excel"
			,"ppt"=>"application/vnd.ms-powerpoint"
			,"gif"=>"image/gif"
			,"png"=>"image/png"
			,"jpeg"=>"image/jpg"
			,"jpg"=>"image/jpg"
			,"mp3"=>"audio/mpeg"
			,"wav"=>"audio/x-wav"
			,"mpeg"=>"video/mpeg"
			,"mpg"=>"video/mpeg"
			,"mpe"=>"video/mpeg"
			,"mov"=>"video/quicktime"
			,"avi"=>"video/x-msvideo"
			,"3gp"=>"video/3gpp"
			,"css"=>"text/css"
			,"jsc"=>"application/javascript"
			,"js"=>"application/javascript"
			,"php"=>"text/html"
			,"htm"=>"text/html"
			,"html"=>"text/html"
		);
		
		return $mime_types[strtolower($file_extension)];
	}
	
	if (isset($_REQUEST['action'])) {
		switch($_REQUEST['action']) {
			case 'logout':
				get_auth();
				
				break;
			case 'directory':
				try {
					$directory_current  = $_REQUEST['path'];
					$directory_children = array_diff(scandir($directory_current), array('.', '..'));
					
					$folders = array();
					$files = array();
					
					foreach ($directory_children as $child) {
						if (is_dir($directory_current . '/' . $child))
							$folders[] = $child;
						else
							$files[] = $child;
					}
					
					header('Content-Type: application/json; charset=utf-8');
					
					$del = '';
					
					echo('[');
					foreach ($folders as $folder) {
						echo($del . "{\"dir\":true,\"name\":\"$folder\",\"extension\":\"\"}");
						$del = ',';
					}
					foreach ($files as $file) {
						echo($del . "{\"dir\":false,\"name\":\"$file\",\"extension\":\"" . (strpos($file, '.') ? pathinfo($file, PATHINFO_EXTENSION) : '') . "\"}");
						$del = ',';
					}
					echo(']');
				} catch (Exception $e) {
					echo('no.');
				}
				
				break;
			case 'preview':
				$directory_current = $_REQUEST['path'];
				$file_current      = $_REQUEST['file'];
				$extension_current = $_REQUEST['extension'];
				
				$TYPE_IMAGE = array('png', 'jpg', 'jpeg', 'gif', 'ico');
				$TYPE_TEXT  = array('txt', 'css', 'less', 'sass', 'php', 'js', 'htm', 'html', 'aspx', 'bat', 'sh', 'py', 'json', 'yaml', 'yml', 'xml', 'xhtml', 'dat', 'log');
				
				$WHITE_LISTED_NAMES = array('.htaccess', 'error_log');
				
				$file_content_type = 'text/plain';
				$can_save = false;
				
				if (in_array($extension_current, $TYPE_IMAGE)) {
					$file_content_type = 'image/' . $extension_current;
					echo('<img style="max-width:100%;max-height:100%;" src="data:image/' . $extension_current . ';base64,' . base64_encode(file_get_contents($directory_current . '/' . $file_current)) . '" />');
				} elseif (in_array($extension_current, $TYPE_TEXT) xor in_array($file_current, $WHITE_LISTED_NAMES)) {
					$file_content_type = 'text/plain';
					echo('<textarea id="editable_file_field" style="resize:none;height:90%;width:100%;">' . htmlentities(file_get_contents($directory_current . '/' . $file_current)) .'</textarea>');
					$can_save = true;
				} else {
					$new_content_type = get_mime_type($extension_current);
					$file_content_type = ($new_content_type !== '' ? $new_content_type : $file_content_type);
					echo("<i>Unknown File Type</i>");
				}
				
				echo('<div style="margin-top:0.5em;text-align:right">');
				echo('	<button id="download_file" onclick="download_file(\'' . $directory_current . '\', \'' . $file_current . '\', \'' . $file_content_type . '\')">Download File</button>');
				if ($can_save) {
					echo('	<button id="save_file" onclick="save_file(\'' . $directory_current . '\', \'' . $file_current . '\')">Save Changes</button>');
				}
				echo('</div>');
				
				break;
			case 'save':
				$directory_current = $_REQUEST['path'];
				$file_current      = $_REQUEST['file'];
				$contents_current  = $_REQUEST['content'];
				
				file_put_contents($directory_current . '/' . $file_current, $contents_current);
				
				break;
			case 'delete':
				$directory_current = $_REQUEST['path'];
				$file_current      = $_REQUEST['file'];
				
				unlink($directory_current . '/' . $file_current);
				break;
			case 'mkdir':
				$directory_current = $_REQUEST['path'];
				$file_current      = $_REQUEST['file'];
				
				mkdir($directory_current . '/' . $file_current);
				break;
			case 'rmdir':
				$directory_current = $_REQUEST['path'];
				$file_current      = $_REQUEST['file'];
				
				delTree($directory_current . '/' . $file_current);
				break;
			case 'rename':
				$directory_current = $_REQUEST['path'];
				$file_old          = $_REQUEST['old_file'];
				$file_new          = $_REQUEST['new_file'];
				
				rename($directory_current . '/' . $file_old, $directory_current . '/' . $file_new);
				break;
			case 'upload':
				$directory_current = $_REQUEST['path'];
				move_uploaded_file($_FILES['file']['tmp_name'], $directory_current . '/' . $_FILES['file']['name']);
			
				break;
			case 'compress':
				$directory_current   = $_REQUEST['path'];
				$selected_filepath   = $_REQUEST['selected_filename'];
				$compressed_filename = $_REQUEST['compressed_filename'];
				
				exec('zip -r ' . $directory_current . '/' .  $compressed_filename . ' ' . $directory_current . '/' .  $selected_filepath);
				
				break;
			case 'decompress':
				$directory_current = $_REQUEST['path'];
				$selected_filepath = $_REQUEST['selected_filename'];
				
				exec('unzip ' . $directory_current . '/' . $selected_filepath . ' -d ' . $directory_current);
				
				break;
			case 'execute':
				$directory_current = $_REQUEST['path'];
				$run_command	   = $_REQUEST['command'];
				
				try {
					chdir($directory_current);
					$command_output = shell_exec($run_command);
					echo($run_command . '<pre>' . ($command_output != '' ? $command_output : 'Done.') . '</pre>');
				} catch (Exception $e) {
					echo($run_command . '<pre><strong>' . $e->getMessage() . '</strong></pre>');
				}
				
				break;
			case 'download':
				$full_path    = $_REQUEST['directory'];
				$filename     = $_REQUEST['filename'];
				$content_type = $_REQUEST['content_type'];
				
				header("Content-disposition: attachment; filename=" . $filename);
				header("Content-type: " . $content_type);
				
				echo(file_get_contents($full_path . '/' . $filename));
				
				break;
		}
		exit;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta name="robots" content="noindex">
		<title>Trudge</title>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<style>
			*{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;}
			body{padding:0;margin:0;height:100%;font-family:sans-serif;}
			button{margin-left:0.75em;}
			#display{position:absolute;width:100%;height:100%;margin:0;padding-bottom:9px;}
			#display td{padding:0 9px;position:relative;vertical-align:top;}
			
			.window{border:1px solid #DDDDDD;padding:9px;position:absolute;left:9px;right:9px;top:50px;bottom:9px;}
			.scroll{overflow-y:scroll;}
			.link{cursor:pointer;color:#0000CC;line-height:32px;}
			.link>.clicker:hover{color:#CC0000;}
			.section{padding:9px;border-bottom:1px solid #EEEEEE;}
			.header{font-weight:bold;border:1px solid #DDDDDD;border-bottom:none;padding:9px;display:inline-block;}
			.section .link{padding-left:18px;}
			.selected{background-color:#E5E5E5;}
			
			.console{background-color:black;color:white;}
		</style>
	</head>
	<body>
		<iframe id="download_frame" style="display:none;"></iframe>
		<table id="display">
			<tr style="height:40px;"><td style="line-height:40px;font-weight:bold;"><!-- TRUDGE INFO -->
				Trudge <?php echo(VERSION); ?>
			</td><td style="text-align:right;vertical-align:middle;"><!-- LOGOUT -->
				<form name="admin_form" method="post" enctype="multipart/form-data">
					<input type="hidden" name="action" value="logout" />
					<input type="submit" value="Clean Logout" />
				</form>
			</td></tr>
			<tr style="height:45%;"><td style="width:75%;padding-top:9px;"><span class="header">Directory: <span id="current_path"></span></span>
				<div id="directory_browse" class="window scroll">
					<div id="parent_dir" class="link section"><img height="32px" width="32px" style="float:left;margin-right:9px;" src="http://ulanders.com/archive/icons/32/folder_green.png" /><span class="clicker">Parent Directory</span></div>
				</div>
			</td><td style="padding-top:9px;"><span class="header">Tools</span>
				<div class="window scroll">
					<div class="section">
						<div class="link" id="reload_directory">Reload Directory</div>
						<div class="link" id="new_file">New File</div>
						<div class="link" id="new_directory">New Directory</div>
						<div class="link" id="delete_file">Delete File</div>
						<div class="link" id="delete_directory">Delete Directory</div>
						<div class="link" id="rename_file">Rename File / Directory</div>
						<div class="link" id="upload_file">Upload File</div>
						<div class="link" id="compress_selected">Compress</div>
						<div class="link" id="decompress_file">Decompress</div>
					</div>
				</div>
			</td></tr>
			<tr><td style="padding-top:9px;"><span class="header">File: <span id="current_file">None</span></span>
				<div class="window" style="overflow:scroll" id="file_preview"></div>
			</td><td style="padding-top:9px;"><span class="header">Output Console</span>
				<div class="console window" style="color:white;overflow-y:scroll;"><div id="console_bottom"></div></div>
			</td></tr>
			<tr style="height:40px"><td colspan="2">
				<input id="command_bar" class="console" placeholder="Execute Command" style="line-height:40px;border:none;width:100%;padding:0 9px;" type="text" name="command" />
			</td></tr>
		</table>
		<div id="msg" style="position:absolute;left:0;top:0;right:0;bottom:0;background-color:rgba(0,0,0,.5);display:none;">
			<div style="margin:auto;display:block;background-color:white;width:400px;margin-top:15%;padding:10px;">
				<span id="title" style="font-weight:bold;"></span>
				<span id="quest" style="display:block;"></span>
				<input type="text" id="popup_value" style="width:100%;margin-top:1em;display:none;" />
				<form id="upload_form" enctype="multipart/form-data">
					<input type="file" id="popup_upload" name="file" style="width:100%;margin-top:1em;display:none;" />
				</form>
				<div style="text-align:right;margin-top:1em;">
					<a href="#close" onclick="$('#msg').hide();">Cancel</a> <button id="exec">Execute</button>
				</div>
			</div>
		</div>
		<script>
			path_current = "<?php echo(unifyURL(dirname(__FILE__))); ?>";
			path_self = "<?php echo(basename(__FILE__)); ?>";
			
			path_backup = "<?php echo(unifyURL(dirname(__FILE__))); ?>";
			
			function send_output(console_output) {
				$('#console_bottom').before('<div><strong>&gt; </strong>' + console_output + '</div>');
                    		document.getElementById('console_bottom').scrollIntoView(true);
			}
			
			function preview_file(file) {				
				$.ajax({type:"GET",url:path_self,data:{'action':'preview', 'path':path_current, 'file':file.name, 'extension':file.extension}})
				.done(function(file_response) {
					$fp = $('#file_preview')
					$fp.empty();
					
					$fp.html(file_response);
					$('#current_file').text(file.name);
				});
			}
			
			function load_directory(announce_reload) {
				$.getJSON(path_self, {'action':'directory', 'path':path_current})
				.success(function(directory_response) {
					$db = $('#directory_browse');
					$db.find('.dir_resp').remove();
					
					$.each(directory_response, function(i, file) {
						var $item = $db.append('<div class="link section dir_resp" dir="' + i + '"><img height="32px" width="32px" style="float:left;margin-right:9px;" src="http://ulanders.com/archive/icons/32/' + (file.dir ? 'folder_blue' : ('file_extension_' + file.extension)) + '.png" /><span class="clicker">' + file.name + '</span></div>');
						(function(scooped_file, $scooped_item) {
							$scooped_item.click(function() {
								$scooped_item.toggleClass('selected');
							});
							$scooped_item.find('.clicker').click(function(e) {
								e.stopPropagation();
								if (file.dir) {
									path_current = path_current + '/' + scooped_file.name;
									load_directory();
								} else {
									preview_file(scooped_file);
								}
							});
						})(directory_response[i], $('.dir_resp[dir="' + i + '"]'));
					});
					if (announce_reload) {
						send_output("Directory Reloaded!");
					}
					path_backup = path_current;
					$('#current_path').text(path_current);
				}).error(function() {
					path_current = path_backup;
					send_output("Can't Access Directory - Permission Denied!")
					$('#current_path').text(path_current);
				});
			}
			
			function show_popup(popup_title, popup_description, popup_type, popup_action) {
				$('#title').html(popup_title);
				$('#quest').html(popup_description);
				
				$text_input = $('#popup_value');
				$file_upload_input = $('#popup_upload');
				
				$('#msg').show();
				
				//  If (popup_type == 0) don't do anything - no input (e.g. notice / warning)
				if (popup_type === 1) {  //  For text input (e.g. filename)
					$text_input.val('');
					$text_input.show();
					
					$text_input.focus();
				} else if (popup_type === 2) {  // For file upload
					//$file_upload_input.replaceWith($file_upload_input.clone()); // Clears the upload field (http://stackoverflow.com/a/1043969)
					$file_upload_input.show();
					
					$file_upload_input.focus();
				}
				
				$('#exec').click(function() {
					$(this).off('click');
					$text_input.hide();
					$file_upload_input.hide();
					$('#msg').hide();
					popup_action(popup_type !== 2 ? $text_input.val() : new FormData($('#upload_form')[0]));
				});
			}
			
			$('#popup_value').keyup(function(e) {
				if (e.keyCode == 13) {
					$('#exec').click();
				}
			});
			
			load_directory(path_current);
			
			is_valid_filename=(function(){
				var rg1=/^[^\\/:\*\?"<>\|]+$/;
				var rg2=/^\./;
				var rg3=/^(nul|prn|con|lpt[0-9]|com[0-9])(\.|$)/i;
				return function is_valid_filename(fname){
					return rg1.test(fname)&&!rg2.test(fname)&&!rg3.test(fname);
				}
			})(); 
			
			//'" (FIXING THAT SYNTAX HIGHLIGHTING)
			
			$('#parent_dir').find('.clicker').click(function() {
				path_current = path_current.substring(0, path_current.lastIndexOf('/'));
				load_directory();
			});
				
			$('#reload_directory').click(function() {
				load_directory(true);
			});
			
			$('#new_file').click(function() {
				show_popup("New File", "Type in the name of the new file:", 1, function(filename) {
					if (is_valid_filename(filename)) {
						$.ajax({type:"GET",url:path_self,data:{'action':'save', 'path':path_current, 'file':filename, 'content':''}})
						.done(function() {
							send_output("New File Created (" + filename + ")!");
							load_directory();
						});
					} else {
						send_output("Invalid File Name!");
					}
				});
			});
			
			$('#new_directory').click(function() {
				show_popup("New Directory", "Type in the name of the new directory:", 1, function(filename) {
					if (is_valid_filename(filename)) {
						$.ajax({type:"GET",url:path_self,data:{'action':'mkdir', 'path':path_current, 'file':filename}})
						.done(function() {
							send_output("New Directory Created (" + filename + ")!");
							load_directory();
						});
					} else {
						send_output("Invalid Directory Name!");
					}
				});
			});
			
			$('#delete_file').click(function() {
				show_popup("Delete File", "Delete file(s)?", 0, function() {
						$('.selected').each(function() {
							var filename = $(this).text();
							$.ajax({type:"GET",url:path_self,data:{'action':'delete', 'path':path_current, 'file':filename}})
							.done(function() {
								send_output("File Deleted (" + filename + ")!");
								load_directory();
							});
						});
				});
			});
			
			$('#delete_directory').click(function() {
				show_popup("Delete Directory", "Delete directory/ies?", 0, function() {
						$('.selected').each(function() {
							var filename = $(this).text();
							$.ajax({type:"GET",url:path_self,data:{'action':'rmdir', 'path':path_current, 'file':filename}})
								.done(function() {
									send_output("Directory Deleted (" + filename + ")!");
									load_directory();
							});
						});
				});
			});
			
			$('#rename_file').click(function() {
				show_popup("Rename File / Directory", "What should the file / directory be renamed?  <i>Note: this will only rename the first file that you have selected.</i>", 1, function(new_filename) {
					if (is_valid_filename(new_filename)) {
						$current_file = $($('.selected').get(0));
						var old_filename = $current_file.text();
						$.ajax({type:"GET",url:path_self,data:{'action':'rename', 'path':path_current, 'old_file':old_filename, 'new_file':new_filename}})
							.done(function() {
								send_output("File / Directory Renamed (" + old_filename + ' -> ' + new_filename + ")!");
								load_directory();
						});
					} else {
						send_output("Invalid File Name!");
					}
				});
			});
			
			$('#upload_file').click(function() {
				show_popup("Upload File", "Please choose the file you would like to upload.  <i>Note: If a file exists with the same name, it will be replaced.</i>", 2, function(upload_form_data) {
					send_output("Uploading file.");
					send_output("This May Take a While...");
					upload_form_data.append('action', 'upload');
					upload_form_data.append('path', path_current);
					$.ajax({
						type:"POST",
						url:path_self,
						xhr: function() {
							var myXhr = $.ajaxSettings.xhr();
							if (myXhr.upload) {
								myXhr.upload.addEventListener('progress', function(e) {
									if (e.lengthComputable) {
										send_output("File Upload @ " + (e.loaded / e.total * 100) + "%...");
									}
								}, false);
							}
							return myXhr;
						},
						data:upload_form_data,
						success: function() {
							send_output("File Successfully Uploaded!");
							load_directory();
						},
						error: function() {
							send_output("File Upload Failed!");
						},
						cache:false,
						contentType:false,
						processData:false
					});
				});
			});
			
			$('#compress_selected').click(function() {
				show_popup("Compress Selected", "What should the archive be called?  <i>Note: Include .zip at the end of the filename.</i>", 1, function(compressed_filename) {
					if (is_valid_filename(compressed_filename)) {
						$current_file = $($('.selected').get(0));
						var selected_filename = $current_file.text();
						send_output("Compressing Selected File/Folder.");
						send_output("This May Take a While...");
						$.ajax({type:"GET",url:path_self,data:{'action':'compress', 'path':path_current, 'selected_filename':selected_filename, 'compressed_filename':compressed_filename}})
							.done(function() {
								send_output("File Compressed (" + compressed_filename + ")!");
								load_directory();
						});
					} else {
						send_output("Invalid Archive Name!");
					}
				});
			});
			
			$('#decompress_file').click(function() {
				$current_file = $($('.selected').get(0));
				var selected_filename = $current_file.text();
				send_output("Decompressing Selected Archive...");
				send_output("This May Take a While...");
				$.ajax({type:"GET",url:path_self,data:{'action':'decompress', 'path':path_current, 'selected_filename':selected_filename}})
					.done(function() {
						send_output("File Decompressed!");
						load_directory();
				});
			});
			
			
			$last_command_used = "";
			$('#command_bar').keyup(function(e) {
				if (e.keyCode == 13) {
					$last_command_used = $.trim($(this).val());
					
					$(this).val(' ');
					
					$.ajax({type:"GET",url:path_self,data:{'action':'execute', 'path':path_current, 'command':$last_command_used }})
						.success(function(command_output) {
							send_output(command_output);
							load_directory();
					});
				} else if (e.keyCode == 38) {
					$(this).val($last_command_used);
				}
			});
			
			$('#command_bar').focus(function() {
				if ($(this).val() == '') {
					$(this).val(' ');
				}
			});
			
			$('#command_bar').blur(function() {
				if ($(this).val() == ' ') {
					$(this).val('');
				}
			});
			
			function download_file(directory, filename, mime_type) {
				send_output("Downloading " + filename + "...");
				$('#download_frame').attr('src', path_self + '?action=download&directory=' + encodeURIComponent(directory) + '&filename=' + encodeURIComponent(filename) + '&content_type=' + encodeURIComponent(mime_type));
			}
			
			function save_file(directory, filename) {
				var new_file_content= $('#editable_file_field').val();
				send_output("Saving " + filename + "...");
				
				$.ajax({
					type:"POST",
					url:path_self,
					data:{'action':'save', 'path':directory, 'file':filename, 'content':new_file_content}
				}).success(function() {
					send_output("Save Complete!");
				});
			}
			
		</script>
	</body>
</html>