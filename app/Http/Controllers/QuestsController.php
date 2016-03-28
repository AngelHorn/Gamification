<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use stdClass;

class QuestsController extends Controller
{

    /**
     * @return Response
     */
    public function getIndex()
    {
        $quests = DB::table('quests')->get();
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
            'exp' => 'numeric',
            'gold' => 'numeric',
            'type' => 'required|numeric',
            'deadline_at' => 'required_if:type,4|date',
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
            'type' => $input['type'],
            'state' => 0,
        );
        isset($input['note']) ? $data_arr['note'] = $input['note'] : '';
        isset($input['exp']) ? $data_arr['exp'] = $input['exp'] : '';
        isset($input['gold']) ? $data_arr['gold'] = $input['gold'] : '';
        isset($input['class_id']) ? $data_arr['class_id'] = $input['class_id'] : '';
        isset($input['father_id']) ? $data_arr['father_id'] = $input['father_id'] : '';
        isset($input['alert_at']) ? $data_arr['alert_at'] = $input['alert_at'] : '';
        isset($input['deadline_at']) ?
            $data_arr_with_deadline = $this->questTypeFilter($input['type'], $input['deadline_at']) :
            $data_arr_with_deadline = $this->questTypeFilter($input['type']);
        $data_arr = array_merge($data_arr, $data_arr_with_deadline);
        $newId = DB::table('quests')->insertGetId($data_arr);
        if (is_int($newId)) {

            $this->export(200, DB::table('quests')->where('id', $newId)->first());
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
    public function putQuest($id)
    {
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
        //我其实想把这边的接口完全与react的action相对应一个action一个url 到时候再说吧
        if (isset($input['state'])) {
            $data_arr['state'] = $input['state'];
            switch ($input['state']) {
                case '0':
                    $data_arr['done_at'] = null;
                    break;
                case '1':
                    $data_arr['done_at'] = date("Y-m-d");
                    break;
            }
        }
        isset($input['text']) ? $data_arr['text'] = $input['text'] : '';
        isset($input['type']) ? $data_arr['type'] = $input['type'] : '';
        isset($input['note']) ? $data_arr['note'] = $input['note'] : '';
        isset($input['exp']) ? $data_arr['exp'] = $input['exp'] : '';
        isset($input['gold']) ? $data_arr['gold'] = $input['gold'] : '';
        isset($input['class_id']) ? $data_arr['class_id'] = $input['class_id'] : '';
        isset($input['father_id']) ? $data_arr['father_id'] = $input['father_id'] : '';
        isset($input['alert_at']) ? $data_arr['alert_at'] = $input['alert_at'] : '';
        isset($input['deadline_at']) ? $data_arr['deadline_at'] = $input['deadline_at'] : '';
//        isset($input['deadline_at']) ?
//            $data_arr_with_deadline = $this->questTypeFilter($input['type'], $input['deadline_at']) :
//            $data_arr_with_deadline = $this->questTypeFilter($input['type']);
//        $data_arr = array_merge($data_arr, $data_arr_with_deadline);
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
            $this->export(200, DB::table('quests')->where('id', $id)->first());
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

    /*
     * @return Array
     */
    private function questTypeFilter($type, $deadline_at = null)
    {
        $data_arr = array();
        switch ($type) {
            case "1":
                $data_arr['deadline_at'] = date('Y-m-d');
                break;
            case "4":
                if ($deadline_at == date('Y-m-d')) {
                    $data_arr['type'] = '1';
                }
                $data_arr['deadline_at'] = $deadline_at;
                break;
            case "0":
            case "2":
            case "3":
            default:
                $data_arr['deadline_at'] = null;
        }
        return $data_arr;
    }
}
