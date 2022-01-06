<?php
session_start();
include 'config.php';
if(empty($_SESSION['user'])){
    header('location:login.php');
}else{
    $fullnames=$_SESSION['user'].' ('.$_SESSION['phonenumber'].')';
}
?>
<div><img src="images/logo.png" width="100" height="100" style="float:left"></div>
<div class="headers"><img src="images/user.png" width="20" height="20" style="float:left"> &nbsp;<b>Welcome <?php echo $fullnames ?></b><a href="index.php"><img src="images/menu.png" height="30" width="30" style="float:right; margin-right:10px;"></a></div>
