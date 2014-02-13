<!DOCTYPE html>
<html>
	<head>
		<meta name="robots" content="noindex">
		<title>Trudge Terminal</title>
        <style>
            * { box-sizing:border-box; -moz-box-sizing:border-box; }
            body { background-color:black; color:white; height:100%; margin:0; padding:0; }
            #console {
                position:absolute;
                top:0;
                right:0;
                bottom:20px;
                left:0;
                width:100%;
                background-color:black;
                overflow-y:scroll;
            }
            #console_input {
                display:block;
                position:absolute;
                bottom:0;
                left:0;
                right:0;
                width:100%;
            }
            #input {
                width:100%;
                border:none;
            }
        </style>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	</head>
	<body>
        <div id="console"><div id="console_bottom"></div></div>
        <div id="console_input">
            <input id="input" type="text" />
        </div>
        <script language="javascript">
            $(document).ready(function() {
                $('#input').keyup(function(e) {
                    if (e.keyCode == 13) {
                        $.ajax({
                            type:'POST',
                             url:'x.php',
                            data:{clc:$(this).val()}
                        }).done(function(res) {
                            $('#console_bottom').before('<pre class="output"><strong>' + $('#input').val() + ':</strong><br />' + res + '</pre');
                            document.getElementById('console_bottom').scrollIntoView(true);
                            $('#input').val('');
                        });
                    }
                });
            });
        </script>
	</body>
</html>