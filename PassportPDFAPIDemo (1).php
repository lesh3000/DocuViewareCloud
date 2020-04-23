 <!DOCTYPE html>
<html>
    <head>
        <title>DocuVieware PHP App</title>
        
       
    </head>
    <body>
    

            <?php
            session_start();
            header('Content-Type: text/html; charset=utf-8');
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            //getting DocuVieware rendered document after it has been processed by user
            if(isset($_POST['getImage'])){

$docuViewareConfig = array(
    
        'SessionId'=>session_id(),
        "ControlId"=> 'docuVieware2',
        "FileName"=> "yourPdf",
        "Format"=> "pdf",
        "PageRange"=> "*"
      
);


 $data_string = json_encode($docuViewareConfig);
            
 $ch = curl_init('https://passportpdfapi.com/api/docuvieware/DocuViewareSave');
 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
 curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_HTTPHEADER, array(
     'X-PassportPDF-API-Key: yourKeyHere',
     'Content-Type: application/json',
     'Content-Length: ' . strlen($data_string),
     
     
     )
 );
 
 $result = curl_exec($ch);
 
 if ($result === false) {
     $info = curl_getinfo($ch);
     curl_close($ch);
     die('Error occured during curl exec.: ' . var_dump($info));
 }
 curl_close($ch);
 $result=json_decode($result);
 
 print '<div id="document">'.var_dump($result).'</div>';

 
}
            
            
            
            
            //Loading document into PassportPDF  API and obtaining FileId to further process the document within API
            $filename ="http://www.pdf995.com/samples/pdf.pdf";
           
            $file = fopen($filename, "rb");

            $contents = base64_encode(file_get_contents($filename));
            fclose($file);
            $dataLoad = array("Content"=>$contents,"FileName"=>"File.pdf");
            
    
            $ch = curl_init('https://passportpdfapi.com/api/PDF/LoadDocument');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataLoad));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'X-PassportPDF-API-Key: yourKeyHere',
                'Content-Type: application/json-patch+json',
                'Content-Length: ' . strlen(json_encode($dataLoad)),
                )
            );
            $result1 = curl_exec($ch);
            if ($result1 === false) {
                $info1 = curl_getinfo($ch);
                curl_close($ch);
                die('Error occured during curl exec.: ' . var_dump($info1));
            }
            else{ 
             $result1=json_decode($result1,true);
             $image= $result1["FileId"];
                
            }
            curl_close($ch);
            
            

            //Rendering DocuVieware control and loading a document into it
            
            
            /****************************************************************
            Two options are possible:
            - load directly by specifying e.g 'DocumentURI'=> 'https://passportpdfapi.com/api/PDF/LoadDocument'
            - load document with help of FileId obtained by the previous call, in such case DocumentURI'=>'fileid:'.$yourFileId,
            ****************************************************************/
            

            $docuViewareConfig = array(
                       'SessionID' => session_id(),
                        "ControlState"=>array(
                        'ControlId' => 'docuVieware2',
                        'Timeout'=>20,
                        "ZoomMode"=> "ZoomModeShrinkToViewerWidth",
                        "PageViewMode"=> "MultiplePagesView",
                         
                        //'DocumentURI'=>'fileid:'.$image,
                       'DocumentURI'=>$filename,
                         
                        'ControlWidth' => '100%',
                        'ControlHeight' => '100%', 
                        "MaxDownloadSize"=>'500000000',
                        "MaxPages"=> 500,
                       
                      )
            );


            $data_string = json_encode($docuViewareConfig);
            
            $ch = curl_init('https://passportpdfapi.com/api/docuvieware/DocuViewareGetControl');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'X-PassportPDF-API-Key: yourKeyHere',
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string),
                
                
                )
            );
            
            $result = curl_exec($ch);
            
            if ($result === false) {
                $info = curl_getinfo($ch);
                curl_close($ch);
                die('Error occured during curl exec.: ' . var_dump($info));
            }
            curl_close($ch);
            $docuViewareControlHtml = $result;
            
       
            ?>
            
           
    <form action="PassportPDFAPIDemo.php" method="POST">
        <input type="submit" value="Get Processed Document" name="getImage">
        </form>
        

        <div id="dvContainer" style="width:1800px;height:1000px;">
            <?php
            $html = json_decode($docuViewareControlHtml);
            print $html->{'Element'};
            ?>
        </div>
        
    </body>
</html>