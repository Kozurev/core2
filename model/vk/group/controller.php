<?php

Core::requireClass('Vk_Group');

class Vk_Group_Controller
{

    /**
     * @param int|null $id
     * @param bool $isSubordinate
     * @return Vk_Group|null
     */
    public static function factory(int $id = null, bool $isSubordinate = true)
    {
        $group = new Vk_Group();

        if (empty($id)) {
            return $group;
        }

        $group->queryBuilder()
            ->where('id', '=', $id);

        if ($isSubordinate === true) {
            $user = User_Auth::current();
            if (empty($user)) {
                return null;
            }

            $director = $user->getDirector();
            if (empty($director)) {
                return null;
            }

            $group->queryBuilder()
                ->where('subordinated', '=', $director->getId());
        }

        return $group->find();
    }


}