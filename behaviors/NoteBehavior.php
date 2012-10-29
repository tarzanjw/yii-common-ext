<?php

/**
* class thực hiện lấy Note và thêm Note cho một đối tượng
* 
* @property string $note
*/
  class NoteBehavior extends CActiveRecordBehavior
  {
	private $_note;
	/**
  	* kind_id của đối tượng được găn Note , được định nghĩa trước
  	* 
  	* @var mixed
  	*/
  	public $kind;
  	 
	function getNote()
	{
		if (!isset($this->_note)) 
			$this->_note = Yii::app()->Note->getNote($this->kind,$this->getOwner()->getPrimaryKey());
		
		return $this->_note;
	}
	
	function getNoteAsString()
	{
		return print_r($this->getNote(), true);
	}
  
  	function canGetProperty($name)
  	{
  		  switch (strtolower($name)) {
  		  	  case 'note':
  		  	  case 'noteasstring':
  		  	  	return true;
		  }
		  
		  return parent::canGetProperty($name);
	  }
  }

  
