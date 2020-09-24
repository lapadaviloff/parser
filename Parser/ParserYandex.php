<?php

include_once 'AbstractParser.php';

class ParserYandex extends AbstractParser
{

	public function parse($content){

		$item = array();
		$i=0;
		//формируем массив из контента
		foreach($content->find('div[class="serp-vacancy search-results__list-item stat i-bem"]') as $element)

		{

			if($temp=$element->find('div[class=serp-vacancy__date]')){
				$item[$i]['serp-vacancy__date']=trim($temp[0]);

			}
			if($temp=$element->find('a[class=link serp-vacancy__source stat__click]')){
				$item[$i]['link serp-vacancy__source stat__click']=trim($temp[0]);
			}
			else {
				$item[$i]['link serp-vacancy__source stat__click']="---";
			}

			if($temp=$element->find('div[class=serp-vacancy__salary]')){
				$item[$i]['vacancy__salary']=trim($temp[0]);
			}
			else {
				$item[$i]['vacancy__salary']="---";
			}


			if($temp=$element->find('H3[class=heading heading_level_3]')){
				$item[$i]['heading heading_level_3']=trim($temp[0]);
			}
			else {
				$item[$i]['heading heading_level_3']="---";
			}

			if($temp=$element->find('div[class=address address_empty_yes serp-vacancy__settlement]'))  
			{

				$item[$i]['address address_empty_yes serp-vacancy__settlement']=trim($temp[0]);
			}
			else {
				$item[$i]['address address_empty_yes serp-vacancy__settlement']="---";
			}
			if($temp=$element->find('div[class=address]'))
			{

				$item[$i]['address address_empty_yes serp-vacancy__settlement']=trim($temp[0]);
			}

			if($temp=$element->find('div[class=metro-item__name]')){
				$item[$i]['metro-item__name']=trim($temp[0]);
			}
			else {
				$item[$i]['metro-item__name']="---";
			}

			if($temp=$element->find('a[class=link link_nav_yes link_minor_yes i-bem]')){
				$item[$i]['link link_nav_yes link_minor_yes i-bem']=trim($temp[0]);
			}
			else {
				$item[$i]['link link_nav_yes link_minor_yes i-bem']="---";
			}

			if($temp=$element->find('div[class=serp-vacancy__requirements]')){
				$item[$i]['serp-vacancy__requirements']=trim($temp[0]);
			}
			else {
				$item[$i]['serp-vacancy__requirements']="---";
			}
			$i++;

		}

		return $item;

	}

	public function getYandexData($dbh){ // dbh соединениес sql
		$contetGeo;
		$content='';
		$count=0;
		$sth = $dbh->prepare("SELECT * FROM `objects`");
		$sth->execute();
		$row = $sth->fetchAll(PDO::FETCH_ASSOC);
		$keys = array_keys($row[0]); //получение списка колонок

		foreach($row as $data ) {

			for ($i = 1; $i < count($keys); $i++) {
				$content =$content.$data[$keys[$i]];

			}

			$contetGeo[$count]['content'] =$content;
			$content ='';
			//выделяем из html строки название города и метро
			$dataTown=$data['address address_empty_yes serp-vacancy__settlement'];
			$dataTown=str_replace('<div class="address address_empty_yes serp-vacancy__settlement"><i class="icon icon_type_address"></i>','',$dataTown);
			$dataTown=str_replace('</div>','',$dataTown);
			$dataTown=str_replace('<div class="address"><i class="icon icon_type_address"></i>','',$dataTown);
			$dataTown=str_replace('</div>','',$dataTown);
			$dataMetro=$data['metro-item__name'];
			$dataMetro=str_replace('<div class="metro-item__name">','',$dataMetro);
			$dataMetro=str_replace('</div>','',$dataMetro);
			
			if($dataTown=='Москва'){

				if($dataMetro!='---'){// запрашиваем адрес метро из заранее заполненой таблицы (папка SQL)
					$sth = $dbh->prepare("SELECT`geocode` FROM `metro`  WHERE adress=:adress");
					$sth->bindParam(':adress',$dataMetro , PDO::PARAM_STR);
					$sth->execute();
					$item = $sth->fetch(PDO::FETCH_ASSOC);
					
					if(!$item){
						$item['geocode']='55.75,37.62'; //если не нашли , то адрес по умолчанию

					}
					$contetGeo[$count]['adress']=$item['geocode'];
				}else {$contetGeo[$count]['adress'] ='55.75,37.62';// если метро отсутствует , то адрес по умолчанию
				
			}

		}else{
			if($dataTown!=='---'){
				//ищем город подмосковья
				$sth = $dbh->prepare("SELECT `geocode` FROM `town` WHERE adress=:adress");
				$sth->bindParam(':adress',$dataTown , PDO::PARAM_STR);
				$sth->execute();
				$item = $sth->fetch(PDO::FETCH_ASSOC);
				if(!$item)$item['geocode']='56.18,38.24';//если не нашли ,то адрес по умолчанию
				$contetGeo[$count]['adress']=$item['geocode'];
			}else {$contetGeo[$count]['adress'] ='56.18,38.24';//город отсутствует ,то адрес по умолчанию
			
		}

	}
	$count ++;
}
//обьединяем информацию по одинаковым адресам.не через sql, потому что сначала эксперементировал так, а потом переписывать не захотел...)
$temp = array();
$tempAdress;
$count=count($contetGeo);
while ($contetGeo) {
	for ($i=0; $i<$count ; $i++) { 
		if($i==0){
			array_push($temp,($contetGeo[0]));	
			$tempAdress=$contetGeo[0]['adress'];
			continue;
		}
		if ($contetGeo[$i]['adress']==$tempAdress){
			$temp[count($temp)-1]['content'] =$temp[count($temp)-1]['content'].'<br/>****************************<br/>'.$contetGeo[$i]['content'];
			unset($contetGeo[$i]);
		}
	}	
	unset($contetGeo[0]);
	$contetGeo = array_values($contetGeo);
	$count =count($contetGeo);
}
return $temp;  
}
}
?>