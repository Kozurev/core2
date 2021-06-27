<?php
/**
 * Страница новостей
 *
 * @author BadWolf
 * @date 18.03.2018 21:21
 * @version 20190221
 * @version 20190412
 * @version 20190427
 * @version 20190526
 * @version 20200401
 * @version 20210615
 */

global $CFG;
$user = User_Auth::current();

//Список директоров
if ($user->groupId() == ROLE_ADMIN) {
    $DirectorController = new User_Controller(User_Auth::current());
    $DirectorController
        ->properties([29, 30, 33])
        ->groupId(6)
        ->isSubordinate(false)
        ->addSimpleEntity('page-theme-color', 'green')
        ->xsl('musadm/users/directors.xsl');

    $DirectorController->queryBuilder()
        ->orderBy('User.id', 'ASC');

    echo "<div class='users'>";
    $DirectorController->show();
    echo "</div>";
} else {
    echo '<script src="https://cdn.tiny.cloud/1/t7307b0z05f5zhsbr4mpdlbrxx9gz5kghs76v6w76led5ld2/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>';
    Core_Page_Show::instance()
        ->js('/templates/template4/js/posts.js');

    $areas = (new Schedule_Area_Assignment($user))->getAreas();

    $postsController = new Post_Controller();
    $postsController->addSimpleEntity('access_post_create', $user->isManagementStaff());
    $postsController->addEntities($areas, 'area');
    $postsController->setAreas($areas);
    $postsController->paginate()->setCurrentPage(request()->get('page', 1));
    $postsController->paginate()->setOnPage(10);
    $postsController->setXsl('musadm/posts/index.xsl');
    $postsController->show();
}