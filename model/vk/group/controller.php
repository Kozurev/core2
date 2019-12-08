<?php

class Vk_Group_Controller extends Controller
{
    /**
     * Vk_Group_Controller constructor.
     * @param User|null $user
     */
    public function __construct(User $user = null)
    {
        if (!is_null($user)) {
            $this->setUser($user);
        }
        $group = new Vk_Group();
        $this->setObject($group);
        $this->setQueryBuilder($group->queryBuilder());
        $this->isWithComments(false);
        parent::__construct(['user' => &$user]);
    }

    /**
     * @param int|null $id
     * @param bool $isSubordinate
     * @return Vk_Group|null
     */
    public static function factory(int $id = 0, bool $isSubordinate = true)
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

    /**
     * @return array
     */
    public function getList() : array
    {
        if ($this->isSubordinate()) {
            $this->getQueryBuilder()->where('subordinated', '=', $this->getSubordinate());
        }

        return $this->getQueryBuilder()->findAll();
    }


}