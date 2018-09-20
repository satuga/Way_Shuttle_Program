<?php
/************************************************************
FILE :	MyPDOManagerDA.php
PURPOSE: parking database transactions
AUTHOR: Gopalan Mani
DATE  : 25 FEB 2015
**************************************************************/
class MyPDOManagerDA extends PDO{

    const DB_HOST='192.168.168.37';
    const DB_PORT='3306';
    const DB_NAME='way_liveservernew';
    const DB_USER='root';
    const DB_PASS='Sapconf18';

    public function __construct($options=null){
        parent::__construct('mysql:host='.MyPDO::DB_HOST.';port='.MyPDO::DB_PORT.';dbname='.MyPDO::DB_NAME,
                            MyPDO::DB_USER,
                            MyPDO::DB_PASS,$options);
    }

    public function query($query){ //secured query with prepare and execute
        $args = func_get_args();
        array_shift($args); //first element is not an argument but the query itself, should removed

        $reponse = parent::prepare($query);
        $reponse->execute($args);
        return $reponse;

    }

    public function insecureQuery($query){ //you can use the old query at your risk ;) and should use secure quote() function with it
        return parent::query($query);
    }

}
?>