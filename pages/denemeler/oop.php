<?php
class Person{
    private $name;
    private $age;
    private $gender;


    public function __construct($name, $age, $gender){
        $this->name = $name;
        $this->age = $age;
        $this->gender = $gender;
    }

    public function getName(){
        return $this->name;
    }
    public function getAge(){
        return $this->age;
    }
    public function getGender(){
        return $this->gender;

    }
    function yasa(){
        echo "Senin adın : $this->name <br>";
        echo "Senin yaşın : $this->age <br>";
        echo "Senin cinsiyetin : $this->gender <br>";
    }

}
$baby = new Person("Babycik", "2", "kadın");


?>