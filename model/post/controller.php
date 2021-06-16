<?php

/**
 * Class Post_Controller
 */
class Post_Controller extends Controller
{
    /**
     * Post_Controller constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        $this->isPaginate(true);
        $this->setObject((new Post));
        $this->setQueryBuilder(Post::query()->orderBy('id', 'desc'));
        parent::__construct($params);
    }

    /**
     * @return Post[]
     */
    public function getPosts(): array
    {
        $areasMultiAccess = Core_Access::instance()->hasCapability(Core_Access::AREA_MULTI_ACCESS, $this->getUser());
        if (!$areasMultiAccess && !empty($this->areasIds)) {
            $areaAssignment = new Schedule_Area_Assignment();
            $this->getQueryBuilder()->leftJoin(
                $areaAssignment->getTableName() . ' AS asgm',
                $this->getObject()->getTableName() . '.id = asgm.model_id 
                AND asgm.model_name = \''.get_class($this->getObject()).'\'');
            $this->getQueryBuilder()
                ->open()
                    ->whereIn('asgm.area_id', $this->areasIds)
                    ->orWhere('asgm.area_id', 'is', 'NULL')
                ->close()
                ->groupBy($this->getObject()->getTableName() . '.id')
                ->groupBy('asgm.area_id');
        }

        $this->paginateExecute();
        $this->foundObjects = $this->getQueryBuilder()->findAll();
        $this->countFoundObjects = count($this->foundObjects);
        $this->foundObjectsIds = array_map(function (Post $post) {
            return $post->getId();
        }, $this->foundObjects);
        return $this->foundObjects;
    }

    /**
     * @param null $outputXml
     * @return mixed
     */
    public function show($outputXml = null)
    {
        global $CFG;
        $posts = $this->getPosts();

        $outputXml = (new Core_Entity)
            ->addSimpleEntity('wwwroot', $CFG->wwwroot)
            ->addEntity($this->paginate(), 'pagination')
            ->addEntities(array_map(function (Post $post): Post {
                $post->refactored_date = refactorDateTimeFormat($post->date);
                return $post;
            }, $posts))
            ->xsl($this->xsl);

        foreach ($this->simpleEntities as $entity) {
            $outputXml->addEntity($entity);
        }

        $observerArgs = [
            'controller' => &$this,
            'outputXml' => &$outputXml
        ];

        Core::notify($observerArgs, 'before.PostController.show');
        return parent::show($outputXml)->show();
    }
}