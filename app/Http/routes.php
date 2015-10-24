<?php

$app->get('','IndexController@getIndex');

$app->get('quests/tree', 'QuestsController@getIndexTree');
$app->get('quests', 'QuestsController@getIndex');
$app->post('quests', 'QuestsController@postIndex');
$app->get('quests/{id}', 'QuestsController@getQuest');
$app->put('quests/{id}', 'QuestsController@putQuest');
$app->delete('quests/{id}', 'QuestsController@deleteQuest');
