<?php
function getProxy(){
	
$lines = file('Proxy/proxy.txt');// загрузка прокси из списка, формат 255.255.255.255:4145, столбиком, только SOCS4
if(!$lines) {
	echo  'not load proxy.txt', "\n";
	return null;
}
$rand_keys = array_rand($lines, 1);  //выбираем в случайном порядке
return $lines[$rand_keys];
}
?>