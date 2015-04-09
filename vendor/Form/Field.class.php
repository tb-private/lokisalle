<?php

namespace Form;

use Entity\Entity;

abstract class Field
{
  protected $errorMessage;
  protected $label;
  protected $name;
  protected $value;
  protected $id;
  protected $classes = array();

  public function __construct(array $options = array()){
    if (!empty($options)){
      $this->hydrate($options);
    }
  }

  abstract public function buildWidget();

  public function hydrate($options){
    foreach ($options as $type => $value){
      $method = 'set'.ucfirst($type);
      if (is_callable(array($this, $method))){
        $this->$method($value);
      }
    }
  }

  public function isValid(){
    // On écrira cette méthode plus tard.
  }

  public function label(){
    return $this->label;
  }

  public function name(){
    return $this->name;
  }

  public function value(){
    return $this->value;
  }

  public function id(){
    return $this->id;
  }

  public function setId($id){
    if (is_string($id)){
      $this->id = $id;
    }
  }

  public function classes(){
    return $this->classes;
  }

  public function setClasses($classes){
    if (is_string($classes)){
      $this->classes[] = $classes;
    }
  }

  public function setLabel($label){
    if (is_string($label)){
      $this->label = $label;
    }
  }

  public function setName($name){
    if (is_string($name)){
      $this->name = $name;
    }
  }

  public function setValue($value){
    $this->value = $value;
    return $this;
  }
}