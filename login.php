<?php

session_start();
 

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;

}

include 'polaczenie.php';
 

require_once "config.php";
 

$username = $password = "";
$username_err = $password_err = $login_err = "";
 

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(empty(trim($_POST["username"]))){
        $username_err = "Podaj nazwe!";
    } else{
        $username = trim($_POST["username"]);
    }
    
   
    if(empty(trim($_POST["password"]))){
        $password_err = "Podaj hasło!";
    } else{
        $password = trim($_POST["password"]);
    }
    

    if(empty($username_err) && empty($password_err)){
    
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
        
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
     
            $param_username = $username;
            
           
            if(mysqli_stmt_execute($stmt)){
               
                mysqli_stmt_store_result($stmt);
                
        
                if(mysqli_stmt_num_rows($stmt) == 1){                    
             
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                       
                            session_start();
                            
                     
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                          
                            header("location: index.php");
                        } else{
                      
                            $login_err = "Zła nazwa lub hasło.";
                        }
                    }
                } else{
                 
                    $login_err = "Zła nazwa lub hasło";
                }
            } else{
                echo "Coś poszło nie tak, spróbuj ponownie później.";
            }

          
            mysqli_stmt_close($stmt);
        }
    }
    

    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="for.css">
    <style>
        body{ font: 14px sans-serif; text-align: center;}
        .wrapper{ width: 360px; padding: 20px; text-align: center; }
      form{
       
        text-align: center;
      }
    
    </style>
</head>
<body bgcolor="">
    <div class="wrapper">
        <h2>Zaloguj się</h2>
        <p>Wpisz swoje dane aby się zalogować</p>

        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" >
       
            <div class="form-group">
                <label>Nazwa</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Hasło</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" name="przycisk" value="Zaloguj" id="poco" >
            </div>
            <p>Nie masz konta?  <a href="register.php">Klikij tutaj</a>.</p>
        <img src="obraz2.png">
        </form>
    </div>
</body>
</html>