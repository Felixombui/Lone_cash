<?php
include 'config.php';
if(isset($_POST['login'])){
    $account=addslashes($_POST['accountno']);
    $password=addslashes($_POST['password']);
    $loginqry=mysqli_query($config,"SELECT * FROM users WHERE accountno='$account' AND password='$password'");
    if(mysqli_num_rows($loginqry)>0){
        session_start();
        $userqry=mysqli_query($config,"SELECT * FROM accounts WHERE accountno='$account'");
        $userrow=mysqli_fetch_assoc($userqry);
        $_SESSION['user']=$userrow['names'];
        $_SESSION['phonenumber']=$userrow['phonenumber'];
        header('location:index.php');
    }else{
        $error='<div align="center" style="color:red"><img src="images/error.png" width="20" height="20" style="text-align:center"><b>Login failed!</b></div>';
    }
}
?>
<div class="loginform">
    <div style="text-align: center;"><img src="images/logo.png" width="150" height="150"></div>
<form action="" method="post">
    <input type="text" name="accountno" placeholder="Enter your account number" required="required">
    <input type="password" name="password" placeholder="Enter your password" required="required">
    <input type="submit" name="login" value="Login">
    <div style="text-align: center; margin-top:10px;"><a href="recover.php">I forgot my password!</a></div>
    <div style="text-align:center"><?php echo $error ?></div>
</form>
</div>
<?php
include 'styles.html';
?>