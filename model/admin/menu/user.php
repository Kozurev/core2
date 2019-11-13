<?php

class Admin_Menu_User
{

    public function show( $aParams )
    {
        $xslPath = "admin/users/users.xsl";
        $groupId = Core_Array::getValue( $aParams, "parent_id", "0" );
        $OutputEntity = Core::factory( "Core_Entity" );
        $title = "Пользователи";

        $User = Core::factory( "User" );
        $search = Core::factory( "Core_Entity" )->_entityName( "search" );

        $searchData = Core_Array::getValue( $aParams, "search", "" );

        if ( $searchData != "" )
        {
            $data = explode( " ", $searchData );

            foreach ( $data as $word )
                $User->queryBuilder()
                    ->where( "name", "like", "%".$word."%", "or" )
                    ->where( "surname", "like", "%".$word."%", "or" );

            $search->value( $searchData );
        }

        $User->where( "group_id", "=", $groupId );


        //Пагинация
        $page = intval( Core_Array::getValue( $aParams, "page", 0 ) );
        $offset = $page * SHOW_LIMIT;
        $userCount = clone $User;
        $totalCount = $userCount->getCount();

        if ( $groupId == "0" )
        {
            $totalCount += Core::factory( "User_Group" )->getCount();
        }

        $countPages = intval( $totalCount / SHOW_LIMIT );

        if ( $totalCount % SHOW_LIMIT != 0 )
        {
            $countPages++;
        }

        if ( $countPages == 0 )
        {
            $countPages = 1;
        }

        $Pagination = Core::factory( "Core_Entity" )
            ->_entityName( "pagination" )
            ->addSimpleEntity( "current_page", ++$page )
            ->addSimpleEntity( "count_pages", $countPages )
            ->addSimpleEntity( "total_count", $totalCount );

        $Users = $User->queryBuilder()
            ->limit( SHOW_LIMIT )
            ->orderBy( "id", "DESC" )
            ->offset( $offset )
            ->findAll();

        if ( $groupId == "0" )
        {
            $Groups = Core::factory( "User_Group" )
                ->orderBy( "sorting" )
                ->limit( SHOW_LIMIT )
                ->offset( $offset )
                ->findAll();

            $OutputEntity->addEntities( $Groups );
        }
        elseif ( $groupId != "0" )
        {
            $UserGroup = Core::factory( "User_Group", $groupId );

            if ( $UserGroup === null )
            {
                exit ( Core::getMessage( "NOT_FOUND", ["Группа пользователя", $groupId] ) );
            }

            $title = $UserGroup->title();
            $OutputEntity->addEntity( $UserGroup );
        }

        $OutputEntity
            ->addEntity( $Pagination )
            ->addEntity( $search )
            ->addSimpleEntity( "title", $title )
            ->addSimpleEntity( "group_id", $groupId )
            ->addEntities( $Users )
            ->xsl( $xslPath )
            ->show();
    }


    public function updateAction( $aParams )
    {
        $pass1 = Core_Array::getValue( $aParams, "pass1", null );
        $pass2 = Core_Array::getValue( $aParams, "pass2", null );

        unset( $aParams["pass1"] );
        unset( $aParams["pass2"] );

        if ( $pass1 != $pass2 )
        {
            exit ( "Введенные пароли не совпадают" );
        }

        if( $pass1 != "" && !is_null($pass1) )
        {
            $aParams["password"] = $pass1;
        }

        Core::factory( "Admin_Menu_Main" )->updateAction( $aParams );
    }


    public function updateForm( $aParams )
    {
        Core::factory( "Admin_Menu_Main" )->updateForm( $aParams, "User", "admin/main/update_form.xsl" );
    }


}