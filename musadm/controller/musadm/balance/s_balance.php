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

Core_Page_Show::instance()->setParam( "body-class", "body-orange" );
Core_Page_Show::instance()->setParam( "title-first", "ЛИЧНЫЙ" );
Core_Page_Show::instance()->setParam( "title-second", "КАБИНЕТ" );
Core_Page_Show::instance()->setParam( "breadcumbs", $breadcumbs );


if(isset($_GET["ajax"]) && $_GET["ajax"] == 1)
{
    Core_Page_Show::instance()->execute();
    exit;
}

/**
 * Блок проверки авторизации и прав доступа
 */
$User = User::current();

if( !$User )
{
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = "";
    header("Location: http://$host$uri/authorize");
    exit;
}

$action = Core_Array::Get( "action", "" );


/**
 * Обновление сожержимого страницы
 */
if( $action == "refreshTablePayments" )
{
    Core_Page_Show::instance()->execute();
    exit;
}


/**
 * Открытие всплывающего окна для начисления оплаты (создания платежа клиента с 2 полями для примечания)
 */
if( $action == "getPaymentPopup" )
{
    $userId =   Core_Array::Get("userid", 0 );
    $User =    Core::factory( "User", $userId );

    Core::factory( "Core_Entity" )
        ->addEntity( $User )
        ->addSimpleEntity( "function", "balance" )
        ->xsl( "musadm/users/balance/edit_payment_popup.xsl" )
        ->show();

    exit;
}


/**
 * Открытие всплывающего окна для покупки тарифа
 */
if( $action == "getTarifPopup" )
{
    $userId =   Core_Array::Get( "userid", 0 );
    $Director = User::current()->getDirector();

    $Tarifs = Core::factory( "Payment_Tarif" )
        ->queryBuilder()
        ->where( "subordinated", "=", $Director->getId() );

    if( !User::isAuthAs() ) $Tarifs->where( "access", "=", "1" );

    $Tarifs = $Tarifs->findAll();
    $User = Core::factory( "User", $userId );

    Core::factory( "Core_Entity" )
        ->addEntity( $User )
        ->addEntities( $Tarifs )
        ->xsl( "musadm/users/balance/buy_tarif_popup.xsl" )
        ->show();

    exit;
}


/**
 * Редактирование
 */
if( $action == "updateNote" )
{
    $userId =   Core_Array::Get( "userid", 0 );
    $note =     Core_Array::Get( "note", "" );
    $User =     Core::factory( "User", $userId );

    Core::factory( "Property", 19 )
        ->getPropertyValues( $User )[0]
        ->value( $note )
        ->save();

    exit;
}


if( $action === "updatePerLesson" )
{
    $userId =   Core_Array::Get( "userid", 0 );
    $value =    Core_Array::Get( "value", 0 );
    $User =     Core::factory( "User", $userId );

    Core::factory( "Property", 32 )
        ->getPropertyValues( $User )[0]
        ->value( $value )
        ->save();

    exit;
}


/**
 * Покупка тарифа
 */
if( $action == "buyTarif" )
{
    $userId =   Core_Array::Get( "userid", 0 );
    $tarifId =  Core_Array::Get( "tarifid", 0 );

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


    //Корректировка пользовательской медианы (средняя стоимость занятия)
    $countLessons = 0;
    if( $oTarif->countIndiv() != 0 )
    {
        $clientRate = "client_rate_indiv";
        $countLessons = $oTarif->countIndiv();
    }
    if( $oTarif->countGroup() != 0 )
    {
        $clientRate = "client_rate_group";
        $countLessons = $oTarif->countGroup();
    }
    if( $oTarif->countIndiv() != 0 && $oTarif->countGroup() != 0 )
    {
        $clientRate = "";
    }

    if( $clientRate != "" && $countLessons !== 0 )
    {
        $ClientRateProperty = Core::factory( "Property" )->getByTagName( $clientRate );
        $newClientRateValue = $oTarif->price() / $countLessons;
        $newClientRateValue = round( $newClientRateValue, 2 );
        $OldClientRateValue = $ClientRateProperty->getPropertyValues( $oUser )[0];
        $OldClientRateValue->value( $newClientRateValue )->save();
    }


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
    $userId =       Core_Array::Get( "userid", 0 );
    $value  =       Core_Array::Get( "value", 0 );
    $description =  Core_Array::Get( "description", "" );
    $type =         Core_Array::Get( "type", 0 );
    $description2 = Core_Array::Get( "property_26", "" );

    $Payment = Core::factory( "Payment" )
        ->user( $userId )
        ->type( $type )
        ->value( $value )
        ->description( $description );
    $Payment->save();

    Core::factory( "Property", 26 )->addNewValue( $Payment, $description2 );

    /**
     * Корректировка баланса ученика
     */
    $User =        Core::factory( "User", $userId );
    $UserBalance = Core::factory( "Property", 12 );
    $UserBalance = $UserBalance->getPropertyValues( $User )[0];
    $balanceOld =  intval( $UserBalance->value() );

    $type == 1
        ?   $balanceNew = $balanceOld + intval( $value )
        :   $balanceNew = $balanceOld - intval( $value );

    $UserBalance
        ->value( $balanceNew )
        ->save();

    exit ( "0" );
}


/**
 * Добавление комментария к платежу
 */
if( $action == "add_note" )
{
    $modelId =  Core_Array::Get( "model_id", 0 );
    $Payment =  Core::factory( "Payment", $modelId );
    $Notes =    Core::factory( "Property", 26 )->getPropertyValues( $Payment );

    Core::factory( "Core_Entity" )
        ->addEntity( $Payment )
        ->addEntities( $Notes, "notes" )
        ->xsl( "musadm/users/balance/add_payment_note.xsl" )
        ->show();

    exit;
}


/**
 * Сохранение данных платежа
 */
if( $action === "payment_save" )
{
    $id =     Core_Array::Get( "id", 0 );
    $value =  Core_Array::Get( "value", 0 );
    $date =   Core_Array::Get( "date", date( "Y-m-d" ) );
    $description = Core_Array::Get( "description", "" );

    $Payment = Core::factory( "Payment", $id );

    $difference = intval( $Payment->value() ) - intval( $value );

    if( $difference !== 0 )
    {
        $User =        Core::factory( "User", $Payment->user() );
        $UserBalance = Core::factory( "Property", 12 );
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


if( $action === "payment_delete" )
{
    $id = Core_Array::Get( "id", 0 );
    $Payment = Core::factory( "Payment", $id );

    $User =         Core::factory( "User", $Payment->user() );
    $UserBalance =  Core::factory( "Property", 12 );
    $UserBalance =  $UserBalance->getPropertyValues( $User )[0];
    $balanceOld =   $UserBalance->value();

    $Payment->type() == 1
        ?   $newBalance = $balanceOld - $Payment->value()
        :   $newBalance = $balanceOld + $Payment->value();

    $UserBalance
        ->value( $newBalance )
        ->save();

    $Payment->delete();

    //$this->execute();
    exit;
}


if( $action == "refreshTasksTable" )
{
    $Tasks = Core::factory( "Task" )
        ->queryBuilder()
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
        ->queryBuilder()
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
    $userId =   Core_Array::Get( "userid", 0 );
    $text =     Core_Array::Get( "text", "" );

    Core::factory( "User" )->addComment( $text, $userId );

    echo "<div class='users'>";
    Core_Page_Show::instance()->execute();
    echo "</div>";

    exit;
}


/**
 * Открытие всплывающего окна для редактирования данных отчета о проведенном занятии
 */
if( $action === "edit_report_popup" )
{
    $id = Core_Array::Get( "id", 0 );
    $Report = Core::factory( "Schedule_Lesson_Report", $id );

    if( $Report == false || $Report->getId() == null )
    {
        die("Отчета с id $id не существует");
    }

    Core::factory( "Core_Entity" )
        ->addEntity( $Report, "rep" )
        ->xsl( "musadm/users/balance/edit_report_popup.xsl" )
        ->show();

    exit;
}


if( $action === "refreshTableUsers" )
{
    echo "<div class='users'>";
    Core_Page_Show::instance()->execute();
    echo "</div>";

    exit;
}