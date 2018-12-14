<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 19.04.2018
 * Time: 23:18
 */


$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = $this->oStructure->getParent()->title();
$breadcumbs[0]->active = 1;
$breadcumbs[1] = new stdClass();
$breadcumbs[1]->title = $this->oStructure->title();
$breadcumbs[1]->active = 1;

$this->setParam( "body-class", "body-orange" );
$this->setParam( "title-first", "ЛИЧНЫЙ" );
$this->setParam( "title-second", "КАБИНЕТ" );
$this->setParam( "breadcumbs", $breadcumbs );


if(isset($_GET["ajax"]) && $_GET["ajax"] == 1)
{
    $this->execute();
    exit;
}

/**
 * Блок проверки авторизации и прав доступа
 */
$oUser = Core::factory("User")->getCurrent();

if(!$oUser)
{
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = "";
    header("Location: http://$host$uri/authorize");
    exit;
}

$action = Core_Array::getValue($_GET, "action", "");


/**
 * Обновление сожержимого страницы
 */
if($action == "refreshTablePayments")
{
//    $oCurentUser = Core::factory("User")->getCurrent();
//    $pageUserId = Core_Array::getValue($_GET, "userid", 0);
//
//    /**
//     * Пользовательские примечания и дата последней авторизации
//     */
//    if( User::isAuthAs() )
//    {
//        $oPropertyNotes = Core::factory("Property", 19);
//        $clienNotes = $oPropertyNotes->getPropertyValues($oUser);
//
//        $oPropertyLastEntry = Core::factory("Property", 22);
//        $lastEntry = $oPropertyLastEntry->getPropertyValues($oUser);
//
//        Core::factory("Core_Entity")
//            ->addEntities($clienNotes, "note")
//            ->addEntities($lastEntry, "entry")
//            ->xsl("musadm/client_notes.xsl")
//            ->show();
//    }

    //echo "<div class='users'>";
        $this->execute();
    //echo "</div>";

    exit;
}


/**
 * Открытие всплывающего окна для начисления оплаты (создания платежа клиента с 2 полями для примечания)
 */
if($action == "getPaymentPopup")
{
    $userId =   Core_Array::getValue($_GET, "userid", 0);
    $oUser =    Core::factory("User", $userId);

    Core::factory("Core_Entity")
        ->addEntity($oUser)
        ->addSimpleEntity( "function", "balance" )
        ->xsl("musadm/users/balance/edit_payment_popup.xsl")
        ->show();

    exit;
}


/**
 * Открытие всплывающего окна для покупки тарифа
 */
if( $action == "getTarifPopup" )
{
    $userId =       Core_Array::getValue( $_GET, "userid", 0 );
    $Director = Core::factory( "User" )->getCurrent()->getDirector();

    $aoTarifs = Core::factory( "Payment_Tarif" )
        ->where( "subordinated", "=", $Director->getId() );

    if( !User::isAuthAs() ) $aoTarifs->where( "access", "=", "1" );

    $aoTarifs =     $aoTarifs->findAll();
    $oUser =        Core::factory( "User", $userId );

    Core::factory( "Core_Entity" )
        ->addEntity( $oUser )
        ->addEntities( $aoTarifs )
        ->xsl( "musadm/users/balance/buy_tarif_popup.xsl" )
        ->show();

    exit;
}


/**
 * Редактирование
 */
if($action == "updateNote")
{
    $userId =   Core_Array::getValue($_GET, "userid", 0);
    $note =     Core_Array::getValue($_GET, "note", "");
    $oUser =    Core::factory("User", $userId);
    $oUserNote = Core::factory("Property", 19);
    $oUserNote = $oUserNote->getPropertyValues($oUser)[0];
    $oUserNote->value($note)->save();
    exit;
}


if( $action === "updatePerLesson" )
{
    $userId =   Core_Array::getValue( $_GET, "userid", 0 );
    $value =    Core_Array::getValue( $_GET, "value", 0 );
    $oUser =    Core::factory( "User", $userId );
    $oPerLesson = Core::factory( "Property", 32 );
    $oPerLesson = $oPerLesson->getPropertyValues( $oUser )[0];
    $oPerLesson->value( $value )->save();
    exit;
}


/**
 * Покупка тарифа
 */
if( $action == "buyTarif" )
{
    $userId =   Core_Array::getValue( $_GET, "userid", 0 );
    $tarifId =  Core_Array::getValue( $_GET, "tarifid", 0 );

    $oUser =    Core::factory( "User", $userId );
    $oTarif =   Core::factory( "Payment_Tarif", $tarifId );

    $oUserBalance =     Core::factory( "Property", 12 );
    $oUserBalance =     $oUserBalance->getPropertyValues( $oUser )[0];

    $oCountIndivLessons = Core::factory( "Property", 13 );
    $oCountGroupLessons = Core::factory( "Property", 14 );

    $oCountIndivLessons = $oCountIndivLessons->getPropertyValues( $oUser )[0];
    $oCountGroupLessons = $oCountGroupLessons->getPropertyValues( $oUser )[0];

    if( $oUserBalance->value() < $oTarif->price() )   die( "Недостаточно средств для покупки данного тарифа" );

    //Корректировка баланса
    $oldBalance = intval( $oUserBalance->value() );
    $newBalance = $oldBalance - intval( $oTarif->price() );
    $oUserBalance->value( $newBalance )->save();

    //Корректировка кол-ва занятий
    if( $oTarif->countIndiv() != 0 ) $oCountIndivLessons->value( $oCountIndivLessons->value() + $oTarif->countIndiv() )->save();
    if( $oTarif->countGroup() != 0 ) $oCountGroupLessons->value( $oCountGroupLessons->value() + $oTarif->countGroup() )->save();

    //Создание платежа
    $oPayment = Core::factory( "Payment" )
        ->type( 2 )
        ->user( $userId )
        ->value( $oTarif->price() )
        ->description( "Покупка тарифа \"" . $oTarif->title() . "\"" )
        ->save();

    exit;
}


if( $action == "savePayment" )
{
    $userid =       Core_Array::getValue( $_GET, "userid", 0 );
    $value  =       Core_Array::getValue( $_GET, "value", 0 );
    $description =  Core_Array::getValue( $_GET, "description", "" );
    $type =         Core_Array::getValue( $_GET, "type", 0 );
    $description2 = Core_Array::getValue( $_GET, "property_26", "" );

    $payment = Core::factory("Payment")
        ->user($userid)
        ->type($type)
        ->value($value)
        ->description($description);
    $payment->save();

    Core::factory( "Property", 26 )->addNewValue( $payment, $description2 );

    /**
     * Корректировка баланса ученика
     */
    $oUser =        Core::factory("User", $userid);
    $oUserBalance = Core::factory("Property", 12);
    $oUserBalance = $oUserBalance->getPropertyValues($oUser)[0];
    $balanceOld =   intval($oUserBalance->value());

    $type == 1
        ?   $balanceNew =   $balanceOld + intval($value)
        :   $balanceNew =   $balanceOld - intval($value);
    $oUserBalance->value($balanceNew);
    $oUserBalance->save();

    echo 0;
    exit;
}


/**
 * Создание / редактирование платежа
 */
if( $action === "edit_payment" )
{
    $id = Core_Array::getValue( $_GET, "id", null );
    $Payment = Core::factory( "Payment", $id );

    /**
     * Указатель на тип обновляемого контента страницы после сохранения данных платежа
     *
     * На данный момент 16.10.2018 платеж редактируется из двух разделов
     *  значение 'client' - редактирование платежа из личного кабинета клиента
     *  значение 'teacher' - редактирование платежа из личного кабинета преподавателя
     */
    $afterSaveAction = Core_Array::getValue( $_GET, "afterSaveAction", null );

    Core::factory( "Core_Entity" )
        ->addEntity( $Payment )
        ->addSimpleEntity( "afterSaveAction", $afterSaveAction )
        ->xsl( "musadm/finances/edit_payment_popup.xsl" )
        ->show();

    exit;
}


/**
 * Добавление комментария к платежу
 */
if($action == "add_note")
{
    $modelId = Core_Array::getValue($_GET,"model_id", 0);
    $oPayment = Core::factory("Payment", $modelId);
    $aoNotes = Core::factory("Property", 26)->getPropertyValues($oPayment);

    Core::factory("Core_Entity")
        ->addEntity($oPayment)
        ->addEntities($aoNotes, "notes")
        ->xsl("musadm/users/balance/add_payment_note.xsl")
        ->show();

    exit;
}


/**
 * Сохранение данных платежа
 */
if( $action === "payment_save" )
{
    $id = Core_Array::getValue( $_GET, "id", 0 );
    $value = Core_Array::getValue( $_GET, "value", 0 );
    $date = Core_Array::getValue( $_GET, "date", date( "Y-m-d" ) );
    $description = Core_Array::getValue( $_GET, "description", "" );

    $Payment = Core::factory( "Payment", $id );

    $difference = intval( $Payment->value() ) - intval( $value );

    if( $difference !== 0 )
    {
        $oUser =        Core::factory( "User", $Payment->user() );
        $oUserBalance = Core::factory( "Property", 12 );
        $oUserBalance = $oUserBalance->getPropertyValues( $oUser )[0];
        $balanceOld = $oUserBalance->value();

        $Payment->type() == 1
            ?   $balanceNew = $balanceOld - $difference
            :   $balanceNew = $balanceOld + $difference;

        $oUserBalance
            ->value( $balanceNew )
            ->save();
    }

    $Payment
        ->value( $value )
        ->datetime( $date )
        ->description( $description )
        ->save();

    $this->execute();
    exit;
}


if( $action === "payment_delete" )
{
    $id = Core_Array::getValue( $_GET, "id", 0 );
    $Payment = Core::factory( "Payment", $id );

    $oUser =        Core::factory( "User", $Payment->user() );
    $oUserBalance = Core::factory( "Property", 12 );
    $oUserBalance = $oUserBalance->getPropertyValues( $oUser )[0];
    $balanceOld = $oUserBalance->value();

    $Payment->type() == 1
        ?   $newBalance = $balanceOld - $Payment->value()
        :   $newBalance = $balanceOld + $Payment->value();

    $oUserBalance
        ->value( $newBalance )
        ->save();

    $Payment->delete();

    $this->execute();
    exit;
}


if( $action == "refreshTasksTable" )
{
    $Tasks = Core::factory( "Task" )
        ->where( "associate", "=", $oUser->getId() )
        ->orderBy( "date", "DESC" )
        ->orderBy( "id", "DESC" )
        ->findAll();

    foreach ( $Tasks as $Task )
    {
        $Task->date( refactorDateFormat( $Task->date() ) );
    }

    $tasksIds = array();

    foreach ( $Tasks as $Task )
    {
        $tasksIds[] = $Task->getId();
    }

    //Поиск всех комментариев, связанных с выбранными задачами
    $Notes = Core::factory( "Task_Note" )
        ->select([
            "Task_Note.id AS id", "date", "task_id", "author_id", "text", "usr.name AS name", "usr.surname AS surname"
        ])
        ->where( "task_id", "IN", $tasksIds )
        ->leftJoin( "User AS usr", "author_id = usr.id" )
        ->orderBy( "date", "DESC" )
        ->findAll();

    //Изменение формата даты и времени комментариев
    foreach ( $Notes as $Note )
    {
        $time = strtotime( $Note->date() );

        if( date( "H:i", $time ) == "00:00" )
        {
            $dateFormat = "d.m.Y";
        }
        else
        {
            $dateFormat = "d.m.Y H:i";
        }

        $Note->date( date( $dateFormat, $time ) );
    }

    global $CFG;
    Core::factory( "Core_Entity" )
        ->addSimpleEntity( "wwwroot", $CFG->rootdir )
        ->addEntities( $Tasks )
        ->addEntities( $Notes )
        ->xsl( "musadm/tasks/client_tasks.xsl" )
        ->show();

    exit;
}


if( $action === "saveUserComment" )
{
    $userId = Core_Array::Get( "userid", 0 );
    $text = Core_Array::Get( "text", "" );

    Core::factory( "User" )->addComment( $text, $userId );

    echo "<div class='users'>";
    $this->execute();
    echo "</div>";

    exit;
}


if( $action === "refreshTableUsers" )
{
    echo "<div class='users'>";
    $this->execute();
    echo "</div>";

    exit;
}