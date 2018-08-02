<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 04.03.2018
 * Time: 17:33
 */

class Constant_Dir_Controller extends Constant_Dir_Controller_Model
{
    public function __construct()
    {
        $this->databaseTableName("Constant_Dir");
    }

    public function findAll()
    {
        $outputData = parent::findAll();

        if($this->properties())
        {
            $outputData = $this->getPropertiesValuesForItems($outputData);
        }

        return $outputData;
    }


    public function show()
    {
        $aoData = $this->findAll();
        Core::factory("Entity")
            ->addEntities($this->childrenObjects)
            ->addEntities($aoData)
            ->xsl($this->xsl())
            ->show();
    }



}