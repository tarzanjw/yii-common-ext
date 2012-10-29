<?php

/**
* Behavior này cho phép một Component truy cập được một số thuộc tính thông qua Registry
* 
* Một thuộc tính được sử dụng thông qua Registry có cấu trúc như sau :
* 
* <code>
* $a = 'abc'; # đơn giản chỉ là abc
* $a = 'registry://abc'; # đọc 'abc' từ registry ra
* 
* </code>
* 
* @author Tarzan <hocdt85@gmail.com>
* 
* @property array $properties danh sách các thuộc tính áp dụng quy tắc 'Regisry'
*/
class RegistryPropertiesBehavior extends CBehavior
{
	protected $_properties=array();
	protected $_propertyValues=array();
	
	public function setProperties($properties)
	{
		$this->_properties = $properties;
	}	
	
	public function getProperties() 
	{ 
		foreach ($this->_properties as $k=>$v) $this->_propertyValues[$k] = $this->getProperty($k);
			
		return $this->_propertyValues; 
	}
	
	protected function calculateProperty($name)
	{
		assert('isset($this->_properties[$name])');
		$v = $this->_properties[$name];
		
		if (is_string($v) && preg_match('{(\w+)://(\w+)}', $v, $m)) {
			$regComp = $m[1];
			$varName = $m[2];
			
			return Yii::app()->$regComp->getRegistry($varName);
		}
		
		return $v;
	}
	
	public function getProperty($name)
	{
		if (!isset($this->_propertyValues[$name])) {
			$this->_propertyValues[$name] = $this->calculateProperty($name);
		}
		
		return $this->_propertyValues[$name];
	}
	
	public function setProperty($name, $value)
	{
		$this->_propertyValues[$name] = $value;
	}
	
	/**
	* @inheritdoc
	*/
	function hasProperty($name)
	{
		if (isset($this->_properties[$name])) return true;
		
		return parent::hasProperty($name);
	}
	
	/**
	* @inheritdoc
	*/
	public function canGetProperty($name)
	{
		if ($this->hasProperty($name)) return true;
		
		return parent::canGetProperty($name);
	}
	
	/**
	* @inheritdoc
	*/
	public function canSetProperty($name)
	{
		if ($this->hasProperty($name)) return true;
		
		return parent::canSetProperty($name);
	}
	
	public function __get($name)
	{
		if ($this->hasProperty($name)) return $this->getProperty($name);
		
		return parent::__get($name);
	}
	
	public function __set($name, $value)
	{
		if ($this->hasProperty($name)) return $this->setProperty($name, $value);
		
		return parent::__get($name);
	}
}