<?php
class PDOModel {
	private static $db;  // PDOのハンドル
	private static $table;  // テーブル名
	private static $pdo_params;  // PDOコンストラクタのパラメータ
	public $id;


	// DB設定XMLファイルの読み込みとパラメータセット
	public static function configuration($xml) {
		$conf = simplexml_load_file($xml);
		self::$pdo_params = get_object_vars($conf);
	}
	
	// データベースへの接続，PDOインスタンスの生成
	public static function connection() {
		$_dsn = self::$pdo_params['dsn'];
		$_user = self::$pdo_params['user'];
		$_password = self::$pdo_params['password'];
		try {
			self::$db = new PDO($_dsn, $_user, $_password);
		} catch(PDOException $e) {
			printf("Error: %s\n", $e->getMessage());
			self::$db = null;
		}
	}

	// 結果セット，レコードオブジェクトの配列を返す
	protected static function getRecords(PDOStatement $stmt) {
		$rets = array();
		while($ret = $stmt->fetchObject(static::MODEL_CLASS)) $rets[] = $ret;
		return $rets;
	}
	
	// 単一のレコードオブジェクトを返す
	protected static function getRecord(PDOStatement $stmt) {
		$ret = $stmt->fetchObject(static::MODEL_CLASS);
		return $ret;
	}

	// すべてのレコードオブジェクト配列を返す
	public static function findAll() {
		$sql = "SELECT * FROM ".static::TABLE_NAME;
		$stmt = self::$db->prepare($sql);
		$stmt->execute();
		return self::getRecords($stmt);
	}

	// 任意のidのレコードオブジェクトを返す
	public static function find($id) {
		$sql = "SELECT * FROM ".static::TABLE_NAME." WHERE id = :id";
		$stmt = self::$db->prepare($sql);
		$stmt->bindParam(':id', $id, PDO::PARAM_STR);
		$stmt->execute();
		return self::getRecord($stmt);
	}

	// 任意のWHERE検索にてレコードオブジェクト配列を返す
	public static function where($cond) {
		$sql = "SELECT * FROM ".static::TABLE_NAME." WHERE ".$cond;
		$stmt = self::$db->query($sql);
		return self::getRecords($stmt);
	}
	
	// 対象オブジェクトレコードの削除
	public function delete() {
		$sql = "DELETE FROM ".static::TABLE_NAME." WHERE id = :id";
		$stmt = self::$db->prepare($sql);
		$stmt->bindParam(':id', $this->id, PDO::PARAM_STR);
		$stmt->execute();
	}	

	// 対象オブジェクトレコードの保存（新規及び更新）
	public function save() {
		$obj = self::find($this->id);
		if($obj == null) {
            // 新規レコード追加（存在しないidの場合）
			$flist = implode(array_keys(static::$fields), ",");
			$vfunc = function($v){return(":".$v);};
			$vlist = implode(array_map($vfunc, array_keys(static::$fields)), ",");
			$insert_sql = "INSERT INTO ".static::TABLE_NAME." (".$flist.") VALUES (".$vlist.")";
			$stmt = self::$db->prepare($insert_sql);
		}
		else {
            // 既存レコードの更新
			$sfunc = function($v){return($v."=:".$v);};
			$slist = implode(array_map($sfunc, array_keys(static::$fields)), ",");
			$update_sql = "UPDATE ".static::TABLE_NAME." SET ".$slist." WHERE id=:id";
			$stmt = self::$db->prepare($update_sql);
		}
		foreach(static::$fields as $key => $value)
			$stmt->bindParam(":".$key, $this->{$key}, $value);
		$stmt->execute();
	}	
}
?>
