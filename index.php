<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Meme generator</title>
<style>
	body { text-align: center; font-size:18px; }
	img { min-width: 384px; }
	input[type="text"]  { width:384px; font-size:inherit; }
	button { width:384px; font-size:inherit; margin: 0 4px; }
	#Share { display:none; }
</style>
</head>

<body>
	
<img src="testing.jpg" id="gag"/> <br/><br/>
<input type="text" placeholder="Upper Text" id="upperTextbox" /> <br/><br/>
<input type="text" placeholder="Lower Text" id="lowerTextbox" /> <br/><br/>
<button id="Generate" onClick="memeIt();" >Go!</button> <br/><br/>
<button id="Share" >Share!</button>
<p id="error"></p>
	
</body>
</html>
<script src="jquery.js" type="text/javascript" ></script>
<script>
	var response;
	function memeIt() 
	{
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
				//console.log(e);
				response=JSON.parse(e);
				//console.log(response);
				//$('#error').html(e);
				var src= response.imageFolder+'/'+response.imageFile+'?c='+ Math.random().toString(36).substring(2, 15);
				$('#gag').attr('src',src);
				
				$('#Share').click( function() {
						
				});
				//$('#Share').css('display','inline');
			}
		});
	}
</script>
