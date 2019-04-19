<html>
<head lang="en">
<meta charset="utf-8">
<title>Image Analyze Using Azure Vision</title>
<link rel="stylesheet" href="style.css" type="text/css" />
<script type="text/javascript" src="js/jquery-1.11.3-jquery.min.js"></script>
<script type="text/javascript" src="js/script.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
<script>
$(document).ready(function(e){
    $("#fupForm").on('submit', function(e){
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'phpQS.php',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData:false,
            beforeSend: function(){
                $('.submitBtn').attr("disabled","disabled");
                $('#fupForm').css("opacity",".5");
            },
            success: function(msg){
                $('.statusMsg').html('');
                /* if(msg == 'success'){
                    $('#fupForm')[0].reset();
                
                }else{
                    $('.statusMsg').html('<span style="font-size:18px;color:#EA4335">Some problem occurred, please try again.</span>');
                } */
				$('.statusMsg').html('<span style="font-size:18px;color:#34A853">Form data submitted successfully.</span>');
				$('.bolbContainerName').html(msg['containerName']);
                $('#fupForm').css("opacity","");
                $(".submitBtn").removeAttr("disabled");
            }
			error
        });
    });
	
	$("#file").change(function() {
        var file = this.files[0];
        var imagefile = file.type;
        var match= ["image/jpeg","image/png","image/jpg"];
        if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2]))){
            alert('Please select a valid image file (JPEG/JPG/PNG).');
            $("#file").val('');
            return false;
        }
		var reader = new FileReader();
		reader.onload = function (e) {
        // get loaded data and render thumbnail.
        document.getElementById("sourceImage").src = e.target.result;
		};
		// read the image file as a data URL.
		reader.readAsDataURL(file);
    });
});
</script>
<script type="text/javascript">
    function processImage() {
        // **********************************************
        // *** Update or verify the following values. ***
        // **********************************************
 
        // Replace <Subscription Key> with your valid subscription key.
        var subscriptionKey = "77ecf1a9069d4c858c7b79c66fdb290d";
 
        // You must use the same Azure region in your REST API method as you used to
        // get your subscription keys. For example, if you got your subscription keys
        // from the West US region, replace "westcentralus" in the URL
        // below with "westus".
        //
        // Free trial subscription keys are generated in the "westus" region.
        // If you use a free trial subscription key, you shouldn't need to change
        // this region.
		var containerName = document.getElementById("container").value;
        var uriBase =
            "https://centralus.api.cognitive.microsoft.com/vision/v2.0/analyze";
 
        // Request parameters.
        var params = {
            "visualFeatures": "Categories,Description,Color",
            "details": "",
            "language": "en",
        };
 
        // Display the image.
        var sourceImageUrl = document.getElementById("file").value;
        document.querySelector("#sourceImage").src = sourceImageUrl;
		var filename = sourceImageUrl.replace(/^.*[\\\/]/, '');
		var urlBlob = "https://gagalcoding.blob.core.windows.net/"+containerName+"/"+filename;
 
        // Make the REST API call.
        $.ajax({
            url: uriBase + "?" + $.param(params),
 
            // Request headers.
            beforeSend: function(xhrObj){
                xhrObj.setRequestHeader("Content-Type","application/json");
                xhrObj.setRequestHeader(
                    "Ocp-Apim-Subscription-Key", subscriptionKey);
            },
 
            type: "POST",
 
            // Request body.
            data: '{"url": ' + '"' + sourceImageUrl + '"}',
        })
 
        .done(function(data) {
            // Show formatted JSON on webpage.
            $("#responseTextArea").val(JSON.stringify(data, null, 2));
        })
 
        .fail(function(jqXHR, textStatus, errorThrown) {
            // Display error message.
            var errorString = (errorThrown === "") ? "Error. " :
                errorThrown + " (" + jqXHR.status + "): ";
            errorString += (jqXHR.responseText === "") ? "" :
                jQuery.parseJSON(jqXHR.responseText).message;
            alert(errorString);
        });
    };
</script> 
<div class="container">
<div class="row">
<div class="col-md-8">
<h1><a href="#" target="_blank">Image Analyze Using Azure Vision</a></h1>
<hr> 
<p class="statusMsg"></p>
<p class="bolbContainerName" id="container"></p>
<form enctype="multipart/form-data" id="fupForm" >
    <div class="form-group">
        <label for="file">IMAGE : </label>
        <input type="file" class="form-control" id="file" name="file" required />
    </div>
    <input type="submit" name="submit" class="btn btn-danger submitBtn" value="UPLOAD"/>
	<button onclick="processImage()" type="button" class="btn btn-danger submitBtn" disabled>Analyze image</button>
</form>
</div>
<img id="sourceImage" width="400" />
<h1></h1>
</div>
</div>
</body>
</html>