<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\sys\Manager;
use common\helpers\StringHelper;

/**
 * 密码初始化
 *
 * Class PasswordController
 * @package console\controllers
 */
class PasswordController extends Controller
{
    /**
     * 初始化
     *
     * @throws \yii\base\Exception
     */
    public function actionInit()
    {
        Yii::$app->set('user', [
            'class' => 'common\models\sys\Manager',
        ]);

        if($model = Manager::findOne(['username' => 'admin']))
        {
            $password_hash = StringHelper::random(10);
            $model->password_hash = Yii::$app->security->generatePasswordHash($password_hash);
            if ($model->save())
            {
                echo '账号; admin' . PHP_EOL;
                echo '密码; ' . $password_hash . PHP_EOL;
                exit();
            }

            echo '密码初始化失败;' . PHP_EOL;
            exit();
        }

        echo '找不到 admin 用户;' . PHP_EOL;
        exit();
    }
}