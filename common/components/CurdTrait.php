<?php
namespace common\components;

use Yii;
use yii\data\Pagination;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\base\InvalidConfigException;
use common\helpers\ResultDataHelper;

/**
 * CURD基类特性
 *
 * 注意：会覆盖父类的继承方法，注意使用
 * Trait CurdTrait
 * @package backend\components
 */
trait CurdTrait
{
    /**
     * 授权可ajax更新的字段
     *
     * @var array
     */
    protected $_ajaxUpdateField = [
        'id',
        'sort',
        'status'
    ];

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if ($this->modelClass === null)
        {
            throw new InvalidConfigException('"modelClass" 属性必须设置.');
        }

        parent::init();
    }

    /**
     * 首页
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $data = $this->modelClass::find();
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => $this->_pageSize]);
        $models = $data->offset($pages->offset)
            ->orderBy('id desc')
            ->limit($pages->limit)
            ->all();

        return $this->render($this->action->id, [
            'models' => $models,
            'pages' => $pages
        ]);
    }

    /**
     * 编辑/新增
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $model = $this->findModel($id);

        if ($model->load($request->post()) && $model->save())
        {
            return $this->redirect(['index']);
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 删除
     *
     * @param $id
     * @return mixed
     * @throws \Throwable
     * @throws yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        if ($this->findModel($id)->delete())
        {
            return $this->message("删除成功", $this->redirect(['index']));
        }

        return $this->message("删除失败", $this->redirect(['index']), 'error');
    }

    /**
     * 更新排序/状态字段
     *
     * @return array
     */
    public function actionAjaxUpdate()
    {
        $data = Yii::$app->request->get();
        $insertData = [];
        foreach ($this->_ajaxUpdateField as $item)
        {
            isset($data[$item]) && $insertData[$item] = $data[$item];
        }

        unset($data);

        if (!($model = $this->modelClass::findOne($insertData['id'])))
        {
            return ResultDataHelper::result(404, '找不到数据');
        }

        $model->attributes = $insertData;
        if (!$model->save())
        {
            return ResultDataHelper::result(422, $this->analyErr($model->getFirstErrors()));
        }

        return ResultDataHelper::result(200, '修改成功');
    }

    /**
     * 编辑/新增
     *
     * @return array|mixed|string|Response
     */
    public function actionAjaxEdit()
    {
        $request  = Yii::$app->request;
        $id = $request->get('id');
        $model = $this->findModel($id);
        if ($model->load($request->post()))
        {
            if ($request->isAjax)
            {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }

            return $model->save()
                ? $this->redirect(['index'])
                : $this->message($this->analyErr($model->getFirstErrors()), $this->redirect(['index']), 'error');
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 返回模型
     *
     * @param $id
     * @return mixed
     */
    protected function findModel($id)
    {
        if (empty($id) || empty(($model = $this->modelClass::findOne($id))))
        {
            $model = new $this->modelClass;
            return $model->loadDefaultValues();
        }

        return $model;
    }
}