<?php

namespace model;

/**
 * 员工管理
 * @author feisanliang
 */
class staff extends common {

    static $staff_info = array();
    public $primary_key = 'uid';

    /**
     * 返回登录信息
     * @param int $uid
     * @return null|array
     */
    public function get_info($uid = NULL, $pass = NULL) {

        $pid = $uid? : $_SESSION['uid'];
        $pass = $pass? : $_SESSION['password'];
        if (!empty($pid) && ($vars = $this->find($pid))) {
            if (md5($vars['password']) === $pass) {
                self::$staff_info[$pid] = $vars;
                return $vars;
            }
        }
        return NULL;
    }

    public function get_info_key($id, $key = NULL) {
        if (!isset(self::$staff_info[$id])) {
            self::$staff_info[$id] = $this->find($id);
        }
        return $key && isset(self::$staff_info[$id][$key]) ? self::$staff_info[$id][$key] : self::$staff_info[$id];
    }

    /**
     * 登录
     */
    public function login() {
        if ($this->get_base('loginqrcode')) {
            if (!$this->t_input->post('verifycode'))
                $this->failure("请输入验证码");
            if (!$this->t_code->check($this->t_input->post('verifycode')))
                $this->failure("验证码不正确");
        }

        if (isset($_SESSION['error']) && $_SESSION['error'] > 10) {
            $this->failure("错误次数太多，请关闭重来");
        }
        $account = $this->t_input->post('account');
        $password = $this->t_input->post('password');

        if (empty($account))
            $this->failure("请输入登录账号");
        if ($this->t_verify->is_email($account)) {
            $where['email'] = $account;
            $type = '邮箱';
        } elseif ($this->t_verify->is_telnumber($account)) {
            $where['mobile'] = $account;
            $type = '手机号';
        } elseif (is_numeric($account)) {
            $where['uid'] = $account;
            $type = '员工号';
        } else {
            $where['username'] = $account;
            $type = '账号';
        }
        $where['status'] = 1;
        $_SESSION['error'] = isset($_SESSION['error']) ? $_SESSION['error'] + 1 : 1;

        $vars = $this->where($where)->find();
        if (!empty($vars['uid'])) {
            if ($vars['password'] === md5($password)) {
                if ($this->get_base('LAN') && !$this->t_verify->is_ipprivate()) {
                    $this->m_operate->failure('登录系统=>不在局域网', $vars['uid']);
                    $this->failure("仅限局域网登录 你的IP是:" . $this->t_client->ip());
                }
                $_SESSION['uid'] = $vars['uid'];
                $_SESSION['password'] = md5($vars['password']);

                $this->cookie->set('staffpassword', array($_SESSION['uid'], $_SESSION['password']), true, 2592000);
                
                $this->m_operate->success('登录系统');

                if ($this->t_input->request('feros_client')) {
                    $this->success('登录成功', $this->url->site('index/client?session_id=' . session_id()));
                } else {
                    $this->success('登录成功', $this->url->site('index'));
                }
            } else {
                $this->m_operate->failure('登录系统=>密码不正确', $vars['uid']);
                $this->failure("登录密码不正确,错误{$_SESSION['error']}次");
            }
        } else {
            $this->failure("登录{$type}不存在,错误{$_SESSION['error']}次");
        }
    }

    public function password_gesture() {
        if ($this->t_input->post('pattern') && is_numeric($this->t_input->post('pattern'))) {
            if (strlen($this->t_input->post('pattern')) < 3)
                $this->failure("手持密码至少3个触点");
            if ($this->limit(1)->update(array('patternlock' => $this->t_input->post('pattern')), $uid)) {
                $this->m_operate->success('修改手势');
                $this->success('修改成功', $this->url->site('home/password'));
            } else {
                $this->m_operate->failure('修改手势');
                $this->failure("没有任何修改");
            }
        }
        $this->failure("参数出错");
    }

    public function change_password($uid = NULL) {
        $old_password = $this->t_input->post('old_password');
        $new_password = $this->t_input->post('new_password');
        $con_password = $this->t_input->post('con_password');
        if (empty($old_password))
            $this->failure("请输入旧密码");
        if (empty($new_password))
            $this->failure("请输入新密码");
        if (empty($con_password))
            $this->failure("请输入确认密码");
        if ($new_password !== $con_password)
            $this->failure("新密码两次不一样");
        $uid = $uid? : $this->get_uid();
        if (!empty($uid) && ($vars = $this->find($uid))) {
            if (md5($old_password) === $vars['password']) {
                if ($this->limit(1)->update(array('password' => md5($new_password)), $uid)) {
                    $this->m_operate->success('修改密码');
                    $this->success('修改成功', $this->url->site('home/password'));
                } else {
                    $this->m_operate->failure('修改密码');
                    $this->failure("没有任何修改");
                }
            }
            $this->failure("旧密码不正确");
        }
    }

    /**
     * 返回当前用户uid
     * @return int
     */
    public function get_uid() {
        return $_SESSION['uid'];
    }

    /**
     * 退出登录
     */
    public function quit($lock = NULL) {
        if (empty($lock)) {
            $this->cookie->delete('staffpassword');
        }
        $this->m_operate->success('退出登录');
        session_destroy();

        $this->success('操作成功', $this->url->site('login/index'));
    }

    public function staff_where($py = NULL) {
        if (!empty($py)) {
            $this->where(array('p.py' => $py));
            $this->left_join('pinyin as p', 'CONV(HEX(LEFT(CONVERT(a.realname USING GBK),1)),16,10) BETWEEN p.begin AND p.end');
        }

        if (isset($_REQUEST['status']))
            $this->where(array('a.status' => $this->t_input->request('status')));
        if ($this->t_input->request('c_id'))
            $this->where(array('a.c_id' => $this->t_input->request('c_id')));
        if (!empty($_REQUEST['keyword'])) {
            $where['%a.realname%'] = $_REQUEST['keyword'];
            $where['%a.username%'] = $_REQUEST['keyword'];
            $where['%a.email%'] = $_REQUEST['keyword'];
            $where['%a.mobile%'] = $_REQUEST['keyword'];
            $this->where_or($where);
        }
        $this->order('a.uid asc');
        $this->left_join('staff_category as c', 'a.c_id = c.id');


        $this->select('a.*,c.name as category');
        $this->from('staff as a');
        return $this;
    }

    public function delete_submit($id) {
        if ($this->get_uid() === '1')
            $this->failure("免费版不提供该功能");
        if ($this->limit(1)->where(array('uid[!]' => $this->get_uid()))->delete($id)) {
            $this->m_operate->success('删除员工');
            $this->success('删除成功');
        } else {
            $this->m_operate->failure('删除员工');
            $this->failure('删除失败');
        }
    }

    public function edit_submit() {
        $this->failure("免费版不提供该功能");
    }

    public function add_submit() {
        if (!is_numeric($this->t_input->post('c_id')))
            $this->failure("请选择员工部门");
        if (!$this->t_input->post('realname'))
            $this->failure("请输入真实姓名");
        if (!$this->t_input->post('username'))
            $this->failure("请输入登录账号");
        if (!$this->t_input->post('password'))
            $this->failure("请输入登录密码");


        $data['status'] = $this->t_input->post('status');
        $data['c_id'] = $this->t_input->post('c_id');
        $data['username'] = $this->t_input->post('username');
        $data['mobile'] = $this->t_input->post('mobile');
        $data['email'] = $this->t_input->post('email');
        $data['wage'] = $this->t_input->post('wage');
        $data['password'] = md5($this->t_input->post('password'));
        $data['realname'] = $this->t_input->post('realname');
        $data['remark'] = $this->t_input->post('remark');
        $data['create'] = time();

        if ($this->where(array('username' => $data['username']))->find())
            $this->failure('登录账号已存在');
        if (!empty($data['email']) && $this->where(array('email' => $data['email']))->find())
            $this->failure('邮箱已存在');
        if (!empty($data['mobile']) && $this->where(array('mobile' => $data['mobile']))->find())
            $this->failure('手机号已存在');
        if (($insert_id = $this->insert($data))) {
            foreach ($this->t_input->post('menu') as $val) {
                if (is_numeric($val)) {
                    $value[] = array(
                        'uid' => $insert_id,
                        'm_id' => (int) $val
                    );
                }
            }
            if (!empty($value))
                $this->m_staff_menu->insert_batch($value);
            $this->m_operate->success('新增员工');
            $this->success('新增成功', $this->url->site('system/staff'));
        } else {
            $this->m_operate->failure('新增员工');
            $this->failure('新增失败');
        }
    }

    public function status($key = NULL) {
        static $sta = array(1 => '正常', 0 => '锁定', -1 => '离职');
        return isset($sta[$key]) ? $sta[$key] : $sta;
    }

    public function input_select($checked = array()) {
        $items = $this->staff_where()->finds();
        $tmpMap = array();

        foreach ($items as $item) {
            $tmpMap[$item['c_id']]['category'] = $item['category'];
            $tmpMap[$item['c_id']]['list'][] = $item;
        }
        $select = '';
        foreach ($tmpMap as $key => $value) {
            $select.="<optgroup label=\"{$value['category']}\">";
            foreach ($value['list'] as $var) {
                $select.="<option value=\"{$var['uid']}\"" . (!empty($checked) && in_array($var['uid'], $checked) ? ' selected' : '') . ">{$var['realname']}</option>";
            }
            $select.="</optgroup>";
        }
        return $select;
    }

}
