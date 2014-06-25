<?php
require_once "pdo_model.php";

class Member extends PDOModel {
	const MODEL_CLASS = 'Member';
	const TABLE_NAME = 'member';
	protected static $fields = array (
		'id' => PDO::PARAM_STR,
		'name' => PDO::PARAM_STR,
		'height' => PDO::PARAM_STR,
		'weight' => PDO::PARAM_STR
	);
}

// bootstrap
PDOModel::configuration('pdo_config.xml');
PDOModel::connection();

/*
// ID:0002のレコードを更新する
$obj = Member::find('0002');
$obj->name = 'Hayato Satsuma';
$obj->height = '168.0';
$obj->save();
*/

/*
// ID:0005のレコードを削除する
$obj = Member::find('0005');
$obj->delete();
*/

/*
// heightが170より大きいレコードを取得
$obj = Member::where('height > 170');
print_r($obj);
*/

/*
// 新規レコードを追加する
$obj = new Member();
$obj->id = '0005';
$obj->name = 'Takamori Saigo';
$obj->height = '169.5';
$obj->weight = '50.9';
$obj->save();
*/

// 全レコードを取得
$obj = Member::findAll();
print_r($obj);

?>
