<?php 

$servername = "localhost";
$username = "root";
$password = ""; //ถ้าไม่ได้ตั้งรหัสผ่านให้ลบ yourpassword ออก
 
try {
  $condb = new PDO("mysql:host=$servername;dbname=db_website;charset=utf8", $username, $password);
  // set the PDO error mode to exception
  $condb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  //echo "Connected successfully";
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>multiple upload </title>
  <!-- sweet alert -->
  <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">

    <!-- js check file type -->
    <script type="text/javascript">
  var _validFileExtensions = [".jpg", ".jpeg", ".png"];     //กำหนดนามสกุลไฟล์ที่สามรถอัพโหลดได้
  function ValidateTypeFile(oForm) {
    var arrInputs = oForm.getElementsByTagName("input");
    for (var i = 0; i < arrInputs.length; i++) {
        var oInput = arrInputs[i];
        if (oInput.type == "file") {
            var sFileName = oInput.value;
            if (sFileName.length > 0) {
                var blnValid = false;
                for (var j = 0; j < _validFileExtensions.length; j++) {
                    var sCurExtension = _validFileExtensions[j];
                    if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                        blnValid = true;
                        break;
                    } // if (sFileName.substr(sFileName.length....
                } // for (var j = 0; j < _validFileExtensions.length; j++) {
 
                //ถ้าเลือกไฟล์ไม่ถุูกต้องจะมี Alert แจ้งเตือน   
                if (!blnValid) {
                    // alert("คำเตือน , " + sFileName + "\n ระบบรองรับเฉพาะไฟล์นามสกุล   : " + _validFileExtensions.join(", "));
                    setTimeout(function() {
                        swal({
                            title: "อัพโหลดไฟล์ไม่ถูกต้อง ",  
                            text: "รองรับ .jpg, .jpeg, .png เท่านั้น !!",
                            type: "error"
                        }, function() {
                            //window.location.reload();
                            //window.location = "product.php?act=add"; //หน้าที่ต้องการให้กระโดดไป
                        });
                      }, 1000);
                    return false;
                } //if (!blnValid) {
            } //if (sFileName.length > 0) {
        } // if (oInput.type == "file") {
    } //for
  
    return true;
} //function ValidateTypeFile(oForm) {
 </script>
</head>
<body>
   <h1> Multiple Upload files</h1>

	<form action="" method="post" onsubmit="return ValidateTypeFile(this);" enctype="multipart/form-data">
		<input name="upload[]" type="file" multiple="multiple"  required accept="image/*" />

		<button type="submit" name="act" value="upload"> upload </button>

	</form>
	
</body>
</html>

<?php 

//https://stackoverflow.com/questions/2704314/multiple-file-upload-in-php

if (isset($_POST['act']) && $_POST['act']=='upload') {

// echo '<pre>';
// print_r($_FILES);
// exit();

//$files = array_filter($_FILES['upload']['name']); //something like that to be used before processing files.

//trigger exception in a "try" block
try {

// Count # of uploaded files in array
$total = count($_FILES['upload']['name']);
//echo $total;
//exit();
// Loop through each file
for( $i=0 ; $i < $total ; $i++ ) {
//สร้างตัวแปรวันที่เพื่อเอาไปตั้งชื่อไฟล์ใหม่
$date1 = date("YmdHis");
//สร้างตัวแปรสุ่มตัวเลขเพื่อเอาไปตั้งชื่อไฟล์ที่อัพโหลดไม่ให้ชื่อไฟล์ซ้ำกัน
$numrand = (mt_rand());
$typefile = strrchr($_FILES['upload']['name'][$i],".");
//Get the temp file path
$tmpFilePath = $_FILES['upload']['tmp_name'][$i];

  //Make sure we have a file path
  if ($tmpFilePath != ""){

  	//โฟลเดอร์ที่เก็บไฟล์
    $path="uploads/";
    //ตั้งชื่อไฟล์ใหม่เป็น สุ่มตัวเลข+วันที่
    $newname = $numrand.'_'.$date1.$typefile;
    $path_copy=$path.$newname;

    //คัดลอกไฟล์ไปยังโฟลเดอร์
    //Upload the file into the temp dir
    if(move_uploaded_file($_FILES['upload']['tmp_name'][$i],$path_copy)) {

    	$stmtUpload = $condb->prepare("INSERT INTO tbl_product_image
                    (
                      ref_p_id,
                      product_image
                    )
                    VALUES 
                    (
                      1,
                      '$newname'
                    )
                    ");
      $stmtUpload->execute();
                    //$condb = null; //close connect db

                    //echo '<pre>';
                    //$stmtUpload->debugDumpParams();

      //Handle other code here
    	//echo '<pre>';
    	//print_r($newFilePath);

    } //if move
  } // ! impty
} //for 

if($stmtUpload->rowCount() > 0){
        echo '<script>
             setTimeout(function() {
              swal({
                  title: "อัพโหลดภาพสำเร็จ",
                  text: "ไฟล์ที่ถูกอัพโหลด '. $total .' files",
                  type: "success"
              }, function() {
                  window.location = "index.php"; //หน้าที่ต้องการให้กระโดดไป
              });
            }, 1000);
        </script>';
    }//if

    //catch exception
}catch(Exception $e) {
  //echo 'Message: ' .$e->getMessage();
  echo '<script>
             setTimeout(function() {
              swal({
                  title: "เกิดข้อผิดพลาด",
                  text: "กรุณาติดต่อผู้ดูแลระบบ",
                  type: "warning"
              }, function() {
                  window.location = "index.php"; //หน้าที่ต้องการให้กระโดดไป
              });
            }, 1000);
        </script>';
}


	
} //isset

//https://devbanban.com/
//ระบบที่มีขาย : https://devbanban.com/?p=4425
 
 ?>