<?php
class UserAccessData {
	public static $tablename = "user_access";

    public $id;
    public $user_id;
    public $module_id;
    public $is_active;
    public $created_at;
    public $updated_at;

	public function UserAccessData(){
		$this->user_id = "";
		$this->module_id = "";
        $this->is_active = 1;
        $this->created_at = "NOW()";
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (user_id, module_id, is_active, created_at) ";
		$sql .= "value ($this->user_id,$this->module_id, 1, NOW())";
		return Executor::doit($sql);
	}

    public function update_status($status){
		$sql = "update ".self::$tablename." set is_active=$status, updated_at=NOW() where user_id=$this->user_id and module_id=$this->module_id";
		return Executor::doit($sql);
	}

    public static function del($user_id, $module_id){
		$sql = "update ".self::$tablename." set is_active=0, updated_at=NOW() where user_id=$user_id and module_id=$module_id";
		return Executor::doit($sql);
	}

    public static function delAllByUserId($user_id){
		$sql = "update ".self::$tablename." set is_active=0, updated_at=NOW() where user_id=$user_id";
		return Executor::doit($sql);
	}

	public static function getByUserModule($user_id, $module_id){
		$sql = "select * from ".self::$tablename." where user_id=$user_id and module_id=$module_id and is_active=1";
		$query = Executor::doit($sql);
		return Model::one($query[0],new UserAccessData());
	}

    public static function getAnyByUserModule($user_id, $module_id){
		$sql = "select * from ".self::$tablename." where user_id=$user_id and module_id=$module_id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new UserAccessData());
	}

	public static function getAllByUserId($user_id){
		$sql = "select * from ".self::$tablename." where user_id=$user_id and is_active=1";
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserAccessData());
	}
}
?>
