<?php

/**
 * This is the model class for table "{{user}}".
 *
 * The followings are the available columns in table '{{user}}':
 * @property string $bu_id
 * @property string $bu_email
 * @property string $bu_name
 * @property string $bu_password
 * @property string $bu_reg_ip
 * @property string $bu_last_ip
 * @property string $bu_weibo
 * @property string $bu_qq
 * @property integer $bu_last_time
 * @property integer $bu_create_time
 * @property integer $bu_status
 * @property integer $bu_reputation
 * @property string $bu_about
 */
class User extends CActiveRecord
{
	public $password;
	public $password_again;
	public $password_current;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('bu_email', 'required'),
			array('bu_email', 'unique'),
			array('bu_email', 'email'),
			array('password, password_current, password_again', 'required', 'on'=>'changepwd'),
			array('password, password_again', 'required', 'on'=>'newpwd'),
			array('password_again','compare', 'compareAttribute'=>'password', 'on'=>'changepwd,newpwd', 'message'=>t('password_no_repeat', 'model')),
			array('bu_last_time, bu_create_time, bu_status, bu_reputation', 'numerical', 'integerOnly'=>true),
			array('bu_email', 'length', 'max'=>255),
			array('bu_name, bu_reg_ip, bu_last_ip, bu_weibo, bu_qq', 'length', 'max'=>25),
			array('password, password_again', 'length', 'min'=>6, 'max'=>50),
			array('bu_password', 'length', 'max'=>100),
			array('bu_about','safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('bu_id, bu_email, bu_name, bu_password, bu_reg_ip, bu_last_ip, bu_weibo, bu_qq, bu_last_time, bu_create_time, bu_status, bu_reputation, bu_about', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'bu_id' => 'Bu',
			'bu_email' => t('bu_email', 'model'),
			'bu_name' => t('bu_name', 'model'),
			'bu_password' => t('bu_password', 'model'),
			'password' => t('password', 'model'),
			'password_current' => t('password_current', 'model'),
			'password_again' => t('password_again', 'model'),
			'bu_reg_ip' => 'Bu Reg Ip',
			'bu_last_ip' => 'Bu Last Ip',
			'bu_weibo' => 'Bu Weibo',
			'bu_qq' => 'Bu Qq',
			'bu_last_time' => 'Bu Last Time',
			'bu_create_time' => 'Bu Create Time',
			'bu_status' => 'Bu Status',
			'bu_reputation' => t('bu_reputation', 'model'),
			'bu_about' => t('bu_about', 'model'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('bu_id',$this->bu_id,true);
		$criteria->compare('bu_email',$this->bu_email,true);
		$criteria->compare('bu_name',$this->bu_name,true);
		$criteria->compare('bu_password',$this->bu_password,true);
		$criteria->compare('bu_reg_ip',$this->bu_reg_ip,true);
		$criteria->compare('bu_last_ip',$this->bu_last_ip,true);
		$criteria->compare('bu_weibo',$this->bu_weibo,true);
		$criteria->compare('bu_qq',$this->bu_qq,true);
		$criteria->compare('bu_last_time',$this->bu_last_time);
		$criteria->compare('bu_create_time',$this->bu_create_time);
		$criteria->compare('bu_status',$this->bu_status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}


	//查询密码是否匹配
	public function validatePassword($password)
	{
		return $this->encrypt($password)===$this->password;
	}

	// 保存数据前自动处理
	protected function beforeSave() {
		if (parent::beforeSave()) {
			//判断是否是新的
			if ($this->isNewRecord) {
				$this->bu_status = 1;
	    		$this->bu_create_time = time();
	    		$this->bu_reg_ip = GetIP();
	    		$this->bu_reputation = 42;//注册送42个积分
			}
			return true;
		}else {
			return false;
		}
	}

	//没有绑定Oauth，创建账号及绑定
	public static function addOauth($userBingding,$salt)
	{
		$model=new User;
        $user = array();
        $user['bu_name'] = $userBingding['domain'];
        $user['salt'] = $salt;
        $user['counts'] = 1;
        $user['created'] = time();
        $user['updated'] = time();
        $model->attributes=$user;
		
        if($model->save()){
            $user_id = $model->id;
            Yii::app()->user->id = $user_id;
            Yii::app()->user->name = $userBingding['domain'];
            $bind = array();
            $bind['user_id'] = $user_id;
            $bind['user_access_token'] = $userBingding['access_token'];
            $BindModel = new UserBinding;
            $BindModel->attributes=$bind;
            return $BindModel->save();
        }
		
	}

}