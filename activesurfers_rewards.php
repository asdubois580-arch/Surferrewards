<?php
////////////////////////////////////////////////////////
// LFMTE ACTIVE SURFER REWARDS MOD 
// (c) 2008-2013 Simon B Kelly. All rights reserved.
// http://replytosimon.com  
//    
// Not for resale.
// Sold at: http://thetrafficexchangescript.com
////////////////////////////////////////////////////////

include "../inc/checkauth.php"; 
include_once "../inc/funcs.php";

if(!isset($_SESSION["adminid"])) { exit; };

?>
<html>

<head>
<script type="text/javascript" src="../inc/qtip.js"></script>
<script type="text/javascript" src="../inc/jsfuncs.js"></script>
<script type="text/javascript" src="../inc/jquery.js"></script>
</head>

<body>
<center>

<?

$time = time();
$today = date("Y-m-d");
$yesterday = date("Y-m-d",$time - 86400); 
$tomorrow = date("Y-m-d",$time + 129600); // add 36 hours just in case it's nearly midnight...

$limit=50;
$offset = isset($_GET['offset'])?$_GET['offset']:'';

if (empty($offset)) { $offset=0; } 

$rewardspan = 5;
if (file_exists("peelads.php")) {
	$rewardspan++;
}
if (file_exists("sqbanners.php")) {
	$rewardspan++;
}
if (file_exists("../inc/porkyp_funcs.php")) {
	$rewardspan++;
}

if (file_exists("minibranders.php")) {
	$rewardspan++;
}
if (file_exists("trafficpods.php")) {
	$rewardspan++;
}

if (file_exists("piggybank.php")) {
	$rewardspan++;
}

####################

//Begin main page

####################

echo("<h4><b>Active Surfer Rewards</b></h4>
");

if(!is_numeric($_GET['promoid'])) {
echo("<p><b>Invalid Promo ID</b></p>");
exit;
}

$res = mysql_query("SELECT COUNT(*) FROM `{$prefix}active_surfers` WHERE awarded>0 and promoid=".$_GET['promoid']);
$number = @mysql_result($res,0);
$limit = 50;
$finish = $offset + $limit;
if($finish > $number) { $finish = $number; }

$numtodraw = 5;
if (isset($_POST['numtodraw']) && is_numeric($_POST['numtodraw'])) {
	$numtodraw = $_POST['numtodraw'];
} elseif (isset($_GET['numtodraw']) && is_numeric($_GET['numtodraw'])) {
	$numtodraw = $_GET['numtodraw'];
}
if ($numtodraw > $number || $numtodraw < 1) { $numtodraw = $number; }

$r1 = mysql_result(mysql_query("SELECT COUNT(id) FROM `{$prefix}active_surfers` WHERE awarded>0 AND upgrade>0 and promoid=".$_GET['promoid']),0);
$r2 = mysql_result(mysql_query("SELECT COUNT(id) FROM `{$prefix}active_surfers` WHERE awarded>0 AND cash>0 and promoid=".$_GET['promoid']),0);
$r3 = mysql_result(mysql_query("SELECT COUNT(id) FROM `{$prefix}active_surfers` WHERE awarded>0 AND credits>0 and promoid=".$_GET['promoid']),0);
$r4 = mysql_result(mysql_query("SELECT COUNT(id) FROM `{$prefix}active_surfers` WHERE awarded>0 AND bannerimps>0 and promoid=".$_GET['promoid']),0);
$r5 = mysql_result(mysql_query("SELECT COUNT(id) FROM `{$prefix}active_surfers` WHERE awarded>0 AND textimps>0 and promoid=".$_GET['promoid']),0);

$r6 = mysql_result(mysql_query("SELECT COUNT(id) FROM `{$prefix}active_surfers` WHERE awarded>0 AND peelimps>0 and promoid=".$_GET['promoid']),0);
$r7 = mysql_result(mysql_query("SELECT COUNT(id) FROM `{$prefix}active_surfers` WHERE awarded>0 AND sqbanimps>0 and promoid=".$_GET['promoid']),0);
$r8 = mysql_result(mysql_query("SELECT COUNT(id) FROM `{$prefix}active_surfers` WHERE awarded>0 AND porkyps>0 and promoid=".$_GET['promoid']),0);

$r9 = mysql_result(mysql_query("SELECT COUNT(id) FROM `{$prefix}active_surfers` WHERE awarded>0 AND mbrands>0 and promoid=".$_GET['promoid']),0);
$r10 = mysql_result(mysql_query("SELECT COUNT(id) FROM `{$prefix}active_surfers` WHERE awarded>0 AND trafficpods>0 and promoid=".$_GET['promoid']),0);

$r11 = mysql_result(mysql_query("SELECT COUNT(id) FROM `{$prefix}active_surfers` WHERE awarded>0 AND piggybank>0 and promoid=".$_GET['promoid']),0);

$res = mysql_query("SELECT SUM(cash), SUM(credits), SUM(bannerimps), SUM(textimps), SUM(peelimps), SUM(sqbanimps), SUM(porkyps), SUM(mbrands), SUM(trafficpods), SUM(piggybank) FROM `{$prefix}active_surfers` WHERE awarded>0 and promoid=".$_GET['promoid']);
$row = mysql_fetch_row($res);

echo 'Here are the total Active Surfer Rewards to date:
<p>
<table align="center" border="1" cellpadding="2" cellspacing="0">
<tr bgcolor="#F0F0F0"><th>&nbsp;</th><th>Upgrades</th><th width=80>Cash</th><th width=80>Credits</th><th width=80>Banners</th><th width=80>Text Links</th>';

if (file_exists("peelads.php")) {
	echo '<th width=80>Peels</th>';
}

if (file_exists("sqbanners.php")) {
	echo '<th width=80>Square Banners</th>';
}

if (file_exists("../inc/porkyp_funcs.php")) {
	echo '<th width=80>Porky Points</th>';
}

if (file_exists("minibranders.php")) {
	echo '<th width=80>Mini Branders</th>';
}
if (file_exists("trafficpods.php")) {
	echo '<th width=80>Pod Points</th>';
}

if (file_exists("piggybank.php")) {
	echo '<th width=80>Piggy Bank</th>';
}

echo '</tr>
<tr align="center"><th>Claims</th><td>'.$r1.'</td><td>'.$r2.'</td><td>'.$r3.'</td><td>'.$r4.'</td><td>'.$r5.'</td>';

if (file_exists("peelads.php")) {
	echo '<td>'.$r6.'</td>';
}

if (file_exists("sqbanners.php")) {
	echo '<td>'.$r7.'</td>';
}

if (file_exists("../inc/porkyp_funcs.php")) {
	echo '<td>'.$r8.'</td>';
}

if (file_exists("minibranders.php")) {
	echo '<td>'.$r9.'</td>';
}
if (file_exists("trafficpods.php")) {
	echo '<td>'.$r10.'</td>';
}

if (file_exists("piggybank.php")) {
	echo '<td>'.$r11.'</td>';
}

echo '</tr>
<tr align="center"><th>Value</th><td>n/a</td><td>$'.($row[0]+0).'</td><td>'.($row[1]+0).'</td><td>'.($row[2]+0).'</td><td>'.($row[3]+0).'</td>';

if (file_exists("peelads.php")) {
	echo '<td>'.($row[4]+0).'</td>';
}

if (file_exists("sqbanners.php")) {
	echo '<td>'.($row[5]+0).'</td>';
}

if (file_exists("../inc/porkyp_funcs.php")) {
	echo '<td>'.($row[6]+0).'</td>';
}

if (file_exists("minibranders.php")) {
	echo '<td>'.($row[7]+0).'</td>';
}
if (file_exists("trafficpods.php")) {
	echo '<td>'.($row[8]+0).'</td>';
}

if (file_exists("piggybank.php")) {
	echo '<td>'.($row[9]+0).'</td>';
}

echo '</tr>
</table>
<br><br>

<table align="center" border="1" cellpadding="5" cellspacing="0">
<tr bgcolor="#F0F0F0"><td align="center">
<form action="activesurfers_rewards.php?promoid='.$_GET['promoid'].'&drawmembers=yes" method="post">
<font size="3"><b>Randomly Draw <input type="text" name="numtodraw" value="'.$numtodraw.'" size="3"> Members Who Received This Reward</b></font><br />
<input type="submit" value="Draw">
</form>
</td></tr>
</table>
<br>

';

if ($_GET['drawmembers'] == "yes" && is_numeric($numtodraw)) {
	echo '<br>Randomly selected '.$numtodraw.' out of '.$number.' members who received this reward.<br><a href="activesurfers_rewards.php?promoid='.$_GET['promoid'].'">Show All Members</a>
	<br><br>If you want to remove a member from the list (your own account, for example) you can click "Exclude".  The list will then be redrawn without that account.<br>';
} else {
	echo '<br>A total of '.$number.' claims have been awarded for this reward.<br>';
}

// Being Sorting
if (($_GET["sortby"] != "") && ($_GET["sortorder"] != "")) {
	
	$sortorder = $_GET["sortorder"];
	if ($sortorder == "ASC") {
		$nextsort = "DESC";
	} else {
		$nextsort = "ASC";
	}
	
	if ($_GET["sortby"] == "claimdate") {
		$sortby = "a.claimed";
	} elseif ($_GET["sortby"] == "dateclicks") {
		$sortby = "s.clicks";
	} elseif ($_GET["sortby"] == "Id") {
		$sortby = "a.usrid";
	} else {
		$sortby = "m.".trim($_GET["sortby"]);
	}
	
} else {
	$sortby = "a.id";
	$sortorder = "ASC";
	$nextsort = "ASC";
}

// End Sorting

?>

<form name="mbrowse" id="mbrowse" method="post" target="_blank" action="giveprize_pop.php">
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td align="center"><table width="1100" border="0" cellpadding="1" cellspacing="0" class="lfmtable">
      <tr>
        <td colspan="17" align="left" valign="bottom">
		<div align="left">Page: <?
		if (!isset($_GET['drawmembers']) || $_GET['drawmembers'] != "yes") {
			// Page Navigation
			$pages=intval($number/$limit);
			if ($offset>1) {
				$prevoffset=$offset-$limit; 
				echo "<a href=\"activesurfers_rewards.php?offset=$prevoffset&sortby=".$_GET['sortby']."&sortorder=".$_GET['sortorder']."&promoid=".$_GET['promoid']."\">Prev</a> \n"; 
			}
			for ($i=1;$i<$pages+2;$i++) {
				$newoffset=$limit*($i-1);
				if($newoffset==$offset) {
					echo "<b>$i</b> ";
				} else {
					echo "<a href=\"activesurfers_rewards.php?offset=$newoffset&sortby=".$_GET['sortby']."&sortorder=".$_GET['sortorder']."&promoid=".$_GET['promoid']."\">$i</a> \n";
				}
			}
			if ($number>($offset+$limit)) {
				$nextoffset=$offset+$limit;
				echo "<a href=\"activesurfers_rewards.php?offset=$nextoffset&sortby=".$_GET['sortby']."&sortorder=".$_GET['sortorder']."&promoid=".$_GET['promoid']."\">Next</a><p>\n";
			}
			// End Page Navigation
		} else {
			echo "<b>1</b>";
		}
		?></div>
		<input name="GivePrize" type="submit" class="lfmtable" style="font-size:9px" value="Give Prize" />
	</td>
      </tr>
      <tr class="admintd">
        <th rowspan="2" align="center" nowrap="NOWRAP" background="images/lfmtablethbg.jpg"><strong>
		<font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="activesurfers_rewards.php?sortby=Id&sortorder=<?=$nextsort?>&promoid=<?=$_GET['promoid']?>">User ID</a></font></strong></th>
        <th rowspan="2" align="center" nowrap="NOWRAP" background="images/lfmtablethbg.jpg"><strong>
		<font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="activesurfers_rewards.php?sortby=firstname&sortorder=<?=$nextsort?>&promoid=<?=$_GET['promoid']?>">First Name</a></font></strong></th>
        <th rowspan="2" align="center" nowrap="NOWRAP" background="images/lfmtablethbg.jpg"><strong>
		<font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="activesurfers_rewards.php?sortby=lastname&sortorder=<?=$nextsort?>&promoid=<?=$_GET['promoid']?>">Last Name</a></font></strong></th>
        <th rowspan="2" nowrap="NOWRAP" background="images/lfmtablethbg.jpg"><strong>
		<font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="activesurfers_rewards.php?sortby=username&sortorder=<?=$nextsort?>&promoid=<?=$_GET['promoid']?>">Username</a></font></strong></th>
        <th rowspan="2" align="center" nowrap="NOWRAP" background="images/lfmtablethbg.jpg"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="activesurfers_rewards.php?sortby=mtype&sortorder=<?=$nextsort?>&promoid=<?=$_GET['promoid']?>">MType</a></font></strong> </th>
 	
 	<th colspan="<? echo($rewardspan); ?>" align="center" background="images/lfmtablethbg.jpg"><font size="1"><b>Rewards Added</b></font></th>
 	
        <th rowspan="2" align="center" nowrap="NOWRAP" background="images/lfmtablethbg.jpg"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="activesurfers_rewards.php?sortby=claimdate&sortorder=<?=$nextsort?>&promoid=<?=$_GET['promoid']?>">Date Claimed</a></font></strong></th>
        
        <th rowspan="2" align="center" nowrap="NOWRAP" background="images/lfmtablethbg.jpg"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="activesurfers_rewards.php?sortby=dateclicks&sortorder=<?=$nextsort?>&promoid=<?=$_GET['promoid']?>">Clicks On Date</a></font></strong></th>
        
        <th rowspan="2" align="center" nowrap="NOWRAP" background="images/lfmtablethbg.jpg">&nbsp;</th>
        <th rowspan="2" width="16" align="center" nowrap="NOWRAP" background="images/lfmtablethbg.jpg"><input type="checkbox" name="checkbox" value="checkbox" onClick="javascript:checkAll(document.getElementById('mbrowse'),'mcheck');" /></th>
      </tr>
      
      <tr>
	<th bgcolor="#EEEEEE" align="center"><font size="1"><b>Upgrade</b></font></th>
	<th bgcolor="#EEEEEE" align="center"><font size="1"><b>Cash</b></font></th>
	<th bgcolor="#EEEEEE" align="center"><font size="1"><b>Credits</b></font></th>
	<th bgcolor="#EEEEEE" align="center"><font size="1"><b>Banners</b></font></th>
	<th bgcolor="#EEEEEE" align="center"><font size="1"><b>Text Links</b></font></th>
	<?
	
	if (file_exists("peelads.php")) {
		echo '<th bgcolor="#EEEEEE" align="center"><font size="1"><b>Peels</b></font></th>';
	}
	
	if (file_exists("sqbanners.php")) {
		echo '<th bgcolor="#EEEEEE" align="center"><font size="1"><b>Square Banners</b></font></th>';
	}
	
	if (file_exists("../inc/porkyp_funcs.php")) {
		echo '<th bgcolor="#EEEEEE" align="center"><font size="1"><b>Porky Points</b></font></th>';
	}
	
	if (file_exists("minibranders.php")) {
		echo '<th bgcolor="#EEEEEE" align="center"><font size="1"><b>Mini Branders</b></font></th>';
	}
	if (file_exists("trafficpods.php")) {
		echo '<th bgcolor="#EEEEEE" align="center"><font size="1"><b>Pod Points</b></font></th>';
	}
	
	if (file_exists("piggybank.php")) {
		echo '<th bgcolor="#EEEEEE" align="center"><font size="1"><b>Piggy Bank</b></font></th>';
	}
	
	?>
      </tr>

<?

if ($_GET['drawmembers'] == "yes" && is_numeric($numtodraw)) {
	
	// Randomly Draw A Set Number Of Members
	
	if (isset($_GET['excludelist']) && strlen($_GET['excludelist']) > 1) {
		$excludequery = " AND a.usrid NOT IN (".substr($_GET['excludelist'], 0, -1).")";
	} else {
		$excludequery = "";
	}
	
	$res = mysql_query("SELECT a.usrid AS usrid, a.upgrade AS upgrade, a.cash AS cash, a.credits AS credits, a.bannerimps AS bannerimps, a.textimps AS textimps, a.peelimps AS peelimps, a.sqbanimps AS sqbanimps, a.porkyps AS porkyps, a.mbrands AS mbrands, a.trafficpods AS trafficpods, a.piggybank AS piggybank, a.claimed AS claimed, m.username AS username, m.firstname AS firstname, m.lastname AS lastname, m.mtype AS mtype, s.clicks AS dateclicks FROM `".$prefix."active_surfers` a LEFT JOIN `".$prefix."members` m ON a.usrid=m.Id LEFT JOIN `".$prefix."surflogs` s ON (a.usrid=s.userid AND a.claimed=s.date) WHERE a.awarded > 0 and a.promoid='".$_GET['promoid']."'".$excludequery." ORDER BY RAND() LIMIT $numtodraw") or die(mysql_error());

} else {

	// Show All Members Who Claimed The Reward

	$res = mysql_query("SELECT a.usrid AS usrid, a.upgrade AS upgrade, a.cash AS cash, a.credits AS credits, a.bannerimps AS bannerimps, a.textimps AS textimps, a.peelimps AS peelimps, a.sqbanimps AS sqbanimps, a.porkyps AS porkyps, a.mbrands AS mbrands, a.trafficpods AS trafficpods, a.piggybank AS piggybank, a.claimed AS claimed, m.username AS username, m.firstname AS firstname, m.lastname AS lastname, m.mtype AS mtype, s.clicks AS dateclicks FROM `".$prefix."active_surfers` a LEFT JOIN `".$prefix."members` m ON a.usrid=m.Id LEFT JOIN `".$prefix."surflogs` s ON (a.usrid=s.userid AND a.claimed=s.date) WHERE a.awarded > 0 and a.promoid='".$_GET['promoid']."' ORDER BY ".$sortby." ".$sortorder." LIMIT $offset,$limit") or die(mysql_error());
	
}

if(mysql_num_rows($res)) {
   while( $row=mysql_fetch_array($res) ) {
   	
   	if($bgcolor == "#FFFFFF") { $bgcolor="#DDDDDD"; } else { $bgcolor="#FFFFFF"; }
	
   	$accname = "n/a";
   	$useraccname = "n/a";
   	$clicksondate = "n/a";
   	
   	if($row['upgrade']>0) { $accname = @mysql_result(mysql_query("SELECT accname FROM {$prefix}membertypes WHERE mtid=".$row['upgrade']),0); }
   	
   	if($row['mtype']>0) { $useraccname = @mysql_result(mysql_query("SELECT accname FROM {$prefix}membertypes WHERE mtid=".$row['mtype']),0); }
   	
   	if (isset($row['dateclicks']) && is_numeric($row['dateclicks']) && $row['dateclicks'] >= 0) { $clicksondate = $row['dateclicks']; }
   	
	echo '<tr bgcolor="'.$bgcolor.'" onMouseOver="this.bgColor=\'#99bb99\';" onMouseOut="this.bgColor=\''.$bgcolor.'\';">
	<td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a target="_blank" href="admin.php?f=mm&searchfield=Id&searchtext='.$row['usrid'].'&sf=browse">'.$row['usrid'].'</a></font></td>
	<td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">'.$row['firstname'].'</font></td>
	<td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">'.$row['lastname'].'</font></td>
	<td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">'.$row['username'].'</font></td>
	<td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">'.$useraccname.'</font></td>
	<td align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">'.$accname.'</font></td>
	<td align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">'.$row['cash'].'</font></td>
	<td align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">'.$row['credits'].'</font></td>
	<td align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">'.$row['bannerimps'].'</font></td>
	<td align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">'.$row['textimps'].'</font></td>';
	
	if (file_exists("peelads.php")) {
		echo '<td align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">'.$row['peelimps'].'</font></td>';
	}
	
	if (file_exists("sqbanners.php")) {
		echo '<td align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">'.$row['sqbanimps'].'</font></td>';
	}
	
	if (file_exists("../inc/porkyp_funcs.php")) {
		echo '<td align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">'.$row['porkyps'].'</font></td>';
	}
	
	if (file_exists("minibranders.php")) {
		echo '<td align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">'.$row['mbrands'].'</font></td>';
	}
	if (file_exists("trafficpods.php")) {
		echo '<td align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">'.$row['trafficpods'].'</font></td>';
	}
	
	if (file_exists("piggybank.php")) {
		echo '<td align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">'.$row['piggybank'].'</font></td>';
	}
	
	echo '<td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">'.$row['claimed'].'</font></td>
	<td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">'.$clicksondate.'</font></td>';
	
	if ($_GET['drawmembers'] == "yes" && is_numeric($numtodraw)) {
		echo '<td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><input type="button" value="Exclude" onclick="window.location=\'activesurfers_rewards.php?promoid='.$_GET['promoid'].'&drawmembers=yes&numtodraw='.$numtodraw.'&excludelist='.$_GET['excludelist'].$row['usrid'].',\';"></font></td>';
	} else {
		echo '<td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>';
	}
	
	echo '<td align="center"><input type="checkbox" class="mcheck" name="memcheck[]" value="'.$row['usrid'].'" onclick="HighlightRowIfChecked(this);" />
	</tr>';
   }

   echo '</table></td></tr><tr><td></td></tr></table></form><p>';

} else {
   echo '<tr><td colspan=17 align="center"><br>There are no awarded claims to show for this reward or promo</td></tr>
   </table></td></tr><tr><td></td></tr></table></form>';
}

echo '
</center>
</body>
</html>';

?>