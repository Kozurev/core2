<?php

class Senler_Settings extends Senler_Settings_Model
{

    /**
     * @param int $id
     * @param bool $isSubordinate
     * @return Senler_Settings|null
     */
    public static function getById(int $id, bool $isSubordinate = true)
    {
        $query = (new self)->queryBuilder()
            ->where((new self)->getTableName() . '.id', '=', $id);

        if ($isSubordinate === true) {
            $user = User_Auth::current();
            if (is_null($user)) {
                return null;
            }
            $director = $user->getDirector();
            if (is_null($director)) {
                return null;
            }
            $query->join((new Vk_Group())->getTableName() . ' AS vk', 'vk.id = vk_group_id AND vk.subordinated = ' . $director->getId());
        }

        return $query->find();
    }

}