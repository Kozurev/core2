class Task {
    /**
     * @returns {string}
     */
    static getApiLink () {
        return root + '/api/task/index.php';
    };


    /**
     * Сохранение задачи
     *
     * @param taskData
     * @param callback
     */
    static save(taskData, callback) {
        taskData.append('action', 'save');

        $.ajax({
            type: 'POST',
            url: Task.getApiLink(),
            dataType: 'json',
            data: taskData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (callback !== undefined) {
                    callback(response);
                }
            },
            error: function() {
                notificationError('При сохранении данных задачи произошла ошибка');
                loaderOff();
            }
        });
    }


    /**
     * Сохранение данных задачи из формы
     *
     * @param formSelector
     * @param callback
     */
    static saveFrom(formSelector, callback) {
        var form = $(formSelector);
        if (form.valid()) {
            var taskData = new FormData(form.get(0));
            Task.save(taskData, callback);
        }
    }

}