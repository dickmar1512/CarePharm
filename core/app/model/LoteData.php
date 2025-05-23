<?php
class LoteData{
	public static $tablename = "lote";

	public function LoteData(){
		$this->fech_ing = "NOW()";
	}
    
    public function add(){
		$sql = "insert into ".self::$tablename." (id_prod, num_lot, fech_ing,id_sell,user_id) ";
		$sql .= "value ($this->id_prod, $this->num_lot,$this->fech_ing,$this->id_sell,$this->user_id)";
		return Executor::doit($sql);
	}
}

?>