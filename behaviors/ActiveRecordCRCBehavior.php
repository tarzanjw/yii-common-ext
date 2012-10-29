<?php

/**
* @property ActiveRecordCRCBehavior $crcBehavior
*/
class InvalidARCRCException extends CException
{
	/**
	* @var ActiveRecordCRCBehavior
	*/
	protected  $_crcBehavior;
	function getCrcBehavior() { return $this->_crcBehavior; }

	function __construct(ActiveRecordCRCBehavior $crcBehavior, $expectedCRC=null)
	{
		$this->crcBehavior = $crcBehavior;

		$msg = sprintf('Invalid CRC value for %s. The current is "%s".', get_class($crcBehavior->getOwner()), $crcBehavior->getOwner()->{$crcBehavior->crcField});
		if (!empty($expectedCRC)) $msg .= ' Expected value is "'.$expectedCRC.'"';

		parent::__construct($msg, 0);
	}
}

/**
* Class này phục vụ việc lấy mã CRC cho một ActiveRecord cụ thể. Thuật toán lấy như sau :
* 	1. ActiveRecord sẽ cấu hình một danh sách các field quan trọng mà sẽ lấy CRC trên đó {@link $crcFields}
* 	2. ActiveRecord sẽ cấu hình một mã bí mật để lấy CRC {@link $crcSecretKey}
* 	3. Các field quan trọng sẽ được ghép lại thành 1 xâu theo thứ tự config, sau đó ghép thêm mã bí mật vào cuối và lấy mã băm theo thuật toán md5
*
* @property string $crcField Field được update giá trị CRC
* @property string[] $crcFields Danh sách các Field để tạo CRC
* @property string $fieldsFormation Format sprintf cac field để tính CRC
* @property string $_crc giá trị mã CRC
* @property string $crcSecretKey Mã bí mật để lấy tạo ra CRC
*
* @author Tarzan <hocdt85@gmail.com>
*/
class ActiveRecordCRCBehavior extends CActiveRecordBehavior
{
	/**
	* Array config format cho Field để đảm bảo tạo CRC toàn vẹn! Mặc định là str
	*
	* @var string[]
	* array('fieldName'=>'formation') # that used by sprintf
	*/
	public $fieldsFormation = array();

	/**
	* List Array các field đưa vào CRC
	*
	* @var string[]
	*/
	protected $_crcFields = array();

	function getCrcFields() { return $this->_crcFields; }
	function setCrcFields($v) { $this->_crcFields = $v; }

	/**
	* Tên field để Save crc
	*
	* @var mixed
	*/
	public $crcField = 'crc';

	/**
	* Key bí mật để mã hóa cùng dữ liệu field bảo vệ
	*
	* @var string
	*/
	public $crcSecretKey = '';

	/**
	* Field sẽ lock khi có lỗi CRC
	*
	* @var string[]
	*/
	public $lockedField = array();

	/**
	* Giá trị CRC
	*
	* @var mixed
	*/
	public $_crc = null;

	/**
	* get CRC of the current object
	*
	* @return string
	*/
	function getCrc($ar = null)
	{
		if(is_null($ar)) $ar = $this->getOwner();

		if (!isset($this->_crc)) {
			$value = '';

			$fields = $this->getCrcFields();

			foreach ($fields as $field){
				$v = $ar->$field;

				$value .= isset($this->fieldsFormation[$field])
					? sprintf($this->fieldsFormation[$field], $v) : $v;
			}

			$value .= $this->crcSecretKey;

			$this->_crc = md5($value);
		}

		return $this->_crc;
	}

	/**
	* Get giá trị CRC thực của AR hiện tại trước khi Update CRC mới
	*
	* @return string
	*/
	function getCrcBeforeUpdate()
	{
		$ar = $this->owner->findByPk($this->owner->getPrimaryKey());

		//Reset de tinh lai theo value hien tai AR
		$this->_resetCrc();

		return $this->getCrc($ar);
	}

	/**
	* Tự động attack vào event after_save của AR để update CRC
	*
	* @param mixed $event
	*/
	function afterSave($event)
	{
		$this->updateCRC();
	}

	/**
	* Tự động attack vào event after_save của AR để update CRC
	*
	* @param mixed $event
	*/
	function beforeSave($event)
	{
		$this->validateCRC();
	}

	/**
	* Reset CRC sau khi Lock
	*
	*/
	function renewCRC()
	{
		$uCrc = $this->getCrcBeforeUpdate();

		if (!$this->owner->updateByPk($this->owner->getPrimaryKey(),array($this->crcField=>$uCrc)))
			throw new Exception($this->owner->getError($crcField),403);
	}

	/**
	* Function check xem CRC có toàn vẹn không
	*/
	protected function validateCRC()
	{
		$activeRecordCRC = $this->owner->{$this->crcField};

		//Reset CRC để get CRC theo giá trị bằng các Fields
		$realCRC = $this->getCrcBeforeUpdate();

		//Nếu CRC không toàn vẹn => Lock AR
		if($realCRC !== $activeRecordCRC && !empty($activeRecordCRC)){
			throw new InvalidARCRCException($this, $realCRC);
		}
	}

	/**
	* Function Update CRC sau event after_save
	*/
	protected function updateCRC()
	{
		//Reset de tinh lai theo CRC After Save
		$this->_resetCrc();

		//Update CRC
		$this->owner->updateByPk($this->owner->getPrimaryKey(),array($this->crcField=>$this->getCrc()));
	}

	/**
	* clear current value of the crc
	*
	*/
	protected function _resetCrc()
	{
		if(isset($this->_crc))
			$this->_crc = null;
	}
}