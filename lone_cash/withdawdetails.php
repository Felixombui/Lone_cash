<?php
include 'headers.php';
$mybal=$_GET['mybal'];
$agentbal=$_GET['agentbal'];
$agentnumber=$_GET['agent'];
$reqamnt=$_GET['reqamnt'];
//calculations
$agentnewbal=$agentbal-$reqamnt;
$mynewbal=$mybal-$reqamnt;
$agntnameqry=mysqli_query($config,"SELECT * FROM agents WHERE agentnumber='$agentnumber'");
$agntnamerow=mysqli_fetch_assoc($agntnameqry);
$agentname=$agntnamerow['agentname'];
if(isset($_POST['submit'])){
    $account=$_SESSION['phonenumber'];
    $transtype='Withdrawal';
    //create random transaction code
    $permitted_chars = '0123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    $transcode= substr(str_shuffle($permitted_chars), 0, 8).'';
    $transdate=date('d-m-Y h:i:s');
    $transamnt='-'.$reqamnt;
    if(mysqli_query($config,"INSERT INTO transactions(account,transtype,prevbal,transamount,newbal,originacc,destacc,transcode,transdate,`status`) VALUES('$account','$transtype','$mybal','$transamnt','$mynewbal','$account','$agentnumber','$transcode','$transdate','Success')")){
        $customersms= $transcode .' confirmed. You have withdrawn Ksh.'.$reqamnt.' from '.$agentname.'-'.$agentnumber.' on '.$transdate.'. Your new balance is Ksh.'.$mynewbal;
        $sms=urlencode($customersms);
        $phone=ltrim($_SESSION['phonenumber'],'0');
        $phoneno='254'.$phone;
        $url='https://sms.macrasystems.com/sendsms/index.php?senderid=SMARTLINK&username=Macra&phonenumber='.$phoneno.'&message='.$sms;
        file_get_contents($url);
        $agentnewbal=$agentbal+$reqamnt;
        if(mysqli_query($config,"INSERT INTO agentaccounts(agentnumber,prevbal,newamount,newbal,time_date,`status`) VALUES('$agentnumber','$agentbal','$reqamnt','$agentnewbal','$transdate','Success')")){
           header('location:transsuccess.php?p='.$phoneno);
        }
    }
}
?>
<div style="margin-top: 20px;">
<form method="post">
Do you want to proceed with the withdrawal?
    <table style="font-weight:bold;"><tr><td>Agent Number</td><td><?php echo $agentnumber ?></td></tr>
    <tr><td>Agent Name: </td><td><?php echo $agentname ?></td></tr>
   <tr><td>Withdraw Amount:</td><td> <?php echo 'Ksh.' .$reqamnt ?></td></tr>
   <tr><td>Transaction Cost: </td><td><?php echo 'Ksh.' .$transcost ?></td></tr>
    <tr><td>New Balance: </td><td><?php echo 'Ksh.' .$mynewbal ?></td></tr>
    </table>
    <input type="submit" name="submit" value="Withdraw">
</form>   
</div>

<?php
include 'styles.html';
?>