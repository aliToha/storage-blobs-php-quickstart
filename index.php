<?php
		require_once 'vendor/autoload.php';
		require_once "./random_string.php";

		use MicrosoftAzure\Storage\Blob\BlobRestProxy;
		use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
		use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
		use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
		use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;require_once 'vendor/autoload.php';
		require_once "./random_string.php";

		use MicrosoftAzure\Storage\Blob\BlobRestProxy;
		use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
		use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
		use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
		use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
		
if(isset($_FILES['image'])){

        $fileToUpload = $_FILES['image']['name'];
		$connectionString = "DefaultEndpointsProtocol=https;AccountName=gagalcoding;AccountKey=m1dfnPBOaNkI9NmFNI8hFgSvcg76ORs5xuUzXsFYux3jp01NYTRptBOYPMz8hJuz3JBGTGMo13Tyliwk6+6aZg==;EndpointSuffix=core.windows.net";
		$urlImage = "https://gagalcoding.blob.core.windows.net/blockblobsfdvrnq/"+$fileToUpload;
		// Create blob client.
		$blobClient = BlobRestProxy::createBlobService($connectionString);

		$createContainerOptions = new CreateContainerOptions();
		
		$createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);

		// Set container metadata.
		$createContainerOptions->addMetaData("key1", "value1");
		$createContainerOptions->addMetaData("key2", "value2");
		$containerName = "blockblobs".generateRandomString();

			try {
				// Create container.
				$blobClient->createContainer($containerName, $createContainerOptions);

				// Getting local file so that we can upload it to Azure
				$myfile = fopen($fileToUpload, "r") or die("Unable to open file!");
				fclose($myfile);
				
				# Upload file as a block blob
				echo "Uploading BlockBlob: ".PHP_EOL;
				echo $fileToUpload;
				echo "<br />";
				
				$content = fopen($fileToUpload, "r");

				//Upload blob
				$blobClient->createBlockBlob($containerName, $fileToUpload, $content);

				// List blobs.
				$listBlobsOptions = new ListBlobsOptions();
				$listBlobsOptions->setPrefix("HelloWorld");

				echo "These are the blobs present in the container: ";

				do{
					$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
					foreach ($result->getBlobs() as $blob)
					{
						echo $blob->getName().": ".$blob->getUrl()."<br />";
					}
				
					$listBlobsOptions->setContinuationToken($result->getContinuationToken());
				} while($result->getContinuationToken());
				echo "<br />";

				// Get blob.
				echo "This is the content of the blob uploaded: ";
				$blob = $blobClient->getBlob($containerName, $fileToUpload);
				fpassthru($blob->getContentStream());
				echo "<br />";
				
				echo"<button onclick='processImage()'>Analyze image</button>";
				echo"<br><br>";
				echo"<div id='imageDiv' style='width:420px; display:table-cell;'>
					Source image:
					<br><br>
					<img src='".$urlImage."' width='400' />
					</div>";
			}
				catch(ServiceException $e){
					// Handle exception based on error codes and messages.
					// Error codes and messages are here:
					// http://msdn.microsoft.com/library/azure/dd179439.aspx
					$code = $e->getCode();
					$error_message = $e->getMessage();
					echo $code.": ".$error_message."<br />";
				}
				catch(InvalidArgumentTypeException $e){
					// Handle exception based on error codes and messages.
					// Error codes and messages are here:
					// http://msdn.microsoft.com/library/azure/dd179439.aspx
					$code = $e->getCode();
					$error_message = $e->getMessage();
					echo $code.": ".$error_message."<br />";
				}
} 
?>
<html>
<head>
    <title>Analyze Sample</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
</head>
<body>
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
        var uriBase =
            "https://centralus.api.cognitive.microsoft.com/vision/v2.0/analyze";
 
        // Request parameters.
        var params = {
            "visualFeatures": "Categories,Description,Color",
            "details": "",
            "language": "en",
        };
 
        // Display the image.
        var sourceImageUrl = document.getElementById("inputImage").value;
        document.querySelector("#sourceImage").src = sourceImageUrl;
 
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
<h1>Analyze image:</h1>
<form action="" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="image" id="fileToUpload">
    <input type="submit" value="Upload Image" name="submit">
</form>
<br><br>
<div id="wrapper" style="width:1020px; display:table;">
    <div id="jsonOutput" style="width:600px; display:table-cell;">
        Response:
        <br><br>
        <textarea id="responseTextArea" class="UIInput"
                  style="width:580px; height:400px;"></textarea>
    </div>
</div>
</body>
</html>