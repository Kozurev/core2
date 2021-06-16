<?php

$action = request()->get('action');

/**
 *
 */
if ($action === 'save') {
    $title = request()->get('title');
    $content = request()->get('content');
    $areasIds = request()->get('areas', []);

    if (empty($title)) {
        Core_Page_Show::instance()->error(500, 'Поле "Заголовок" обязательно для заполнения', true);
    }
    if (empty($content)) {
        Core_Page_Show::instance()->error(500, 'Поле "Контент" обязательно для заполнения', true);
    }

    $post = new Post();
    $post->title = $title;
    $post->content = $content;

    try {
        $post->save();
        foreach ($areasIds as $areasId) {
            (new Schedule_Area_Assignment)->createAssignment($post, $areasId);
        }

        exit(json_encode([
            'status' => true,
            'message' => 'Пост успешно сохранен'
        ]));
    } catch (\Throwable $throwable) {
        Core_Page_Show::instance()->error(500, $throwable->getMessage(), true);
    }

}