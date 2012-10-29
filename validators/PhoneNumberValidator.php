<?php

/**
* Class này dùng cho việc kiểm tra 1 số điện thoại có đúng hay không.
*
* Sau khi validate, nếu giá trị đúng là phone thì validate này sẽ chuẩn hóa phone
* về 1 dạng đồng nhất
*
* @author Tarzan <hocdt85@gmail.com>
*/
class PhoneNumberValidator extends CValidator
{
	/**
	 * @var boolean whether the attribute value can be null or empty. Defaults to true,
	 * meaning that if the attribute is empty, it is considered valid.
	 */
	public $allowEmpty=false;

	/**
	* Validator này sẽ chuẩn hóa phone về 1 dạng  số đồng nhất, có 3 dạng tiền tố
	* có thể có: 0, 84, +84
	*
	* @var string
	*/
	public $prefixNumber = '+84';

	/**
	* Pattern cho số điện thoại di động
	*
	* @var mixed
	*/
	public $pattern = '(?:84|0)(9\d{8}|1\d{9})';

	/**
	 * Validates a single attribute.
	 * This method should be overridden by child classes.
	 * @param CModel $object the data object being validated
	 * @param string $attribute the name of the attribute to be validated.
	 */
	protected function validateAttribute($object,$attribute)
	{
        $value = $object->$attribute;
        if (empty($value) && $this->allowEmpty) return;

        $pattern = '{^'.$this->pattern.'$}is';
		$text = preg_replace('{[\+\-\(\)\[\]\|\s:]}', '', $value);
		if (preg_match($pattern, $text, $m)) {
			 $object->$attribute = $this->prefixNumber.$m[1];
			 return;
		} else {
			$message=$this->message!==null?$this->message:Yii::t('view','{attribute} ({phone_no}) is not a valid phone number.');

			$this->addError($object, $attribute,
				$message,
				array(
            		'{phone_no}'=>$value,
				)
			);
		}
	}

	/**
	 * Returns the JavaScript needed for performing client-side validation.
	 * Do not override this method if the validator does not support client-side validation.
	 * Two predefined JavaScript variables can be used:
	 * <ul>
	 * <li>value: the value to be validated</li>
	 * <li>messages: an array used to hold the validation error messages for the value</li>
	 * </ul>
	 * @param CModel $object the data object being validated
	 * @param string $attribute the name of the attribute to be validated.
	 * @return string the client-side validation script. Null if the validator does not support client-side validation.
	 * @see CActiveForm::enableClientValidation
	 * @since 1.1.7
	 */
	public function clientValidateAttribute($object,$attribute)
	{
	}

}