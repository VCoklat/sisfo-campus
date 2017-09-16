<?php

include "header.dbf.php";
include "dbf.function.php";

function TampilkanHeaderAktifMhsw() {
	global $arrID;
	$optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', "ProdiID");
  CheckFormScript("tahun");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='CreateDBFAktifMhsw'>
  <tr><th class=ttl colspan=5>$arrID[Nama]</th></tr>
	<tr><td class=inp>Tahun Akademik</td><td class=ul colspan=4><input type=text name=tahun value='$_SESSION[tahun]' size=15></td></tr>
  <tr><td class=inp>Prodi</td><td class=ul colspan=4><select name='prodi'>$optprd</td></tr>
    <tr><td class=inp>Dari NIM:</td>
    <td class=ul><input type=text name='DariNPM' value='$_SESSION[DariNPM]' size=20 maxlength=50></td>
    <td class=inp>Sampai NIM:</td>
    <td class=ul><input type=text name='SampaiNPM' value='$_SESSION[SampaiNPM]' size=20 maxlength=50></td>
    <td class=ul><input type=submit name='Cetak' value='Proses'></td></tr>
  </form></table></p>";
}
	
function CreateDBFAktifMhsw() {
	global $HeaderAKTFMHS;
	if (!empty($_SESSION['DariNPM'])) {
    $_SESSION['SampaiNPM'] = (empty($_SESSION['SampaiNPM']))? $_SESSION['DariNPM'] : $_SESSION['SampaiNPM'];
    $_npm = "and '$_SESSION[DariNPM]' <= MhswID and MhswID <= '$_SESSION[SampaiNPM]' ";
  } else $_npm = '';
	$_prdn = (!empty($_SESSION['prodi'])) ? "and ProdiID = '$_SESSION[prodi]'" : '';
  $s = "select MhswID
    from khs
    where TahunID = '$_SESSION[tahun]' $_prdn $_npm 
    and StatusMhswID in ('A', 'C')
		order by MhswID";
  $r = _query($s);
  $n = 0;
	
	$DBFName = "dikti/TRAKM-$_SESSION[tahun].DBF";
	DBFCreate($DBFName, $HeaderAKTFMHS);
	
  while ($w = _fetch_array($r)) {
    $n++;
    $_SESSION["DBF-MHSWID-$n"] = $w['MhswID'];
  }
	$_SESSION["DBF-TAHUN"] = $_SESSION['tahun'];
	$_SESSION["DBF-FILES"] = $DBFName;
  $_SESSION["DBF-POS"] = 0;
  $_SESSION["DBF-MAX"] = $n;
  echo "<p>Akan diproses <font size=+1>$n</font> data.</p>";
  echo "<p><IFRAME src='dikti.aktifitasmhsw.go.php' frameborder=0 height=400 width=600>
  </IFRAME></p>";
}

$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');
$tahun = GetSetVar('tahun');

TampilkanJudul("Proses membuat Aktifitas Mahasiswa untuk Dikti");
TampilkanHeaderAktifMhsw();
if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();

?>
