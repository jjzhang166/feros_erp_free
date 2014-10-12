<?php

/**
 * 菜单管理
 */

namespace model;

class city extends common {

    public $primary_key = 'id';

    public function json() {
        $json = $this->cache->file->get('cityjson');
        if (empty($json)) {
            $items = $this->city_where()->finds();
            $json = json_encode($this->gen_tree($items, 'v'));
            $this->cache->file->set('cityjson', $json);
        }
        return $json;
    }


    public function city_where() {
        $this->order('p.py asc');
        $this->left_join('pinyin as p', 'CONV(HEX(LEFT(CONVERT(a.name USING GBK),1)),16,10) BETWEEN p.begin AND p.end');
        $this->select('a.id as v,a.name as n,a.pid');
        $this->from('city as a');
        return $this;
    }

}
