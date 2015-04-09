<?php

namespace Form\Field;
use Form;

class Text extends Form\Field
{
  protected $maxLength;

  public function buildWidget()
  {
    $widget = '';

    if (!empty($this->errorMessage)) {
      $widget .= $this->errorMessage.'<br />';
    }

    $widget .= '<label>'.$this->label.'</label><input type="text" name="'.$this->name.'" name="'.$this->name.'" class="'.$this->name.'"';

    if (!empty($this->value)) {
      $widget .= ' value="'.htmlspecialchars($this->value).'"';
    }

    if (!empty($this->maxLength)) {
      $widget .= ' maxlength="'.$this->maxLength.'"';
    }

    $widget .= ' />';
    return $widget ;
  }

  public function setMaxLength($maxLength)
  {
    $maxLength = (int) $maxLength;

    if ($maxLength > 0){
      $this->maxLength = $maxLength;
    } else {
      throw new \RuntimeException('La longueur maximale doit être un nombre supérieur à 0');
    }
  }
}