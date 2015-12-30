<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use stdClass;

class BagItemsController extends Controller
{

    /**
     * @return Response
     */
    public function getIndex()
    {
        $bag_items = DB::table('bag_items')->get();
        $this->export(200, $bag_items);
    }

    /**
     * @return Response
     */
    public function postIndex()
    {
        $input = Input::all();
        $rules = array(
            'id' => 'required|numeric',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $this->export(403);
            die;
        }
        $item_counts = DB::table('items')->where('id', $input['id'])->count();
        if ($item_counts < 1) {
            $this->export(404);
            die;
        }

        $data_arr = array(
            'item_id' => $input['id'],
            'created_at' => date("Y-m-d H:i:s"),
        );
        $newId = DB::table('bag_items')->insertGetId($data_arr);
        if (is_int($newId)) {
            $this->export(200, DB::table('bag_items')->where('id', $newId)->first());
        } else {
            $this->export(500);
        }
    }

    /**
     * @param  int $id
     * @return Response
     */
    public function putItem($id)
    {
        $input = Input::all();
        $rules = array(
            'price' => 'required|numeric',
            'name' => 'required',
            'img' => 'required',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $this->export(403);
            die;
        }

        $data_arr = array();
        isset($input['price']) ? $data_arr['price'] = $input['price'] : null;
        isset($input['name']) ? $data_arr['name'] = $input['name'] : null;
        isset($input['img']) ? $data_arr['img'] = $input['img'] : null;
        $isCommit = DB::table('items')->where('id', $id)->update($data_arr);
        if ($isCommit > 0) {
            $this->export(200, DB::table('items')->where('id', $id)->first());
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
