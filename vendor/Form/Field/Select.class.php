<?php

namespace Form\Field;
use Form;

class Select extends Form\Field
{
  protected $maxLength;
  private $options;

  public function buildWidget()
  {
    $widget = '';
    $widget .= '<label>'.$this->label.'</label><select name="'.$this->name.'" name="'.$this->name.'" class="'.$this->name.'">';
    foreach ($this->options as $option=> $value) {
      if ($value == $this->value) {
        $selected = "selected";
      }
      else $selected ='';
      $widget .= "<option value='$value' $selected/>$value</option>";
    }
    return $widget .= ' </select>';
  }

  public function addOption($option)
  {
    $this->options[] = $option;
  }

  public function setOptions($options){
    foreach ($options as $option) {
      $this->options[] = $option;
    }
  }
}