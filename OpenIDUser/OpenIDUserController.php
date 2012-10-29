<?php

require_once __DIR__.'/LightOpenID.php';

Yii::import('ext.common.Hq.HqController.HqController');

/**
* This class support all operations with OpenID
*
* <code>
CREATE TABLE open_id_user(
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(255) NOT NULL,
  name TINYTEXT NOT NULL,
  avatar TINYTEXT DEFAULT NULL,
  enable TINYINT(1) NOT NULL DEFAULT 1,
  roles SET('ADMIN','MODERATOR','MEMBER') NOT NULL DEFAULT 'MEMBER',
  created_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE INDEX UK_open_id_user__email (email)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci;
* </code>
*
* @property string $contUrl the url that will be redirected to when user signed in
* @property CWebUser $user
* @property string $modelClass
*/
class OpenIDUserController extends HqController
{
    public $defaultAction = 'admin';
    public $layout = 'column1';

    public $roles=array();

    public $requiredAttributes = array(
		'contact/email',
		'namePerson',
		'namePerson/first',
		'namePerson/last',
    );

    public $optionalAttributes = array(
		'contact/phone',
		'person/guid',
		'person/gender',
		'media/image/default',
		'company/name',
		'company/email',
		'person/website',
		'merchant/website',
    );

    public $attributesMapping = array(
		'contact/email'=>'email',
		'namePerson'=>'name',
//		'namePerson/first',
//		'namePerson/last',
		'contact/phone'=>'phone',
		'person/guid'=>'id',
		'person/gender'=>'gender',
		'media/image/default'=>'avatar',
    );

    private $_modelClass='ext.common.OpenIDUser.OpenIDUser';

    function setModelClass($v) { $this->_modelClass = $v; }
    function getModelClass()
    {
    	if (strpos($this->_modelClass, '.') !== false)
    		$this->_modelClass = Yii::import($this->_modelClass);

    	return $this->_modelClass;
    }

    public $userComponentName='hqUser';

    function init()
    {
        parent::init();

        if ($this->userComponentName != 'user')
        	Yii::app()->setComponent('user', Yii::app()->{$this->userComponentName});
    }

    function filters()
	{
		return array('accessControl');
	}

	function accessRules()
	{
		return CMap::mergeArray(array(
			array('allow', 'actions'=>array('signIn','signOut'), 'users'=>array('*')),
		), parent::accessRules());
	}

    function getViewPath()
    {
		return __DIR__.'/views';
    }

    private $_assetsUrl;
    public function getAssetsUrl()
    {
        if ($this->_assetsUrl === null)
            $this->_assetsUrl =
            	Yii::app()->getAssetManager()->publish(
            		__DIR__.'/assets',
            		false,
            		-1,
            		false
            	);
        return $this->_assetsUrl;
    }

	private $_contUrl;
	function getContUrl()
	{
        if (!isset($this->_contUrl)) {
        	$x = Yii::app()->getRequest()->getQuery('_cont');
            if (!empty($x)) return $this->_contUrl = $x;

            $x = $this->getUser()->getReturnUrl();
            if (!empty($x)) return $this->_contUrl = $x;

    		return $this->_contUrl = Yii::app()->homeUrl;
        }

        return $this->_contUrl;
	}

	/**
	* @return CWebUser
	*/
	function getUser()
	{
		return Yii::app()->user;
	}

	/**
	* Hàm này sẽ làm 2 bước sau, theo thứ tự ưu tiên từ cao đến thấp :
	* 	1. nếu cửa sổ hiện tại là popup ($_GET['popup]) thì đóng cửa sổ, refresh cửa sổ chính
	* 	2. redirect về địa chỉ cuối cùng
	*/
	function returnLastUrl()
	{
		if (isset($_GET['popup']) && $_GET['popup']) {
			$this->renderPartial('close_popup');
			return;
		}
		$this->redirect($this->getContUrl());
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=CActiveRecord::model($this->modelClass)->findByPk($id);
		if($model===null)
			throw new CHttpException(404,$this->modelClass.' #'.$id.' does not exist.');
		return $model;
	}

	function actions()
	{
		return array(
        	'signIn'=>'OpenIDSignInAction',
        	'admin'=>'OpenIDAdminAction',
		);
	}

	function actionSignout()
	{
    	$lastUrl = $this->getContUrl();

        $this->getUser()->logout();
        $this->redirect($lastUrl);
	}
}

class OpenIDUserIdentity extends CBaseUserIdentity
{
	public $email;

	public $name;

	public $avatar;

	public function __construct($attrs)
	{
        if (isset($attrs['email'])) $this->email = $attrs['email'];
        if (isset($attrs['name'])) $this->name = $attrs['name'];
        if (isset($attrs['avatar'])) $this->avatar = $attrs['avatar'];

        if (empty($this->avatar)) $this->avatar = 'http://www.gravatar.com/avatar/'.md5(strtolower(trim($this->email)));
	}

	/**
	* @inheritdoc
	*
	*/
    public function authenticate()
    {
    	$this->setState('email', $this->email);
    	$this->setState('name', $this->name);
    	$this->setState('avatar', $this->avatar);

        return self::ERROR_NONE;
    }

    public function getId()
    {
        return $this->email;
    }
}

class OpenIDSignInAction extends CAction
{
    protected $_providers = array(
    	'google'=>'https://www.google.com/accounts/o8/id',
    	'yahoo'=>'http://open.login.yahooapis.com/openid20/www.yahoo.com/xrds',
    	'vg'=>'http://id.vatgia.com/OpenID/server/',
    );

	protected function doGainAccess($openIDIdentity)
	{
		$openid = new LightOpenID();
		$openid->identity = $openIDIdentity;

		$openid->required = $this->getController()->requiredAttributes;
		$openid->optional = $this->getController()->optionalAttributes;

		$get = $_GET;
		$get['step'] = 'callback';
		$openid->returnUrl = $this->getController()->createAbsoluteUrl($this->getId(), $get);

		$url = $openid->authUrl();
		$this->getController()->redirect($url);
	}

	protected function normalizeAttributes($attrs)
	{
		$nAttrs = array();

		if (!isset($attrs['namePerson']) || empty($attrs['namePerson'])) {
			$e = array();
			if (isset($attrs['namePerson/first']) && !empty($attrs['namePerson/first']))
				$e[] = $attrs['namePerson/first'];
			if (isset($attrs['namePerson/last']) && !empty($attrs['namePerson/last']))
				$e[] = $attrs['namePerson/last'];

			if (!empty($e)) $attrs['namePerson'] = implode(' ', $e);
		}

		$mapping = $this->getController()->attributesMapping;
		foreach ($attrs as $name=>$value) {
			if (isset($mapping[$name])) $nAttrs[$mapping[$name]] = $value;
		}

		return $nAttrs;
	}

	protected function saveUserInfo($attrs)
	{
		$className = $this->getController()->modelClass;

        $model = new $className();
        $model->setAttributes($attrs);

        if (!is_null(CActiveRecord::model($className)->findByAttributes(array('email'=>$attrs['email'])))) return;

        if (!$model->save()) throw new CHttpException(500, print_r($model->getErrors(), true));
	}

	protected function doCallback()
	{
		if (Yii::app()->getRequest()->getQuery('openid_mode') !== 'id_res') {
			return $this->controller->returnLastUrl();
		}

		$openid = new LightOpenID();

		if ($openid->validate()) {
			$attrs = $this->normalizeAttributes($openid->getAttributes());

			$user = new OpenIDUserIdentity($attrs);
			$user->authenticate();

			$this->saveUserInfo($attrs);
			Yii::app()->user->login($user);

			$this->getController()->returnLastUrl();
		}
		else {
			throw new CHttpException(501, "Invalid openid operation");
		}
	}

	function run($p=null, $step = 'gain_access')
	{
        if (!Yii::app()->user->isGuest)
        	return $this->getController()->returnLastUrl();

		if (empty($p)) {
	        $this->getController()->render('signin', array());
	        return;
		}

        if (!isset($this->_providers[$p])) throw new CHttpException('Invalid provider', 400);

        switch ($step) {
			case 'gain_access' :
				$this->doGainAccess($this->_providers[$p]);
				break;
			case 'callback' :
				$this->doCallback();
				break;
			default:
				throw new CHttpException(400, 'Invalid step');
		}
	}
}

class OpenIDAdminAction extends CAction
{
    protected function enableUser($email, $enable)
    {
    	$modelClass = $this->getController()->modelClass;

    	$model = CActiveRecord::model($modelClass)->findByAttributes(array('email'=>$email));
    	if (is_null($model)) return;

    	$model->enable = $enable;
		$model->save();
    }

    /**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=OpenIdUser::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function doUpdate($id)
	{
		$modelClass = $this->getController()->modelClass;
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST[$modelClass]))
		{
			$post = $_POST[$modelClass];
//			if(empty($post['roles'])) $post['roles'] = array();
			$model->attributes=$post;
			if($model->save())
				Yii::app()->user->setFlash('success', 'Update success');
			else
				Yii::app()->user->setFlash('error', 'Update failed');
		}

		$this->controller->render('update',array(
			'model'=>$model,
		));
	}

    function run()
    {
    	if (isset($_GET['update'])) return $this->doUpdate($_GET['update']);

    	$modelClass = $this->getController()->modelClass;
 		$model=new $modelClass('search');

        $model->unsetAttributes();  // clear any default values
        if(isset($_REQUEST[$modelClass]))
            $model->attributes=$_REQUEST[$modelClass];

        $this->getController()->render('admin',array(
            'model'=>$model,
        ));
    }
}