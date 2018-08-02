<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 18.05.2018
 * Time: 12:02
 */

class Admin_Menu_Task
{
    public function show($aParams)
    {
        $taskId = Core_Array::getValue($aParams, "parent_id", 0);
        $oOutputEntity = Core::factory("Core_Entity");
        $sXslPath = "admin/tasks/tasks.xsl";

        if($taskId == 0)
        {
            $title = "Задачи";
            $modelName = "Task";

            $aoItems = Core::factory($modelName);
            $aoTaskTypes = Core::factory("Task_Type")->findAll();
            $oOutputEntity->addEntities($aoTaskTypes);
        }
        else
        {
            $title = "Комментарии к задаче";
            $modelName = "Task_Note";

            $aoItems = Core::factory($modelName)->where("task_id", "=", $taskId);
        }

        //$aoItems = Core::factory($modelName);

        //Пагинация
        $page = intval(Core_Array::getValue($aParams, "page", 0));
        $offset = $page * SHOW_LIMIT;

        $totalCount = $aoItems->getCount();
        $countPages = intval($totalCount / SHOW_LIMIT);
        if($totalCount % SHOW_LIMIT)    $countPages++;
        if($countPages == 0) $countPages++;

        $oPagination = Core::factory("Core_Entity")
            ->name("pagination")
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("current_page")
                    ->value(++$page)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("count_pages")
                    ->value($countPages)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("total_count")
                    ->value($totalCount)
            );

        $aoItems
            ->queryBuilder()
            ->orderBy("date", "DESC")
            ->limit(SHOW_LIMIT)
            ->offset($offset);

        if($modelName == "Task_Note")
            $aoItems->where("task_id", "=", $taskId);

        $aoItems = $aoItems->findALl();

        if($modelName == "Task")
            foreach ($aoItems as $oItem)
            {
                $oItemNote = Core::factory("Task_Note")
                    ->where("task_id", "=", $oItem->getId())
                    ->orderBy("date", "DESC")
                    ->find();

                if($oItemNote != false)
                $oItem->addEntity($oItemNote);
                $oItem->date(refactorDateFormat($oItem->date()));
            }
        else
            foreach ($aoItems as $oItem)
            {
                $oItem->addEntity($oItem->getAuthor());
            }


        $oOutputEntity
            ->xsl($sXslPath)
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("title")
                    ->value($title)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("model_name")
                    ->value($modelName)
            )
            ->addEntity(
                Core::factory("Core_Entity")
                    ->name("parent_id")
                    ->value($taskId)
            )
            ->addEntity($oPagination)
            ->addEntities($aoItems)
            ->show();
    }


    public function updateAction($aParams)
    {
        $modelId = Core_Array::getValue($aParams, "id", 0);
        if($modelId > 0)    $oTask = Core::factory("Task", $modelId);
        else    $oTask = Core::factory("Task");

        $date = Core_Array::getValue($aParams, "date", date("Y-m-d"));
        $type = Core_Array::getValue($aParams, "type", 2);
        $done = Core_Array::getValue($aParams, "done", 0);
        $text = Core_Array::getValue($aParams, "text", "");

        $oTask
            ->date($date)
            ->type($type)
            ->done($done);
        $oTask = $oTask->save();

        Core::factory("Task_Note")
            ->taskId($oTask->getId())
            ->text($text)
            ->save();

        echo "0";
    }


    public function updateForm($aParams)
    {
        Core::factory("Admin_Menu_Main")->updateForm($aParams, "Task", "admin/main/update_form.xsl");
    }


}