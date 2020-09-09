<?php
/**
 * Класс-контроллер для работы с филиалами
 *
 * @author Kozurev Egor
 * @date 18.02.2019 15:31
 * @version 20190219
 * Class Schedule_Area_Controller
 */
class Schedule_Area_Controller
{

    /**
     * Кастомная фабрика для филиалов
     *
     * @param int|null $id
     * @param bool $isSubordinate
     * @return Schedule_Area|null
     */
    public static function factory(int $id = null, bool $isSubordinate = true)
    {
        $area = new Schedule_Area();

        if (is_null($id)) {
            return $area;
        }

        $area->queryBuilder()
            ->where('id', '=', $id);

        if ($isSubordinate === true) {
            $authUser = User_Auth::current();
            if (is_null($authUser)) {
                return null;
            }

            $director = $authUser->getDirector();
            if (is_null($director)) {
                return null;
            }

            $area->queryBuilder()
                ->where('subordinated', '=', $director->getId());
        }
        return $area->queryBuilder()->find();
    }
}