<?php

/**
 * 菜单管理
 */

namespace model;

class member_points extends common {

    public $primary_key = 'id';

    public function add($member, $type, $title, $value, $m_id = NULL) {
        $data['uid'] = $this->get_uid();
        $data['member'] = $member;
        $data['type'] = $type;
        $data['title'] = $title;
        $data['value'] = $value;
        $data['time'] = time();
        $data['m_id'] = $m_id;
        $this->insert($data);
    }

}
