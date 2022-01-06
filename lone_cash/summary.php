<?php
include 'headers.php';
$myaccount=$_SESSION['phonenumber'];
$qry=mysqli_query($config,"SELECT * FROM transactions WHERE account='$myaccount'");
if(isset($_POST['search'])){
    $startdate=$_POST['startdate'];
    $enddate=$_POST['enddate'];
    $qry=mysqli_query($config,"SELECT * FROM transactions WHERE account='$myaccount' AND transdate between'$startdate' AND '$enddate'");
}
?>
<form action="" method="post">
    <div style="border:1px solid pink; box-shadow:2px 2px 2px brown; margin-top:5px;">
    View summary by dates:<br>
    From: <input type="date" name="startdate" style="width:30%;"> To: <input type="date" name="enddate" style="width:30%;">
    <input type="submit" name="search" value="Show" style="width:20%">
    </div>
</form>

<div style="border:1px solid pink; box-shadow:2px 2px 2px brown; margin-top:5px;">
<table style="width:100%;"><tr style="font-weight:bold; color:white; background-color:grey;"><td>From</td><td>To</td><td>Amount</td><td>Balance</td><td>Date</td></tr>
<?php
while($row=mysqli_fetch_assoc($qry)){
    $from=$row['originacc'];
    $to=$row['destacc'];
    $amount=$row['transamount'];
    $fulldate=explode(' ',$row['transdate']);
    $date=$fulldate[0];
    $transcode=$row['transcode'];
    $balance=$row['newbal'];
    if($from==$myaccount){
        $from="Me";
    }
    if($to==$myaccount){
        $to='Me';
    }
    echo '<tr style="background-color:pink;><td>'.$from.'</td><td>'.$to.'</td><td>'.$amount.'</td><td>'.$balance.'</td><td>'.$date.'</td></tr>';
}
?>
</table>
</div>
<?php
include 'styles.html';
?>