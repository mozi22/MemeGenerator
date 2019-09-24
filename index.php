<?php if ( !isset($_SESSION) ) session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" /> 
<title>Meme generator</title>
<script src="jquery.js" type="text/javascript" ></script>
<script>
	var uploadedFile;
	var processData;
	var response;
	function memeIt() 
	{
		SendAjaxRequest( $('#upperTextbox').val(), $('#lowerTextbox').val());
	}
	
	function SendAjaxRequest(umsg,dmsg,type='both')
	{
		processData={ upmsg : umsg, downmsg : dmsg, position: type, uploadedImage: uploadedFile };
		console.log(processData);
		$.ajax({
			url: 'generator.php',
			type: 'GET',
			data: processData,
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

	
	function fileSelected() {
		var count = document.getElementById('fileToUpload').files.length;
		document.getElementById('progress').innerHTML = "";
		for (var index = 0; index < count; index ++)
		{
			var file = document.getElementById('fileToUpload').files[index];
			var fileSize = 0;
			if (file.size > 1024 * 1024)
				fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
			else
				fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';
			console.log(file);
			console.log('Size: ' + fileSize);
		}
		if (count>0) uploadFile();
	}
	function uploadFile() {
		var fd = new FormData();
			var count = document.getElementById('fileToUpload').files.length;
			for (var index = 0; index < count; index ++)
			{
				var file = document.getElementById('fileToUpload').files[index];
				fd.append('myFile', file);
			}
		var xhr = new XMLHttpRequest();
		xhr.upload.addEventListener("progress", uploadProgress, false);
		xhr.addEventListener("load", uploadComplete, false);
		xhr.addEventListener("error", uploadFailed, false);
		xhr.addEventListener("abort", uploadCanceled, false);
		xhr.open("POST", "savetofile.php");
		xhr.send(fd);
	}
	function uploadProgress(evt) {
		if (evt.lengthComputable) {
			var percentComplete = Math.round(evt.loaded * 100 / evt.total);
			document.getElementById('progress').innerHTML = percentComplete.toString() + '%';
			//console.log(percentComplete.toString() + '%');
		}
		else {
			console.log('unable to compute');
		}
	}
	function uploadComplete(evt) {
	/* This event is raised when the server send back a response */
		var file=evt.target.responseText;
		console.log(file);
		//window.location='meme.php';
		uploadedFile=file;
		$('#gag').attr('src','uploads/'+file);
	}
	function uploadFailed(evt) {
		console.log("There was an error attempting to upload the file.");
	}
	function uploadCanceled(evt) {
		console.log("The upload has been canceled by the user or the browser dropped the connection.");
	}
	
</script>	
<style>
	body { text-align: center; font-size:18px; }
	#gag { min-width: 384px; max-width: calc( 100% - 12px); }
	#fileToUpload { display:none;}
	input[type="text"]  { width:384px; font-size:inherit; }
	button { width:384px; font-size:inherit; margin: 0 4px; }
	#takePhoto { width:190px; }
	#uploadImage { width:190px; }
	#Share { display:none; }
	.inlineLogo { vertical-align: middle; display:inline; }
	@media screen and (max-width: 384px){
		body {transform: scale(.65);}
	}
</style>
</head>

<body onLoad="$('#upperTextbox').focus();">
	
<img src="arya.jpg" id="gag"/> <br/><br/>
<input type="file" name="fileToUpload" id="fileToUpload" onchange="fileSelected();" accept="image/*" capture="camera" /> 
 <br/><br/>
<!-- button id="takePhoto" onClick="" >Take Photo</button -->
<button id="uploadImage" onClick="$('#fileToUpload').trigger('click');" >New Image</button><span id="progress"></span> <br/><br/>
<input type="text" placeholder="Upper Text" id="upperTextbox" /> <br/><br/>
<input type="text" placeholder="Lower Text" id="lowerTextbox" /> <br/><br/>
<button id="Generate" onClick="memeIt();" >Go!</button> <br/><br/>
<button id="Share" >Share!</button> <br/><br/>
<a href="https://github.com/n8bar/MemeGenerator">See the code on <img class="inlineLogo" src="https://github.com/favicon.ico" alt="GitHub logo" width="24" />GitHub</a>
	
</body>
</html>
