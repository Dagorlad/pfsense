<?php
/*
	services_ntpd.php

	Copyright (C) 2013	Dagorlad
	Copyright (C) 2012	Jim Pingle
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/
/*
	pfSense_MODULE:	ntpd
*/

##|+PRIV
##|*IDENT=page-services-ntpd
##|*NAME=Services: NTP
##|*DESCR=Allow access to the 'Services: NTP' page.
##|*MATCH=services_ntpd.php*
##|-PRIV

require("guiconfig.inc");

if (empty($config['ntpd']['interface']))
	if ($config['installedpackages']['openntpd'] && empty($config['installedpackages']['openntpd']['config'][0]['interface'])) {
		$pconfig['interface'] = explode(",", $config['installedpackages']['openntpd']['config'][0]['interface']);
		unset($config['installedpackages']['openntpd']);
	} else
		$pconfig['interface'] = array();
else
	$pconfig['interface'] = explode(",", $config['ntpd']['interface']);

if ($_POST) {

	unset($input_errors);
	$pconfig = $_POST;

	if (!$input_errors) {
		if (is_array($_POST['interface']))
			$config['ntpd']['interface'] = implode(",", $_POST['interface']);
		elseif (isset($config['ntpd']['interface']))
			unset($config['ntpd']['interface']);

		if (!empty($_POST['gpsport']) && file_exists('/dev/'.$_POST['gpsport']))
			$config['ntpd']['gpsport'] = $_POST['gpsport'];
		elseif (isset($config['ntpd']['gpsport']))
			unset($config['ntpd']['gpsport']);

		if (!empty($_POST['gpsorphan']) && ($_POST['gpsorphan'] < 16) && ($_POST['gpsorphan'] != '12'))
			$config['ntpd']['orphan'] = $_POST['gpsorphan'];
		elseif (isset($config['ntpd']['orphan']))
			unset($config['ntpd']['orphan']);

		if (!empty($_POST['logpeer']))
			$config['ntpd']['logpeer'] = $_POST['logpeer'];
		elseif (isset($config['ntpd']['logpeer']))
			unset($config['ntpd']['logpeer']);

		if (!empty($_POST['logsys']))
			$config['ntpd']['logsys'] = $_POST['logsys'];
		elseif (isset($config['ntpd']['logsys']))
			unset($config['ntpd']['logsys']);

		if (!empty($_POST['clockstats']))
			$config['ntpd']['clockstats'] = $_POST['clockstats'];
		elseif (isset($config['ntpd']['clockstats']))
			unset($config['ntpd']['clockstats']);

		if (!empty($_POST['loopstats']))
			$config['ntpd']['loopstats'] = $_POST['loopstats'];
		elseif (isset($config['ntpd']['loopstats']))
			unset($config['ntpd']['loopstats']);

		if (!empty($_POST['peerstats']))
			$config['ntpd']['peerstats'] = $_POST['peerstats'];
		elseif (isset($config['ntpd']['peerstats']))
			unset($config['ntpd']['peerstats']);

		if (!empty($_POST['statsgraph']))
			$config['ntpd']['statsgraph'] = $_POST['statsgraph'];
		elseif (isset($config['ntpd']['statsgraph']))
			unset($config['ntpd']['statsgraph']);

		if (!empty($_POST['leaptxt']))
			$config['ntpd']['leapsec'] = base64_encode($_POST['leaptxt']);
		elseif (isset($config['ntpd']['leapsec']))
			unset($config['ntpd']['leapsec']);

		if (is_uploaded_file($_FILES['leapfile']['tmp_name']))
			$config['ntpd']['leapsec'] = base64_encode(file_get_contents($_FILES['leapfile']['tmp_name']));

		write_config("Updated NTP Server Settings");

		$retval = 0;
		$retval = system_ntp_configure();
		$savemsg = get_std_save_message($retval);

	}
}

$pconfig['ntpd'] = &$config['ntpd'];
$pgtitle = array(gettext("Services"),gettext("NTP"));
$shortcut_section = "ntp";
include("head.inc");

?>
<script type="text/javascript">
	function show_advanced(showboxID, configvalueID) {
		document.getElementById(showboxID).innerHTML='';
		aodiv = document.getElementById(configvalueID);
		aodiv.style.display = "block";
	}
</script>


<body link="#0000CC" vlink="#0000CC" alink="#0000CC">
<?php include("fbegin.inc"); ?>
<form action="services_ntpd.php" method="post" name="iform" id="iform" enctype="multipart/form-data" accept-charset=utf-8>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
	<td>
<?php
	$tab_array = array();
	$tab_array[] = array(gettext("NTP"), true, "services_ntpd.php");
	$tab_array[] = array(gettext("PPS"), false, "services_ntpd_pps.php");
	$tab_array[] = array(gettext("Serial GPS"), false, "services_ntpd_gps.php");
	display_top_tabs($tab_array);
?>
	</td>
  </tr>
  <tr>
	<td>
		<div id="mainarea">
		<table class="tabcont" width="100%" border="0" cellpadding="6" cellspacing="0">
			<tr>
				<td colspan="2" valign="top" class="listtopic"><?=gettext("NTP Server Configuration"); ?></td>
			</tr>
			<tr>
				<td width="22%" valign="top" class="vncellreq">Interface(s)</td>
				<td width="78%" class="vtable">
<?php
	$interfaces = get_configured_interface_with_descr();
	$carplist = get_configured_carp_interface_list();
	foreach ($carplist as $cif => $carpip)
		$interfaces[$cif] = $carpip." (".get_vip_descr($carpip).")";
	$aliaslist = get_configured_ip_aliases_list();
	foreach ($aliaslist as $aliasip => $aliasif)
		$interfaces[$aliasip] = $aliasip." (".get_vip_descr($aliasip).")";
	$size = (count($interfaces) < 10) ? count($interfaces) : 10;
?>
					<select id="interface" name="interface[]" multiple="true" class="formselect" size="<?php echo $size; ?>">
<?php	
	foreach ($interfaces as $iface => $ifacename) {
		if (!is_ipaddr(get_interface_ip($iface)) && !is_ipaddr($iface))
			continue;
		echo "<option value='{$iface}'";
		if (in_array($iface, $pconfig['interface']))
			echo "selected";
		echo ">{$ifacename}</option>\n";
	} ?>
					</select>
					<br/>
					<br/><?php echo gettext("Interfaces without an IP address will not be shown."); ?>
					<br/>
					<br/><?php echo gettext("Selecting no interfaces will listen on all interfaces with a wildcard."); ?>
					<br/><?php echo gettext("Selecting all interfaces will explicitly listen on only the interfaces/IPs specified."); ?>
				</td>
			</tr>
<?php /* Probing would be nice, but much more complex. Would need to listen to each port for 1s+ and watch for strings. */ ?>
<?php $serialports = glob("/dev/cua?[0-9]{,.[0-9]}", GLOB_BRACE); ?>
<?php if (!empty($serialports)): ?>
			<tr>
				<td width="22%" valign="top" class="vncellreq">Serial GPS</td>
				<td width="78%" class="vtable">
					<select name="gpsport">
						<option value="">none</option>
			<?php foreach ($serialports as $port):
				$shortport = substr($port,5);
				$selected = ($shortport == $config['ntpd']['gpsport']) ? " selected" : "";?>
						<option value="<?php echo $shortport;?>"<?php echo $selected;?>><?php echo $shortport;?></option>
			<?php endforeach; ?>
					</select>
					<br/>
					<br/><?php echo gettext("The GPS must provide NMEA format output!"); ?>
					<br/>
					<br/><?php echo gettext("All serial ports are listed, be sure to pick only the port with the GPS attached."); ?>
					<br/>
					<br/><?php echo gettext("It is best to configure at least 2 servers under"); ?> <a href="system.php"><?php echo gettext("System > General"); ?></a> <?php echo gettext("to avoid loss of sync if the GPS data is not valid over time. Otherwise ntpd may only use values from the unsynchronized local clock when providing time to clients."); ?>
				</td>
			</tr>
<?php endif; ?>
			<tr>
				<td width="22%" valign="top" class="vncellreq">Orphan mode</td>
				<td width="78%" class="vtable">
					<input name="gpsorphan" type="text" class="formfld unknown" id="gpsorphan" min="1" max="16" size="20" value="<?=htmlspecialchars($pconfig['orphan']);?>"><?php echo gettext("(0-15)");?><br>
					<?php echo gettext("Orphan mode allows the system clock to be used when no other clocks are available. The number here specifies the stratum reported during orphan mode and should normally be set for a number high enough to insure any others servers available to clients are preferred over this server. (default: 12)."); ?>
				</td>
			</tr>
			<tr>
				<td width="22%" valign="top" class="vncellreq">NTP graphs</td>
				<td width="78%" class="vtable">
					<input name="statsgraph" type="checkbox" class="formcheckbox" id="statsgraph" <?php if($pconfig['statsgraph']) echo " checked"; ?>>
					<?php echo gettext("Enable rrd graphs of NTP statistics (default: disabled)."); ?>
				</td>
			</tr>
			<tr>
				<td width="22%" valign="top" class="vncellreq">Syslog logging</td>
				<td width="78%" class="vtable">
					<?php echo gettext("These options enable additional messages from NTP to be written to the System Log."); ?>
					<br/><br/>
					<input name="logpeer" type="checkbox" class="formcheckbox" id="logpeer" <?php if($pconfig['logpeer']) echo " checked"; ?>>
					<?php echo gettext("Enable logging of peer messages (default: disabled)."); ?>
					<br/>
					<input name="logsys" type="checkbox" class="formcheckbox" id="logsys" <?php if($pconfig['logsys']) echo " checked"; ?>>
					<?php echo gettext("Enable logging of system messages (default: disabled)."); ?>
				</td>
			</tr>
			<tr>
				<td width="22%" valign="top" class="vncellreq">Statistics logging</td>
				<td width="78%" class="vtable">
					<div id="showstatisticsbox">
					<input type="button" onClick="show_advanced('showstatisticsbox', 'showstatistics')" value="<?=gettext("Advanced");?>"></input> - <?=gettext("Show statistics logging options");?></a>
					</div>
					<div id="showstatistics" style="display:none">
					<strong><?php echo gettext("Warning: ")?></strong><?php echo gettext("these options will create persistant daily log files in /var/log/ntp."); ?>
					<br/><br/>
					<input name="clockstats" type="checkbox" class="formcheckbox" id="clockstats" <?php if($pconfig['clockstats']) echo " checked"; ?>>
					<?php echo gettext("Enable logging of reference clock statistics (default: disabled)."); ?>
					<br/>
					<input name="loopstats" type="checkbox" class="formcheckbox" id="loopstats" <?php if($pconfig['loopstats']) echo " checked"; ?>>
					<?php echo gettext("Enable logging of clock discipline statistics (default: disabled)."); ?>
					<br/>
					<input name="peerstats" type="checkbox" class="formcheckbox" id="peerstats" <?php if($pconfig['peerstats']) echo " checked"; ?>>
					<?php echo gettext("Enable logging of NTP peer statistics (default: disabled)."); ?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="22%" valign="top" class="vncellreq">Leap seconds</td>
				<td width="78%" class="vtable">
					<div id="showleapsecbox">
					<input type="button" onClick="show_advanced('showleapsecbox', 'showleapsec')" value="<?=gettext("Advanced");?>"></input> - <?=gettext("Show Leap second configuration");?></a>
					</div>
					<div id="showleapsec" style="display:none">
					<?php echo gettext("A leap second file allows NTP to advertize an upcoming leap second addition or subtraction.");?>
					<?php echo gettext("Normally this is only useful if this server is a stratum 1 time server.");?>
					<br/><br/>
					<?php echo gettext("Enter Leap second configuration as text:");?><br />
					<textarea name="leaptxt" class="formpre" id="leaptxt" cols="65" rows="7"><?php $text = base64_decode(chunk_split($pconfig['leapsec'])); echo $text;?></textarea><br/>
					<strong><?php echo gettext("Or");?></strong><?php echo gettext(", select a file to upload:");?>
					<input type="file" name="leapfile" class="formfld file" id="leapfile">
					</div>
				</td>
			</tr>
			<tr>
				<td width="22%" valign="top">&nbsp;</td>
				<td width="78%">
				<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save");?>">
				</td>
			</tr>
		</table>
</div></td></tr></table>
</form>
<?php include("fend.inc"); ?>
</body>
</html>
<!--
/*
</br><p> $text</p></br>
<?php $text = base64_decode(chunk_split($pconfig['leapsec']));?>
<?php var_dump($text); ?>
</br><p> _FILES</p></br>
<?php var_dump($_FILES); ?>
</br><p> $config</p></br>
<?php var_dump($config['ntpd']['gps']); ?>
</br><p> $_POST</p></br>
<?php var_dump($_POST); ?>
</br><p> $pconfig</p></br>
<?php var_dump($pconfig); ?>
*/
-->
