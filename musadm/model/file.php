<?php
/**
 * Файловый менеджер
 *
 * @author BadWolf
 * @date 27.09.2019 12:41
 * Class File
 */
class File extends File_Model
{
    /**
     * Дирректория для загрузки файла
     *
     * @var string
     */
    protected static $uploadDir = ROOT . '/upload';

    /**
     * Название соответствующие значению type_id или одной из констант с префиксом self::TYPE_
     *
     * @var array
     */
    protected static $typeNames = [
        0 => 'По умолчанию',
        1 => 'Анкета'
    ];

    /**
     * Список разрешенных к загрузке разрешений файлов
     *
     * @var array
     */
    protected static $allowedExtensions = [
        'jpg', 'png', 'jpeg', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar', 'pdf'
    ];

    /**
     * Текст ошибок при загрузке файла из массива $_FILES
     * Каждая ошибка соответствует значению $_FILES['file']['error']
     *
     * @var array
     */
    protected static $errorsMessages = [
        0 => 'Файл успешно загружен на сервер', //UPLOAD_ERR_OK
        1 => 'Размер принятого файла превысил максимально допустимый размер', //UPLOAD_ERR_INI_SIZE
        2 => 'Размер загружаемого файла превысил значение MAX_FILE_SIZE, указанное в HTML-форме', //UPLOAD_ERR_FORM_SIZE
        3 => 'Загружаемый файл был получен только частично', //UPLOAD_ERR_PARTIAL
        4 => 'Файл не был загружен', //UPLOAD_ERR_NO_FILE
        6 => 'Отсутствует временная папка', //UPLOAD_ERR_NO_TMP_DIR
        7 => 'Не удалось записать файл на диск', //UPLOAD_ERR_CANT_WRITE
        8 => 'PHP-расширение остановило загрузку файла' //UPLOAD_ERR_EXTENSION
    ];

    /**
     * Список ошибок при загрузке файла
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Указатель на необходимость проверки разрешения файла
     *
     * @var bool
     */
    protected $isCheckExtension = true;




    /**
     * @param bool $isCheckExtension
     * @return $this
     */
    public function setCheckExtension(bool $isCheckExtension)
    {
        $this->isCheckExtension = $isCheckExtension;
        return $this;
    }


    /**
     * @return bool
     */
    public function getCheckExtension() : bool
    {
        return $this->isCheckExtension;
    }


    /**
     * @param string $error
     * @return $this
     */
    public function appendError(string $error)
    {
        $this->errors[] = $error;
        return $this;
    }


    public function getErrors(): array
    {
        return $this->errors;
    }


    /**
     * Валидация загружаемого файла на сервер
     *
     * @param array $fileData
     * @return bool
     */
    public function validate(array $fileData)
    {
        if ($fileData['error'] > 0) {
            $message = 'Ошибка загрузки файла "' . $fileData['name'] . '": '
                . Core_Array::getValue(self::$errorsMessages, $fileData['error'], 'Неизвестная ошибка');
            $this->appendError($message);
        }

        //Проверка типа на соответствие разрешения загружаемого файла
        if ($this->getCheckExtension()) {
            $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
            if (!in_array($extension,self::$allowedExtensions) ) {
                $this->appendError('Недопустимое разрешение файла ".' . $extension . '"');
            }
        }

        return empty($this->getErrors());
    }


    /**
     * Загрузка файла на сервер
     *
     * @param array $fileData
     * @param int $typeId
     * @return bool
     */
    public function upload(array $fileData, int $typeId = null)
    {
        if (!empty($this->getId())) {
            $this->removeFile();
        }

        if (!$this->validate($fileData)) {
            return false;
        }

        $fileExtension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
        $newFileName = uniqidReal() . '.' . $fileExtension;
        $this->name($newFileName);
        $this->realName(basename($fileData['name']));
        $this->type($fileData['type']);
        if (!is_null($typeId)) {
            $this->typeId($typeId);
        }

        $fileDirPath = self::$uploadDir . '/' . $this->typeId();
        if (!is_dir($fileDirPath)) {
            if (!mkdir($fileDirPath)) {
                $this->appendError('Невозможно создать дирректорию для хранения файла');
                return false;
            }
        }

        if (!move_uploaded_file($fileData['tmp_name'], $fileDirPath . '/' . $newFileName)) {
            $this->appendError('Не удалось переместить загружаемый файл на сервер');
            return false;
        }

        if (empty($this->save())) {
            $this->appendError($this->_getValidateErrorsStr());
            return false;
        }
        return true;
    }


    /**
     * Удаление файла с сервера
     *
     * @return bool
     */
    public function removeFile()
    {
        $filePath = $this->getFilePath();
        if (is_file($filePath)) {
            return unlink($filePath);
        } else {
            return false;
        }
    }


    /**
     * Формировние пути к файлу на сервере
     *
     * @return string
     */
    public function getFilePath()
    {
        return self::$uploadDir . '/' . $this->typeId() . '/' . $this->name();
    }


    /**
     * Формирование ссылки на скачивание файла
     *
     * @return string
     */
    public function getLink()
    {
        global $CFG;
        return $CFG->wwwroot . '/api/file?action=download&file=' . $this->name();
    }


    /**
     * @param $object
     * @return File_Assignment|null
     */
    public function makeAssignment($object)
    {
        Core::requireClass('File_Assignment');
        if (empty($this->getId())) {
            return null;
        }
        if (!method_exists($object, '_getModelId') || !method_exists($object, 'getId')) {
            return null;
        }
        if ($object->_getModelId() === MODEL_UNDEFINED) {
            return null;
        }
        $Assignment = new File_Assignment();
        $Assignment->fileId($this->getId());
        $Assignment->modelId($object->_getModelId());
        $Assignment->objectId($object->getId());
        $Assignment->save();
        return $Assignment;
    }


    /**
     * Поиск файлов, принадлежащих объекту
     *
     * @param $object
     * @return array|null
     */
    public static function getFiles($object)
    {
        Core::requireClass('File_Assignment');
        if (!method_exists($object, '_getModelId') || !method_exists($object, 'getId')) {
            return null;
        }
        if ($object->_getModelId() === MODEL_UNDEFINED) {
            return null;
        }

        $File = new File();
        $Assignment = new File_Assignment();

        return $File->queryBuilder()
            ->join(
                $Assignment->getTableName() . ' as asgm', 'asgm.model_id = ' . $object->_getModelId() .
                ' AND asgm.object_id = ' . $object->getId() . ' AND asgm.file_id = ' . $File->getTableName() . '.id'
            )
            ->findAll();
    }


    /**
     * Скачивание файла
     *
     * @param string $fileName
     */
    public static function download(string $fileName)
    {
        $File = self::getByName($fileName);
        if (is_null($File)) {
            return;
        }
        if (!is_file($File->getFilePath())) {
            return;
        }

        header('Content-Type: ' . $File->type());
        header('Content-Disposition: attachment; filename="' . $File->realName() . '";');
        header('Content-Length: ' . filesize($File->getFilePath()));
        readfile($File->getFilePath());
    }


    /**
     * @param int $fileId
     * @return File|null
     */
    public static function getById(int $fileId)
    {
        $File = new File;
        if ($fileId === 0) {
            return $File;
        } else {
            return $File->queryBuilder()->where('id', '=', $fileId)->find();
        }
    }


    /**
     * @param string $fileName
     * @return File|null
     */
    public static function getByName(string $fileName)
    {
        return Core::factory('File')
            ->queryBuilder()
            ->where('name', '=', $fileName)
            ->find();
    }


    /**
     * @return void
     */
    public function delete()
    {
        Core::notify([&$this], 'before.File.delete');
        $this->removeFile();
        parent::delete();
        Core::notify([&$this], 'after.File.delete');
    }
}