<?php
$current = CutData::getCurrent();

if($current!=null){
	$current->update();
}
	print "<script>window.location='./?view=cuts';</script>";

?>