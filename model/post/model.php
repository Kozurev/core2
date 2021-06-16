<?php

/**
 * Class Post_Model
 */
class Post_Model extends Core_Entity
{
    /**
     * @var string|null
     */
    public ?string $title = null;

    /**
     * @var string|null
     */
    public ?string $content = null;

    /**
     * @var int|null
     */
    public ?int $author_id = null;

    /**
     * @var string|null
     */
    public ?string $author_name = null;

    /**
     * @var string|null
     */
    public ?string $date = null;

    /**
     * @return $this|null
     */
    public function save(): ?self
    {
        if (is_null($this->date)) {
            $this->date = date('Y-m-d H:i:s');
        }
        if (is_null($this->author_id)) {
            $user = User_Auth::current();
            if (!is_null($user)) {
                $this->author_id = $user->getId();
                if (is_null($this->author_name)) {
                    $this->author_name = $user->getFio();
                }
            }
        }
        return parent::save();
    }

    /**
     * @return array[]
     */
    public function schema(): array
    {
        return [
            'title' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'content' => [
                'required' => true,
                'type' => PARAM_STRING
            ],
            'author_id' => [
                'required' => true,
                'type' => PARAM_INT
            ],
            'author_name' => [
                'required' => true,
                'type' => PARAM_INT,
                'maxlength' => 255
            ],
//            'date' => [
//                'required' => false,
//                'type' => PARAM_DATETIME
//            ]
        ];
    }

}