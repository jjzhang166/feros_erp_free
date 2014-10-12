<?php

/**
 * 销售提示
 */

namespace model;

class staff_wage extends common {

    public $primary_key = 'id';

    public function add($orders, $list, $sales) {


        if (empty($list))
            return;
        if (empty($list['deduct']))
            return;

        $deduct_type = $list['deduct_type'];
        if ($deduct_type === '2') {
            $deduct = $list['deduct'];
        } elseif ($deduct_type === '1') {
            $deduct = $sales * ($list['deduct'] / 100);
        }
        if (!empty($deduct) && $deduct > 0) {

            $data['uid'] = $this->get_uid();
            $data['time'] = time();
            $data['orders'] = $orders;
            $data['wage'] = $deduct;
            $data['basic'] = $this->m_staff->get_info_key($this->get_uid(), 'wage');
            $this->insert($data);
        }
    }

    public function wage_where() {

        if ($this->t_input->request('timea'))
            $this->where(array('a.time[>=]' => strtotime($this->t_input->request('timea') . ' 00:00:00')));
        if ($this->t_input->request('timeb'))
            $this->where(array('a.time[<=]' => strtotime($this->t_input->request('timeb') . ' 23:59:59')));


        if (isset($_REQUEST['staff_uid']))
            $this->where(array('a.uid' => $this->t_input->request('staff_uid')));



        $this->left_join('staff as s', 's.uid=a.uid');

        $this->select('a.*');

        $this->order('a.id desc');
        $this->from('staff_wage as a');
        return $this;
    }

    public function chart($day) {

        $list = $this->finds();
        $d = array();
        for ($index = 0; $index < $day; $index++) {
            $date = date('Y-m-d', strtotime($_REQUEST['timea']) + ($index * 86400));
            $datea = strtotime($date . ' 00:00:00');
            $dateb = strtotime($date . ' 23:59:59');
            $d['wage'][$index + 1] = $this->get_charttime($list, $datea, $dateb);
            $d['basic'][$index + 1] = $this->get_basic($list, $datea, $dateb);
            $d['date'][$index + 1] = "'{$date}'";
        }


        return $d;
    }

    private function get_basic($list, $datea, $dateb) {
        $vars = 0;
        foreach ($list as $var) {
            if ($var['time'] >= $datea && $var['time'] <= $dateb)
                $vars = $var['basic'];
        }
        return $vars;
    }

    private function get_charttime($list, $datea, $dateb) {
        $vars = 0;
        foreach ($list as $var) {
            if ($var['time'] >= $datea && $var['time'] <= $dateb)
                $vars+= $var['wage'];
        }
        return $vars;
    }

}
