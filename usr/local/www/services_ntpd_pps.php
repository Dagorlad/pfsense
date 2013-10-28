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
	pfSense_MODULE:	ntpd_gps
*/

##|+PRIV
##|*IDENT=page-services-ntpd-gps
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
<form action="services_ntpd_gps.php" method="post" name="iform" id="iform">
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
			<td colspan="2" valign="top" class="listtopic"><?=gettext("NTP Serial GPS Configuration"); ?></td>
		</tr>
		<tr>
			<td width="22%" valign="top" class="vncellreq">
			</td>
			<td width="78%" class="vtable">Radios such as those that listen WWVB or DCF77 or a GPS connected via a serial port may be used as a PPS reference for NTP.
			<br/>
			<br/><?php echo gettext("At least 2 servers need to be configured under"); ?> <a href="system.php"><?php echo gettext("System > General"); ?></a> <?php echo gettext("to provide a time source."); ?>
			</td>
		</tr>
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