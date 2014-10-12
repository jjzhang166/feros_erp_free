<?php

/**
 * 消息管理
 */

namespace model;

class remind extends common {

    public $primary_key = 'id';

    public function get_remind() {
        $where['uid'] = $this->get_uid();
        $where['status'] =0;
        $var = $this->where($where)->find();
        if (!empty($var)) {
            $this->limit(1)->update(array('status' => 1), $var['id']);
            $this->success($var['message']);
        } else {
            $this->failure('没有任何消息');
        }
    }

}
