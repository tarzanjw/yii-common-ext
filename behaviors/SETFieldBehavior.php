<?php

/**
* Class này phục vụ việc chuyển đổi 1 thuộc tính kiểu SET của MySQL giữa dạng string của MySQL và dạng array của PHP
* 	1. ActiveRecord sẽ cấu hình một danh sách các field kiểu SET {@link $fields}
* 
* @property string|array $explodeSeparator 1+ xâu dùng để tách các giá trị, mặc định là ','
* 
* @author Tarzan <hocdt85@gmail.com>
*/
class SETFieldBehavior extends CActiveRecordBehavior
{
	/**
	* Danh sách những fields bị ảnh hưởng
	* 
	* @var array
	*/
	public $fields = array();
	
	/**
	* Có bỏ qua các giá trị rỗng hay không?
	* 
	* @var boolean
	*/
	public $ignoreEmptyValue = true;
	
	/**
	* xâu dùng để ghép các giá trị, mặc định là ','
	* 
	* @var string
	*/
	public $implodeSeparator = ',';
	
	/**
	* @var array
	*/
	private $_explodeSeparator = array(',');
	
	/**
	* Thiết lập giá trị ngăn cách xâu
	* 
	* @param string|array $value danh sách các giá trị ngăn cách xâu, nếu là 1 giá trị thì đc coi là danh sách có 1 phần tử
	*/
	function setExplodeSeparator($value)
	{
		if (!is_array($value)) $value = array($value);
		
		$this->_explodeSeparator = $value;
	}
	
	/**
	* @return array
	*/
	function getExplodeSeparator()
	{
		return $this->_explodeSeparator;
	}
	
	/**
	* Alias for implode with separator = {@link getSeparator()}
	* 
	* @param array $value
	* 
	* @return string
	*/
	protected function implode($value)
	{
		return implode($this->implodeSeparator, $value);
	}
	
	/**
	* @param string $v
	* @return boolean
	*/
	protected function isNotEmpty($v) { return !empty($v); }
	
	/**
	* Alias for explode with separator = {@link getSeparator()}
	* 
	* @param string $value
	* @return array
	*/
	protected function explode($value)
	{
		$pieces = array($value);
		$newPieces = array();
		foreach ($this->getExplodeSeparator() as $s) {
			foreach ($pieces as $piece) $newPieces = $newPieces + explode($s, $piece);
			$pieces = $newPieces;
			$newPieces = array();
		}
		
		if ($this->ignoreEmptyValue) $pieces = array_filter($pieces, array($this, 'isNotEmpty'));
		
		return $pieces;
	}
	
	/**
	* @inheritdoc
	*/
	function beforeSave($event)
	{
		foreach ($this->fields as $f) {
			$x = $event->sender->$f;
			$x = is_array($x) ? $this->implode($x) : '';
			$event->sender->$f = $x;
		}
		
		return true;
	}
	
	/**
	* @inheritdoc
	*/
	function afterSave($event)
	{
		foreach ($this->fields as $f) {
			$event->sender->$f=$this->explode($this->getOwner()->$f);
		}
	}
	
	static $xxx = 0;
	
	/**
	* @inheritdoc
	*/
	function afterFind($event)
	{
		foreach ($this->fields as $f) $event->sender->$f=$this->explode($this->getOwner()->$f);
	}
}