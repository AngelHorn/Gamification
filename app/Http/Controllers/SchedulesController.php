<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use stdClass;

class SchedulesController extends Controller
{

    /**
     * @return Response
     */
    public function getIndex()
    {
        $quests = DB::table('schedules')->get();
        $this->export(200, $quests);
    }

    /**
     * @return Response
     */
    public function postIndex()
    {
        $input = Input::all();
        $rules = array(
            'text' => 'required',
            'start_at' => 'required|date',
            'repeat_type' => 'required|numeric',
            'exp' => 'numeric',
            'gold' => 'numeric',
            'alert_at' => 'date',
            'class_id' => 'numeric',
            'father_id' => 'numeric',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $this->export(403);
            die;
        }

        $data_arr = array(
            'text' => $input['text'],
            'repeat_type' => $input['repeat_type'],
            'start_at' => $input['start_at'],
            'state' => 0,
        );
        isset($input['note']) ? $data_arr['note'] = $input['note'] : null;
        isset($input['exp']) ? $data_arr['exp'] = $input['exp'] : null;
        isset($input['gold']) ? $data_arr['gold'] = $input['gold'] : null;
        isset($input['class_id']) ? $data_arr['class_id'] = $input['class_id'] : null;
        isset($input['father_id']) ? $data_arr['father_id'] = $input['father_id'] : null;
        isset($input['alert_at']) ? $data_arr['alert_at'] = $input['alert_at'] : null;

        $newId = DB::table('schedules')->insertGetId($data_arr);
        if (is_int($newId)) {
            $this->export(200, DB::table('schedules')->where('id', $newId)->first());
        } else {
            $this->export(500);
        }
    }

    /**
     * @return Response
     */
    public function getQuest($id)
    {
        $quest = DB::table('quests')->where('id', $id)->first();
        if ($quest) {
            $this->export(200, $quest);
        } else {
            $this->export(404);
        }
    }

    /**
     * @param  int $id
     * @return Response
     */
    public function putSchedule($id)
    {
        //更新repeat_type的时候 一定要检测start_at
        $input = Input::all();
        $rules = array(
            'exp' => 'numeric',
            'gold' => 'numeric',
            'type' => 'numeric',
            'deadline_at' => 'date',
            'alert_at' => 'date',
            'class_id' => 'numeric',
            'father_id' => 'numeric',
            'state' => 'numeric',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $this->export(403);
            die;
        }

        $data_arr = array();
        isset($input['text']) ? $data_arr['text'] = $input['text'] : '';
        isset($input['type']) ? $data_arr['type'] = $input['type'] : '';
        isset($input['state']) ? $data_arr['state'] = $input['state'] : '';
        isset($input['note']) ? $data_arr['note'] = $input['note'] : '';
        isset($input['exp']) ? $data_arr['exp'] = $input['exp'] : '';
        isset($input['gold']) ? $data_arr['gold'] = $input['gold'] : '';
        isset($input['class_id']) ? $data_arr['class_id'] = $input['class_id'] : '';
        isset($input['father_id']) ? $data_arr['father_id'] = $input['father_id'] : '';
        isset($input['deadline_at']) ? $data_arr['deadline_at'] = $input['deadline_at'] : '';
        isset($input['alert_at']) ? $data_arr['alert_at'] = $input['alert_at'] : '';

        $isCommit = DB::table('quests')->where('id', $id)->update($data_arr);
        if ($isCommit > 0) {
            $this->export(200, DB::table('quests')->where('id', $id)->first());
        } else {
            $this->export(500);
        }
    }

    /**
     * @param  int $id
     * @return Response
     */
    public function deleteQuest($id)
    {
        $isCommit = DB::table('quests')->where('id', $id)->update(array(
            'state' => 4,
        ));
        if ($isCommit > 0) {
            $this->export(200);
        } else {
            $this->export(500);
        }
    }

    /**
     * @return Response
     */
    public function getIndexTree()
    {
        $quests = DB::table('quests')->get();
//        $tree_json = $this->arrayToTreeMap(json_decode(json_encode($quests), 1));
        $this->export(200, $quests);
    }

    /*
     * 暂时弃用 在客户端重新写了一个新的function来代替他
     */
    private function arrayToTreeMap($menus)
    {
        $id = $level = 0;
        $menu_objects = array();
        $tree = array();
        $not_root_menu = array();
        foreach ($menus as $menu) {
            $menu_object = new stdClass();
            $menu_object->name = $menu['text'];
            $menu_object->menu = $menu;
            $id = $menu['id'];
            $level = $menu['father_id'];
            $menu_object->children = array();
            $menu_objects[$id] = $menu_object;
            if ($level) {
                $not_root_menu[] = $menu_object;
            } else {
                $tree[] = $menu_object;
            }
        }

        foreach ($not_root_menu as $menu_object) {
            $menu = $menu_object->menu;
            $id = $menu['id'];
            $level = $menu['father_id'];
            $menu_object->size = 100;
            if (isset($menu_objects[$level]->size)) {
                unset($menu_objects[$level]->size);
            }
            $menu_objects[$level]->children[] = $menu_object;
        }
        return array("name" => "Root", "children" => $tree);
    }

}
