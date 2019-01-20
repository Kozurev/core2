<?php
/**
 * Настройки страницы со списком пользователей различных групп
 *
 * @author Kozurev Egor
 * @date 11.04.2018 22:16
 */


if( Core_Page_Show::instance()->StructureItem->getId() == 5 )
{
    $title2 = "КЛИЕНТОВ";
    $breadcumb = "клиентов";
}
else 
{
    $title2 = "СОТРУДНИКОВ";
    $breadcumb = "сотрудников";
}

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->title;
$breadcumbs[0]->active = 1;

Core_Page_Show::instance()->setParam( "body-class", "body-primary" );
Core_Page_Show::instance()->setParam( "title-first", "СПИСОК" );
Core_Page_Show::instance()->setParam( "title-second", $title2 );
Core_Page_Show::instance()->setParam( "breadcumbs", $breadcumbs );


$Director = User::current()->getDirector();
if ( !$Director )    exit ( Core::getMessage("NOT_DIRECTOR") );
$subordinated = $Director->getId();

/**
 *	Блок проверки авторизации
 */
$User = User::current();
$accessRules = ["groups"    => [2, 6]];

if ( !User::checkUserAccess( $accessRules, $User ) )
{
    Core_Page_Show::instance()->error404();
}


$action = Core_Array::Get( "action", null );


/**
 * Форма редактирования клиента
 */
if ( $action == "updateFormClient" )
{
    $userid = Core_Array::Get( "userid", 0 );
    $output = Core::factory( "Core_Entity" );

    if ( $userid )
    {
        $PaymentUser = Core::factory( "User", $userid );

        if ( $PaymentUser === null )
        {
            exit ( Core::getMessage( "NOT_FOUND", ["Пользователь", $userId] ) );
        }

        if ( !User::isSubordinate( $PaymentUser, $User ) )
        {
            exit ( Core::getMessage( "NOT_SUBORDINATE", ["Пользователь", $userId] ) );
        }


        $AreaAssignments = Core::factory( "Schedule_Area_Assignment" )->getAssignments( $PaymentUser );

        if ( count( $AreaAssignments ) > 0 )
        {
            $PaymentUser->addSimpleEntity( "area_id", $AreaAssignments[0]->areaId() );
        }


        $Properties[] = Core::factory( "Property", 16 )->getPropertyValues( $PaymentUser )[0];    //Доп. телефон
        $Properties[] = Core::factory( "Property", 9  )->getPropertyValues( $PaymentUser )[0];    //Ссылка вк
        $Properties[] = Core::factory( "Property", 17 )->getPropertyValues( $PaymentUser )[0];    //Длительность урока
        //$Properties[] = Core::factory( "Property", 15 )->getPropertyValues( $PaymentUser )[0];    //Студия
        $Properties[] = Core::factory( "Property", 18 )->getPropertyValues( $PaymentUser )[0];    //Соглашение подписано
        $Properties[] = Core::factory( "Property", 28 )->getPropertyValues( $PaymentUser )[0];
        $Properties =   array_merge( $Properties, Core::factory( "Property", 21 )->getPropertyValues( $PaymentUser) );   //Учителя
    }
    else
    {
        $PaymentUser = Core::factory( "User" );

        $Properties[] =   Core::factory( "Property_Int" )
            ->value(
                Core::factory( "Property", 17 )->defaultValue()
            );
    }


    $Areas = Core::factory( "Schedule_Area" )->getList( true );

    $PropertyLists = Core::factory( "Property_List_Values" )->queryBuilder()
        ->where( "subordinated", "=", $subordinated )
        ->where( "property_id", "=", 21 )
        ->orderBy( "value" )
        ->findAll();

    $PropertyLists = array_merge( $PropertyLists,
        Core::factory( "Property_List_Values" )->queryBuilder()
            ->where( "subordinated", "=", $subordinated )
            ->where( "property_id", "=", 15 )
            ->orderBy( "sorting" )
            ->findAll()
    );

    $output
        ->addEntity( $PaymentUser )
        ->addEntities( $Areas, "areas" )
        ->addEntities( $Properties, "property_value" )
        ->addEntities( $PropertyLists, "property_list" )
        ->xsl( "musadm/users/edit_client_popup.xsl" )
        ->show();

    exit;
}


/**
 * Форма редактирования учителя
 */
if ( $action == "updateFormTeacher" )
{
    $userId = Core_Array::Get( "userid", 0 );
    $output = Core::factory( "Core_Entity" );

    if ( $userId != 0 )
    {
        $Teacher = Core::factory( "User", $userId );

        if ( $Teacher === null )
        {
            exit ( Core::getMessage( "NOT_FOUND", ["Преподаватель", $userId] ) );
        }

        $Properties[] = Core::factory( "Property", 20 )->getPropertyValues( $Teacher )[0];    //Инструмент
        $Properties[] = Core::factory( "Property", 31 )->getPropertyValues( $Teacher )[0];    //Инструмент

        $output->addEntities( $Properties, "property_value" );
    }
    else
    {
        $Teacher = Core::factory( "User" );
    }

    $PropertyLists =  Core::factory( "Property_List_Values" )->queryBuilder()
        ->where( "property_id", "=", 20 )
        ->where( "subordinated", "=", $subordinated )
        ->orderBy( "sorting" )
        ->findAll();

    $output
        ->addEntity( $Teacher )
        ->addEntities( $PropertyLists, "property_list" )
        ->xsl( "musadm/users/edit_teacher_popup.xsl" )
        ->show();

    exit;
}


/**
 * Форма редактирования директора
 */
if ( $action == "updateFormDirector" )
{
    $userId = Core_Array::Get( "userid", 0 );
    $output = Core::factory( "Core_Entity" );

    if ( $userId != 0 )
    {
        $Director = Core::factory( "User", $userId );

        if ( $Director === null )
        {
            exit ( Core::getMessage( "NOT_FOUND", ["Директор", $userId] ) );
        }

        $City = Core::factory( "Property", 29 )->getPropertyValues( $Director )[0]; //Город
        $Link = Core::factory( "Property", 33 )->getPropertyValues( $Director )[0]; //Город
        $Organization = Core::factory( "Property", 30 )->getPropertyValues( $Director )[0]; //Организация

        $output->addEntity( $City, "property_value" );
        $output->addEntity( $Link, "property_value" );
        $output->addEntity( $Organization, "property_value" );
    }
    else
    {
        $Director = Core::factory( "User" );
    }

    $output
        ->addEntity( $Director )
        ->xsl( "musadm/users/edit_director_popup.xsl" )
        ->show();

    exit;
}


/**
 * Форма редактирования менеджера
 */
if ( $action == "updateFormManager" )
{
    $userId = Core_Array::Get( "userid", 0 );
    $output = Core::factory( "Core_Entity" );

    if( $userId != 0 )
    {
        $Manager = Core::factory( "User", $userId );

        if ( $Director === null )
        {
            exit ( Core::getMessage( "NOT_FOUND", ["Директор", $userId] ) );
        }
    }
    else
    {
        $Manager = Core::factory( "User" );
    }

    $output
        ->addEntity( $Manager )
        ->xsl( "musadm/users/edit_manager_popup.xsl" )
        ->show();

    exit;
}


/**
 * Обновление таблиц
 */
if ( $action == "refreshTableUsers" )
{
    $this->execute();
    exit;
}


/**
 * Форма для создания платежа
 */
if ( $action == "getPaymentPopup" )
{
    $userId = Core_Array::Get( "userid", 0 );
    $PaymentUser = Core::factory( "User", $userId );

    if ( $PaymentUser === null )
    {
        exit ( Core::getMessage( "NOT_FOUND", ["Пользователь", $userId] ) );
    }

    Core::factory( "Core_Entity" )
        ->addEntity( $PaymentUser )
        ->addSimpleEntity( "function", "clients" )
        ->xsl( "musadm/users/balance/edit_payment_popup.xsl" )
        ->show();

    exit;
}


/**
 * Сохранение платежа
 */
if ( $action == "savePayment" )
{
    $userid =       Core_Array::Get( "userid", 0 );
    $value  =       Core_Array::Get( "value", 0 );
    $description =  Core_Array::Get( "description", "" );
    $type =         Core_Array::Get( "type", 0 );

    $Payment = Core::factory( "Payment" )
        ->user( $userid )
        ->type( $type )
        ->value( $value )
        ->description( $description );
    $Payment->save();

    //Корректировка баланса ученика
    $PaymentUser = Core::factory( "User", $userid );

    if ( $PaymentUser === null )
    {
        exit ( Core::getMessage( "NOT_FOUND", ["Пользователь", $userId] ) );
    }


    $UserBalance = Core::factory( "Property", 12 );
    $UserBalance = $UserBalance->getPropertyValues( $User )[0];
    $balanceOld =  intval( $UserBalance->value() );

    $type == 1
        ?   $balanceNew =   $balanceOld + intval( $value )
        :   $balanceNew =   $balanceOld - intval( $value );

    $UserBalance->value( $balanceNew );
    $UserBalance->save();

    exit ( "0" );
}


/**
 * При сохранении пользователя идет проверка на дублирования логина
 */
if ( $action == "checkLoginExists" )
{
    $userId = Core_Array::Get( "userid", 0 );
    $login = Core_Array::Get( "login", "" );

    if( $login == "" )
    {
        exit ( "Логин не может быть пустым" );
    }

    $User = Core::factory(  "User" )->queryBuilder()
        ->where( "id", "<>", $userId )
        ->where( "login", "=", $login )
        ->find();

    if( $User !== null )
    {
        exit ( "Пользователь с таким логином уже существует" );
    }

    exit;
}


/**
 * Экспорт пользователей в Excell
 */
if ( $action === "export" )
{
    User::checkUserAccess( ["groups" => [2, 6]] );

    header( "Content-type: application/vnd.ms-excel" );
    header( "Content-Disposition: attachment; filename=demo.xls" );

    $Users = Core::factory( "User" )->queryBuilder()
        ->select( ["name", "surname", "phone_number"] )
        ->where( "group_id", "=", 5 )
        ->where( "phone_number", "<>", "" )
        ->where( "active", "=", 1 )
        ->findAll();

    foreach ( $Users as $User )
    {
        $Property = Core::factory( "Property", 16 );
        $Numbers = $Property->getPropertyValues( $User );
        $User->addEntities( $Numbers, "numbers" );
    }

    Core::factory( "Core_Entity" )
        ->addEntities( $Users )
        ->xsl( "musadm/users/export.xsl" )
        ->show();

    exit;
}


/**
 * Получение данных лида
 */
if ( $action === "getLidData" )
{
    $lidId = Core_Array::Get( "lidid", 0 );
    $Lid = Core::factory( "Lid", $lidId );
    if( $Lid === null )    exit ( Core::getMessage( "NOT_FOUND", ["Лид", $lidId] ) );

    $LidEncode = new stdClass();
    $LidEncode->name = $Lid->name();
    $LidEncode->surname = $Lid->surname();
    $LidEncode->phone = $Lid->number();
    $LidEncode->vk = $Lid->vk();

    echo json_encode( $LidEncode );
    exit;
}


$aTitle[] = Core_Page_Show::instance()->Structure->title();

if( get_class( Core_Page_Show::instance()->StructureItem ) == "User_Group" )
    $aTitle[] = $this->StructureItem->title();

if( get_class( Core_Page_Show::instance()->StructureItem) == "User" )
    $aTitle[] = $this->oStructureItem->surname() . " " . $this->oStructureItem->name();

$this->title = array_pop( $aTitle );