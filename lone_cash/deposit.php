<?php
include 'headers.php';
if(isset($_POST['submit'])){
    $connect=mysqli_connect('macrasystems.com','macrasys','playgroundkasa2015','macrasys_mpesa') or die('Connection Failed');
    $transid=addslashes($_POST['transactioncode']);
    $mpesaqry=mysqli_query($connect,"SELECT * FROM mpesa_payments WHERE TransID='$transid'");
    if(mysqli_num_rows($mpesaqry)>0){
        $mpesarow=mysqli_fetch_assoc($mpesaqry);
        //fetch amount from mpesa
        $amount=$mpesarow['TransAmount'];
        $mpnames=$mpesarow['FirstName'].' '.$mpesarow['LastName'];
        $mpnumber=$mpesarow['MSISDN'];
        $origin=$mpnames.'('.$mpnumber.')';
        $date=$mpesarow['TransDate'];
        //check my previous balance
        $myaccount=$_SESSION['phonenumber'];
        $balqry=mysqli_query($config,"SELECT * FROM transactions WHERE account='$myaccount' ORDER BY id DESC LIMIT 1");
        if(mysqli_num_rows($balqry)>0){
            $balrow=mysqli_fetch_assoc($balqry);
            $prevbal=$balrow['newbal'];
        }else{
            $prevbal=0;
        }
        //calculate new balance
        $newbal=$prevbal+$amount;
        //insert the new transaction into transactions table
        if(mysqli_query($config,"INSERT INTO transactions(account,transtype,prevbal,transamount,newbal,originacc,destacc,transcode,transdate,`status`) VALUES('$myaccount','Deposit','$prevbal','$amount','$newbal','$origin','$myaccount','$transid','$date','Success')")){
            $result='<img src="images/success.png" width="20" height="20" align="left"> Transaction successful. Ksh. '.$amount.' deposited.';
        }
    }else{
        $result='<img src="images/error.png" width="20" height="20" align="left"> Payment not received!';
    }
}
?>
<div style="border:1px solid pink; box-shadow:2px 2px 2px grey;">
To deposit from mpesa:
<ol>
    <li>Go to Mpesa</li>
    <li>Select Lipa Na Mpesa</li>
    <li>Buy Goods & Services</li>
    <li>Enter Till Number: 5354881</li>
    <li>Enter Amount</li>
    <li>Enter your pin and Confirm</li>
    <li>Enter The Transaction Code </li>
    <li>Submit for validation and deposit</li>
</ol>
<form action="" method="post">
    <input type="text" name="transactioncode" placeholder="Enter Mpesa Transaction Code" required="required">
    <input type="submit" name="submit" value="Submit Code">
</form>
<?php echo $result ?>
</div>

<?php
include 'styles.html';
?>