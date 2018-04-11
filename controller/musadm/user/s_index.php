<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 11.04.2018
 * Time: 22:16
 */

$aTitle[] = $this->oStructure->title();

if(get_class($this->oStructureItem) == "User_Group")
    $aTitle[] = $this->oStructureItem->title();

if(get_class($this->oStructureItem) == "User")
    $aTitle[] = $this->oStructureItem->surname() . " " . $this->oStructureItem->name();

$this->title = array_pop($aTitle);