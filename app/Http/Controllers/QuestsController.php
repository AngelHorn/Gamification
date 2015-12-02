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
        $rules = array();
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $this->export(40003);
            die;
        }
        $data_arr = array(
          'text'=>$input['text'],
          'note'=>"",
          'exp'=>0,
          'gold'=>0,
          'state'=>0,
          'type'=>$input['type'],
          'deadline_at'=>null,
          'alert_at'=>null,
          'class_id'=>null,
          'father_id'=>null,
        );
        $newId = DB::table('quests')->insertGetId($data_arr);
        if (is_int($newId)) {

            $this->export(200,DB::table('quests')->where('id',$newId)->first());
        } else {
            $this->export(50000);
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
            $this->export(40400);
        }
    }

    /**
     * @param  int $id
     * @return Response
     */
    public function putQuest($id)
    {
        $input = Input::all();
        $rules = array();
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $this->export(40003);
            die;
        }
        $data_arr = $input;
        $isCommit = DB::table('quests')->where('id', $id)->update($data_arr);
        if ($isCommit > 0) {
            $this->export(200);
        } else {
            $this->export(50000);
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
            $this->export(50000);
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
