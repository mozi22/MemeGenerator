<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Meme generator</title>
</head>

<body>

    <div style="width:40%;margin:30px auto;" >
        <table>
            <tr>
                <td colspan="2" align="center">
                    <img src="testing.jpg" id="gag" />
                </td>
            </tr>
            <tr>
                <td>
                	<input type="text" placeholder="Upper Text" id="upperTextbox"/>	
                </td>
                <td>
                	<input type="text" placeholder="Lower Text" id="lowerTextbox"/>	
                </td>
            </tr>
            <tr>
            	<!-- td><p style="font-size:14px;color:#CCC;">Image updates on textbox focus out</p></td -->
				<td colspan="2" align="center">
					<button id="Generate" onClick="memeIt();"><big>Go!</big></button>
				</td>
            </tr>
        </table>
    </div>
<div id="error"></div>
</body>
</html>
<script src="jquery.js" type="text/javascript" ></script>
<script>

	/*
	$('#lowerTextbox').focusout(function(){
		if($.trim($(this).val()) != "")
		{
			SendAjaxRequest($('#upperTextbox').val(),$(this).val(),"lower");
		}
	});
	$('#upperTextbox').focusout(function(){
		if($.trim($(this).val()) != "")
		{
			SendAjaxRequest($(this).val(),$('#lowerTextbox').val(),"upper");
		}
	});
	/**/
	
	function memeIt() {
		SendAjaxRequest( $('#upperTextbox').val(), $('#lowerTextbox').val());
	}
	
	function SendAjaxRequest(umsg,dmsg,type='both')
	{
		$.ajax({
			url: 'generator.php',
			type: 'GET',
			data: { upmsg : umsg, downmsg : dmsg, position: type },
			success:function(e)
			{
				$('#error').html(e);
				$('#gag').attr('src',e);
			}
		});
	}
</script>
