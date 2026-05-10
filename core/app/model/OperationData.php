<?php

#[AllowDynamicProperties]
class OperationData
{
	public static $tablename = "operation";
	public $id;
	public $product_id;
	public $q;
	public $prec_alt;
	public $descuento;
	public $operation_type_id;
	public $sell_id;
	public $descripcion;
	public $idpaquete;
	public $created_at;
	public $cu;
	public $name;
	public $cut_id;
	public $VENTAS;
	public $stock_in;
	public $importe_in;
	public $stock_out;
	public $importe_out;
	public $stock_inv;
	public $importe_inv;
	public $Paquete;
	public $barcode;
	public $is_oficial;
	public $estado;
	public $fecha;
	public $producto;
	public $estado_op;
	public $tipo_comprobante_id;
	public $serie;
	public $comprobante;
	public $usuario;

	public function OperationData()
	{
		$this->name = "";
		$this->product_id = "";
		$this->q = "";
		$this->cu = 0;
		$this->cut_id = "";
		$this->operation_type_id = "";
		$this->descripcion = "";
		$this->idpaquete = "";
		$this->estado = 1;
		$this->created_at = "NOW()";
	}

	public function add()
	{
		$sql = "insert into " . self::$tablename . " (product_id,q,cu, prec_alt,descuento, operation_type_id,sell_id,created_at, descripcion,idpaquete,estado) ";
		$sql .= "value (\"$this->product_id\",\"$this->q\", $this->cu,$this->prec_alt,$this->descuento, $this->operation_type_id,$this->sell_id,\"$this->created_at\", \"$this->descripcion\",\"$this->idpaquete\",1)";
		return Executor::doit($sql);
	}

	public static function delById($id)
	{
		$sql = "update " . self::$tablename . " set estado=0 where id=$id";
		Executor::doit($sql);
	}
	public function del()
	{
		$sql = "update " . self::$tablename . " set estado=0 where id=$this->id";
		Executor::doit($sql);
	}

	public function cancel()
	{
		$sql = "update " . self::$tablename . " set estado=0 where id=$this->id";
		Executor::doit($sql);
	}

	// partiendo de que ya tenemos creado un objecto OperationData previamente utilizamos el contexto
	public function update()
	{
		$sql = "update " . self::$tablename . " set product_id=\"$this->product_id\",q=\"$this->q\" where id=$this->id";
		Executor::doit($sql);
	}

	public function updateReab($celda, $valor)
	{
		if ($celda == "q"):
			$sql = "update " . self::$tablename . " set " . $celda . "=" . $valor . " where sell_id=$this->sell_id and product_id = $this->product_id";
		else:
			$sql = "update " . self::$tablename . " set " . $celda . "=" . $valor . ", cu=" . $valor . " where sell_id=$this->sell_id and product_id = $this->product_id";
		endif;
		$query = Executor::doit($sql);

		return $query[0];
	}

	public static function getById($id)
	{
		$sql = "select * from " . self::$tablename . " where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0], new OperationData());
	}


	public static function getAll()
	{
		$sql = "select * from " . self::$tablename . " where estado=1";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}


	public static function getAllByDateOfficial($start, $end)
	{
		$sql = "select * from " . self::$tablename . " where estado=1 and date(created_at) >= \"$start\" and date(created_at) <= \"$end\" order by created_at desc";

		if ($start == $end) {
			$sql = "select * from " . self::$tablename . " where estado=1 and date(created_at) = \"$start\" order by created_at desc";
		}

		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getAllByDateOfficialBP($product, $start, $end)
	{
		$sql = "select * from " . self::$tablename . " where estado=1 and date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and product_id=$product order by created_at desc";

		if ($start == $end) {
			$sql = "select * from " . self::$tablename . " where estado=1 and date(created_at) = \"$start\" order by created_at desc";
		}

		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getAllMovByDateProductId($product_id, $start, $end)
	{
		$sql = "select op.product_id,
					SUM(CASE WHEN op.operation_type_id = 1 THEN op.q ELSE 0 END) AS stock_in,
					SUM(CASE WHEN op.operation_type_id = 1 THEN op.q * op.prec_alt ELSE 0 END) AS importe_in,
					SUM(CASE WHEN op.operation_type_id = 2 THEN op.q ELSE 0 END) AS stock_out,
					SUM(CASE WHEN op.operation_type_id = 2 THEN op.q * op.prec_alt ELSE 0 END) AS importe_out,
					(SUM(CASE WHEN op.operation_type_id = 1 THEN op.q ELSE 0 END) - 
					SUM(CASE WHEN op.operation_type_id = 2 THEN op.q ELSE 0 END)) AS stock_inv,
					((SUM(CASE WHEN op.operation_type_id = 1 THEN op.q ELSE 0 END) - 
					SUM(CASE WHEN op.operation_type_id = 2 THEN op.q ELSE 0 END)) * 
					(SUM(CASE WHEN op.operation_type_id = 1 THEN op.q * op.prec_alt ELSE 0 END) / 
					NULLIF(SUM(CASE WHEN op.operation_type_id = 1 THEN op.q ELSE 0 END), 0))) AS importe_inv 
	  			from " . self::$tablename . " as op where op.estado=1 and date(op.created_at) between '" . $start . "' and '" . $end . "'  ";

		if ($product_id != 0) {
			$sql .= " and op.product_id = $product_id";
		}

		$sql .= " GROUP BY op.product_id ";


		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	/*REVISA ID DE LAS SOLICITUDADES MAS VENDIDAS*/
	public static function getAllByDateVendido($start, $end)
	{
		//$sql = "select product_id,COUNT(product_id)*q AS VENTAS   from ".self::$tablename." 
		$sql = "select product_id,sum(q) AS VENTAS   from " . self::$tablename . " 
 			where 
 			estado = 1 and
 			date(created_at) >= \"$start\" and 
 			date(created_at) <= \"$end\" and
 			operation_type_id = 2
 			group by product_id 
 			order by VENTAS desc  LIMIT 5";

		if ($start == $end) {
			$sql = "select  product_id,sum(q) AS VENTAS from " . self::$tablename . " 
				where date(created_at) = \"$start\" and
 				operation_type_id = 2
 				group by product_id 
 				order by VENTAS desc  LIMIT 5";
		}

		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}


	/*CANTIDAD EXCATA VENDIDOS POR PRODUCTO*/
	public static function getAllByDateVendidoProducto($start, $end, $product_id)
	{
		$sql = "select product_id, q   from " . self::$tablename . " 
 			where 
 			estado = 1 and
 			date(created_at) >= \"$start\" and 
 			date(created_at) <= \"$end\" and
 			operation_type_id = 2
 			and product_id = $product_id";

		if ($start == $end) {
			$sql = "select  product_id, q   from " . self::$tablename . " 
				where estado = 1 and date(created_at) = \"$start\" and
 				operation_type_id = 2 and 
 				product_id = $product_id";
		}

		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public function getProduct()
	{
		return ProductData::getById($this->product_id);
	}
	public function getOperationtype()
	{
		return OperationTypeData::getById($this->operation_type_id);
	}
	public function getPaquete()
	{
		$sql = "select idpaquete id,barcode,nombre name,descripcion description  from paquete where idpaquete=" . $this->idpaquete;
		$query = Executor::doit($sql);
		return Model::one($query[0], new ProductData());

	}

	public static function getQYesF($product_id)
	{
		$q = 0;
		$operations = self::getAllByProductId($product_id);
		$input_id = OperationTypeData::getByName("entrada")->id;
		$output_id = OperationTypeData::getByName("salida")->id;
		foreach ($operations as $operation) {
			if ($operation->operation_type_id == $input_id) {
				$q += $operation->q;
			} else if ($operation->operation_type_id == $output_id) {
				$q += (-$operation->q);
			}
		}
		// print_r($data);
		return $q;
	}

	public static function getCantidadbyOperation($product_id, $operation_type_id)
	{
		$sql = "SELECT sum(q) as Cantidad FROM " . self::$tablename . " WHERE estado=1 AND product_id = $product_id AND operation_type_id = $operation_type_id";

		$query = Executor::doit($sql);
		return Model::one($query[0], new OperationData());
	}



	public static function getAllByProductIdCutId($product_id, $cut_id)
	{
		$sql = "select * from " . self::$tablename . " where estado=1 and product_id=$product_id and cut_id=$cut_id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getAllByProductId($product_id)
	{
		$sql = "select * from " . self::$tablename . " where estado=1 and product_id=$product_id  order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}


	public static function getAllByProductIdCutIdOficial($product_id, $cut_id)
	{
		$sql = "select * from " . self::$tablename . " where product_id=$product_id and cut_id=$cut_id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}


	public static function getAllProductsBySellId($sell_id)
	{
		$sql = "select * from " . self::$tablename . " where estado=1 and sell_id=$sell_id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getAllProductsBySellIdAll($sell_id)
	{
		$sql = "select * from " . self::$tablename . " where sell_id=$sell_id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getAllProductsBySellId2($sell_id)
	{
		$sql = "SELECT product_id,q,prec_alt,descuento,created_at,idpaquete 
			FROM operation 
			WHERE sell_id=$sell_id
			AND idpaquete= 'X' 
			UNION
			SELECT '0' product_id,'1' q,SUM(prec_alt*q) prec_alt,SUM(descuento*q) descuento,created_at,idpaquete
			FROM operation
			WHERE sell_id=$sell_id
			AND idpaquete <> 'X'
			GROUP BY created_at,idpaquete";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}


	public static function getAllByProductIdCutIdYesF($product_id, $cut_id)
	{
		$sql = "select * from " . self::$tablename . " where product_id=$product_id and cut_id=$cut_id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
		//return $array;
	}

	////////////////////////////////////////////////////////////////////
	public static function getOutputQ($product_id, $cut_id)
	{
		$q = 0;
		$operations = self::getOutputByProductIdCutId($product_id, $cut_id);
		$input_id = OperationTypeData::getByName("entrada")->id;
		$output_id = OperationTypeData::getByName("salida")->id;
		foreach ($operations as $operation) {
			if ($operation->operation_type_id == $input_id) {
				$q += $operation->q;
			} else if ($operation->operation_type_id == $output_id) {
				$q += (-$operation->q);
			}
		}
		// print_r($data);
		return $q;
	}

	public static function getOutputQYesF($product_id)
	{
		$q = 0;
		$operations = self::getOutputByProductId($product_id);
		$input_id = OperationTypeData::getByName("entrada")->id;
		$output_id = OperationTypeData::getByName("salida")->id;
		foreach ($operations as $operation) {
			if ($operation->operation_type_id == $input_id) {
				$q += $operation->q;
			} else if ($operation->operation_type_id == $output_id) {
				$q += (-$operation->q);
			}
		}
		// print_r($data);
		return $q;
	}

	public static function getInputQYesF($product_id)
	{
		$q = 0;
		$operations = self::getInputByProductId($product_id);
		$input_id = OperationTypeData::getByName("entrada")->id;
		foreach ($operations as $operation) {
			if ($operation->operation_type_id == $input_id) {
				$q += $operation->q;
			}
		}
		// print_r($data);
		return $q;
	}


	public static function getOutputByProductIdCutId($product_id, $cut_id)
	{
		$sql = "select * from " . self::$tablename . " where estado=1 and product_id=$product_id and cut_id=$cut_id and operation_type_id=2 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}


	public static function getOutputByProductId($product_id)
	{
		$sql = "select * from " . self::$tablename . " where estado=1 and product_id=$product_id and operation_type_id=2 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getInputByProductId($product_id)
	{
		$sql = "select * from " . self::$tablename . " where estado=1 and product_id=$product_id and operation_type_id=1 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}


	////////////////////////////////////////////////////////////////////
	public static function getInputQ($product_id, $cut_id)
	{
		$q = 0;
		//return Model::many($query[0],new OperationData());
		$operations = self::getInputByProductId($product_id);
		$input_id = OperationTypeData::getByName("entrada")->id;
		$output_id = OperationTypeData::getByName("salida")->id;
		foreach ($operations as $operation) {
			if ($operation->operation_type_id == $input_id) {
				$q += $operation->q;
			} else if ($operation->operation_type_id == $output_id) {
				$q += (-$operation->q);
			}
		}
		// print_r($data);
		return $q;
	}


	public static function getInputByProductIdCutId($product_id, $cut_id)
	{
		$sql = "select * from " . self::$tablename . " where estado=1 and product_id=$product_id and cut_id=$cut_id and operation_type_id=1 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getInputByProductIdCutIdYesF($product_id, $cut_id)
	{
		$sql = "select * from " . self::$tablename . " where estado=1 and product_id=$product_id and cut_id=$cut_id and operation_type_id=1 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public function update_created_at()
	{
		$sql = "update " . self::$tablename . " set created_at=$this->created_at where id=$this->id";
		Executor::doit($sql);
	}


	////////////////////////////////////////////////////////////////////////////

	public static function getMonthlySalesSummary($sd = null, $ed = null)
	{
		$sql = "SELECT 
					p.id,
					p.name,
					p.stock,
					IFNULL(SUM(o.q), 0) as total_qty,
					IFNULL(SUM(o.q * o.prec_alt), 0) as total_amount,
					GROUP_CONCAT(DISTINCT LPAD(MONTH(o.created_at), 2, '0') ORDER BY MONTH(o.created_at) SEPARATOR ', ') as months_list,
					COUNT(DISTINCT YEAR(o.created_at), MONTH(o.created_at)) as total_months,
					IFNULL(SUM(o.q) / NULLIF(COUNT(DISTINCT YEAR(o.created_at), MONTH(o.created_at)), 0), 0) as avg_qty_month
				FROM product p
				INNER JOIN operation o ON p.id = o.product_id AND o.operation_type_id = 2 AND o.estado = 1";

		if ($sd && $ed) {
			$sql .= " AND DATE(o.created_at) BETWEEN '$sd' AND '$ed' ";
		}

		$sql .= " WHERE p.is_active = 1
				GROUP BY p.id
				ORDER BY total_months DESC, total_qty DESC";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getMonthlySalesDetails($sd = null, $ed = null)
	{
		$sql = "SELECT 
					YEAR(o.created_at) as anio, 
					MONTH(o.created_at) as mes, 
					p.id as product_id,
					p.name as producto, 
					SUM(o.q) as cantidad_total, 
					SUM(o.q * o.prec_alt) as total_venta
				FROM operation o
				INNER JOIN product p ON o.product_id = p.id
				WHERE o.operation_type_id = 2 
				AND o.estado = 1
				AND p.is_active = 1";

		if ($sd && $ed) {
			$sql .= " AND DATE(o.created_at) BETWEEN '$sd' AND '$ed' ";
		}

		$sql .= " GROUP BY YEAR(o.created_at), MONTH(o.created_at), p.id
				ORDER BY anio DESC, mes DESC, cantidad_total DESC";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getStockBeforeDate($date)
	{
		$sql = "SELECT product_id, 
					   SUM(CASE WHEN operation_type_id = 1 THEN q 
								WHEN operation_type_id = 2 THEN -q 
								ELSE 0 END) as stock
				FROM operation
				WHERE DATE(created_at) < '$date' AND estado = 1
				GROUP BY product_id";
		$query = Executor::doit($sql);
		$stocks = [];
		while($r = $query[0]->fetch_array()){
			$stocks[$r['product_id']] = $r['stock'];
		}
		return $stocks;
	}

	public static function getMonthlyMovementsBetweenDates($sd, $ed)
	{
		$sql = "SELECT product_id,
					   DATE_FORMAT(created_at, '%Y-%m') as mes,
					   SUM(CASE WHEN operation_type_id = 1 THEN q ELSE 0 END) as purchase_qty,
					   SUM(CASE WHEN operation_type_id = 2 THEN q ELSE 0 END) as sales_qty,
					   SUM(CASE WHEN operation_type_id = 2 THEN q * prec_alt ELSE 0 END) as sales_amount
				FROM operation
				WHERE DATE(created_at) BETWEEN '$sd' AND '$ed' AND estado = 1
				GROUP BY product_id, DATE_FORMAT(created_at, '%Y-%m')";
		$query = Executor::doit($sql);
		$movements = [];
		while($r = $query[0]->fetch_array()){
			$movements[$r['product_id']][$r['mes']] = [
				'purchase_qty' => $r['purchase_qty'],
				'sales_qty' => $r['sales_qty'],
				'sales_amount' => $r['sales_amount']
			];
		}
		return $movements;
	}

	public static function getSalesAndStockByProductReport($sd, $ed)
	{
		$sql = "SELECT 
					p.id as product_id,
					p.name as producto,
					SUM(CASE WHEN op.operation_type_id = 2 AND DATE(op.created_at) BETWEEN '$sd' AND '$ed' THEN op.q ELSE 0 END) as cantidad_vendida,
					SUM(CASE WHEN DATE(op.created_at) <= '$ed' THEN 
							 CASE WHEN op.operation_type_id = 1 THEN op.q 
								  WHEN op.operation_type_id = 2 THEN -op.q 
								  ELSE 0 END 
						 ELSE 0 END) as stock_final
				FROM product p
				LEFT JOIN operation op ON p.id = op.product_id AND op.estado = 1
				WHERE p.is_active = 1
				GROUP BY p.id
				HAVING cantidad_vendida > 0 OR stock_final > 0
				ORDER BY cantidad_vendida DESC, p.name ASC";
		
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getSalesSummary($sd, $ed)
	{
		$sql = "SELECT p.name as producto,
					   SUM(op.q) as qty,
					   SUM(op.q * op.prec_alt) as total
				FROM operation op
				JOIN product p ON p.id = op.product_id
				WHERE op.operation_type_id = 2 
				  AND op.estado = 1
				  AND DATE(op.created_at) BETWEEN '$sd' AND '$ed'
				GROUP BY p.id
				ORDER BY total DESC";
		$query = Executor::doit($sql);
		$data = [];
		while($r = $query[0]->fetch_array()){
			$data[] = $r;
		}
		return $data;
	}

	public static function getBincartReport($sd, $ed, $user_id = 0)
	{
		$sql = "SELECT 
					op.created_at as fecha,
					p.name as producto,
					op.estado as estado_op,
					s.tipo_comprobante as tipo_comprobante_id,
					s.serie,
					s.comprobante,
					u.username as usuario,
					u.name as user_name,
					u.lastname as user_lastname,
					op.operation_type_id,
					op.q,
					op.product_id
				FROM operation op
				INNER JOIN product p ON op.product_id = p.id
				LEFT JOIN sell s ON op.sell_id = s.id
				LEFT JOIN user u ON s.user_id = u.id
				WHERE DATE(op.created_at) BETWEEN '$sd' AND '$ed' ";
		
		if ($user_id != 0) {
			$sql .= " AND s.user_id = $user_id ";
		}
		
		$sql .= " ORDER BY op.created_at ASC, op.id ASC ";

		$query = Executor::doit($sql);
		$operations = array();
		while ($r = $query[0]->fetch_array()) {
			$op = new OperationData();
			$op->fecha = $r['fecha'];
			$op->producto = $r['producto'];
			$op->estado_op = $r['estado_op'];
			$op->tipo_comprobante_id = $r['tipo_comprobante_id'];
			$op->serie = $r['serie'];
			$op->comprobante = $r['comprobante'];
			
			// Format user
			$op->usuario = trim($r['user_name'].' '.$r['user_lastname']);
			if(empty($op->usuario)) $op->usuario = $r['usuario'];
			
			$op->operation_type_id = $r['operation_type_id'];
			$op->q = $r['q'];
			$op->product_id = $r['product_id'];
			$operations[] = $op;
		}

		// Calculate initial stock for products involved
		$products_involved = array();
		foreach ($operations as $op) {
			$products_involved[$op->product_id] = 0;
		}

		if (count($products_involved) > 0) {
			$prod_ids = implode(",", array_keys($products_involved));
			$sql_stock = "SELECT product_id, SUM(CASE WHEN operation_type_id=1 THEN q ELSE -q END) as stock_inicial 
						  FROM operation 
						  WHERE DATE(created_at) < '$sd' AND estado=1 AND product_id IN ($prod_ids) 
						  GROUP BY product_id";
			$query_stock = Executor::doit($sql_stock);
			while ($r = $query_stock[0]->fetch_array()) {
				$products_involved[$r['product_id']] = $r['stock_inicial'];
			}
		}

		// Calculate running balance
		foreach ($operations as &$op) {
			if ($op->estado_op == 1) {
				if ($op->operation_type_id == 1) {
					$products_involved[$op->product_id] += $op->q;
				} else {
					$products_involved[$op->product_id] -= $op->q;
				}
			}
			$op->saldo = $products_involved[$op->product_id];
		}

		return array_reverse($operations);
	}
}

?>