<?php

$app->get('', 'IndexController@getIndex');

$app->get('quests/tree', 'QuestsController@getIndexTree');
$app->get('quests', 'QuestsController@getIndex');
$app->post('quests', 'QuestsController@postIndex');
$app->get('quests/{id}', 'QuestsController@getQuest');
$app->put('quests/{id}', 'QuestsController@putQuest');
$app->delete('quests/{id}', 'QuestsController@deleteQuest');

$app->get('schedules', 'SchedulesController@getIndex');
$app->post('schedules', 'SchedulesController@postIndex');
$app->put('schedules/{id}', 'SchedulesController@putSchedule');

$app->get('items', 'ItemsController@getIndex');
$app->post('items', 'ItemsController@postIndex');
$app->put('items/{id}', 'ItemsController@putItem');

$app->get('bag_items', 'BagItemsController@getIndex');
$app->post('bag_items', 'BagItemsController@postIndex');
