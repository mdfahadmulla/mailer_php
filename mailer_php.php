<?php
//var_dump ($_POST);

    //google recaptcha
    $public_key = "xyz";
    $private_key = "xyz";
    $url = "https://www.google.com/recaptcha/api/siteverify";

    if(isset($_POST['submit']))
    {
        $response_key = $_POST['g-recaptcha-response'];
		$response = file_get_contents($url.'?secret='.$private_key.'&response='.$response_key.'&remoteip='.$_SERVER['REMOTE_ADDR']);
        $response = json_decode($response); 

        if($response->success == 1)
        {

            $name = $_POST['name']; // Get Name value from HTML Form
            $email_id = $_POST['email']; // Get Email Value
            $mobile_no = $_POST['mobile']; // Get Mobile No
            $exp = $_POST['experience']; // Get Experience Value
            $linkedin = $_POST['linkedin']; // Get LinkedIn URL
            $msg = $_POST['message']; // Get Message Value
            $job_profile = $_POST['job-profile']; // Get job-profile Value

            // Upload attachment file
            $targetDir = "uploads/";
            $fileName = basename($_FILES["attachment"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);

                    // Upload file to the server
                    if(move_uploaded_file($_FILES["attachment"]["tmp_name"], $targetFilePath)){
                        $uploadedFile = $targetFilePath;
                    }

            $to = "info@xyz.com"; // You can change here your Email
            $subject = "'$name' wants to apply for '$job_profile' position"; // This is your subject
                
            // HTML Message Starts here
            $message ="
            <html>
                <body>
                    <table style='width:600px;'>
                        <tbody>
                            <tr>
                                <td style='width:150px'><strong>Name: </strong></td>
                                <td style='width:400px'>$name</td>
                            </tr>
                            <tr>
                                <td style='width:150px'><strong>Email ID: </strong></td>
                                <td style='width:400px'>$email_id</td>
                            </tr>
                            <tr>
                                <td style='width:150px'><strong>Mobile No: </strong></td>
                                <td style='width:400px'>$mobile_no</td>
                            </tr>
                            <tr>
                                <td style='width:150px'><strong>Experience: </strong></td>
                                <td style='width:400px'>$exp</td>
                            </tr>
                            <tr>
                                <td style='width:150px'><strong>LinkedIn URL: </strong></td>
                                <td style='width:400px'>$linkedin</td>
                            </tr>
                            <tr>
                                <td style='width:150px'><strong>Job Position: </strong></td>
                                <td style='width:400px'>$job_profile</td>
                            </tr>
                            <tr>
                                <td style='width:150px'><strong>Message to HR: </strong></td>
                                <td style='width:400px'>$msg</td>
                            </tr>
                        </tbody>
                    </table>
                </body>
            </html>
            ";
            // HTML Message Ends here
                
            $semi_rand = md5(time()); 
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
                
            // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= 'From: Job Application <info@xyz.com>' . "\r\n";
            $headers .= "MIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 

            $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
            "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 

            // Preparing attachment
            if(is_file($uploadedFile)){
                $message .= "--{$mime_boundary}\n";
                $fp =    @fopen($uploadedFile,"rb");
                $data =  @fread($fp,filesize($uploadedFile));
                @fclose($fp);
                $data = chunk_split(base64_encode($data));
                $message .= "Content-Type: application/octet-stream; name=\"".basename($uploadedFile)."\"\n" . 
                "Content-Description: ".basename($uploadedFile)."\n" .
                "Content-Disposition: attachment;\n" . " filename=\"".basename($uploadedFile)."\"; size=".filesize($uploadedFile).";\n" . 
                "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
            }

            $message .= "--{$mime_boundary}--";
            $returnpath = "-f" . $email_id;

            $mail = mail($to, $subject, $message, $headers, $returnpath);

            @unlink($uploadedFile);

            if($mail){
                // Message if mail has been sent
                echo "<script>
                        alert('Mail has been sent Successfully. Our team of experts will get back to you shortly');
                    </script>";
                    header('Location: ../index');
            }
        
            else{
                // Message if mail has been not sent
                echo "<script>
                        alert('EMAIL FAILED. Please try again');
                    </script>";
            }
        }
        else
		{   
            echo "<script>
                alert('Please verify you are not a robot and try again');
            </script>";
        }
    }   
?>  