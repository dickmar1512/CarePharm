<?php
	class EmpresaData
	{
		public static $tablename = "empresa";
		public $Emp_Ruc;
		public $Emp_RazonSocial;
		public $Emp_Descripcion;
		public $Emp_Direccion;
		public $Emp_Telefono;
		public $Emp_Celular;
		public $Emp_Sucursal;
		public $Emp_Logo;
		public $Emp_personaId;
		public $Emp_personaToken;
		public $Emp_IdEmpresa;

		public function EmpresaData()
		{
			$this->Emp_Ruc         = "";
			$this->Emp_RazonSocial = "";
			$this->Emp_Descripcion = "";
			$this->Emp_Direccion   = "";
			$this->Emp_Telefono    = "";
			$this->Emp_Celular     = "";
			$this->Emp_Sucursal    = "";
			$this->Emp_Logo        = "";
			$this->Emp_personaId   = "";
			$this->Emp_personaToken= "";
			$this->Emp_IdEmpresa   = "";
		}

		public static function getDatos()
		{
			$sql = "select * from ".self::$tablename."";
			$query = Executor::doit($sql);
			return Model::one($query[0],new EmpresaData());
		}

		public function update()
		{
			$sql = "UPDATE ".self::$tablename." set Emp_Ruc='".$this->Emp_Ruc."', Emp_RazonSocial='".$this->Emp_RazonSocial."', Emp_Descripcion='".$this->Emp_Descripcion."', Emp_Direccion='".$this->Emp_Direccion."', Emp_Telefono='".$this->Emp_Telefono."', Emp_Celular='".$this->Emp_Celular."' WHERE Emp_IdEmpresa=$this->Emp_IdEmpresa";
			Executor::doit($sql);
		}
	}
?>