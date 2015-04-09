<?php

namespace Form\Field;
use Form;

class Textarea extends Form\Field
{
  protected $maxLength;

  public function buildWidget()
  {
    $widget = '';

    if (!empty($this->errorMessage)) {
      $widget .= $this->errorMessage.'<br />';
    }

    $widget .= '<textarea rows="4" cols="50" name="'.$this->name.'" name="'.$this->name.'" id="'.$this->name.'"';

    if (!empty($this->maxLength)) {
      $widget .= ' maxlength="'.$this->maxLength.'"';
    }

    $widget .= '>';

    if (!empty($this->value)) {
      $widget .= htmlspecialchars($this->value);
    }

    return $widget .= ' </textarea>';
  }

  public function setMaxLength($maxLength)
  {
    $maxLength = (int) $maxLength;

    if ($maxLength > 0) {
      $this->maxLength = $maxLength;
    } else {
      throw new \RuntimeException('La longueur maximale doit être un nombre supérieur à 0');
    }
  }
}