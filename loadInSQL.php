<?php
$flag=true;
$parsePage=0;
include ('Parser/ParserYandex.php');
include ('SQL/SQLconnect.php');
$content='';
$sqlConnect = new SQLconnect();
$parserYandex = new ParserYandex();
$list=array();
$lists=array();
$temp=array();

$lines = file('linkList.txt',FILE_IGNORE_NEW_LINES); //загрузка списка адресов из файла, через # количество страниц
if(!$lines) {
	echo  'not load linkList.txt'.PHP_EOL;
	exit;
}
//перебираем адреса по одному
foreach ($lines as $key => $value) {
	$pieces = explode("#", $value);
	if($pieces[0]=='')continue;  //если нет адреса переходим на другую строку
	$adress=$pieces[0];
	$parsePage=$pieces[1];
	if($parsePage!=0){               //если указанно 0  то пропускаем
		while ($parsePage!=0){
			echo "try ";
			try{
				$content=$parserYandex->loadDOM($adress.'&page_num='.$parsePage);
		//$parserYandex->loadDOM('http://192.168.1.100:81/');
			}catch (Exception $e) {
				echo  $e->getMessage();
				continue;           
			}
			$list = $parserYandex->parse($content);
			if(!$list)continue;

			if ($flag){       //если первая страница, то просто добавляем первой в общий  массив
				$lists=$list;
				$flag=false;
			} 
			else{
				$temp = array_merge($lists, $list);
				$lists=$temp;  // добовляем последующие страницы в массив
			}
			$content='';
			$parsePage--;

		}
		$sqlConnect->insertObjects($lists);    // добовляем массив в sql таблицу

	}
}

?>