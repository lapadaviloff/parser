<?php
class SQLconnect{
	protected  $dbh;

	function __construct() {
		try {
			
			$this->dbh = new PDO('mysql:dbname=yandex_point_db;host=localhost','qqq','123'); //соединяемся с sql сервером
		} catch (PDOException $e) {
			die($e->getMessage());

		}
		
	}
	public function insertObjects($content){
		$info='';
		$tables='';
		$sth='';
		$this->dbh;
		//очищаем данные objects
		$sth = $this->dbh->prepare("DELETE FROM `objects`");
		$sth->execute();
		$sth =  $this->dbh->prepare("ALTER TABLE `objects` AUTO_INCREMENT=0");
		$sth->execute();
		$info="('";
		$tables="(`";
		//формируем запрос и вставляем данные из распарсенного контента в sql
		foreach($content as $data ) {
			foreach ($data as $var=>$key ) {
				$tables=$tables.$var."`,`";
				$info=$info.$key."','";
			}	
			$tables = substr($tables,0,-2);
			$tables = $tables.")";
			$info = substr($info,0,-2);
			$info = $info.")";
			
			$sth = $this->dbh->prepare("INSERT INTO `objects` ".$tables." VALUES ".$info);
			$sth->execute();
			$info="('";
			$tables="(`";

		}	
	} 
	public function getDbh(){
		return $this->dbh;
	}
}
?>