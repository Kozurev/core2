<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 19.04.2018
 * Time: 23:18
 */


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->getParent()->title();
$breadcumbs[0]->active = 1;
$breadcumbs[1] = new stdClass();
$breadcumbs[1]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[1]->active = 1;

Core_Page_Show::instance()->setParam( 'body-class', 'body-orange' );
Core_Page_Show::instance()->setParam( 'title-first', 'ЛИЧНЫЙ' );
Core_Page_Show::instance()->setParam( 'title-second', 'КАБИНЕТ' );
Core_Page_Show::instance()->setParam( 'breadcumbs', $breadcumbs );


if ( isset( $_GET['ajax'] ) && $_GET['ajax'] == 1 )
{
    Core_Page_Show::instance()->execute();
    exit;
}

/**
 * Блок проверки авторизации и прав доступа
 */
$User = User::current();

if ( !$User )
{
    $host  = Core_Array::Server( 'HTTP_HOST', '', PARAM_STRING );
    $uri   = rtrim( dirname( Core_Array::Server( 'PHP_SELF', '', PARAM_STRING ) ), '/\\' );
    $extra = '';
    header( "Location: http://$host$uri/authorize" );
    exit;
}

$action = Core_Array::Get( 'action', '' );

Core::factory( 'User_Controller' );


/**
 * Обновление сожержимого страницы
 */
if ( $action == 'refreshTablePayments' )
{
    Core_Page_Show::instance()->execute();
    exit;
}


/**
 * Открытие всплывающего окна для начисления оплаты (создания платежа клиента с 2 полями для примечания)
 */
if ( $action == 'getPaymentPopup' )
{
    if ( !User::checkUserAccess( ['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]] ) )
    {
        Core_Page_Show::instance()->error( 403 );
    }

    $userId = Core_Array::Get('userid', null, PARAM_INT );

    $User = User_Controller::factory( $userId );

    Core::factory( 'Core_Entity' )
        ->addEntity( $User )
        ->addSimpleEntity( 'function', 'balance' )
        ->xsl( 'musadm/users/balance/edit_payment_popup.xsl' )
        ->show();

    exit;
}


/**
 * Открытие всплывающего окна для покупки тарифа
 */
if ( $action == 'getTarifPopup' )
{
    $userId =   Core_Array::Get( 'userid', null, PARAM_INT );
    $Director = $User->getDirector();

    $Tarifs = Core::factory( 'Payment_Tarif' )
        ->queryBuilder()
        ->where( 'subordinated', '=', $Director->getId() );

    if ( User::current()->groupId() == ROLE_CLIENT )
    {
        $Tarifs->where( 'access', '=', '1' );
    }

    $Tarifs = $Tarifs->findAll();
    $User = User_Controller::factory( $userId );

    Core::factory( 'Core_Entity' )
        ->addEntity( $User )
        ->addEntities( $Tarifs )
        ->xsl( 'musadm/users/balance/buy_tarif_popup.xsl' )
        ->show();

    exit;
}


/**
 * Редактирование
 */
if ( $action == 'updateNote' )
{
    if ( !User::checkUserAccess( ['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]] ) )
    {
        Core_Page_Show::instance()->error( 403 );
    }

    $userId =   Core_Array::Get( 'userid', null, PARAM_INT );
    $note =     Core_Array::Get( 'note', '', PARAM_STRING );

    $User = User_Controller::factory( $userId );

    Core::factory( 'Property', 19 )
        ->getPropertyValues( $User )[0]
        ->value( $note )
        ->save();

    exit;
}


if ( $action === 'updatePerLesson' )
{
    if ( !User::checkUserAccess( ['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]] ) )
    {
        Core_Page_Show::instance()->error( 403 );
    }

    $userId =   Core_Array::Get( 'userid', null, PARAM_INT );
    $value =    Core_Array::Get( 'value', 0, PARAM_INT );

    $User = User_Controller::factory( $userId );

    Core::factory( 'Property', 32 )
        ->getPropertyValues( $User )[0]
        ->value( $value )
        ->save();

    exit;
}


/**
 * Покупка тарифа
 */
if ( $action == 'buyTarif' )
{
    $userId =   Core_Array::Get( 'userid', null, PARAM_INT );
    $tarifId =  Core_Array::Get( 'tarifid', 0, PARAM_INT );

    $User = User_Controller::factory( $userId );
    $Tarif = Core::factory( 'Payment_Tarif', $tarifId );

    $UserBalance = Core::factory( 'Property', 12 );
    $UserBalance = $UserBalance->getPropertyValues( $User )[0];

    $CountIndivLessons = Core::factory( 'Property', 13 );
    $CountGroupLessons = Core::factory( 'Property', 14 );

    $CountIndivLessons = $CountIndivLessons->getPropertyValues( $User )[0];
    $CountGroupLessons = $CountGroupLessons->getPropertyValues( $User )[0];

    if ( $UserBalance->value() < $Tarif->price() )
    {
        die( "Недостаточно средств для покупки данного тарифа" );
    }


    //Корректировка баланса
    $oldBalance = intval( $UserBalance->value() );
    $newBalance = $oldBalance - intval( $Tarif->price() );
    $UserBalance->value( $newBalance )->save();


    //Корректировка кол-ва занятий
    if ( $Tarif->countIndiv() != 0 )
    {
        $CountIndivLessons->value( $CountIndivLessons->value() + $Tarif->countIndiv() )->save();
    }

    if ( $Tarif->countGroup() != 0 )
    {
        $CountGroupLessons->value( $CountGroupLessons->value() + $Tarif->countGroup() )->save();
    }


    //Корректировка пользовательской медианы (средняя стоимость занятия)
    $countLessons = 0;
    if ( $Tarif->countIndiv() != 0 )
    {
        $clientRate = 'client_rate_indiv';
        $countLessons = $Tarif->countIndiv();
    }
    if ( $Tarif->countGroup() != 0 )
    {
        $clientRate = 'client_rate_group';
        $countLessons = $Tarif->countGroup();
    }
    if ( $Tarif->countIndiv() != 0 && $Tarif->countGroup() != 0 )
    {
        $clientRate = '';
    }

    if ( $clientRate != '' && $countLessons !== 0 )
    {
        $ClientRateProperty = Core::factory( 'Property' )->getByTagName( $clientRate );
        $newClientRateValue = $Tarif->price() / $countLessons;
        $newClientRateValue = round( $newClientRateValue, 2 );
        $OldClientRateValue = $ClientRateProperty->getPropertyValues( $User )[0];
        $OldClientRateValue->value( $newClientRateValue )->save();
    }


    //Создание платежа
    $oPayment = Core::factory( 'Payment' )
        ->type( 2 )
        ->user( $userId )
        ->value( $Tarif->price() )
        ->description( "Покупка тарифа \"" . $Tarif->title() . "\"" )
        ->save();

    exit;
}


if ( $action == 'savePayment' )
{
    if ( !User::checkUserAccess( ['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]] ) )
    {
        Core_Page_Show::instance()->error( 403 );
    }

    $userId =       Core_Array::Get( 'userid', null, PARAM_INT );
    $value  =       Core_Array::Get( 'value', 0, PARAM_INT );
    $description =  Core_Array::Get( 'description', '', PARAM_STRING );
    $type =         Core_Array::Get( 'type', 0, PARAM_INT );
    $description2 = Core_Array::Get( 'property_26', '' );

    $Payment = Core::factory( 'Payment' )
        ->user( $userId )
        ->type( $type )
        ->value( $value )
        ->description( $description );
    $Payment->save();

    Core::factory( 'Property', 26 )->addNewValue( $Payment, $description2 );


    /**
     * Корректировка баланса ученика
     */
    $User =        Core::factory( 'User', $userId );
    $UserBalance = Core::factory( 'Property', 12 );
    $UserBalance = $UserBalance->getPropertyValues( $User )[0];
    $balanceOld =  intval( $UserBalance->value() );

    $type == 1
        ?   $balanceNew = $balanceOld + intval( $value )
        :   $balanceNew = $balanceOld - intval( $value );

    $UserBalance
        ->value( $balanceNew )
        ->save();

    exit ( '0' );
}


/**
 * Добавление комментария к платежу
 */
if ( $action == 'add_note' )
{
    if ( !User::checkUserAccess( ['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]] ) )
    {
        Core_Page_Show::instance()->error( 403 );
    }

    $modelId =  Core_Array::Get( 'model_id', 0, PARAM_INT );
    $Payment =  Core::factory( 'Payment', $modelId );
    $Notes =    Core::factory( 'Property', 26 )->getPropertyValues( $Payment );

    Core::factory( 'Core_Entity' )
        ->addEntity( $Payment )
        ->addEntities( $Notes, 'notes' )
        ->xsl( 'musadm/users/balance/add_payment_note.xsl' )
        ->show();

    exit;
}


/**
 * Сохранение данных платежа
 */
if ( $action === 'payment_save' )
{
    if ( !User::checkUserAccess( ['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]] ) )
    {
        Core_Page_Show::instance()->error( 403 );
    }

    $id =     Core_Array::Get( 'id', 0, PARAM_INT );
    $value =  Core_Array::Get( 'value', 0, PARAM_INT );
    $date =   Core_Array::Get( 'date', date( 'Y-m-d' ), PARAM_STRING );
    $description = Core_Array::Get( 'description', '', PARAM_STRING );

    $Payment = Core::factory( 'Payment', $id );

    $difference = intval( $Payment->value() ) - intval( $value );

    if ( $difference !== 0 )
    {
        $User =        User_Controller::factory( $Payment->user() );
        $UserBalance = Core::factory( 'Property', 12 );
        $UserBalance = $UserBalance->getPropertyValues( $User )[0];
        $balanceOld =  $UserBalance->value();

        $Payment->type() == 1
            ?   $balanceNew = $balanceOld - $difference
            :   $balanceNew = $balanceOld + $difference;

        $UserBalance
            ->value( $balanceNew )
            ->save();
    }

    $Payment
        ->value( $value )
        ->datetime( $date )
        ->description( $description )
        ->save();

    Core_Page_Show::instance()->execute();
    exit;
}


if ( $action === 'payment_delete' )
{
    if ( !User::checkUserAccess( ['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]] ) )
    {
        Core_Page_Show::instance()->error( 403 );
    }

    $id = Core_Array::Get( 'id', 0, PARAM_INT );
    $Payment = Core::factory( 'Payment', $id );

    $User =         User_Controller::factory( $Payment->user() );
    $UserBalance =  Core::factory( 'Property', 12 );
    $UserBalance =  $UserBalance->getPropertyValues( $User )[0];
    $balanceOld =   $UserBalance->value();

    $Payment->type() == 1
        ?   $newBalance = $balanceOld - $Payment->value()
        :   $newBalance = $balanceOld + $Payment->value();

    $UserBalance
        ->value( $newBalance )
        ->save();

    $Payment->delete();

    exit;
}


if ( $action == 'refreshTasksTable' )
{
    if ( !User::checkUserAccess( ['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]] ) )
    {
        Core_Page_Show::instance()->error( 403 );
    }

    $Tasks = Core::factory( 'Task' )
        ->queryBuilder()
        ->where( 'associate', '=', $oUser->getId() )
        ->orderBy( 'date', 'DESC' )
        ->orderBy( 'id', 'DESC' )
        ->findAll();

    foreach ( $Tasks as $Task )
    {
        $Task->date( refactorDateFormat( $Task->date() ) );
    }

    $tasksIds = [];

    foreach ( $Tasks as $Task )
    {
        $tasksIds[] = $Task->getId();
    }

    //Поиск всех комментариев, связанных с выбранными задачами
    $Notes = Core::factory( 'Task_Note' )
        ->queryBuilder()
        ->select([
            'Task_Note.id AS id', 'date', 'task_id', 'author_id', 'text', 'usr.name AS name', 'usr.surname AS surname'
        ])
        ->whereIn( 'task_id', $tasksIds )
        ->leftJoin( 'User AS usr', 'author_id = usr.id' )
        ->orderBy( 'date', 'DESC' )
        ->findAll();

    //Изменение формата даты и времени комментариев
    foreach ( $Notes as $Note )
    {
        $time = strtotime( $Note->date() );

        if( date( 'H:i', $time ) == '00:00' )
        {
            $dateFormat = 'd.m.Y';
        }
        else
        {
            $dateFormat = 'd.m.Y H:i';
        }

        $Note->date( date( $dateFormat, $time ) );
    }

    global $CFG;

    Core::factory( 'Core_Entity' )
        ->addSimpleEntity( 'wwwroot', $CFG->rootdir )
        ->addEntities( $Tasks )
        ->addEntities( $Notes )
        ->xsl( 'musadm/tasks/client_tasks.xsl' )
        ->show();

    exit;
}


if ( $action === 'saveUserComment' )
{
    if ( !User::checkUserAccess( ['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]] ) )
    {
        Core_Page_Show::instance()->error( 403 );
    }

    $userId =   Core_Array::Get( 'userid', 0, PARAM_INT );
    $text =     Core_Array::Get( 'text', '', PARAM_STRING );

    Core::factory( 'User' )->addComment( $text, $userId );

    echo "<div class='users'>";
    Core_Page_Show::instance()->execute();
    echo "</div>";

    exit;
}


/**
 * Открытие всплывающего окна для редактирования данных отчета о проведенном занятии
 */
if ( $action === 'edit_report_popup' )
{
    if ( !User::checkUserAccess( ['groups' => [ROLE_DIRECTOR]] ) )
    {
        Core_Page_Show::instance()->error( 403 );
    }

    $id = Core_Array::Get( 'id', 0, PARAM_INT );
    $Report = Core::factory( 'Schedule_Lesson_Report', $id );

    if ( $Report == false || $Report->getId() == null )
    {
        die ( "Отчета с id $id не существует" );
    }

    Core::factory( 'Core_Entity' )
        ->addEntity( $Report, 'rep' )
        ->xsl( 'musadm/users/balance/edit_report_popup.xsl' )
        ->show();

    exit;
}


if ( $action === 'refreshTableUsers' )
{
    echo "<div class='users'>";
    Core_Page_Show::instance()->execute();
    echo "</div>";

    exit;
}