<?php

namespace controller;

class json extends common {

    public function __construct() {
        parent::__construct();
        header('Content-Type:application/json;charset=utf-8');
    }

    public function finance_categoryAction() {
        echo $this->m_finance_category->json();
    }

    public function menuAction($id = null) {
        echo $this->m_menu->json($id);
    }

    public function cityAction() {
        echo $this->m_city->json();
    }

    public function productAction() {
        echo $this->m_product->json();
    }
    public function memberAction() {
        echo $this->m_member->json();
    }
    

}
