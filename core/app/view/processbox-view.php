<?php
$sells = SellData::getSellsUnBoxed();

if(count($sells)){
	$box = new BoxData();
	$box->created_at = date("Y-m-d H:i:s");	
	$box ->user_id = $_SESSION["user_id"];
	echo "box==>".json_encode($box);
	$b = $box->add();
	foreach($sells as $sell){
		$sell->box_id = $b[1];
		$sell->update_box();
	}
	Core::redir("././?view=b&id=".$b[1]);
}

?>