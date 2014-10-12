<?php

/**
 * 日程管理
 */

namespace model;

class calendar extends common {

    public $primary_key = 'id';

  

    public function edit_submit() {
        if (!$this->t_input->post('id'))
            $this->failure('参数出错');

        if ($this->t_input->post('remove')) {
            if ($this->limit(1)->where(array('id' => $this->t_input->post('id'), 'uid' => $this->get_uid()))->delete()) {
                $this->m_operate->success('删除日程');
                $this->success('删除成功', $this->url->site('home/calendar'));
            } else {
                $this->m_operate->failure('删除日程');
                $this->failure('删除失败');
            }
        }




        $events = stripslashes(trim($_POST['event'])); //事件内容
        $events = strip_tags($events); //过滤HTML标签，并转义特殊字符

        $isallday = $_POST['isallday']; //是否是全天事件
        $isend = $_POST['isend']; //是否有结束时间

        $startdate = trim($_POST['startdate']); //开始日期
        $enddate = trim($_POST['enddate']); //结束日期

        $s_time = $_POST['s_hour'] . ':' . $_POST['s_minute'] . ':00'; //开始时间
        $e_time = $_POST['e_hour'] . ':' . $_POST['e_minute'] . ':00'; //结束时间

        if ($isallday == 1 && $isend == 1) {
            $starttime = strtotime($startdate);
            $endtime = strtotime($enddate);
        } elseif ($isallday == 1 && $isend == "") {
            $starttime = strtotime($startdate);
        } elseif ($isallday == "" && $isend == 1) {
            $starttime = strtotime($startdate . ' ' . $s_time);
            $endtime = strtotime($enddate . ' ' . $e_time);
        } else {
            $starttime = strtotime($startdate . ' ' . $s_time);
        }
        if (empty($events))
            $this->failure('请输入日程内容');

        $data['uid'] = $this->get_uid();
        $data['title'] = $events;
        $data['starttime'] = $starttime;
        $data['endtime'] = $endtime;
        $data['allday'] = $isallday;
        $data['color'] = $_POST['color']? : '';

        if ($this->limit(1)->where(array('id' => $this->t_input->post('id'), 'uid' => $this->get_uid()))->update($data)) {
            $this->m_operate->success('更新日程');
            $this->success('更新成功', $this->url->site('home/calendar'));
        } else {
            $this->m_operate->failure('更新日程');
            $this->failure('更新失败');
        }
    }

    public function add_submit() {
        $events = stripslashes(trim($_POST['event'])); //事件内容
        $events = strip_tags($events); //过滤HTML标签，并转义特殊字符

        $isallday = $_POST['isallday']; //是否是全天事件
        $isend = $_POST['isend']; //是否有结束时间

        $startdate = trim($_POST['startdate']); //开始日期
        $enddate = trim($_POST['enddate']); //结束日期

        $s_time = $_POST['s_hour'] . ':' . $_POST['s_minute'] . ':00'; //开始时间
        $e_time = $_POST['e_hour'] . ':' . $_POST['e_minute'] . ':00'; //结束时间

        if ($isallday == 1 && $isend == 1) {
            $starttime = strtotime($startdate);
            $endtime = strtotime($enddate);
        } elseif ($isallday == 1 && $isend == "") {
            $starttime = strtotime($startdate);
        } elseif ($isallday == "" && $isend == 1) {
            $starttime = strtotime($startdate . ' ' . $s_time);
            $endtime = strtotime($enddate . ' ' . $e_time);
        } else {
            $starttime = strtotime($startdate . ' ' . $s_time);
        }
        if (empty($events))
            $this->failure('请输入日程内容');

        $data['uid'] = $this->get_uid();
        $data['title'] = $events;
        $data['starttime'] = $starttime;
        $data['endtime'] = $endtime;
        $data['allday'] = $isallday;
        $data['color'] = $_POST['color']? : '';

        if ($this->insert($data)) {
            $this->m_operate->success('新增日程');
            $this->success('新增成功', $this->url->site('home/calendar'));
        } else {
            $this->m_operate->failure('新增日程');
            $this->failure('新增失败');
        }
    }

    public function drag_submit() {
        $start = ($_REQUEST['start'] / 1000) - 28800;
        $start = strtotime(date('Y-m-d H:i', $start));
        $list = $this->find($this->t_input->post('id'));
        if (!empty($list)) {
            $data['starttime'] = $start;

            $time = $start - $list['starttime'];
            if (!empty($time)) {
                if (!empty($data['endtime'])) {
                    if ($time > 0)
                        $data['endtime'] = $list['endtime'] + $time;
                    elseif ($time < 0)
                        $data['endtime'] = $list['endtime'] - $time;
                }
                //$this->failure(date('Y-m-d H:i', $data['starttime']) . ' ' . date('Y-m-d H:i', $data['endtime']));

                if ($this->update($data, $list['id'])) {
                    $this->m_operate->success('更新日程');
                    $this->success('更新成功');
                } else {
                    $this->m_operate->failure('更新日程');
                }
            }
        }
        $this->failure('没有任何修改');
    }

    public function get_json() {
        $where['uid'] = $this->get_uid();
        $where['starttime[>=]'] = strtotime($this->t_input->request('start'));
        $where['endtime[<=]'] = strtotime($this->t_input->request('end'));
        $data = array();
        foreach ($this->where($where)->finds() as $row) {
            $data[] = array(
                'id' => $row['id'],
                'title' => $row['title'],
                'start' => date('Y-m-d H:i', $row['starttime']),
                'end' => !empty($row['endtime']) ? date('Y-m-d H:i', $row['endtime']) : NULL,
                //'url' => $row['url'],
                'allDay' => (!empty($row['allday']) ? TRUE : FALSE),
                'color' => !empty($row['color']) ? $row['color'] : NULL
            );
        }

        return json_encode($data);
    }

}
