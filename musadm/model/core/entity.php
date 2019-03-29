<?php
/**
 * Класс, реализующий методы для преобразования объектов в XML,
 * парсинг сформированного XML документа XSL шаблоном и вывод результата
 *
 * Простой тэг имеет значение и не может иметь вложенных XML-сущьностей
 * Сложный тэг не имеет значнеия но может иметь вложенные XML-сущьности
 *
 * @author Bad Wolf
 * @date 02.08.2018
 * @version 20190218
 * Class Core_Entity
 */
class Core_Entity extends Core_Entity_Model
{
    /**
     * Core_Entity constructor.
     * @param int|null $id
     */
//    public function __construct(int $id = null)
//    {
//        global $DB;
//
//        if (!is_null($id)) {
//            try {
//                //TODO: адаптировать данный метод не только для CMS Moodle; сделать его независимым
//                $existingRecord = $DB->get_record($this->getTableName(false), ['id' => $id]);
//            } catch(dml_exception $exception) {
//                die($exception->getMessage());
//            }
//
//            if ($existingRecord !== false) {
//                $data = get_object_vars($existingRecord);
//                foreach ($data as $propName => $propValue) {
//                    $setterName = toCamelCase($propName);
//                    $this->$setterName($propValue);
//                }
//            }
//        }
//    }

    /**
     * @return Orm
     */
    public function queryBuilder() : Orm
    {
        if (is_null($this->orm())) {
            $this->orm(new Orm($this));
        }

        return $this->orm();
    }

    /**
     * @return array
     */
    public function findAll() : array
    {
        return $this->queryBuilder()->findAll();
    }

    /**
     * @return null|object
     */
    public function find()
    {
        return $this->queryBuilder()->find();
    }

    /**
     * @return $this
     */
    public function save()
    {
        if ($this->timestamps() === true) {
            //Если это новый объект
            if (empty($this->id)) {
                $this->timecreated(time());
                $this->timemodified(time());
            } else {
                $this->timemodified(time());
            }
        }

        if ($this->validate() === true) {
            $this->queryBuilder()->save($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function delete()
    {
        if ($this->safeDelete() === false) {
            $this->queryBuilder()->delete($this);
        } else {
            $this->markAsDeleted();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function markAsDeleted()
    {
        if (isset($this->id) && !empty($this->id) && isset($this->deleted)) {
            $this->deleted(1);
            $this->save();
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getCount() : int
    {
        return $this->queryBuilder()->getCount();
    }

    /**
     * Валидация значнеий сохраняемого объекта
     *
     * @return bool
     */
    public function validate() : bool
    {
        if (!method_exists($this, 'schema')) {
            return true;
        }

        foreach ($this->schema() as $propName => $rules) {
            $isRequired = Core_Array::getValue($rules, 'required', false, PARAM_BOOL);
            $type = Core_Array::getValue($rules, 'type', null, PARAM_STRING);
            $minLength = Core_Array::getValue($rules, 'minlength', null, PARAM_INT);
            $maxLength = Core_Array::getValue($rules, 'maxlength', null, PARAM_INT);
            $minVal = Core_Array::getValue($rules, 'minval', null, PARAM_FLOAT);
            $maxVal = Core_Array::getValue($rules, 'maxval', null. PARAM_FLOAT);


            //check required
            if ($isRequired === true) {
                if (is_null($this->$propName)) {
                    return false;
                }
            } elseif ($isRequired === false && is_null($this->$propName)) {
                return true;
            }

            //check type
            //TODO: add type validate

            //check maxlength
            if (!is_null($maxLength) && $type === PARAM_STRING) {
                if (mb_strlen($this->$propName) > $maxLength) {
                    return false;
                }
            }

            //check minlength
            if (!is_null($minLength) && $type === PARAM_STRING) {
                if (mb_strlen($this->$propName) < $minLength) {
                    return false;
                }
            }

            //check max val
            if (!is_null($maxLength) || in_array($type, [PARAM_INT, PARAM_FLOAT])) {
                if ($this->$propName > $maxLength) {
                    return false;
                }
            }

            //check min val
            if (!is_null($minVal) || in_array($type, [PARAM_INT, PARAM_FLOAT])) {
                if ($this->$propName < $maxVal) {
                    return false;
                }
            }

        } //end schema foreach

        return true;
    }

    /**
     * Возвращает название таблицы для объекта
     *
     * @param bool $isWithPrefix
     * @return string
     */
    public function getTableName(bool $isWithPrefix = true) : string
    {
        global $CFG;

        if (method_exists($this, 'databaseTableName')) {
            return $this->databaseTableName();
        } else {
//            $table = mb_strtolower(get_class($this)) . 's';
//            if ($isWithPrefix === true) {
//                $table = $CFG->prefix . $table;
//            }
            return get_class($this);
        }
    }

    /**
     * Формирует из не пустых свойств объекта ассоциативный массив для конструктора запросов
     * а именно для метода save
     *
     * @return array
     */
    public function getObjectProperties() : array
    {
        $result = [];

        $Model = $this->getModel(); //Модель данного объекта если такая существует

        //ФОрмирование
        if (!is_null($Model)) {
            $modelProperties = get_object_vars($Model);

            foreach ($modelProperties as $propertyName => $propertyValue) {
                $value = $this->$propertyName;
                if (!is_object($value) && !is_array($value)) {
                    $result[$propertyName] = $value;
                }
            }
        } elseif (isset($this->tableRows) && is_array($this->tableRows)) {
            foreach ($this->tableRows as $propertyName) {
                if(!is_array($this->$propertyName) && !is_object($this->$propertyName)) {
                    $result[$propertyName] = $this->$propertyName;
                }
            }
        } else {
            $properties = get_object_vars($this);
            foreach ($properties as $propertyName => $propertyValue) {
                if(!is_array($propertyValue) && !is_object($propertyValue)) {
                    $result[$propertyName] = $propertyValue;
                }
            }
        }

        return $result;
    }

    /**
     * Поиск класса-модели для объекта
     * Данный метод адаптирован под немного другую систему но не стал удалять
     *
     * @return mixed | null
     */
    public function getModel()
    {
        $modelClassName = get_class($this) . '_Model';
        $Model = Core::factory($modelClassName);

        if (!is_null($Model)) {
            $properties = get_object_vars($Model);

            foreach ($properties as $propertyName => $propertyValue) {
                //Формирование названия сеттера для свойства
                if ($propertyName == 'id') {
                    $snakeCaseSetter = 'getId';
                    $camelCaseSetter = 'getId';
                } else {
                    $snakeCaseSetter = $propertyName;
                    $camelCaseSetter = toCamelCase($propertyName);
                }

                $value = $this->$propertyName;

                if (is_array($value) || is_object($value)) {
                    continue;
                }

                if (method_exists($Model, $snakeCaseSetter)) {
                    $Model->$snakeCaseSetter($value);
                } elseif (method_exists($Model, $camelCaseSetter)) {
                    $Model->$camelCaseSetter($value);
                }
            }
        }

        return $Model;
    }

    /**
     * Конвертирует, к примеру, "Structure_Item" в "structure_item"
     * Я знаю что этот метод реализован одной стандартной PHP-шной функцией mb_strtolower
     * но всё равно этот метод был создан с тем что в будущем формат преобразования немного изменится
     *
     * @param $inputName - название модели, которое необходимо отконвертировать
     * @return string - название модели без больших букв
     */
    protected function renameModelName($inputName) : string
    {
        $segments = explode('_', $inputName);
        $outputName = '';

        foreach ($segments as $segment) {
            if ($outputName == '') {
                $outputName .= lcfirst($segment);
            } else {
                $outputName .= '_' . lcfirst( $segment );
            }
        }

        return $outputName;
    }

    /**
     * Добавление дочерней сущьности в XML
     *
     * @param $obj
     * @param null $tag
     * @return $this
     */
    public function addEntity($obj, $tag = null)
    {
        if (!is_object($obj)) {
            return $this;
        }

        if (!is_null($tag)) {
            if (method_exists($obj,  '_customTag')) {
                $obj->_customTag($tag);
            } elseif (get_class($obj) == 'stdClass') {
                $obj->_customTag = $tag;
            }
        }

        if ($this->_entityValue() == '') {
            $this->childrenObjects[] = $obj;
        }

        return $this;
    }

    /**
     * Добавление массива дочерних сущьностей в XML
     *
     * @param $Children
     * @param null $tags
     * @return $this
     */
    public function addEntities($Children, $tags = null)
    {
        if (is_array($Children) && count($Children) > 0) {
            foreach ($Children as $Child) {
                if (is_object($Child)) {
                    $this->addEntity($Child, $tags);
                }
            }
        }

        return $this;
    }

    /**
     * Добавление простой дочерней сущьности в XML
     *
     * @param $name - название тэга
     * @param $value - значение
     * @return $this
     */
    public function addSimpleEntity($name, $value)
    {
        if (is_null($value)) {
            $value = '';
        }

        $this->addEntity(
            Core::factory('Core_Entity')
                ->_entityName($name)
                ->_entityValue($value)
        );

        return $this;
    }

    /**
     * Преобразование объекта в XML-сущьность
     * так же выполняется рекурсивное преобразование дочерних сущьностей
     *
     * @param $obj - объект, который необходимо преобразовать в XML-сущьность
     * @param $xmlObj - объект конечной XML-сущьности
     * @return DOMElement
     */
    public function createEntity($obj, DOMDocument $xmlObj)
    {
        $xml = $xmlObj;

        if ($obj instanceof Core_Entity) {
            //Формирование простого тэга
            if (is_null($obj->_entityValue())) {
                return $xml->createElement($obj->_entityName(), $obj->_entityValue());
            } else {
                $tagName = $obj->_entityName();
            }
        } else {
            if (method_exists($obj, '_customTag') && $obj->_customTag() != '') {
                $tagName = $obj->_customTag();
            } elseif (isset($obj->_customTag) && $obj->_customTag != '') {
                $tagName = $obj->_customTag;
            } else {
                $tagName = $this->renameModelName(get_class($obj));
            }
        }

        //Создание тэга
        $objTag = $xml->createElement($tagName);
        $objData = get_object_vars($obj);

        //Преобразование объекта в XML сущьность
        foreach ($objData as $key => $val) {
            if (is_array($val) || $key != 'childrenObjects') {
                continue;
            }

            //Если совйство представляет из себя массив дочерних сущьностей
            if ($key == 'childrenObjects') {
                foreach ($val as $childObject) {
                    $objChildTag = $this->createEntity($childObject, $xml);
                    $objTag->appendChild($objChildTag);
                }
            } elseif ($val !== '' && !is_null($val)) {
                $objTag->appendChild($xml->createElement($key, strval($val)));
            } elseif ($val === '' || is_null($val)) {
                $objTag->appendChild($xml->createElement($key, ''));
            }
        }

        return $objTag;
    }

    /**
     * Метод для парсинга сформированного XML файла указанным XSL шаблоном
     * и формирование/вывод конечного HTML-кода
     *
     * @param bool $isShowing - указатеь на то будет ли HTML код сразу выводиться на странице
     *                          либо метод вернет его в виде строки
     * @return string
     */
    public function show(bool $isShowing = true) : string
    {
        if ($this->xsl() == '') {
            exit ('Не указан путь к XSL шаблону');
        }

        $xmlText = '<?xml version="1.0" encoding="utf-8"?>
		<?xml-stylesheet type="text/xsl" href="' . $this->xsl() . '"?>';
        $xmlText .= '<' . $this->_entityName() . '></' . $this->_entityName() . '>';

        $xml = new DOMDocument();
        $xml->loadXML($xmlText);
        $rootTag = $xml->getElementsByTagName($this->_entityName())->item(0);

        foreach ($this->childrenObjects as $obj) {
            $rootTag->appendChild($this->createEntity($obj, $xml));
        }

        // Объект стиля
        $xsl = new DOMDocument();
        $xsl->load($this->xsl());

        // Создание парсера
        $proc = new XSLTProcessor();

        // Подключение стиля к парсеру
        $proc->importStylesheet($xsl);

        // Обработка парсером исходного XML-документа
        $parsed = $proc->transformToXml($xml);

        // Вывод результирующего кода
        if ($isShowing == true) {
            echo $parsed;
        }

        return $parsed;
    }

    /**
     * Сеттер для идентификатора объекта
     * Данный метод желательно использовать лишь в крайних случаях
     *
     * @param int $id
     * @return $this
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }


    /**
     * @return int
     */
    public function getId() : int
    {
        return intval($this->id);
    }


    /**
     * @param $id
     * @return $this|int
     */
    public function id($id = null)
    {
        if (!is_null($id)) {
            return $this->setId($id);
        } else {
            return $this->getId();
        }
    }

    /**
     * Указатель на наличие свойств $timecreated и $timemodified в таблице объекта
     * Если необходимо хранить значения даты создания и последнего сохранения объекта то данный метод необходимо
     * переопределить в модели объекта с возвращаемым логическим значнием - true
     *
     * @return bool
     */
    public function timestamps()
    {
        return false;
    }

    /**
     * Указатель на "мягкое"/полное удаление объекта из таблицы
     * при "мягком" удалении - объект сохраняется в таблице со значением deleted = 1
     * Для мягкого удаления объекта необходимо наличие столбца с названием deleted в таблице объекта, а также
     * переопределить данный метод в моделе объекта с возвращаемым логическим значением - true
     *
     * @return bool
     */
    public function safeDelete()
    {
        return false;
    }

}