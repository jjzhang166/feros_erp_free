<?php

/**
 * 日志管理
 */

namespace model;

class operate extends common {

    public $primary_key = 'id';

    public function add($title, $status, $uid = NULL) {

        $data['uid'] = $uid? : $this->get_uid();
        if (empty($data['uid']))
            return;
        $ip = $this->tool->ip->info();
        $data['title'] = $title;
        $data['status'] = (int) $status;
        $data['time'] = time();
        $data['ip'] = $ip['ip'];
        $data['country'] = $ip['country'];
        $data['client'] = $this->t_verify->is_mobile() ? 'mobile' : 'pc';
        $data['area'] = $ip['area'];
        $data['url'] = $this->router->get_path_info();
        $data['data'] = serialize($_POST);
        $this->from('operate_' . $this->hash_db($data['uid']))->insert($data);
    }

    public function success($title, $uid = NULL) {
        $this->add($title, 1, $uid);
    }

    public function failure($title, $uid = NULL) {
        $this->add($title, 0, $uid);
    }

    public function logwhere($py = NULL, $uid = NULL) {
         if ($this->t_input->request('timea'))
            $this->where(array('a.time[>=]' => strtotime($this->t_input->request('timea'). ' 00:00:00')));
        if ($this->t_input->request('timeb'))
            $this->where(array('a.time[<=]' => strtotime($this->t_input->request('timeb'). ' 23:59:59')));
        
        
        if (!empty($py)) {
            $this->where(array('p.py' => $py));
            $this->left_join('pinyin as p', 'CONV(HEX(LEFT(CONVERT(a.title USING GBK),1)),16,10) BETWEEN p.begin AND p.end');
        }

        if (empty($uid)) {
            if (!empty($_REQUEST['staff_uid']) && is_numeric($_REQUEST['staff_uid']))
                $uid = $_REQUEST['staff_uid'];
            else
                $uid = $this->get_uid();
        }

        $this->where(array('a.uid' => $uid));
        if (is_numeric($this->t_input->request('status')))
            $this->where(array('a.status' => $this->t_input->request('status')));

       


        if ($this->t_input->request('keyword')) {
            $where['%title%'] = $this->t_input->request('keyword');
            $where['%ip%'] = $this->t_input->request('keyword');
            $where['%country%'] = $this->t_input->request('keyword');
            $this->where_or($where);
        }
        $this->order('id desc');


        $this->from('operate_' . $this->hash_db($uid) . ' as a')->select('a.*,m.realname')->left_join('staff as m', 'a.uid=m.uid');
        return $this;
    }

}
