<?php

namespace app\models;

use dektrium\user\models\User as BaseUser;
use dektrium\user\helpers\Password;
use dektrium\user\models\Token;
use yii\web\ForbiddenHttpException;

class User extends BaseUser
{
    const ROLE_ADMIN = 'admin';
    const ROLE_PARTNER = 'partner';
    const ROLE_USER = 'user';
    
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    
    /**
     * @author Pawel Brzozowski (bizley)
     * @param mixed $token
     * @param null $type
     * @return self
     * @throws ForbiddenHttpException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if (!preg_match('/^(\d+):(\d+):(.+)$/', $token, $matches)) {
            throw new ForbiddenHttpException('Invalid token provided');
        }
        list(, $userId, $stamp, $checksum) = $matches;
        $now = time();
        if ($now > $stamp + 60 || $now < $stamp - 60) {
            throw new ForbiddenHttpException('Invalid token provided | Bad Timestamp');
        }
        $user = static::findIdentity($userId);
        if ($user === null || empty($user->api_key) || !$user->verifyChecksum($stamp, $checksum)) {
            throw new ForbiddenHttpException('Invalid token provided');
        }
//        return self::findOne(['access_token' => $token]);
        return $user;
    }

    /**
     * @author Pawel Brzozowski (bizley)
     * @param string $stamp
     * @param string $checksum
     * @return bool
     */
    public function verifyChecksum($stamp, $checksum)
    {
        return sha1($stamp . $this->api_key) === $checksum;
    }
    
    function validatePassword($password) {
        // by dektrium: https://github.com/dektrium/yii2-user/blob/981b3d13a295552fb8e711b4de451d520b694ed0/models/LoginForm.php
        return Password::validate($password, $this->password_hash);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'][] = 'api_key';
        $scenarios['update'][] = 'api_key';
        $scenarios['register'][] = 'api_key';
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules['apiKeyLength'] = ['api_key', 'string', 'max' => 16];
        $rules['passwordLength'] = ['password', 'string', 'min' => 3];

        return $rules;
    }
    
    // Override User model registration
    public function register(){
        
//        if ($this->getIsNewRecord() == false) {
//            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
//        }
        
        $transaction = $this->getDb()->beginTransaction();

        try {
            $this->confirmed_at = $this->module->enableConfirmation ? null : time();
            $this->password     = $this->module->enableGeneratingPassword ? Password::generate(8) : $this->password;

            $this->trigger(self::BEFORE_REGISTER);

            if (!$this->validate() || !$this->save()) {
                $transaction->rollBack();
                return false;
            }

            if ($this->module->enableConfirmation) {
                // @var Token $token
                $token = \Yii::createObject(['class' => Token::className(), 'type' => Token::TYPE_CONFIRMATION]);
                $token->link('user', $this);
            }

            $this->mailer->sendWelcomeMessage($this, isset($token) ? $token : null);
            $this->trigger(self::AFTER_REGISTER);
            
            $this->status = self::STATUS_INACTIVE;

            $transaction->commit();

            // set default role 'user'
            $this->addRole(self::ROLE_USER);
        
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::warning($e->getMessage());
            throw $e;
        }
    }
    
    public function addRole($role) {
        $auth = \Yii::$app->authManager;
        $userRole = $auth->getRole($role);
        
        $auth->assign($userRole, $this->getId());        
    }
    
    // setPassword & generateAuthKey are implemented just to make yii2-admin's registration works
    public function setPassword($password)
    {
        $this->password_hash = \Yii::$app->security->generatePasswordHash($password);
    }    
    
    public function generateAuthKey()
    {
        $this->auth_key = \Yii::$app->security->generateRandomString();
    }    
    
    public static function isGuest() {
        return \Yii::$app->user->isGuest;
    }
    
    public static function isAdmin() {
        return (!self::isGuest() && (\Yii::$app->user->identity->isAdmin /*|| $this->getRole() == ROLE_ADMIN */));
    }
    
    public static function isPartner() {
        return \Yii::$app->user->can(self::ROLE_PARTNER);
    }
    
    public static function isUser() {
        return \Yii::$app->user->can(self::ROLE_USER);
    }
    
    public static function getCurrentUser() {
        return \Yii::$app->user;
    }
    
}