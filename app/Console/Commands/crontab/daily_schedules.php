<?php
/*
 * 30 0 * * * /usr/local/bin/php /root/nginx/html/gamification/app/Console/Commands/crontab/daily_schedules.php
 */
$app = require __DIR__ . '/../../../../bootstrap/app.php';
$todayDateAt = date("Y-m-d");
$schedules_list = DB::table('schedules')
    ->where('state', 0)
    ->where('start_at', '<=', $todayDateAt)
    ->get();

//1=只一次的任务；2=学习任务；3=无限重复
foreach ($schedules_list as $schedule) {

    //判断时间是不是应该今天处理 和type有关系
    switch ($schedule->repeat_type) {
        case 1:
            if ($schedule->start_at == $todayDateAt) {
                handlerRepeatType_1($schedule);
            }
            break;
        case 2:
            //判断学习重复周期中是否有今日
            $repeat_ordinal = computeRepeatType_2($schedule->start_at, $todayDateAt);
            if ($repeat_ordinal !== false) {
                handlerRepeatType_2($schedule, $repeat_ordinal, $todayDateAt);
            }
            break;
        case 3:
        case 4:
        case 5:
        case 6:
            //判断重复周期种是否有今日
            $isToday = computeRepeatType_3($schedule, $todayDateAt);
            if ($isToday === true) {
                handlerRepeatType_3($schedule, $todayDateAt);
            }
            break;
        default:
            Log::error("出现一个非正常的日程条目:" . json_encode($schedule));
    }
}

function handlerRepeatType_1($schedule)
{
    $data_arr = array(
        'text' => $schedule->text,
        'type' => 1,
        'note' => $schedule->note,
        'exp' => $schedule->exp,
        'gold' => $schedule->gold,
        'deadline_at' => $schedule->start_at,
        'schedule_id' => $schedule->id,
        'state' => 0,
    );

    DB::beginTransaction();
    $is_commit_c = DB::table('quests')->insertGetId($data_arr);
    //更新start_at到今天
    $is_commit_u = DB::table('schedules')->where('id', $schedule->id)
        ->update(array('state' => 1));
    if ($is_commit_c && is_int($is_commit_u)) {
        DB::commit();
    } else {
        DB::rollback();
    }
}

function handlerRepeatType_2($schedule, $repeat_ordinal, $todayDateAt)
{
    $data_arr = array(
        'text' => $schedule->text . '[' . $repeat_ordinal . ']',
        'type' => 1,
        'note' => $schedule->note,
        'exp' => $schedule->exp,
        'gold' => $schedule->gold,
        'deadline_at' => $todayDateAt,
        'schedule_id' => $schedule->id,
        'state' => 0,
    );
    if ($repeat_ordinal === 10) {
        //最后一次
        DB::beginTransaction();
        $is_commit_c = DB::table('quests')->insertGetId($data_arr);
        //更新start_at到今天
        $is_commit_u = DB::table('schedules')->where('id', $schedule->id)
            ->update(array('state' => 1));
        if ($is_commit_c && is_int($is_commit_u)) {
            DB::commit();
        } else {
            DB::rollback();
        }
    } else {
        DB::table('quests')->insertGetId($data_arr);
    }
}

function handlerRepeatType_3($schedule, $todayDateAt)
{
    $data_arr = array(
        'text' => $schedule->text,
        'type' => 1,
        'note' => $schedule->note,
        'exp' => $schedule->exp,
        'gold' => $schedule->gold,
        'deadline_at' => $todayDateAt,
        'schedule_id' => $schedule->id,
        'state' => 0,
    );

    DB::beginTransaction();
    $is_commit_c = DB::table('quests')->insertGetId($data_arr);
    //更新start_at到今天
    $is_commit_u = DB::table('schedules')->where('id', $schedule->id)
        ->update(array('start_at' => $todayDateAt));
    if ($is_commit_c && is_int($is_commit_u)) {
        DB::commit();
    } else {
        DB::rollback();
    }
}

/*
 * 计算今天是否是一个学习任务的标准日
 * @return Bool or int
 */
function computeRepeatType_2($schedule_start_at, $todayDateAt)
{
    $repeat_days_list = array();
    for ($i = 0; $i <= 10; $i++) {
        array_push($repeat_days_list, pow(2, $i));
    }
    $difference_days = (strtotime($todayDateAt) - strtotime($schedule_start_at)) / 86400;
    return array_search($difference_days, $repeat_days_list);
}

/*
 * 计算今天是否是一个重复任务的标准日
 * @return Bool
 */
function computeRepeatType_3($schedule, $todayDateAt)
{
    //3=按日；4=按周；5=按月；6=按年；
    switch ($schedule->repeat_type) {
        case 3:
            $newDay_at = strtotime("+1 day", strtotime($schedule->start_at));
            break;
        case 4:
            $newDay_at = strtotime("+1 week", strtotime($schedule->start_at));
            break;
        case 5:
            $newDay_at = strtotime("+1 month", strtotime($schedule->start_at));
            break;
        case 6:
            $newDay_at = strtotime("+1 year", strtotime($schedule->start_at));
            break;
        default:
            Log::error("我们中出了一个叛徒:" . json_encode($schedule));
            $newDay_at = false;
    }

    return strtotime($todayDateAt) >= $newDay_at;
}