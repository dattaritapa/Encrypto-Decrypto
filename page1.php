<?php
// Initialize the session
session_start();
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$ckey = $msg = $rcv = "";
$ckey_err = $msg_err = $rcv_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if cipher key is empty
    if(empty(trim($_POST["cipherkey"]))){
        $ckey_err = "Please enter the key.";
    } else{
        $ckey = trim($_POST["cipherkey"]);
    }
    
    // Check if message box is empty
    if(empty(trim($_POST["message"]))){
        $msg_err = "Please enter your message.";
    } else{
        $msg = trim($_POST["message"]);
    }

    //Check if reciever box is empty
    if(empty(trim($_POST["reciever"]))){
        $rcv_err = "Please enter the reciever's username.";
    } else{
        $rcv = trim($_POST["reciever"]);
    }
    
    // Validate credentials
    if(empty($rcv_err)){
        // Prepare a select statement
        $sql = "SELECT user_id, username FROM user WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_rcv);
            
            // Set parameters
            $param_rcv = $rcv;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1)
                {                    
                    
                    $sql = "INSERT INTO receivers (rcv_name, en_msg, en_key ) VALUES (?, ?, ?)";                          
                            
                            // Redirect user to welcome page
                            header("location: logout.php");
                } 
                else
                {
                    // Display an error message if username doesn't exist
                    $rcv_err = "The receiver is not registered.";
                }
            } 
            else
            {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Message</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <p>Please fill in the details.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($ckey_err)) ? 'has-error' : ''; ?>">
                <label>Cipher Key</label>
                <input type="text" name="cipherkey" class="form-control" value="<?php echo $ckey; ?>">
                <span class="help-block"><?php echo $ckey_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($msg_err)) ? 'has-error' : ''; ?>">
                <label>Message</label>
                <input type="message" name="message" class="form-control">
                <span class="help-block"><?php echo $msg_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($rcv_err)) ? 'has-error' : ''; ?>">
                <label>Receiver</label>
                <input type="reciever" name="reciever" class="form-control">
                <span class="help-block"><?php echo $rcv_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Send">
            </div>
        </form>
    </div>    
</body>
</html>