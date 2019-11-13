class FileManager {
    /**
     * @returns {string}
     */
    static getApiLink () {
        return root + '/api/file/index.php';
    };


    /**
     * Загрузка файла на сервер
     *
     * @param fileId
     * @param fileTypeId
     * @param fileInput
     * @param modelName
     * @param objectId
     * @param callBack
     */
    static upload(fileId, fileTypeId, fileInput, modelName, objectId, callBack) {
        let fd = new FormData;
        fd.append('file', fileInput.prop('files')[0]);
        fd.append('action', 'upload');
        fd.append('fileId', fileId);
        fd.append('typeId', fileTypeId);
        fd.append('modelName', modelName);
        fd.append('objectId', objectId);

        $.ajax({
            url: FileManager.getApiLink(),
            data: fd,
            type: 'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                if (typeof callBack === 'function') {
                    callBack(response);
                }
            },
            error: function() {
                notificationError('Произошла ошибка при загрузке файла');
            }
        });
    }

}