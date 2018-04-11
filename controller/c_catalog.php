<?php
//Если это страница элемента структуры
if(is_object($this->oStructureItem) && $this->oStructureItem->getId())
{
	echo "<h4>Страница товара</h4><pre>";
	print_r($this->oStructureItem);
	echo "</pre>";
}
//Если это страница структуры
else
{
	echo "<h4>Страница Каталога </h4><pre>";
	print_r($this->oStructure);
	echo "</pre>";
}