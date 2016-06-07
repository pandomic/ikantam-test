<?php
namespace Engine;


class TestClass
{
    protected $id;
    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }
}