<?php

/**
 * 菜单管理
 */

namespace model;

class menu extends common {

    static $vars;
    public $primary_key = 'id';
    private $formats, $meun, $select;

    public function menu_delete($id) {
        if ($this->where(array('parentid' => $id, 'lock' => 0))->find())
            $this->failure('请先删除子目录');
        if ($this->where(array('id' => $id, 'lock' => 0))->limit(1)->delete()) {
            $this->m_operate->success('删除系统菜单');
            $this->success('删除成功');
        } else {
            $this->m_operate->failure('删除系统菜单');
            $this->failure('删除失败');
        }
    }

    public function add_submit() {
        $this->failure("免费版不提供该功能");
    }

    public function edit_submit() {
        $this->failure("免费版不提供该功能");
    }

    public function menu_submit() {
        if ($this->t_input->post('sort')) {
            $array = explode('|', $this->t_input->post('sort'));
            $count = count($array);
            foreach ($array as $id) {
                $sql[] = $this->limit(1)->update(array('sort' => $count--), $id, true);
            }
            if (!empty($sql)) {
                $this->exec(implode(";", $sql));
            }
            $this->m_operate->success('修改菜单排序');
            $this->success('排序成功');
        }
    }

    public function json($id = NULL) {
        $where['grade'] = array(1, 2);
        if (!empty($id))
            $where['id[!]'] = $id;
        $array = $this->where($where)->finds();
        $i = 0;
        $json[] = array('v' => '0', 'n' => '一级目录');
        foreach ($array as $value) {
            if ($value['grade'] == 1) {
                $i++;
                $json[$i]['v'] = $value['id'];
                $json[$i]['n'] = $value['name'];
                $json[$i]['s'][] = array('v' => '0', 'n' => '二级目录');
                foreach ($array as $var) {
                    if ($var['parentid'] === $value['id'])
                        $json[$i]['s'][] = array('v' => $var['id'], 'n' => $var['name']);
                }
            }
        }
        return json_encode($json);
    }

    public function get_formats() {
        return $this->gen_tree($this->menu_where()->finds(), 'id', 'parentid');
    }

    public function menu_where() {

        $this->order('sort desc');

        return $this;
    }

    public function gets() {
        return self::$vars? : self::$vars = $this->where(array('status' => 1))->order('sort desc')->finds();
    }

    public function get_find_tree() {
        return $this->gen_tree($this->gets(), 'id', 'parentid');
    }

    /**
     * 返回菜单 json
     */
    public function get_bui() {
        $authority = $this->m_authority->menu();
        foreach ($this->get_find_tree() as $key => $vars) {
            $menu[$key]['id'] = $vars['controller'];
            $menu[$key]['homePage'] = $vars['home'];
            if (is_array($vars['s'])) {
                foreach ($vars['s'] as $key2 => $value) {
                    $menu[$key]['menu'][$key2]['text'] = $value['name'];
                    if (is_array($value['s'])) {
                        foreach ($value['s'] as $key3 => $value3) {
                            foreach ($authority as $val) {
                                if (($value3['controller'] === $val['controller'] && $value3['action'] === $val['action']) || $this->get_uid() === '1')
                                    $menu[$key]['menu'][$key2]['items'][$key3] = array(
                                        'id' => $value3['controller'] . $value3['action'],
                                        'text' => $value3['name'],
                                        'href' => $this->url->site("{$value3['controller']}/{$value3['action']}"),
                                        'closeable' => $value3['closeable'] ? true : false,
                                    );
                            }
                        }
                        if (empty($menu[$key]['menu'][$key2]['items']))
                            unset($menu[$key]['menu'][$key2]);
                    }
                }
                if (empty($menu[$key]['menu'][$key2]))
                    unset($menu[$key]['menu'][$key2]);

                if (empty($menu[$key]['menu']))
                    unset($menu[$key]);else
                        $m[]=$vars['id'];
            }
        }
        foreach ($menu as $value) {
            $e[]=$value;
        }
        return array($m,$e);
    }

}
