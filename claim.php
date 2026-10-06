<?php
////////////////////////////////////////////////////////
// LFMTE ACTIVE SURFER REWARDS MOD 
// (c) 2008-2019 Simon B Kelly. All rights reserved.
// http://replytosimon.com  
//    
// Not for resale.
// Sold at: http://thetrafficexchangescript.com
////////////////////////////////////////////////////////

//include 'inc/tsettings.php';
//echo date_default_timezone_get();
include "inc/userauth.php";

ob_start();

$page = array();

$userid = intval($_SESSION['userid']);
// Get all of the Members Details Here and put them into $member_row to be used throughout the script.
$sql = "SELECT
					email,
					mtype,
					upgend,
					lastip,
					clickstoday
						FROM {$prefix}members
							WHERE Id=$userid
								LIMIT 1
				";
$getuserdata = mysql_query($sql);

/** *********************************************
*   Get ALL of our Members ROW Fields HERE!
*/
if(($getuserdata) && (mysql_num_rows($getuserdata) == 1))
{
	$member_row = mysql_fetch_assoc($getuserdata);
}
else
{
// This is a Fatal Error and we have no reason to continue
	die('Error Accessing Table:  File: '.__FILE__.' : '.__LINE__);
}

$today = date("Y-m-d");
$time = time();

//@TB Added Init stuff
$prize = isset($_POST['prize'])?$_POST['prize']:'';
$_GET['promoid'] = isset($_GET['promoid'])?$_GET['promoid']:-1;


$claim = '<br><br>';

// Has member already claimed today?
$res = mysql_query("SELECT id FROM {$prefix}active_surfers WHERE usrid=$userid AND promoid=0 AND claimed='$today'");
if($res)
{
	$alreadyclaimed = (mysql_num_rows($res) > 0)?true:false;
}
else
{
	$alreadyclaimed = false;
}
// minimum number required for award?
$res = mysql_query("SELECT surf FROM {$prefix}active_surfer_rewards WHERE active > 0 ORDER BY surf LIMIT 1");
if(($res) && (mysql_num_rows($res) > 0))
{
	$row = mysql_fetch_assoc($res);
	$min = $row['surf'];
}
else
{ 
	$min = 0;
}
// get info for best prize available...
$sql = "SELECT * 
					FROM {$prefix}active_surfer_rewards 
						WHERE active>0 
							AND surf<={$member_row['clickstoday']} 
								ORDER BY surf DESC
									LIMIT 1
				";
$res = mysql_query($sql);
if(($res) && (mysql_num_rows($res) == 1))
{
	$asr_row = mysql_fetch_array($res);
	if($prize=="upgrade") { $amount = $asr_row['upgrade']; }
	if($prize=="cash") { $amount = $asr_row['cash']; }
	if($prize=="credits") { $amount = $asr_row['credits']; }
	if($prize=="bannerimps") { $amount = $asr_row['bannerimps']; }
	if($prize=="textimps") { $amount = $asr_row['textimps']; }

	if($prize=="peelimps") { $amount = $asr_row['peelimps']; }
	if($prize=="sqbanimps") { $amount = $asr_row['sqbanimps']; }
	if($prize=="porkyps") { $amount = $asr_row['porkyps']; }
	
	if($prize=="mbrands") { $amount = $asr_row['mbrands']; }
	if($prize=="trafficpods") { $amount = $asr_row['trafficpods']; }
	
	if($prize=="piggybank") { $amount = $asr_row['piggybank']; }
}
else
{
	$amount = 0;
}

if($_GET['promoid'] == 0 && ($amount > 0))
{

if($alreadyclaimed == true)
{
	$claim .=  '<font color="red">You have already submitted a claim today!</font>';
}
else
{
	$sql = "INSERT INTO {$prefix}active_surfers (
						usrid,
						promoid,
						{$prize},
						views,
						claimed
						) VALUES (
						$userid,
						0,
						$amount,
						{$member_row['clickstoday']},
						'$today'
					)";
	mysql_query($sql) or die(mysql_error());
	$ffrm = mysql_insert_id();
// should prize be awarded automatically?
	if($asr_row['active'] == 2) {
		
// Process an Upgrade Prize
		if($prize == 'upgrade')
		{
			$member_row['upgend'] = date("Y-m-d",strtotime($member_row['upgend'])+86400);                       // add 24 hours to existing expiry date
			if($member_row['upgend'] <= date("Y-m-d")) { $member_row['upgend'] = date("Y-m-d",time()+129600); } // add 36 hours to current PHP time
			if($member_row['mtype'] > $amount) { $amount = $member_row['mtype']; }                            // don't downgrade existing members!
			mysql_query("UPDATE {$prefix}members SET mtype=$amount, upgend='{$member_row['upgend']}' WHERE Id=$userid LIMIT 1") or die(mysql_error());
		}
// Process a Cash Prize
		elseif($prize == "cash")
		{
			$sql = "INSERT INTO {$prefix}sales (
				affid,
				saledate,
				itemamount,
				itemname,
				commission,
				txn_id,
				prize
				) VALUES (
				$userid,
				NOW(),
				'$amount',
				'Active Surfer Reward',
				'$amount',
				'0',
				1
				)";
			$result = mysql_query($sql);
			if(!$result)
			{
				die('Error Accessing Table:  File: '.__FILE__.' : '.__LINE__);
			}
		}
// Process a Porky Points Prize
		elseif($prize=="porkyps")
		{
			if (file_exists("inc/porkyp_funcs.php"))
			{
				require_once "inc/porkyp_funcs.php";
				$porkytransfer = porkytransferpoints($member_row['email'], $member_row['lastip'], $amount);
				if ($porkytransfer !== true)
				{
					echo("There was a problem with this Porky Points transaction.");
					exit;
				}
			}
			else
			{
				echo("Porky Points is not setup correctly at this site.");
				exit;
			}
		}
		elseif($prize=="mbrands")
		{
			mysql_query("UPDATE {$prefix}members SET mbrandimps=mbrandimps+$amount WHERE Id=$userid LIMIT 1") or die(mysql_error());
		}
		elseif($prize=="trafficpods")
		{
			require_once "inc/trafficpods_funcs.php";
			trafficpods_addpoints($userid, $amount);
		}
		elseif($prize=="piggybank")
		{
			mysql_query("Insert into ".$prefix."piggybank (userid, transdate, descr, amount, status, ipnsaleid) values (".$userid.", NOW(), 'Active Surfer Reward', '".$amount."', '0', '0')") or die(mysql_error());
		}
		else
		{
			mysql_query("UPDATE {$prefix}members SET $prize=$prize+$amount WHERE Id=$userid LIMIT 1") or die(mysql_error());
		}
		mysql_query("UPDATE {$prefix}active_surfers SET awarded=$time WHERE id=$ffrm") or die(mysql_error());
		// Query settings table for sitename
		$sql = "SELECT
						sitename,
						replyaddress
						FROM ".$prefix."settings";

		$res = mysql_query($sql);
		if(($res) && (mysql_num_rows($res) > 0))
		{
			$settings_row = mysql_fetch_array($res);
			$sitename=$settings_row['sitename'];
			$replyaddress=$settings_row["replyaddress"];
		}
		else
		{
// No point going on so we can terminate
			die('Error Accessing Table:  File: '.__FILE__.' : '.__LINE__);
		}
			if($prize=="upgrade")
			{
// The case of having a non existant mtid should be checked in the main code
				$sql = "SELECT accname FROM {$prefix}membertypes WHERE mtid=$amount";
				$result = mysql_query($sql);
				if(($result) && (mysql_num_rows($result) > 0))
				{
					$mytpe_row = mysql_fetch_assoc($result);
					$accname = $mytpe_row['accname'];
					unset($mytpe_row);
				}
				else
				{
// TODO Handle a Non Existant mtid - this should never happen but it can!
//  We need to decide on what to do as this is a Live Claim and throwing up a DIE is a little drastic
// Need to have some default backup prize to replace it.
					die('Error Accessing Table:  File: '.__FILE__.' : '.__LINE__);
				}
				$reward = '1 day '.$accname.' Upgrade';
			}
			elseif($prize=="commission") { $reward = '$'.$amount.' USD cash'; }
			elseif($prize=="credits") { $reward = $amount.' Credits'; }
			elseif($prize=="bannerimps") { $reward = $amount.' Banner Impressions'; }
			elseif($prize=="textimps") { $reward = $amount.' Text Link Impressions'; }
			elseif($prize=="peelimps") { $reward = $amount.' Peel Ad Impressions'; }
			elseif($prize=="sqbanimps") { $reward = $amount.' Square Banner Impressions'; }
			elseif($prize=="porkyps") { $reward = $amount.' Porky Points'; }
			
			elseif($prize=="mbrands") { $reward = $amount.' Mini Branders'; }
			elseif($prize=="trafficpods") { $reward = $amount.' Pod Points'; }
			
			elseif($prize=="piggybank") { $balancename = @mysql_result(@mysql_query("Select value from `".$_SESSION["prefix"]."piggybank_settings` where field='balancename'"), 0); $reward = '$'.$amount.' '.$balancename; }
			
		$claim .= "<em>Your reward has been added to your account.</em>";
		} else {
		$claim .=  '<p>Your claim has been received and should be processed shortly.';
		}
	}
	$claim .=  '<p>Surf '.$min.' or more sites tomorrow and you can claim another reward...<br><br>';
}
else
{
if(!$min) {
	// No Rewards
	} else {
	$claim .=  '<font color="black" size="3"><b>';
	if($member_row['clickstoday'] < 1) {
		$claim .=  'You have not surfed any pages today.<br><br>';
		} else {
		$claim .=  'Today you have surfed '.$member_row['clickstoday'].' pages.<br><br>';
		}
	if($min <= $member_row['clickstoday']) {
		if($alreadyclaimed == false) {
			$claim .=  '</b></font><p>Choose your prize and click the button to submit your claim:
<form action="claim.php?promoid=0" method="POST">
<select name="prize">
<option value="none">PLEASE SELECT:</option>';

			if($asr_row['upgrade']>0 && ($member_row['mtype']==1 || $member_row['mtype']==$asr_row['upgrade'])) {
				$accname = mysql_result(mysql_query("SELECT accname FROM {$prefix}membertypes WHERE mtid=".$asr_row['upgrade']),0);
				$claim .=  '<option value="upgrade">1-day '.$accname.' Upgrade</option>';
				}

			if($asr_row['cash']>0) { $claim .=  '<option value="cash">$'.$asr_row['cash'].' USD cash</option>'; }
			if($asr_row['credits']>0) { $claim .=  '<option value="credits">'.$asr_row['credits'].' Credits</option>'; }
			if($asr_row['bannerimps']>0) { $claim .=  '<option value="bannerimps">'.$asr_row['bannerimps'].' Banner Imps</option>'; }
			if($asr_row['textimps']>0) { $claim .=  '<option value="textimps">'.$asr_row['textimps'].' Text Link Imps</option>'; }
			
			if($asr_row['peelimps']>0) { $claim .=  '<option value="peelimps">'.$asr_row['peelimps'].' Peel Imps</option>'; }
			if($asr_row['sqbanimps']>0) { $claim .=  '<option value="sqbanimps">'.$asr_row['sqbanimps'].' Square Banner Imps</option>'; }
			
			if($asr_row['mbrands']>0) { $claim .=  '<option value="mbrands">'.$asr_row['mbrands'].' Mini Branders</option>'; }
			if($asr_row['trafficpods']>0) { $claim .=  '<option value="trafficpods">'.$asr_row['trafficpods'].' Pod Points</option>'; }
			
			if($asr_row['piggybank']>0) { $balancename = @mysql_result(@mysql_query("Select value from `".$_SESSION["prefix"]."piggybank_settings` where field='balancename'"), 0); $claim .=  '<option value="piggybank">$'.$asr_row['piggybank'].' '.$balancename.'</option>'; }
			
			if ($asr_row['porkyps'] > 0 && file_exists("inc/porkyp_funcs.php")) {
				require_once "inc/porkyp_funcs.php";
				$porkystatus = porkychecklicense();
				if (is_numeric($porkystatus) && $porkystatus > 0) {
					if($asr_row['porkyps'] > 0 && $asr_row['porkyps'] <= $porkystatus) { $claim .=  '<option value="porkyps">'.$asr_row['porkyps'].' Porky Points</option>'; }
				}
			}
			$claim .=  '
</select>
<p><input type="submit" value="Submit Claim">
</form>
<br>
<b>';
			} else {
			$claim =  '<p><font color="black" size="3"><b>Thanks for surfing today!<br>Come back tomorrow to claim another reward...</b></font></p>';
			}
		}
	$claim .= '</b></font>';
	}
}
echo '<center>
<br><br><br>
<table border="1" bordercolor="black" cellpadding="5" cellspacing="0" width="500">
<tr><td align="center">

<font face="Tahoma" size="4"><b>Daily Active Surfer Rewards</b></font>
<hr>
<p>
<font size="1">' . date('F jS h:i A') . '</font>
<br>
<font size="2">You have <b>' . (23 - date("H")) . ' hours and ' . (59 - date("i")). ' minutes</b> left to claim a reward for today.</font>
<hr>
' . $claim;
if($alreadyclaimed == false)
{
	$res = mysql_query("SELECT * FROM `{$prefix}active_surfer_rewards` WHERE active ORDER BY surf");
	if(mysql_num_rows($res))
	{
    while($row=mysql_fetch_array($res)) {
		$award = '';
		if($row['upgrade']>0 && $member_row['mtype']==1)
		{
			$accname = @mysql_result(mysql_query("SELECT accname FROM {$prefix}membertypes WHERE mtid=".$row['upgrade']),0);
			$award.= ' or 1 day '.$accname.' Upgrade';
		}
		if($row['cash']>0) { $award.=' or $'.$row['cash'].' USD cash'; }
		if($row['credits']>0) { $award.=' or '.$row['credits'].' Credits'; }
		if($row['bannerimps']>0) { $award.=' or '.$row['bannerimps'].' Banners'; }
		if($row['textimps']>0) { $award.=' or '.$row['textimps'].' Text Links'; }
		
		if($row['peelimps']>0) { $award.=' or '.$row['peelimps'].' Peels'; }
		if($row['sqbanimps']>0) { $award.=' or '.$row['sqbanimps'].' Square Banners'; }
		
		if($row['mbrands']>0) { $award.=' or '.$row['mbrands'].' Mini Branders'; }
		if($row['trafficpods']>0) { $award.=' or '.$row['trafficpods'].' Pod Points'; }
		
		if($row['piggybank']>0) { $balancename = @mysql_result(@mysql_query("Select value from `".$_SESSION["prefix"]."piggybank_settings` where field='balancename'"), 0); $award.=' or $'.$row['piggybank'].' '.$balancename; }

		if ($row['porkyps'] > 0 && file_exists("inc/porkyp_funcs.php")) {
			require_once "inc/porkyp_funcs.php";
			$porkystatus = porkychecklicense();
			if (is_numeric($porkystatus) && $porkystatus > 0) {
				if($row['porkyps'] > 0 && $row['porkyps'] <= $porkystatus) { $award.=' or '.$row['porkyps'].' Porky Points'; }
			}
		}
		if($award == '') { $award = 'a prize'; } else { $award = substr($award, 4); }
		echo '<br><u><font face="Arial" size="3" color="#0000FF">Surf '.$row['surf'].' Pages</u></font><br><br><font face="Arial" size="2">Get '.$award.'<br><br>';
		}
	} else {
	echo '<p>There are no active surfer rewards available at the moment.';
	}
}
	echo '</font></td></tr></table>';

include("claim_promos.php");

echo '<br><br><br>
</center>
';
$page['style']= <<<EOD
  <style type="text/css">
	hr {
	border: 0;
	clear:both;
	display:block;
	width: 98%;
	background-color: #BBBBBB;
	height: 1px;
	}
  </style>
EOD;
$page['content'] = ob_get_clean();

// Display 
include "inc/theme.php";
load_template ($theme_dir."/header.php");
load_template ($theme_dir."/mmenu.php");
echo $page['style'];
echo $page['content'];
load_template ($theme_dir."/footer.php");
exit();