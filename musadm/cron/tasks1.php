<?php
/**
 * Создание зачач для клиентов с кол-вом уроков 1 или менее
 *
 * User: Egor
 * Date: 21.11.2018
 * Time: 18:37
 */


$sql = "
DROP TEMPORARY TABLE IF EXISTS t_indiv;
DROP TEMPORARY TABLE IF EXISTS t_group;

CREATE temporary TABLE t_indiv
SELECT User.id
FROM User
JOIN Property_Int AS pi ON User.id = pi.object_id 
WHERE 
	User.group_id = 5
    AND active = 1
    AND pi.model_name = \"User\"
    AND pi.property_id = 13 
    AND pi.value <= 1
GROUP BY id;
    
CREATE temporary TABLE t_group
SELECT User.id
FROM User
JOIN Property_Int AS pi ON User.id = pi.object_id 
WHERE 
	User.group_id = 5
    AND active = 1
    AND pi.model_name = \"User\"
    AND pi.property_id = 14 
    AND pi.value <= 1
GROUP BY id;
    
SELECT t_indiv.id FROM t_indiv
JOIN t_group ON t_group.id = t_indiv.id";

