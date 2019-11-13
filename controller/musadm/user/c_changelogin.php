<?php

$userId = Core_Array::Get( "userid", null );

if( $userId === null )
{
    $User = User::current();
}
else
{
    $User = Core::factory( "User", $userId );
}


/**
 * Проверка на принадлежность клиента, под которым происходит авторизация,
 * тому же директору, которому принадлежит и менеджер
 */
$subordinated = User::current()->getDirector()->getId();

if( $User->subordinated() !== $subordinated )
{
    debug( $User->subordinated(), 1 );
    debug( $subordinated, 1 );
    die( "Доступ к личному кабинету данного пользователя заблокирован, так как он принадлежит другой организации" );
}


Core::factory("Core_Entity")
	->addEntity( $User )
	->xsl("musadm/users/changelogin.xsl")
	->show();