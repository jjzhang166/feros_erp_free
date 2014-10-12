<?php

namespace controller;

class login extends \base\controller {

    protected $base;

    public function __construct() {
        $this->assign('base', $this->base = $this->m_common->get_base());
        $this->config->view->trace = false;
    }

    public function indexAction() {
        if ($this->tool->verify->is_post())
            return $this->m_staff->login();

        if (is_array($this->get_info())) {
            $this->display('login/patternlock');
        }

//        else {
//            echo '<script type="text/javascript">
//    var closeHandler = function () {
//        ferosClient.close();
//    }
//    addEventListener("ferosClientReady", closeHandler);
//</script>';
//            //$this->t_header->redirect(base64_decode('aHR0cDovL2Zlcm9zLmNvbS5jbi8='));
//        }
        else {
            if ($this->t_verify->is_mobile()) {
                $this->display('login/mobile');
            } else {
                $this->display('login/index');
            }
        }
    }

    public function lockAction() {
        if (is_array(($staff = $this->get_info()))) {
            if ($this->t_input->post('pattern') && !empty($staff['patternlock']) && $staff['patternlock'] === $this->t_input->post('pattern')) {
                $info = $this->cookie->get('staffpassword', true);
                $_SESSION['uid'] = $info[0];
                $_SESSION['password'] = $info[1];
                $this->m_operate->success('登录系统=>手势解锁');
                $this->m_common->success("解锁成功", $this->url->site('index'));
            }
        }
        $this->m_common->failure("解锁密码错误");
    }

    private function get_info() {
        $info = $this->cookie->get('staffpassword', true);
        if (is_array($info) && count($info) === 2) {
            $staff = $this->m_staff->get_info($info[0], $info[1]);
            if (!empty($staff['patternlock'])) {
                $this->assign('staff', $staff);

                return $staff;
            }
        }
        return false;
    }

    public function quitAction($lock = NULL) {
        $this->m_staff->quit($lock);
    }

    public function codeAction() {
        $this->t_code->width = 118;
        $this->t_code->height = 30;
        $this->t_code->font_size = 12;
        $this->t_code->background = '#ffffff';
        $this->t_code->doimage();
    }

    public function qrcodeAction() {
        $this->display();
    }

    public function clientAction() {


        $this->display();
    }

}
