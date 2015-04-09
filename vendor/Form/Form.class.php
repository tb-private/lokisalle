<?php

namespace Form;

use Lib;
use Form\Field;
use Entity\Entity;
use Form\Field\Submit;

class Form
{
  protected $entity;
  public $fields = array();
  protected $method;
  protected $action;

  public function __construct($entity = NULL, $method = NULL)
  {
    $this->setEntity($entity);
    $this->method = ($method != 'get')? 'post' : 'get';
  }

  public function add(Field $field, $EntityValue = false){
    if($EntityValue){
      $attr = 'get' . ucfirst($field->name()); // On récupère le nom du champ.
      $field->setValue($this->entity->$attr()); // On assigne la valeur correspondante au champ.
    }
    $this->fields[$field->name()] = $field;
    return $this;
  }

  public function setAction($url){
    if(filter_var($url, FILTER_VALIDATE_URL)){
      $array = parse_url($url);
      $path  = $array['path'];
      $this->action = $path;
    }
    else return false;
  }

  public function toHtml(){
    $html = '<form enctype="multipart/form-data" action="'.$this->action.'" method="' . $this->method . '">';

    foreach ($this->fields as $field)
    {
      $html .= '<div class="field field-'. strtolower(str_replace('Form\\Field\\', '', get_class($field)))  .'">';
      $html .= $field->buildWidget();
      $html .= '</div>';
    }
    $html .= $this->addSubmitHtml();
    $html .= '</form>';
    return $html;
  }

  public function isValid(){
    $valid = true;
    foreach ($this->fields as $field)
    {
      if (!$field->isValid())
      {
        $valid = false;
      }
    }
    return $valid;
  }

  public function hydrate($options){
      foreach ($this->fields as $field)
      {
        if (isset($options[$field->name()]))
        {
          $this->setFieldValue($field->name(), $options[$field->name()]);
        }
      }
      return $this;
  }

  public function getEntity()
  {
    return $this->entity;
  }

  public function setEntity($entity)
  {
    $this->entity = $entity;
  }

  public function checkSubmit(){
    $filter = array_filter(
      $this->fields,
      function ($e) {
        if($e instanceof Submit){
          return true;
        }
      }
    );
    return !empty($filter);
  }

  public function addSubmitHtml(){
    if($this->checkSubmit()){
      return false;
    }
    return '<input type="submit" name="sumbit" class"auto-submit" value="Envoyer"/>';
  }


  public function setFieldValue($name, $value)
  {
    if(is_object($this->fields[$name])){
      $this->fields[$name]->setValue($value);
      return  $this;
    }
    return $this;
  }

  public function remove($name)
  {
    if(is_object($this->fields[$name])){
      unset($this->fields[$name]);
    }
    return $this;
  }

  public function selfCreate(){
    foreach ($this->entity->recordableAttributes as $key => $value) {

      if(!is_numeric($key)){
        $key = 'Form\\Field\\' . $key;
        $this->add(new $key(array(
            'label' => $value,
            'name'  => $value,
        )));
      }

      else{
        $this->add(new Field\Text(array(
            'label' => $value,
            'name'  => $value,
        )));
      }

    }
    return $this;
  }

  public function selfHydrate(){
    $this->hydrate($this->entity->getRecordable());
    return $this;
  }

}