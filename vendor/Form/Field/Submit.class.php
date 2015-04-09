<?php

namespace Form\Field;

use Form;

class Submit extends Form\Field
{

  public function buildWidget(){
    $html = '<input type="submit" name="'.$this->name.'"';
    if (!empty($this->value)) {
      $html .= ' value="'.htmlspecialchars($this->value).'"';
    }
    if (!empty($this->id)) {
      $html .= ' value="'. htmlspecialchars($this->id).'"';
    }
    if (empty($this->class)) {
      $this->class = array();
    }
     $html .= ' class=" '. $this->name. ' ' . htmlspecialchars(implode(' ', $this->class)) .'"' ;
    return $html .= ' />';
  }

}