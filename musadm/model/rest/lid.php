<?php
/**
 * @author BadWolf
 * @date 15.07.2019 15:00
 */
class Rest_Lid extends Rest_Controller
{
    /**
     * Rest_Lid constructor.
     */
    public function __construct()
    {
        $this->apiUrl = 'http://musadm/musadm/api/lids/api.php';
    }


    /**
     * @return bool|string
     */
    public function getList()
    {
        return parent::getList(); // TODO: Change the autogenerated stub
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return parent::getById($id); // TODO: Change the autogenerated stub
    }
}