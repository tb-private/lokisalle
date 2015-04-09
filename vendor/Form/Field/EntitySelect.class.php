<?php

namespace Form\Field;

use Form;

class EntitySelect extends Form\Field
{
  protected $maxLength;
  private $options;

  public function buildWidget()
  {
    $widget = '';
    $widget .= '<label>'.$this->label.'</label><select name="'.$this->name.'" name="'.$this->name.'" class="'.$this->name.'">';
    foreach ($this->options as $option=> $value) {
      if ($option == $this->value) {
        $selected = "selected";
      }
      else {
        $selected ='';
      }
      $widget .= "<option value='$option' $selected/>$value</option>";
    }
    return $widget .= ' </select>';
  }

  public function addOption($option)
  {
    $this->options[] = $option;
  }

  public function setOptions($options){
    $entity = $options[0];
    $attribute = 'id';
    $attribute =  $options[1];

    $class = ucfirst($entity);
    $tmp = "Repository\\".$class."Repository";
    $repository = new $tmp;
    $options = $repository->findAll();
    $method = 'get' . ucfirst($attribute);

    foreach ($options as $option) {
      $this->options[$option->getId()] = $option->$method();
    }
  }
}