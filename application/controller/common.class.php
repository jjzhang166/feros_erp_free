<?php

namespace controller;

class common extends \base\controller {

    protected $member, $uid, $base;

    public function __construct() {
        $this->member = $this->m_staff->get_info();
        if (empty($this->member))
            $this->t_header->redirect($this->url->site('login'));
        $this->uid = $this->member['uid'];
        
        $this->assign('base', $this->base = $this->m_common->get_base());
        $this->assign('member', $this->member);
        $this->assign('pinyin', array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'W', 'X', 'Y', 'Z'));
    }

    protected function get_day() {
        return 86400 * ($this->m_common->get_base('days')? : 30);
    }

}
