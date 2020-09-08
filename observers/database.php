<?php

Core::attachObserver('after.orm.*', function($args) {
    if (Orm::isDebugSql()) {
        $data = $args[Orm::OBSERVER_ARG_METHOD].': ' . $args[Orm::OBSERVER_ARG_QUERY];
        Log::instance()->debug(Log::TYPE_ORM, $data);
    }
});