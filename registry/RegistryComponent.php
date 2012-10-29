<?php

class RegistryComponent extends CApplicationComponent
{
	public $registryARClass='Registry';

	public $caseInsensitive=false;

	private $_registries;

	/**
	* @return Registry[]
	*/
	function getRegistries()
	{
		if (!isset($this->_registries)) {
			$rs=CActiveRecord::model($this->registryARClass)->findAll();

			foreach ($rs as $r) $this->_registries[$this->caseInsensitive ? strtolower($r->name):$r->name]=$r->value;
		}

		return $this->_registries;
	}

	/**
	* Lấy giá trị của 1 registry.
	*
	* @param string $name tên của registry cần lấy
	*
	* @return mixed
	*/
	function getRegistry($name)
	{
		$regs = $this->getRegistries();
		$_name = $this->caseInsensitive ? strtolower($name) : $name;
		if (isset($regs[$_name])) return $regs[$_name];

		throw new CException(Yii::t('message','Registry "{registry}" is not defined.',
			array('{registry}'=>$name)));
	}

	function __get($name)
	{
		$regs = $this->getRegistries();
		$_name = $this->caseInsensitive ? strtolower($name) : $name;
		if (isset($regs[$_name])) return $regs[$_name];

		return parent::__get($name);
	}
}
