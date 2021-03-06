<?php
/**
 * Массив названий всех воможных воможностей (прав)
 *
 * @author BadWolf
 * @date 03.05.2019 9:30
 */
return [
    'task_read' => 'Просмотр задачи',
    'task_create' => 'Создание задачи',
    'task_edit' => 'Изменение задачи',
    'task_delete' => 'Удаление задачи',
    'task_append_comment' => 'Создание комментария к задаче',

    'lid_read' => 'Просмотр лидов',
    'lid_create' => 'Создание лида',
    'lid_edit' => 'Изменение лида',
    'lid_delete' => 'Удаление лида',
    'lid_append_comment' => 'Добавление комментария к лиду',
    'lid_statistic' => 'Просмотр статистики лидов',

    'user_read_clients' => 'Просмотр клиентов',
    'user_read_teachers' => 'Просмотр преподавателей',
    'user_read_managers' => 'Просмотр менеджеров',
    'user_create_client' => 'Создание учетных записей клиентов',
    'user_create_teacher' => 'Создание учетных записей преподавателей',
    'user_create_manager' => 'Создание учетных записей менеджеров',
    'user_edit_client' => 'Редактирование учетных записей клиентов',
    'user_edit_teacher' => 'Редактирование учетных записей преподавателей',
    'user_edit_manager' => 'Редактирование учетных записей менеджеров',
    'user_archive_client' => 'Архивация клиентов',
    'user_archive_teacher' => 'Архивация преподавателей',
    'user_archive_manager' => 'Архивация менеджеров',
    'user_delete' => 'Удаление пользователей',
    'user_edit_lessons' => 'Редактирование кол-ва занятий клиентов',
    'user_append_comment' => 'Добавление комментария к пользователю',
    'user_lc_client' => 'Доступ в личный кабинет клиента',
    'user_lc_teacher' => 'Доступ в личный кабинет преподавателя',
    'user_export' => 'Экспорт пользователей',

    'schedule_read' => 'Просмотр расписания',
    'schedule_read_user' => 'Просмотр индивидуального расписания',
    'schedule_create' => 'Создание занятия, постановка в график',
    'schedule_edit' => 'Изменение занятия, отмена урока',
    'schedule_delete' => 'Удаление занятия',
    'schedule_absent_read' => 'Просмотр периодов отсутствия',
    'schedule_absent_create' => 'Созздание периода отсутствия',
    'schedule_absent_edit' => 'Редактирование периода отсутствия',
    'schedule_absent_delete' => 'Удаление периода отсутствия',
    'schedule_report_read' => 'Просмотр отчетов о проведенных занятий',
    'schedule_report_create' => 'Создание отчета о проведенном занятии',
    'schedule_report_edit' => 'Редактирование отчета о проведенном занятии',
    'schedule_report_delete' => 'Удаление отчета о проведенном занятии',
    'schedule_lesson_time' => 'Подбор времени занятия для самостоятельной постановки в график',

    'schedule_group_read' => 'Просмотр групп',
    'schedule_group_create' => 'Создание группы',
    'schedule_group_edit' => 'Редактирование группы',
    'schedule_group_delete' => 'Удаление группы',

    'area_read' => 'Просмотр филиалов',
    'area_create' => 'Создание филиала',
    'area_edit' => 'Изменение филиала',
    'area_delete' => 'Удаление филиала',
    'area_multi_access' => 'Доступ ко всем фмлиалам',

    'payment_read_all' => 'Просмотр хозрасходов',
    'payment_read_client' => 'Просмотр платежей клиентов',
    'payment_read_teacher' => 'Просмотр платежей преподавателей',
    'payment_create_all' => 'Создание хозрасходов',
    'payment_create_client' => 'Создание платежей клиентов',
    'payment_create_teacher' => 'Создание выплат преподавателям',
    'payment_edit_all' => 'Редактирование хозрасходов',
    'payment_edit_client' => 'Редактирование платежей клиентов',
    'payment_edit_teacher' => 'Редактирование выплат преподавателям',
    'payment_delete_all' => 'Удаление хозрасходов',
    'payment_delete_client' => 'Удаление платежей клиентов',
    'payment_delete_teacher' => 'Удаление выплат преподавателям',
    'payment_config' => 'Финансовые настройки',

    'payment_tarif_read' => 'Просмотр тарифов',
    'payment_tarif_create' => 'Создание тарифа',
    'payment_tarif_edit' => 'Редактирование тарифа',
    'payment_tarif_delete' => 'Удаление тарифа',
    'payment_tarif_buy' => 'Покупка тарифа',

    'certificate_read' => 'Просмотр сертификатов',
    'certificate_create' => 'Создание сертификата',
    'certificate_edit' => 'Изменение сертификата',
    'certificate_delete' => 'Удаление сертификата',
    'certificate_append_comment' => 'Добавление комментария к сертификату',

    'teacher_clients_read' => 'Просмотр списка клиентов преподавателя',
    'teacher_clients_edit' => 'Редактирование списка клиентов преподавателя',

    'teacher_schedule_time_read' => 'Просмотр рабочего графика преподавателя',
    'teacher_schedule_time_create' => 'Создание рабочего графика преподавателя',
    'teacher_schedule_time_edit' => 'Редактирование рабочего графика преподавателя',
    'teacher_schedule_time_delete' => 'Удаление рабочего графика преподавателя',

    'statistic_read' => 'Просмотр статистики',

    'cron' => 'Доступ к скриптам крона',

    'integration_vk' => 'Интеграция с ВК',
    'integration_senler' => 'Интеграция с сервисом рассылок Senler',
    'integration_my_calls' => 'Интеграция с сервисом "Мои звонки"',
];