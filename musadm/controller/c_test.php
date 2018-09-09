<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */



$dbh = new mysqli("37.140.192.32:3306", "u4834_root", "n1omY2_1", "u4834955_core");
$dbh->query("SET NAMES utf8");

Core::factory( "Orm" )->executeQuery( "TRUNCATE Property_Int_Assigment" );
Core::factory( "Orm" )->executeQuery( "TRUNCATE Property_Text_Assigment" );
Core::factory( "Orm" )->executeQuery( "TRUNCATE Property_String_Assigment" );
Core::factory( "Orm" )->executeQuery( "TRUNCATE Property_Bool_Assigment" );
Core::factory( "Orm" )->executeQuery( "TRUNCATE Property_List_Assigment" );

Core::factory( "Orm" )->executeQuery( "TRUNCATE Property_Int" );
Core::factory( "Orm" )->executeQuery( "TRUNCATE Property_String" );
Core::factory( "Orm" )->executeQuery( "TRUNCATE Property_Text" );
Core::factory( "Orm" )->executeQuery( "TRUNCATE Property_Bool" );
Core::factory( "Orm" )->executeQuery( "TRUNCATE Property_List" );


$limit = 50;
$time = 2000;
$step = Core_Array::getValue( $_GET, "step", 0 );
$offset = Core_Array::getValue( $_GET, "offset", 0 );
$totalCount = Core_Array::getValue( $_GET, "total_count", 0 );

switch( $step )
{
    /**
     * Лиды
     */
    case 0:
        $lidLimit = 50;

        if( $totalCount == 0 )
            $totalCount = $dbh->query( "SELECT count(*) AS count FROM Lid" )->fetch_object()->count;

        if( $offset == 0 )
            Core::factory( "Orm" )->executeQuery( "TRUNCATE Lid" );

        $lids = $dbh->query( "SELECT name, surname, number, vk, source, control_date, value_id
                                    FROM Lid
                                    LEFT JOIN Property_List AS pl ON Lid.id = pl.object_id
                                    WHERE pl.model_name = \"Lid\" LIMIT  $lidLimit OFFSET $offset" );

        if( ($offset + $lidLimit) <= $totalCount )
        {
            while( $lid = $lids->fetch_object() )
            {
                $Lid = Core::factory( "Lid" )
                    ->name( $lid->name )
                    ->surname( $lid->surname )
                    ->number( $lid->number )
                    ->source( $lid->source )
                    ->controlDate( $lid->control_date );
                $Lid->save();

                Core::factory( "Property_List" )
                    ->model_name( "Lid" )
                    ->property_id( 27 )
                    ->object_id( $Lid->getId() )
                    ->value( $lid->value_id )
                    ->save();
            }

            $offset += $lidLimit;

            echo "Обработано $offset/$totalCount лидов";
            ?><script>
            setTimeout( function(){
                window.location.href = "/musadm/test?total_count=<?=$totalCount?>&offset=<?=$offset?>&step=0";
            },<?=$time?> );
        </script><?
        }
        else
        {
            echo "Обработано $offset/$totalCount лидов";
            ?><script>
            setTimeout( function(){
                window.location.href = "/musadm/test?step=1";
            }, <?=$time?> );
            </script><?
        }



    break;

    /**
     * Пользователи и всё, что с ними связано
     */
    case 1:
        if( $offset == 0 )
        {
            Core::factory( "Orm" )->executeQuery( "TRUNCATE User" );
            Core::factory( "Orm" )->executeQuery( "DELETE FROM Property_List_Values WHERE property_id = 21" );

            //Создание моего админского профиля
            $User = Core::factory( "User" );
            $User
                ->name( "Егор" )
                ->surname( "Козырев" )
                ->patronimyc( "Алексеевич" )
                ->phoneNumber( "8-980-378-28-56" )
                ->login( "alexoufx" )
                ->password( "0000" )
                ->email( "creative27016@gmail.com" )
                ->groupId( 1 )
                ->superuser( 1 );
            $User->save();

            //Создание админского профиля Артура
            $User = Core::factory( "User" );
            $User
                ->name( "Артур" )
                ->surname( "Герус" )
                ->patronimyc( "Андреевич" )
                ->phoneNumber( "30-18-77" )
                ->login( "artur" )
                ->password( "0000" )
                ->groupId( 1 )
                ->superuser( 1 );
            $User->save();

            //Создание директорского профиля Артура
            $User = Core::factory( "User" );
            $User
                ->name( "Артур" )
                ->surname( "Герус" )
                ->patronimyc( "Андреевич" )
                ->phoneNumber( "30-18-77" )
                ->login( "director1" )
                ->password( "0000" )
                ->groupId( 6 );
            $User->save();

            $User->authorize( true );
        }

        if( $totalCount == 0 )
            $totalCount = $dbh->query( "SELECT count(*) as count FROM User" )->fetch_object()->count;

        if( ($offset + $limit) <= $totalCount )
        {
            $Users = $dbh->query( "SELECT * FROM User LIMIT $limit OFFSET $offset" );
            while( $user = $Users->fetch_object() )
            {
                $User = Core::factory( "User" )
                    ->name( $user->name )
                    ->surname( $user->surname )
                    ->phoneNumber( $user->phone_number )
                    ->login( $user->login )
                    ->password( $user->password )
                    ->groupId( $user->group_id )
                    ->active( $user->active )
                    ->save();


            }

            $offset += $limit;

            echo "Обработано $offset/$totalCount пользователей";
            ?><script>
            setTimeout( function(){
                window.location.href = "/musadm/test?total_count=<?=$totalCount?>&offset=<?=$offset?>&step=1";
            },<?=$time?> );
        </script><?
        }
        else
        {
            echo "Обработано $offset/$totalCount пользователей";
            ?><script>
                setTimeout( function(){
                    window.location.href = "/musadm/test?step=2";
                }, <?=$time?> );
            </script><?
        }
        break;



    case 2: echo "<h1>Скрипт окончил свою работу</h1>";












}

