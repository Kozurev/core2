<?php
/**
 * Класс комментария
 *
 * @author Kozurev Egor
 * @date 31.01.2019 10:25
 * @version 20190802
 *
 * Class Comment
 */
class Comment extends Comment_Model
{

    /**
     * @param null $id
     * @return Comment | null
     */
    public static function factory($id = null)
    {
        if (empty($id)) {
            return Core::factory('Comment');
        } else {
            return Core::factory('Comment', $id);
        }
    }


    /**
     * Создание комментария и связи с объектом
     *
     * @param $object
     * @param $text
     * @param null $authorId
     * @param null $datetime
     * @throws Exception
     * @return Comment
     */
    public static function create($object, $text, $authorId = null, $datetime = null)
    {
        $Comment = new Comment();
        $Comment->text($text);
        if (!is_null($authorId)) {
            $Comment->authorId($authorId);
        }
        if (!is_null($datetime)) {
            $Comment->datetime($datetime);
        }
        $Comment->save();

        if (is_null($Comment->makeAssignment($object))) {
            $Comment->delete();
            return null;
        } else {
            return $Comment;
        }
    }


    /**
     * Получение массива комментариев объекта
     *
     * @param $object
     * @return array
     * @throws Exception
     */
    public static function getAll($object)
    {
        $Assignment = self::getAssignment($object);

        if (is_null($Assignment)) {
            return [];
        }

        if (!method_exists($object, 'getId')) {
            throw new Exception('Comment::getAll: объект не соответсвует требованиям: отсутствует обязательный метод getId');
        }

        if (!is_object($Assignment)) {
            throw new Exception('Comment::getAll: связь комментариев и объектов должна быть объектом');
        }

        if (!method_exists($Assignment, 'getTableName')) {
            throw new Exception('Comment::getAll: связующий объект не соответствует требованиям: 
                                            отсутствует обязательный метод getTableName');
        }

        $Comment = new Comment();
        $CommentsQuery = $Comment->queryBuilder()
            ->join(
                $Assignment->getTableName() . ' AS asgm',
                'asgm.object_id = ' . $object->getId().' AND asgm.comment_id = '.$Comment->getTableName().'.id'
            )
            ->orderBy('datetime', 'DESC');

        $observerArgs = [
            'queryBuilder' => &$CommentsQuery,
            'object' => &$object
        ];

        Core::notify($observerArgs, 'before.Comment.getAll');

        $Comments = $CommentsQuery->findAll();

        $observerArgs['comments'] = &$Comments;
        Core::notify($observerArgs, 'after.Comment.getAll');
        return $Comments;
    }


    /**
     * Получение объекта связующего комментарии и прочие элементы системы
     *
     * @param $object
     * @param int $id
     * @return Comment_Assignment|mixed|null
     */
    public static function getAssignment($object, int $id = 0)
    {
        if (!is_object($object)) {
            return null;
        }

        if (method_exists($object, 'getCommentAssignment')) {
            return $object->getCommentAssignment();
        } else {
            $asgmTabName = get_class($object) . '_Comment_Assignment';
            return Core::factory($asgmTabName, $id);
        }
    }


    /**
     * Создание связи комментария и объекта
     *
     * @param $object
     * @throws Exception
     * @return Comment_Assignment|mixed
     */
    public function makeAssignment($object)
    {
        if (!is_object($object)) {
            throw new Exception('Comment->makeAssignment: Передаваемый параметр "object" должен быть объектом');
        }

        if (method_exists($object, 'makeCommentAssignment')) {
            return $object->makeCommentAssignment($this);
        }

        $Assignment = self::getAssignment($object);

        if (is_null($Assignment)) {
            throw new Exception('Comment->makeAssignment: Объект не имеет связующей таблицы с комментариями');
        }

        if (!method_exists($object, 'getId')) {
            throw new Exception('Comment->makeAssignment: Объект не соответствует требованиям для создания комментария');
        }

        if (!method_exists($Assignment, 'commentId') || !method_exists($Assignment, 'objectId')) {
            throw new Exception('Comment->makeAssignment: модель связи комментария с объектом не соответствует требованиям');
        }

        $Assignment->commentId($this->getId());
        $Assignment->objectId($object->getId());
        $Assignment->save();
        return $Assignment;
    }


    /**
     * @return $this|null
     */
    public function save()
    {
        if (empty($this->datetime())) {
            $this->datetime = date('Y-m-d H:i:s');
        }

        $User = User::parentAuth();

        if (empty($this->authorId()) && !is_null($User)) {
            $this->authorId($User->getId());
        }
        if (empty($this->authorFullname()) && !empty($this->authorId()) && !is_null($User)) {
            $this->authorFullname($User->surname()  . ' ' . $User->name());
        }
        $this->text = htmlspecialchars($this->text);

        Core::notify([&$this], 'before.Comment.save');

        if (empty(parent::save())) {
            return null;
        }

        Core::notify([&$this], 'after.Comment.save');

        return $this;
    }


    /**
     * @return $this|void
     */
    public function delete()
    {
        Core::notify([&$this], 'before.Comment.delete');
        parent::delete();
        Core::notify([&$this], 'after.Comment.delete');
    }

}