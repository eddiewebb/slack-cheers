<?php


//TODO: see http://www.redbeanphp.com/index.php?p=/models to implement as rb model with validation.


class Cheer extends \RedBeanPHP\SimpleModel{
        public function open() {
           error_log("called open: ".$this->id);
        }
        public function dispense() {
           error_log("dispensed: ".$this->bean);
        }
        public function update() {
            global $lifeCycle;
            $lifeCycle .= "called update() ".$this->bean;
        }
        public function after_update() {
            global $lifeCycle;
            $lifeCycle .= "called after_update() ".$this->bean;
        }
        public function delete() {
            global $lifeCycle;
            $lifeCycle .= "called delete() ".$this->bean;
        }
        public function after_delete() {
            global $lifeCycle;
            $lifeCycle .= "called after_delete() ".$this->bean;
        }

}

