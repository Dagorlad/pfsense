<?php
/* $Id$ */
/*
	Copyright (C) 2013	Dagorlad
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
	pfSense_MODULE:	ntpd_pps
*/

##|+PRIV
##|*IDENT=page-services-ntpd-pps
##|*NAME=Services: NTP PPS page
##|*DESCR=Allow access to the 'Services: NTP PPS' page..
##|*MATCH=services_ntpd_pps.php*
##|-PRIV

require("guiconfig.inc");


$pconfig = &$config['ntpd']['pps'];

$pgtitle = array(gettext("Services"),gettext("NTP PPS"));
$shortcut_section = "ntp";
include("head.inc");
?>

<body link="#0000CC" vlink="#0000CC" alink="#0000CC">
<?php include("fbegin.inc"); ?>
<form action="services_ntpd_pps.php" method="post" name="iform" id="iform">
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
	<td>
<?php
	$tab_array = array();
	$tab_array[] = array(gettext("NTP"), false, "services_ntpd.php");
	$tab_array[] = array(gettext("PPS"), true, "services_ntpd_pps.php");
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
			<td colspan="2" valign="top" class="listtopic"><?php gettext("NTP PPS Configuration"); ?></td>
		</tr>
		<tr>
			<td width="22%" valign="top" class="vncellreq">
			</td>
			<td width="78%" class="vtable">Devices with a PPS output such as radios that listen to WWVB or DCF77 or a GPS via a serial port may be used as a PPS reference for NTP.
			<br/>
			<br/><?php echo gettext("At least 3 servers need to be configured under"); ?> <a href="system.php"><?php echo gettext("System > General"); ?></a> <?php echo gettext("to provide a time source."); ?>
			</td>
		</tr>
<?php $serialports = glob("/dev/cua?[0-9]{,.[0-9]}", GLOB_BRACE); ?>
<?php if (!empty($serialports)): ?>
		<tr>
			<td width="22%" valign="top" class="vncellreq">Serial port</td>
			<td width="78%" class="vtable">
				<select name="ppsport" class="formselect">
					<option value="">none</option>
					<?php foreach ($serialports as $port):
						$shortport = substr($port,5);
						$selected = ($shortport == $pconfig['port']) ? " selected" : "";?>
						<option value="<?php echo $shortport;?>"<?php echo $selected;?>><?php echo $shortport;?></option>
					<?php endforeach; ?>
				</select>&nbsp;
				<?php echo gettext("All serial ports are listed, be sure to pick the port with the PPS source attached."); ?>
				<br/><br/>
				<select id="ppsspeed" name="ppsspeed" class="formselect">
					<option value="0"<?php if(!$pconfig['speed']) echo ' selected'; ?>>4800</option>
					<option value="16"<?php if($pconfig['speed'] === '16') echo ' selected';?>>9600</option>
					<option value="32"<?php if($pconfig['speed'] === '32') echo ' selected';?>>19200</option>
					<option value="48"<?php if($pconfig['speed'] === '48') echo ' selected';?>>38400</option>
					<option value="64"<?php if($pconfig['speed'] === '64') echo ' selected';?>>57600</option>
					<option value="80"<?php if($pconfig['speed'] === '80') echo ' selected';?>>115200</option>
				</select>&nbsp;<?php echo gettext("Serial port baud rate (default: 4800)."); ?>
				<br/>
			</td>
		</tr>
		<tr>
			<td width="22%" valign="top" class="vncellreq">Fudge time 1</td>
			<td width="78%" class="vtable">
				<input name="ppsfudge1" type="text" class="formfld unknown" id="ppsfudge1" min="-1" max="1" size="20" value="<?=htmlspecialchars($pconfig['fudge1']);?>">(<?php echo gettext("seconds");?>)<br>
				<?php echo gettext("Fudge time 1 is used to specify the PPS signal offset");?> (<?php echo gettext("default");?>: 0.0).</td>
		</tr>
		<tr>
			<td width="22%" valign="top" class="vncellreq">Stratum</td>
			<td width="78%" class="vtable">
				<input name="ppsstratum" type="text" class="formfld unknown" id="ppsstratum" max="16" size="20" value="<?=htmlspecialchars($pconfig['stratum']);?>"><?php echo gettext("(0-16)");?><br>
				<?php echo gettext("This may be used to change the PPS Clock stratum");?> (<?php echo gettext("default");?>: 0). <?php echo gettext("This may be useful if, for some reason, you want ntpd to prefer a different clock"); ?></td>
		</tr>
		<tr>
			<td width="22%" valign="top" class="vncellreq">Flags</td>
			<td width="78%" class="vtable">
				<table>
					<tr>
						<td>
				<?php echo gettext("Normally there should be no need to change these options from the defaults."); ?></br>
						</td>
					</tr>
				</table>
				<table>
					<tr>
						<td>
							<input name="ppsflag1" type="checkbox" class="formcheckbox" id="ppsflag1"<?php if($pconfig['flag1']) echo ' checked'; ?>>
						</td>
						<td>
							<span class="vexpl"><?php echo gettext("Enable PPS signal processing (default: enabled)."); ?></span>
						</td>
					</tr>
					<tr>
						<td>
							<input name="ppsflag2" type="checkbox" class="formcheckbox" id="ppsflag2"<?php if($pconfig['flag2']) echo ' checked'; ?>>
						</td>
						<td>
							<span class="vexpl"><?php echo gettext("Enable falling edge PPS signal processing (default: rising edge)."); ?></span>
						</td>
					</tr>
					<tr>
						<td>
							<input name="ppsflag3" type="checkbox" class="formcheckbox" id="ppsflag3"<?php if($pconfig['flag3']) echo ' checked'; ?>>
						</td>
						<td>
							<span class="vexpl"><?php echo gettext("Enable kernel PPS clock discipline (default: enabled)."); ?></span>
						</td>
					</tr>
					<tr>
						<td>
							<input name="ppsflag4" type="checkbox" class="formcheckbox" id="ppsflag4"<?php if($pconfig['flag4']) echo ' checked'; ?>>
						</td>
						<td>
							<span class="vexpl"><?php echo gettext("Obscure location in timestamp (default: unobscured)."); ?></span>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td width="22%" valign="top" class="vncellreq">Clock ID</td>
			<td width="78%" class="vtable">
				<input name="ppsrefid" type="text" class="formfld unknown" id="ppsrefid" maxlength= "4" size="20" value="<?php htmlspecialchars($pconfig['refid']);?>"><?php echo gettext("(1 to 4 charactors)");?><br>
				<?php echo gettext("This may be used to change the PPS Clock ID");?> (<?php echo gettext("default");?>: PPS).</td>
		</tr>
	</table>
	<?php endif; ?>
</table>
</form>
<?php include("fend.inc"); ?>
</body>
</html>
<!--
/*
</br><p> $config</p></br>
<?php var_dump($config['ntpd']['gps']); ?>
</br><p> $_POST</p></br>
<?php var_dump($_POST); ?>
</br><p> $pconfig</p></br>
<?php var_dump($pconfig); ?>
*/
-->