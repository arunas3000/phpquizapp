

<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require './vendor/autoload.php';


//General functions
function clean($data){
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
  return $data;
}

function redirect($location){
    
    return header("Location: {$location}");
}

function set_message($message) {
    
    if(!empty($message)) {
        
        $_SESSION['message'] = $message;
        
    } else {
        
        $message = "";
    }
}

function display_message() {
    
    if(isset($_SESSION['message'])) {
        echo $_SESSION['message'];
         unset($_SESSION['message']);
    }
}

//Function to make sure that the data is submitted from the page alone
function token_generator() {
    
    $token = $_SESSION['token'] =  md5(uniqid(mt_rand(), true));
    
    return $token;
}

function validation_error($error_message) {
    
    $error_message = <<<EOD
    <div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    $error_message
</div>
EOD;
    
    return $error_message;
}

function email_exists($email) {
    
    $sql = "SELECT id FROM users WHERE email = '$email'";
    
   $result = query($sql);
    
    if(mysqli_num_rows($result) == 1){
        
        return true;
        
    } else {
        
        return false;
        
    }
}

function username_exists($username) {
    
    $sql = "SELECT id FROM users WHERE username = '$username'";
    
    $result = query($sql);
    
    if(mysqli_num_rows($result) == 1){
        return true;
    } else {
        return false;
    }
}


 
//User register validation functions

function validate_user_registration() {
    
   $errors = array();
    
   
    
    if(isset($_POST['register-submit'])) {
     
        //If form is submited, sanitize the input data
        //The value is from the attribute "name"
        $first_name = clean($_POST['first_name']);
        $last_name = clean($_POST['last_name']);
        $username = clean($_POST['username']);
        $email = clean($_POST['email']);
        $password = clean($_POST['password']);
        $confirm_pasword = clean($_POST['confirm_password']);
        
     
        
        if(strlen($username)<3 || strlen($username)>55) {
            array_push($errors, "Your username must be between 3 and 20 characters long") ;
        }
        
        if(username_exists($username)) {
             array_push($errors, "Sorry, this username already registered") ;
        }
        
        if(strlen($first_name)<3 || strlen($first_name)>20) {
            array_push($errors, "Your first name must be between 3 and 20 characters long") ;
        }
        
        if(strlen($last_name)<3 || strlen($last_name)>20) {
            array_push($errors, "Your last name must be between 3 and 20 characters long") ;
        }
        
        if(email_exists($email)) {
             array_push($errors, "Sorry, this email already registered") ;
        }
        
        if(strlen($email)<3 || strlen($email)>50) {
            array_push($errors, "Your email must be between 3 and 50 characters long") ;
        }
        
        if(strlen($password)<6 || strlen($password)>25) {
            array_push($errors, "Your password must be between 6 and 25 characters long");
        }
        
        
        if($password!==$confirm_pasword) {
            array_push($errors, "Your passwords must match") ;
        }
        
        //If errors array is not empty loop through it and display the messages
        if(!empty($errors)) {
            foreach ($errors as $error) {
                

            echo validation_error($error);  
                
            }
        } else {
            if(register_user($username, $first_name, $last_name, $email, $password)){
                
                set_message("<p class='bg-success text-center'>Please check your email for activation link</p>");
                redirect("helper.php");
                 
            } else {
                set_message("<p class='bg-success text-center'>Sorry, we couldn't register the user</p>");
                redirect("register.php");
            }
        }
    }
}


//Register user functions
function register_user($username, $first_name, $last_name, $email, $password) {
    
    //There is no need to clean because cleaned data will be passed to the function
        $username = escape($username);
        $first_name = escape($first_name);
        $last_name  = escape($last_name);
        $email = escape($email);
        $password = escape($password);
        
        
        
    if(email_exists($email)) {
        return false;
    } else if (username_exists($username)) {
        return false;
    } else {
        $password = password_hash($password, PASSWORD_BCRYPT, array('cost'=>12));
        
        $validation = md5($username . microtime());
        
        //Insert data into the table, 0 is for active, it's 0 by default
        $sql = "INSERT INTO users(username, first_name, last_name, email, password, validation_code, created_at, active) 
        VALUES('$username','$first_name', '$last_name', '$email', '$password', '$validation', NOW(), 0 )";
        
        $result = query($sql);
        confirm($result);
        
        $subject = "Activate Account";
        $msg = "Please click the link below to activate your account:
        <a href = 'https://lenovo33.000webhostapp.com/activate.php?email=$email&code=$validation'>Click here</a>
        ";
        
        send_email($email, $subject, $msg);
        
        
        return true;
    }
    
} 

function send_email($email, $subject, $msg) {
    $mail = new PHPMailer();                            
try {
    //Server settings
    $mail->SMTPDebug = 0;                                
    $mail->isSMTP();                                      
    $mail->Host = Config::SMTP_HOST;  
    $mail->SMTPAuth = true;                               
    $mail->Username = Config::SMTP_USER;                 
    $mail->Password = Config::SMTP_PASSWORD;                           
    $mail->SMTPSecure = 'tls';                           
    $mail->Port = Config::SMTP_PORT; 
    $mail->Subject = $subject;
    $mail->Body    = $msg;
    $mail->AltBody = $msg;
    $mail->setFrom('noreply@phpquizzes.com', 'PHP Quizzes');
    $mail->addAddress($email);

    $mail->send();
    echo "<p class='bg-success'>Message has been sent</p>";
    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
}
    
//Activate user functions

function activate_user() {
    //This is a function that checks if the activation link that is displayed in a browser, matches info from the database
    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        
        if(isset($_GET['email'])) {
           $email = escape(clean($_GET['email']));
            $validation_code = escape(clean($_GET['code']));
            
           
            
            $sql = "SELECT id FROM users WHERE email = '$email' AND validation_code = '$validation_code' ";
            
            $result = query($sql);
            
            confirm($result);
            
            if(mysqli_num_rows($result) == 1) {
                
                $sql2 = "UPDATE users SET active = 1, validation_code = 0 WHERE email =  '$email' AND validation_code = '$validation_code' ";
                
                 $result2 = query($sql2);
            
                confirm($result2);
                
                set_message("<p class='bg-success'>Your account has been activated</p>");
                redirect("login.php");
            } else {
                //The message will appear in login.php page
                set_message("<p class='bg-danger'>Sorry, your account hasn't been activated</p>");
                redirect("login.php");
            }     
                
        }
    }
}

//User login validation functions

function validate_user_login() {
    
   $errors = array();
    
   
    
    if(isset($_POST['login-submit'])) {
        
        $email = clean($_POST['email']);
        $password = clean($_POST['password']);
        //Check if checkbox is checked
        $remember = isset($_POST['remember']);
        
        
        if(empty($email)) {
            
             array_push($errors, "Password field cannot be empty") ;
            
        }
        
        if(empty($password)) {
            
             array_push($password, "Password field cannot be empty") ;
            
        }
        
        if(!empty($errors)) {
            
            foreach ($errors as $error) {
                

            echo validation_error($error);  
                
            }
        } else {
            if(login_user($email, $password, $remember)) {
                
                redirect("start.php");
                
            } else {
                echo validation_error("Your credentials are not correct");
            }
        }
    }
}


//User login function

function login_user($email, $password, $remember) {
    
    $email = escape($email);
    
       $sql = "SELECT password, id, user_level FROM users WHERE email = '$email' AND active=1";
    
    $result = query($sql);
    
    if(mysqli_num_rows($result) == 1){
        
        //Convert the query to an array
        $row = fetch_array($result);
        $db_password = $row['password'];
        
        //Assign session variable for adminstrator
        
        
        
        //Compare passwords
        if(password_verify($password, $db_password)) {
            
            //Set cookie for one day if 'remember me' is clicked
            if($remember == "on") {
                setcookie('email', $email, time()+86400);
            }
            
            //Set session variables in order to indicated that user is logged in( for logged_in function)
            $_SESSION['email'] = $email;
            $_SESSION['user_level'] = $row['user_level'];
            
            
            return true;
            
        } else {
            return false;
        }
        
        return true;
    } else {
        
        return false;
        
    }
    
}

//Logged in function

function logged_in() {
    
    //Check if session of cookie is not expired(cookie is set for 'remember me' and session is for logging out)
    if(isset($_SESSION['email']) || isset($_COOKIE['email'])) {
        
        return true;
        
    } else {
        
        return false;
        
    }
    
}

//Recover password function(for recover.php)

function recover_password() {
    
     if(isset($_POST['recover-submit'])) {
         
         //Check if submitted token value is the same as submited value for better security
         if(isset($_SESSION['token']) && $_POST['token'] === $_SESSION['token']) {
             
             $email = escape($_POST['email']);
             
             if(email_exists($email)) {
                 
                 //Set new validation code(was set to 0 before)
                 $validation_code = escape(md5($email . microtime())); 
                 
                 //Set cookie for 1minute so actiavtion window won't be available after that time
                 setcookie('temp_access_code', $validation_code, time()+900);
                 
                 //Update with new validation code
                 $sql = "UPDATE users SET validation_code = '$validation_code' WHERE email =  '$email' ";
                 
                 $result = query($sql);
                 confirm($result);
                 
                 $subject = "Please reset your password";
                 $message = "Here is your password reset code: {$validation_code}
                 Click here to reset your password <a href = 'https://lenovo33.000webhostapp.com/code.php?email=$email&code=$validation_code'>Click here</a>
                 ";
                 
                
                 
                 send_email($email, $subject, $message);

                    set_message("<p class='bg-success text-center'> Please check your email or spam folder for password reset link</p>");
                    redirect("helper.php");
            
             } else {
                 
                 echo validation_error("This email doesn't exists");
                 
             }
             
         } else {
             
             redirect("index.php");
             
         }     
    }       
}

function cancel() {
    if(isset($_POST['cancel-submit'])){
             
             redirect("login.php");
             
         }
}


//Code validation function(for code.php)

function validate_code () {
    //If cookies is not expired, check if the validation code matches the one in the database
    if(isset($_COOKIE['temp_access_code'])) {
        
     
           //Check if the email and code is set
           if(!isset($_GET['email']) && !isset($_GET['code'])) {
                
                redirect("index.php");
                
            } else if (empty($_GET['email'])|| empty($_GET['code'])) {
                
                redirect("index.php");
                
            } else {
                //This will check if the data displayed in the browser matches the one set in the database
                if(isset($_POST['code'])) {
                    
                    
                   $validation_code = escape(clean($_POST['code']));
                    $email = escape(clean($_GET['email']));
                    
                    $sql = "SELECT id FROM users WHERE validation_code = '$validation_code' AND email='$email'";
    
                    $result = query($sql);
                    
                    confirm($result);
                    //If validation codes matches redirect to password reset page
                    if(mysqli_num_rows($result) == 1){
                        
                      //Confirm that user came from code.php page
                        redirect("reset.php?email=$email&code=$validation_code");
                        
                    } else {
                          //Set 5 minutes access limitation for the page
                        setcookie('temp_access_code', $validation_code, time()+900);
                        echo validation_error("Sorry, wrong validation code");
                    }
                    
                }
                
            }
    } else {
        set_message("<p class='bg-danger text-center'>Sorry, your recovery link has expired</p>");
        redirect("recover.php");
        
    }
    
}

//Password reset function(goes to reset.php)

function password_reset() {
    
    //Check if the cookie is not expired
        if(isset($_COOKIE['temp_access_code'])) {
            
            if(isset($_GET['email'])&& isset($_GET['code'])){
                
                //Check if the form was submited
            if(isset($_SESSION['token']) && isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
          
            $email = $_GET['email'];
            $password = escape($_POST['password']);
            $confirm_password = escape($_POST['confirm_password']);
            $updated_password =  password_hash($password, PASSWORD_BCRYPT, array('cost'=>12));
            
            
            if ($password === $confirm_password) {
                
                ////Update user table with a new password and reset validation code to 0 and reactivate your user
                 $sql = "UPDATE users SET password = '$updated_password', validation_code = 0, active = 1 WHERE email =  '$email'";
                 $result = query($sql);
                 confirm($result);
                 
                 
                 set_message("<p class='bg-success text-center'>Your password has been updated, please log in</p>");
                 redirect("login.php");
                 
            } else {
                
                validation_error("Your passwords don't match");
                
            }
       
                
            }
     } 
     } else {
         
         set_message("<p class='bg-danger'>Sorry, your access to this page has expired</p>");
         redirect("recover.php");
     
    } 
}