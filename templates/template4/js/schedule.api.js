class Schedule {
    /**
     * @returns {string}
     */
    static getApiLink () {
        return root + '/api/schedule/index.php';
    };


    static clearCache() {
        localStorage.removeItem('schedule.getAreasList.areas');
        localStorage.removeItem('schedule.getAreasList.lastParams');
    }

    /**
     * Проверка на существование периода отсутствия у клиента на определенную дату
     *
     * @param params
     * @param callBack
     */
    static checkAbsentPeriod(params, callBack) {
        params.action = 'checkAbsentPeriod';
        $.ajax({
            type: 'GET',
            url: this.getApiLink(),
            dataType: 'json',
            data: params,
            success: function(response) {
                callBack(response);
            }
        });
    }

    /**
     * Получение списка филлиалов
     *
     * @param params
     * @param callBack
     */
    static getAreasList(params, callBack) {
        params.action = 'getAreasList';
        let cache = localStorage.getItem('schedule.getAreasList.areas');
        let lastCacheParams = localStorage.getItem('schedule.getAreasList.lastParams');

        if (JSON.stringify(params) == lastCacheParams && cache != null) {
            callBack(JSON.parse(cache));
        } else {
            $.ajax({
                type: 'GET',
                url: this.getApiLink(),
                dataType: 'json',
                data: params,
                success: function(response) {
                    localStorage.setItem('schedule.getAreasList.lastParams', JSON.stringify(params));
                    localStorage.setItem('schedule.getAreasList.areas', JSON.stringify(response));
                    callBack(response);
                }
            });
        }
    }

    /**
     * Сохранение периода отсутствия
     *
     * @param absent
     * @param callback
     */
    static saveAbsentPeriod(absent, callback) {
        absent.action = 'saveAbsentPeriod';
        $.ajax({
            type: 'POST',
            url: this.getApiLink(),
            dataType: 'json',
            data: absent,
            success: function(response) {
                if (typeof callback == 'function') {
                    callback(response);
                }
            },
            error: function(response) {
                checkResponseStatus(response);
            }
        });
    }

    /**
     * Проверка на совпадение времени занятия с рабочим временем преподавателя
     *
     * @param data
     * @param callback
     */
    static isInTeacherTime(data, callback) {
        data.action = 'isInTeacherTime';
        $.ajax({
            type: 'POST',
            url: this.getApiLink(),
            dataType: 'json',
            data: data,
            success: function (response) {
                if (callback !== undefined) {
                    callback(response);
                }
            },
            error: function () {
                notificationError('При проверке рабочего времени преподавателя произошла ошибка');
                loaderOff();
            }
        });
    }

    /**
     * Сохранение рабочего времени преподавателя
     *
     * @param data
     * @param callback
     */
    static saveTeacherTime(data, callback) {
        data.action = 'saveTeacherTime';
        $.ajax({
            type: 'POST',
            url: this.getApiLink(),
            dataType: 'json',
            data: data,
            success: function (response) {
                if (callback !== undefined) {
                    callback(response);
                }
            },
            error: function () {
                notificationError('При сохранении рабочего времени преподавателя произошла ошибка');
                loaderOff();
            }
        });
    }

    /**
     * Удаление рабочего времени преподавателя
     *
     * @param id
     * @param callback
     */
    static removeTeacherTime(id, callback) {
        $.ajax({
            type: 'POST',
            url: this.getApiLink(),
            dataType: 'json',
            data: {
                action: 'removeTeacherTime',
                id: id
            },
            success: function (response) {
                if (callback !== undefined) {
                    callback(response);
                }
            },
            error: function () {
                notificationError('При сохранении рабочего времени преподавателя произошла ошибка');
                loaderOff();
            }
        });
    }

    /**
     * Поиск свободного врмемени преподавателя рядом с другими его занятиями
     *
     * @param teacherId
     * @param date
     * @param lessonDuration
     * @param callback
     */
    static getNearestTeacherTime(teacherId, date, lessonDuration, callback) {
        $.ajax({
            type: 'GET',
            url: this.getApiLink(),
            dataType: 'json',
            data: {
                action: 'getTeacherNearestTime',
                teacherId: teacherId,
                date: date,
                lessonDuration: lessonDuration
            },
            success: function (response) {
                if (callback !== undefined) {
                    callback(response);
                }
            },
            error: function () {
                notificationError('При получении свободного времени преподавателя произошла ошибка');
                loaderOff();
            }
        });
    }

    /**
     * Получение графика работы преподавателя (не расписания занятий!)
     *
     * @param teacherId
     * @param callback
     */
    static getTeacherSchedule(teacherId, callback) {
        $.ajax({
            type: 'GET',
            url: this.getApiLink(),
            dataType: 'json',
            data: {
                action: 'getTeacherSchedule',
                teacherId: teacherId
            },
            success: function (response) {
                if (callback !== undefined) {
                    callback(response);
                }
            },
            error: function () {
                notificationError('При получении расписания преподавателя произошла ошибка');
                loaderOff();
            }
        });
    }

    /**
     * Сохранение занятия
     *
     * @param lessonData
     * @param callback
     */
    static saveLesson(lessonData, callback) {
        lessonData.action = 'saveLesson';
        $.ajax({
            type: 'POST',
            url: this.getApiLink(),
            dataType: 'json',
            data: lessonData,
            success: function(response) {
                if (typeof callback === 'function') {
                    callback(response);
                }
            }
        });
    }

    /**
     * Получение краткой статистики по отчетам/типам занятий
     *
     * @param params
     * @param callback
     */
    static getReportsStatistic(params, callback) {
        params.action = 'getReportsStatistic';
        $.ajax({
            type: 'GET',
            url: this.getApiLink(),
            data: params,
            dataType: 'json',
            success: function(response) {
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function() {
                notificationError('При получении данных произошла ошибка');
                loaderOff();
            }
        });
    }
}