<?php

class Model extends SQLite3 {

    private $atributos = array();

    public function __construct(){
        parent::__construct('../db/mochinho.sqlite3');
    }

    public function __get($nome){
        return @$this->atributos[$nome];
    }

    public function __set($nome, $valor){
        $this->atributos[$nome] = $valor;
    }

}

?>
