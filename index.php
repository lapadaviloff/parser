<!DOCTYPE html>
<html>
<head>
	<title>парсер PHP</title>
	<meta charset="utf-8">
	<style>
		.content { 
			width: 380px;
			word-break:break-all;
		}
	</style>
</head>
<body>
	<?php
	include ('SQL/SQLconnect.php');
	include ('Parser/ParserYandex.php');
	$sqlConnect = new SQLconnect();
	$parserYandex = new ParserYandex();
	$out=$parserYandex->getYandexData($sqlConnect->getDbh());
	?>
	<div id="map" style="width: 100%; height:600px"></div>
	<script src="https://api-maps.yandex.ru/2.1/?lang=ru-RU" type="text/javascript"></script>
	<script type="text/javascript">
		
		//выаодим распарсенные данные на яндекс карту
		ymaps.ready(init);
		function init() {
			var myMap = new ymaps.Map("map", {
				center: [55.75,37.62],
				zoom: 10
			}, {
				searchControlProvider: 'yandex#search'
			});
			geoObjects = [];
			count=0;
			var clusterer = new ymaps.Clusterer({
            /**
             * Через кластеризатор можно указать только стили кластеров,
             * стили для меток нужно назначать каждой метке отдельно.
             * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/option.presetStorage.xml
             */
             preset: 'islands#invertedVioletClusterIcons',
            /**
             * Ставим true, если хотим кластеризовать только точки с одинаковыми координатами.
             */
             groupByCoordinates: false,
            /**
             * Опции кластеров указываем в кластеризаторе с префиксом "cluster".
             * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/ClusterPlacemark.xml
             */
             clusterDisableClickZoom: true,
             clusterHideIconOnBalloonOpen: false,
             geoObjectHideIconOnBalloonOpen: false
         });
			
			<?php foreach ($out as $temp): ?>

				geoObjects[count] = new ymaps.Placemark([
					<?php echo $temp['adress']; ?>
					], 

					{
						balloonContent: '<?php echo '<div class="content">'.$temp['content'].'</div>'; ?>'
					}, 

					{
						preset: 'islands#icon',
						iconColor: '#0000ff'
					});
				
				count++;
				

			<?php endforeach; ?>
			
			clusterer.add(geoObjects);
			myMap.geoObjects.add(clusterer);

			myMap.setBounds(clusterer.getBounds(), {
				checkZoomRange: true
			});
		}

	</script>
</body>
</html>
