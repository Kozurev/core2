<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 18.03.2018
 * Time: 21:21
 */

/*
*	Блок проверки авторизации
*/
$User = User::current();
$access = ['groups' => [1, 2, 6]];

if( $User === null )
{
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim( dirname( $_SERVER['PHP_SELF'] ), '/\\' );
    header( "Location: http://$host$uri/authorize?back=$host$uri/" );
    exit;
}


/**
 * Настроки редиректа
 */
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim( dirname( $_SERVER['PHP_SELF'] ), '/\\' );


Core_Page_Show::instance()->setParam( 'body-class', 'body-green' );
Core_Page_Show::instance()->setParam( 'title-first', 'ГЛАВНАЯ' );
Core_Page_Show::instance()->setParam( 'title-second', 'СТРАНИЦА' );


if( Core_Array::Get( 'ajax', null ) === null )
{
    if( !User::checkUserAccess( $access, $User ) )
    {
        header( "Location: http://$host$uri/authorize?back=/$uri" );
    }

    if( $User->groupId() == 6 )
    {
        header( "Location: http://$host$uri/user/client" );
    }

    if( $User->groupId() == 5 )
    {
        header( "Location: http://$host$uri/balance" );
    }

    if( $User->groupId() == 4 )
    {
        header( "Location: http://$host$uri/schedule" );
    }
}


$action = Core_Array::Get(  'action', null );


$Director = $User->getDirector();
$subordinated = $Director->getId();


/**
 *
 */
if ( $action === 'getPropertyListPopup' )
{
    $propId = Core_Array::Get( 'prop_id', null );

    if ( $propId === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    $Property = Core::factory( 'Property', $propId );

    if ( $Property === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    $Values = $Property->getList();

    Core::factory( 'Core_Entity' )
        ->addEntity( $Property )
        ->addEntities( $Values )
        ->xsl( 'musadm/edit_property_list.xsl' )
        ->show();

    exit;
}


/**
 * Сохранение элемента списка дополнительного свойства
 */
if ( $action === 'savePropertyListValue' )
{
    $id =     Core_Array::Get( 'id', 0 );
    $propId = Core_Array::Get( 'prop_id', null );
    $value =  Core_Array::Get( 'value', null );

    if ( $propId === null || $value === null || $propId <= 0 || $value == '' )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    $NewValue = Core::factory( 'Property_List_Values', $id );

    if ( $NewValue === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    $NewValue
        ->property_id( $propId )
        ->value( $value )
        ->sorting( 0 );
    $NewValue->save();

    $returnJson = new stdClass();
    $returnJson->id = $NewValue->getId();
    $returnJson->propertyId = $propId;
    $returnJson->value = $value;

    echo json_encode( $returnJson );
    exit;
}


/**
 * Удаление элемента списка дополнения свйоства
 */
if ( $action === 'deletePropertyListValue' )
{
    $id = Core_Array::Get( 'id', null );

    if ( $id === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }


    $PropertyListValue = Core::factory( 'Property_List_Values', $id );

    if ( $PropertyListValue === null )
    {
        Core_Page_Show::instance()->error( 404 );
    }

    $PropertyListValue->delete();

    exit;
}


/**
 * Обработчик для сохранения значения доп. свойства
 */
if ( $action === 'savePropertyValue' )
{
    $propertyName = Core_Array::Get( 'prop_name', null );
    $propertyValue= Core_Array::Get( 'value', null );
    $modelId =      Core_Array::Get( 'model_id', null );
    $modelName =    Core_Array::Get( 'model_name', null );

    $Property = Core::factory( 'Property' )->getByTagName( $propertyName );

    if ( $Property === null )
    {
        exit ( 'Свойство с названием ' . $propertyName . ' не существует' );
    }

    $Object = Core::factory( $modelName, $modelId );

    if ( !is_object( $Object ) || $Object->getId() == 0 )
    {
        exit ( "Объекта класса $modelName с id $modelId не существует" );
    }

    $Value = $Property->getPropertyValues( $Object )[0];
    $Value->value( $propertyValue )->save();

    exit;
}


/**
 * Обновление таблицы лидов
 */
if( $action == 'refreshLidTable' )
{
    Core::factory( 'Lid_Controller' );
    $LidController = new Lid_Controller( $User );
    $LidController
        ->lidId(
            Core_Array::Get( 'lidid', null )
        )
        ->isShowPeriods( false )
        ->show();

    exit;
}


/**
 * Обновление таблицы
 */
if( $action === 'refreshTasksTable' )
{
    Core::factory( 'Task_Controller' );
    $TaskController = new Task_Controller( User::current() );
    $TaskController
        ->isShowPeriods( false )
        ->isSubordinate( true )
        ->isLimitedAreasAccess( true )
        ->show();

    exit;
}


if( $action === 'search_client' )
{
    $surname = Core_Array::Get( 'surname', null );
    $name    = Core_Array::Get( 'name', null );
    $phone   = Core_Array::Get( 'phone', null );

    $User = Core::factory( 'User' );
    $User->queryBuilder()
        ->where( 'group_id', '=', 5 )
        ->where( 'subordinated', '=', $subordinated )
        ->where( 'active', '=', 1 );

    if( !is_null( $surname ) )
    {
        $User->queryBuilder()
            ->open()
                ->where( 'surname', 'LIKE', "%$surname%" )
                ->orWhere( 'surname', 'LIKE', "$surname%" )
                ->orWhere( 'surname', 'LIKE', "%$surname" )
                ->orWhere( 'surname', '=', $surname )
            ->close();
    }

    if( !is_null( $name ) )
    {
        $User->queryBuilder()
            ->open()
                ->where( 'name', 'LIKE', "%$name%" )
                ->orWhere( 'name', 'LIKE', "$name%" )
                ->orWhere( 'name', 'LIKE', "%$name" )
                ->orWhere( 'name', '=', $name )
            ->close();
    }

    if( !is_null( $phone ) )
    {
        $User->queryBuilder()
            ->open()
                ->where( 'phone_number', 'LIKE', "%$phone%" )
                ->orWhere( 'phone_number', 'LIKE', "$phone%" )
                ->orWhere( 'phone_number', 'LIKE', "%$phone" )
                ->orWhere( 'phone_number', '=', $phone )
            ->close();
    }

    $Users = $User->findAll();

    if( count( $Users ) !== 0 )
    {
        $UserGroup = Core::factory( 'User_Group', 5 );
        $PropertiesList = Core::factory( 'Property' )->getPropertiesList( $UserGroup );

        foreach ( $Users as $User )
        {
            foreach ( $PropertiesList as $prop )
            {
                $User->addEntities( $prop->getPropertyValues( $User ), 'property_value' );
            }
        }

        echo "<div class='users'>";
        Core::factory( 'Core_Entity' )
            ->addSimpleEntity( 'page-theme-color', 'green' )
            ->addSimpleEntity( 'active-btn-panel', '0' )
            ->addSimpleEntity( 'wwwroot', $CFG->rootdir )
            ->addSimpleEntity( 'table-type', 'active' )
            ->addEntities( $Users )
            ->xsl( 'musadm/users/clients.xsl' )
            ->show();
        echo "</div>";
    }

    exit;
}


if( $action === 'getObjectInfoPopup' )
{
    $id =     Core_Array::Get( 'id', 0 );
    $model =  Core_Array::Get( 'model', '' );

    $Object = Core::factory( $model, $id );

    if( $Object === null )
    {
        exit ( "<h2>Объект с переданными данными не найден или был удален</h2>" );
    }

    $Output = Core::factory( 'Core_Entity' )
        ->xsl( 'musadm/object.xsl' );

    switch ( $model )
    {
        case 'Task' :
            Core::factory( 'Task_Controller' );
            $TaskController = new Task_Controller( User::current() );
            $TaskController
                ->isShowPeriods( false )
                ->isShowButtons( false )
                ->isPeriodControl( false )
                ->taskId( $id )
                ->xsl( 'musadm/tasks/all.xsl' )
                ->show();
            exit;

        case 'Lid' :
            Core::factory( 'Lid_Controller' );
            $LidController = new Lid_Controller( User::current() );
            $LidController
                ->isShowPeriods( false )
                ->isShowButtons( false )
                ->isPeriodControl( false )
                ->lidId( $id )
                ->xsl( 'musadm/lids/lids.xsl' )
                ->show();
            exit;

        case 'Certificate' :
            $Object->sellDate( refactorDateFormat( $Object->sellDate() ) );
            $Object->activeTo( refactorDateFormat( $Object->activeTo() ) );

            $Notes = Core::factory( "Certificate_Note" )->queryBuilder()
                ->select( [ "certificate_id", "author_id", "date", "text", "surname", "name"] )
                ->where( "certificate_id", "=", $id )
                ->leftJoin( "User AS u", "u.id = author_id" )
                ->orderBy( "date", "DESC" )
                ->orderBy( "Certificate_Note.id", "DESC" )
                ->findAll();

            foreach ( $Notes as $Note )
            {
                $Note->date( refactorDateFormat( $Note->date() ) );
            }

            $Output->addEntities( $Notes, "note" );
            break;

        default: echo "<h2>Ошибка: отсутствует обработчик для модели '". $model ."'</h2>";
    }

    $Output
        ->addEntity( $Object )
        ->show();

    exit;
}


if( $action === "refreshTableUsers" )
{
    Core_Page_Show::instance()->execute();
    exit;
}