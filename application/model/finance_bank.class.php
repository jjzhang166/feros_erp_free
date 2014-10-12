<?php

/**
 * 银行管理
 */

namespace model;

class finance_bank extends common {

    public $primary_key = 'id';

    /**
     * 支出
     */
    public function expenditure($money, $id) {
        $data['money[-]'] = abs($money);
        $this->limit(1)->update($data, $id) ? $this->m_operate->success('银行支出变更') : $this->m_operate->failure('银行支出变更');
    }

    /**
     * 收入
     */
    public function income($money, $id) {
        $data['money[+]'] = abs($money);
        $this->limit(1)->update($data, $id) ? $this->m_operate->success('银行收入变更') : $this->m_operate->failure('银行收入变更');
    }

    /**
     * 删除银行
     */
    public function bank_delete($id) {
        if ($this->limit(1)->delete($id)) {
            $this->m_operate->success('删除银行');
            $this->success('删除成功', $this->url->site('finance/bank'));
        } else {
            $this->m_operate->failure('删除银行');
            $this->failure('删除失败', $this->url->site('finance/bank'));
        }
    }

    /**
     * 新增银行
     */
    public function bank_add_submit() {
        $data['bank'] = $this->t_input->post('bank');
        $data['money'] = $this->t_input->post('money')? : 0;
        $data['remark'] = $this->t_input->post('remark');
        $data['default'] = $this->t_input->post('default') ? 1 : 0;
        if (empty($data['bank']))
            $this->failure('请输入银行名称');
        if (!empty($data['default']))
            $this->update(array('default' => 0));
        if ($this->insert($data)) {
            $this->m_operate->success('新增银行');
            $this->success('新增成功', $this->url->site('finance/bank'));
        } else {
            $this->m_operate->failure('新增银行');
            $this->failure('新增失败');
        }
    }

    /**
     * 修改银行
     */
    public function bank_edit_submit() {
        if (!$this->t_input->post('id'))
            $this->failure('参数出错');
        $data['bank'] = $this->t_input->post('bank');


        $money = $this->t_input->post('money')? : 0;
        //$data['money'] =
        if (is_numeric($money)) {
            if ($money > 0)
                $data['money[+]'] = abs($money);
            elseif ($money < 0)
                $data['money[-]'] = abs($money);
        }

        $data['remark'] = $this->t_input->post('remark');
        $data['default'] = $this->t_input->post('default') ? 1 : 0;
        if (empty($data['bank']))
            $this->failure('请输入银行名称');
        if (!empty($data['default']))
            $this->update(array('default' => 0));
        if ($this->limit(1)->update($data, $this->t_input->post('id'))) {
            $this->m_operate->success('修改银行');
            $this->success('修改成功', $this->url->site('finance/bank'));
        } else {
            $this->m_operate->failure('修改银行');
            $this->failure('没有任何修改');
        }
    }

    /**
     * 修改银行排序
     */
    public function bank_sort_submit() {
        if ($this->t_input->post('sort')) {
            $array = explode('|', $this->t_input->post('sort'));
            $count = count($array);
            foreach ($array as $id)
                $this->limit(1)->update(array('sort' => $count--), $id);
            $this->m_operate->success('修改银行排序');
            $this->success('排序成功');
        }
    }

    public function bandk_where($py = NULL) {
        if (!empty($py))
            $this->where(array('p.py' => $py));

        if (!empty($_REQUEST['start'])) {
            $this->where(array('a.money[>=]' => $_REQUEST['start']));
        }
        if (!empty($_REQUEST['end'])) {
            $this->where(array('a.money[<=]' => $_REQUEST['end']));
        }


        if (!empty($_REQUEST['keyword'])) {
            $where['%a.bank%'] = $_REQUEST['keyword'];
            $where['p.py'] = $_REQUEST['keyword'];
            $this->where_or($where);
        }


        $this->order('sort desc');
        $this->left_join('pinyin as p', 'CONV(HEX(LEFT(CONVERT(a.bank USING GBK),1)),16,10) BETWEEN p.begin AND p.end');
        $this->select('p.py,a.*');
        $this->from('finance_bank as a');
        return $this;
    }

}
