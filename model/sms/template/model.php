<?php


namespace Model\Sms;

/**
 * Class Template_Model
 * @package Model\Sms
 */
class Model
{
    /**
     * @var string|null
     */
    protected ?string $tag = null;

    /**
     * @var string|null
     */
    protected ?string $text = null;

    /**
     * @param string|null $tag
     * @return $this|string
     */
    public function tag(string $tag = null)
    {
        if (is_null($tag)) {
            return strval($this->tag);
        } else {
            $this->tag = $tag;
            return $this;
        }
    }

    /**
     * @param string|null $text
     * @return $this|string
     */
    public function text(string $text = null)
    {
        if (is_null($text)) {
            return strval($this->text);
        } else {
            $this->text = $text;
            return $this;
        }
    }

}