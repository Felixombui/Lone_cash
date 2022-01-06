<?php
include 'headers.php';
$date=date('d-m-y h:i:s');
if(isset($_POST['transfer'])){
    $account=addslashes($_POST['accountno']);
    $amount=addslashes($_POST['amount']);
    $myaccount=$_SESSION['phonenumber'];
    $myaccountname=$_SESSION['user'];
    //check my balance
    $balqry=mysqli_query($config,"SELECT * FROM transactions WHERE account='$myaccount' ORDER BY id DESC LIMIT 1");
    if(mysqli_num_rows($balqry)>0){
        $balrow=mysqli_fetch_assoc($balqry);
        $mybal=$balrow['newbal'];
        if($mybal>$amount){
            //check if recipient account exists
            $recqry=mysqli_query($config,"SELECT * FROM accounts WHERE accountno='$account'");
            if(mysqli_num_rows($recqry)>0){
                //account exists
                $recrow=mysqli_fetch_assoc($recqry);
                $recaccount=$recrow['accountno'];
                $accountnames=$recrow['names'];
                //check if transfering to self
                if($recaccount==$myaccount){
                    $error='<img src="images/error.png" width="20" height="20" align="left"> You cannot transfer to your own account!';
                }else{
                    //check recipient account balances
                    $accbalqry=mysqli_query($config,"SELECT * FROM transactions WHERE account='$account' ORDER BY id limit 1");
                    if(mysqli_num_rows($accbalqry)>0){
                        $accbalrow=mysqli_fetch_assoc($accbalqry);
                        $recbal=$accbalrow['newbal'];
                    }else{
                        $recbal='0';
                    }
                    //calculate my balance
                    $mynewbal=$mybal-$amount;
                    //create transaction code
                    $permitted_chars = '0123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
                    $transcode= substr(str_shuffle($permitted_chars), 0, 8).'';
                    //complete transfer
                    $mytransamnt='-'.$amount;
                    //echo $mytransamnt; working
                    //insert sender transaction
                    if(mysqli_query($config,"INSERT INTO transactions(account,transtype,prevbal,transamount,newbal,originacc,destacc,transcode,transdate,`status`) VALUES('$myaccount','Send','$mybal','$mytransamnt','$mynewbal','$myaccount','$recaccount','$transcode','$date','Success')")){
                        //insert recipient transaction
                        $newrecbal=$recbal+$amount;
                        if(mysqli_query($config,"INSERT INTO transactions(account,transtype,prevbal,transamount,newbal,originacc,destacc,transcode,transdate,`status`) VALUES('$recaccount','Receive','$recbal','$amount','$newrecbal','$myaccount','$recaccount','$transcode','$date','Success'")){
                            //create message for sender
                            $sendersms=urlencode('Dear '.$myaccountname.', Ksh.'.$amount.' has been sent to '.$accountnames.' '.$recaccount.' on '.$date.'. Transaction: '.$transcode);
                            //prepare sender phone number
                            $chars=strlen($myaccount);
                            if($chars<11){
                                $phone=ltrim($myaccount,'0');
                                $phoneno='254'.$phone;
                            }
                            $url='http://sms.macrasystems.com/sendsms/index.php?username=macra&senderid=SMARTLINK&phonenumber='.$phoneno.'&message='.$sendersms;
                            file_get_contents($url);
                            //prepare receiver sms
                            $receiversms=urlencode('Dear '.$accountnames.', you have received Ksh.'.$amount.' from '.$myaccountname.' '.$myaccount.' on '.$date.'. Transaction: '.$transcode);
                            //prepare reciver phone number
                            $recChars=strlen($recaccount);
                            if($recChars<11){
                                $recphone=ltrim($recaccount);
                                $recphonno='254'.$recphone;
                            }
                            $recurl='http://sms.macrasystems.com/sendsms/index.php?username=macra&senderid=SMARTLINK&phonenumber='.$phoneno.'&message='.$receiversms;
                            //file_get_contents($recurl);
                            header('location:transsuccess.php');
                        }
                    }
                }
                
            }else{
                //account does not exist
                $error='<img src="images/error.png" width="20" height="20" align="left"> Recipient account does not exist!';
            }
            
        }else{
            $error='<img src="images/error.png" width="20" height="20" align="left"> Insufficient funds!';
        }
    }else{
        $error='<img src="images/error.png" width="20" height="20" align="left"> Insufficient funds!';
    }
}
?>
<form method="post">
    <input type="text" name="accountno" placeholder="Enter Account to transfer to" required="required">
    <input type="text" name="amount" placeholder="Enter Amount" required="required">
    <input type="submit" name="transfer" value="Transfer">
</form>
<?php echo $error ?>
<?php
include 'styles.html';
?>