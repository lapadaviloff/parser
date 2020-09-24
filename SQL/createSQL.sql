
CREATE TABLE IF NOT EXISTS `objects` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `serp-vacancy__date` TEXT NOT NULL,
  `link serp-vacancy__source stat__click` TEXT NOT NULL,
  `vacancy__salary` TEXT NOT NULL,
  `heading heading_level_3` TEXT NOT NULL,
  `address address_empty_yes serp-vacancy__settlement` TEXT NOT NULL,
  `metro-item__name` TEXT NOT NULL,
  `link link_nav_yes link_minor_yes i-bem` TEXT NOT NULL,
  `serp-vacancy__requirements` TEXT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `town` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `adress` varchar(255) NOT NULL,
  `geocode` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
LOAD DATA LOCAL INFILE 'town.csv'
 INTO TABLE town
 FIELDS TERMINATED BY ';'
 LINES TERMINATED BY '\n'
 (adress,geocode);
 

