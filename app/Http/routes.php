<?php

$app->get('quests', 'QuestsController@getIndex');
$app->post('quests', 'QuestsController@postIndex');
$app->put('quests/{id}', 'QuestsController@putIndex');
$app->delete('quests/{id}', 'QuestsController@deleteIndex');
