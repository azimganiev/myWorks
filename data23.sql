CREATE TABLE IF NOT EXISTS `data2`.`city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `c_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

INSERT INTO `data2`.`city` (`id`, `c_id`, `name`) VALUES
(1, 1, 'Москва'),
(2, 1, 'Астрахань'),
(3, 2, 'Мюнхен'),
(4, 2, 'Дюссельдорф'),
(5, 3, 'Детроит'),
(6, 3, 'Мичиган');

CREATE TABLE IF NOT EXISTS `data2`.`country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

INSERT INTO `data2`.`country` (`id`, `name`) VALUES
(1, 'Россия'),
(2, 'Германия'),
(3, 'США');

CREATE TABLE IF NOT EXISTS `data2`.`market` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `c_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  KEY `c_id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

INSERT INTO `data2`.`market` (`id`, `c_id`, `name`) VALUES
(1, 1, 'связной'),
(2, 1, 'наган'),
(3, 2, 'зоомир'),
(4, 2, 'взрослый'),
(5, 3, 'евромаг'),
(6, 3, 'лабиринт'),
(7, 4, 'евромаг2'),
(8, 5, 'винчестер'),
(9, 5, 'у Мэри'),
(10, 6, 'виндзор'),
(11, 6, 'ПО для всех');
