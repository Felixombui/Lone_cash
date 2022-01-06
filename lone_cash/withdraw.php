<?php
include 'headers.php';
if(isset($_POST['withdraw'])){
    $agentnumber=addslashes($_POST['agentnumber']);
    $amount=addslashes($_POST['amount']);
    $agntqry=mysqli_query($config,"SELECT * FROM agents WHERE agentnumber='$agentnumber'");
    if(mysqli_num_rows($agntqry)>0){
        $agntbalqry=mysqli_query($config,"SELECT * FROM agentaccounts WHERE agentnumber='$agentnumber' order by id DESC LIMIT 1");
        if(mysqli_num_rows($agntbalqry)>0){
            $agntbalrow=mysqli_fetch_assoc($agntbalqry);
            $prevbal=$agntbalrow['newbal'];
            $expectedbal=$prevbal-$amount;
                if($expectedbal>-1){
                    //check customer account
                    $custacc=$_SESSION['phonenumber'];
                    $custqry=mysqli_query($config,"SELECT * FROM transactions WHERE account='$custacc' ORDER BY id DESC LIMIT 1");
                    if(mysqli_num_rows($custqry)>0){
                            $custrow=mysqli_fetch_assoc($custqry);
                            $custbal=$custrow['newbal'];
                            if($custbal>$amount){
                                //proceed to withdraw
                                header('location:withdawdetails.php?agent='.$agentnumber.'&agentbal='.$prevbal.'&mybal='.$custbal.'&reqamnt='.$amount);
                            }else{
                                $error='<img src="images/error.png" width="20" height="20" align="left"> Your account balance is insufficient to complete this transaction!'; 
                            }
                    }else{
                        $error='<img src="images/error.png" width="20" height="20" align="left"> Your account balance is insufficient to complete this transaction!'; 
                    }
                }else{
                    $error='<img src="images/error.png" width="20" height="20" align="left"> Sorry2! We cannot complete your transaction as requested.Please use another agent.';
                }
        }else{
            $error='<img src="images/error.png" width="20" height="20" align="left"> Sorry1! We cannot complete your transaction as requested.Please use another agent';
        }
        
    }else{
        $error='<img src="images/error.png" width="20" height="20" align="left"> The agent number does not exist!';
    }
}
?>
<form method="post">
    <input type="text" name="agentnumber" placeholder="Enter Agent Number" required="required">
    <input type="number" name="amount" placeholder="Enter Amount to withdraw" required="required">
    <input type="submit" name="withdraw" value="Withdraw">
</form>
    <?php echo $error ?>
<?php
include 'styles.html';
?>