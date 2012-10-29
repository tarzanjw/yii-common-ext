<?php

/**
* Class này cho phép populate một mã độc lập từ ID của AR nhằm phục vụ việc giao dịch và giấu Id khi giao tiếp với các dịch vụ khác
* 
* Thuật toán như sau :
* 	1. Sử dụng Id (được lưu tại {@link $idField}) để làm nhân tố ngẫu nhiên
* 	2. Id này sẽ được đưa qua 1 bộ lọc {@link $mask}, theo phép xor để được 1 Id mới tương ứng 1-1 với Id cũ
* 	3. Các giá trị ngẫu nhiên sẽ được thêm vào cho đến khi xâu trả về có độ dài = {@link $length} ở dạng biểu diễn hex
* 	4. Giá trị này sẽ được lưu vào thuộc tính {@link $ukField} của AR, tại sự kiện after_save
* 
* @author Tarzan <hocdt85@gmail.com>
* 
*/
class UniqueIdPopulatorBehavior extends CActiveRecordBehavior
{
	static $DICTIONARY = array('A','B','C','D','E','F','0','9','8','7','6','5','4','3','2','1');
	static $POSITIONS = array(8, 6, 4, 2, 0);
	
	/**
	* Tên thuộc tính của AR chứa Id
	* 
	* @var string
	*/
	public $idField='id';
	
	/**
	* Tên thuộc tính của AR chứa UniqueId
	* 
	* @var string
	*/
	public $ukField='uk';
	
	/**
	* Mặt nạ để biến đổi Id
	* 
	* @var integer
	*/
	public $mask=0x52a2fab6;
	
	/**
	* Độ dài mong đợi của UK
	* 
	* @var integer
	*/
	public $length=13;	
	
	/**
	* Tiền tố cho các UK
	* 
	* @var string
	*/
	public $prefix='';
	
	/**
	* Tự động đính kèm vào event after_save của AR để tính toán UK nếu là bản ghi mới
	* 
	*/
	function afterSave($event)
	{
		$ukField = $this->ukField;
		if ($this->owner->isNewRecord || empty($this->owner->$ukField)) $this->populateUniqueId();
	}
	
	/**
	* Tìm 1 bản ghi AR có UK biết trước
	* 
	* @param string $uk mã UK cần tìm
	* @return CActiveRecord
	*/
	function findByUk($uk)
	{
		return $this->owner->findByAttributes(array($this->ukField=>$uk));
	}
	
	/**
	* Lấy 1 chữ số hex ngẫu nhiên
	* 
	* @return string
	*/
	protected function getRandomDigit()
	{
		return self::$DICTIONARY[rand(0, 15)];
	}
	
	/**
	* Chuyển đổi từ Id sang Uk
	* 
	* @param integer $id
	* 
	* @return string
	*/
	protected function id2uk($id)
	{
		$id = $id ^ $this->mask;
		$id = strtoupper(dechex($id));
		
		if ($this->length <= 8) return $id;
		
		foreach (self::$POSITIONS as $p) {
			$id = substr($id, 0, $p).$this->getRandomDigit().substr($id, $p);
			if (strlen($id) >= $this->length) break;
		}
		
		$flag = true;
		while (strlen($id) < $this->length) {
			$id = $flag ? ($id.$this->getRandomDigit()):($this->getRandomDigit().$id);
			$flag = !$flag;
		}
		
		return $id;
	}
	
	protected function populateUniqueId()
	{
		$idField = $this->idField;
		$ukField = $this->ukField;
		
		$this->owner->$ukField = $this->prefix.$this->id2uk($this->owner->$idField);
		
		$tmp = $this->owner->isNewRecord;
		$this->owner->isNewRecord = false;
		if (!$this->owner->update(false, array($ukField)))
			throw new CException(Yii::t('Can not save UK on :uk field.'."\n".$this->owner->getError($ukField), array(':uk'=>$ukField)));
		$this->owner->isNewRecord = $tmp;
			
		Yii::log(
			'Populated UK for '.get_class($this->owner).' with id='.$this->owner->$idField.' to "'.$this->owner->$ukField.'".'
			, CLogger::LEVEL_TRACE
		);
	}
}