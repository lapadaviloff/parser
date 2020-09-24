<?php
include_once 'dom/HtmlWeb.php';
use simplehtmldom\HtmlWeb;

abstract class AbstractParser
{
	
	abstract  public function parse($content);

	public function loadDOM($linc) {
		echo $linc.PHP_EOL;
		$content=''; 
		while (!$content) {
			$client = new HtmlWeb();
			$content = $client->load($linc);//загрузка контента через curl в DOM обьект
			if (!$content) {
				throw new Exception('error load link'.PHP_EOL);

			}
		}

		return $content;
	}

}
?>