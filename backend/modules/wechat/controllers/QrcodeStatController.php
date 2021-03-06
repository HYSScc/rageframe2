<?php
namespace backend\modules\wechat\controllers;

use common\helpers\ExcelHelper;
use Yii;
use yii\data\Pagination;
use common\models\wechat\QrcodeStat;
use common\components\CurdTrait;

/**
 * 微信二维码统计
 *
 * Class QrcodeStatController
 * @package backend\modules\wechat\controllers
 */
class QrcodeStatController extends WController
{
    use CurdTrait;

    /**
     * @var string
     */
    public $modelClass = 'common\models\wechat\QrcodeStat';

    /**
     * 首页
     */
    public function actionIndex()
    {
        $request  = Yii::$app->request;
        $type = $request->get('type','');
        $keyword  = $request->get('keyword','');
        $from_date  = $request->get('from_date', date('Y-m-d', strtotime("-60 day")));
        $to_date  = $request->get('to_date', date('Y-m-d', strtotime("+1 day")));

        $data = QrcodeStat::find()
            ->andFilterWhere(['like', 'name', $keyword])
            ->andFilterWhere(['type' => $type])
            ->andFilterWhere(['between','created_at', strtotime($from_date), strtotime($to_date)]);

        $attention_data = clone $data;
        $scan_data = clone $data;

        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => $this->_pageSize]);
        $models = $data->offset($pages->offset)
            ->orderBy('id desc')
            ->limit($pages->limit)
            ->all();

        // 关注统计
        $attention_count = $attention_data->andWhere(['type' => QrcodeStat::TYPE_ATTENTION])->count();
        // 扫描统计
        $scan_count = $scan_data->andWhere(['type' => QrcodeStat::TYPE_SCAN])->count();

        return $this->render('index',[
            'models' => $models,
            'pages' => $pages,
            'type' => $type,
            'attention_count' => $attention_count,
            'scan_count' => $scan_count,
            'keyword' => $keyword,
            'from_date' => $from_date,
            'to_date' => $to_date,
        ]);
    }

    /**
     * 导出
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport()
    {
        $request = Yii::$app->request;
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        $dataList = QrcodeStat::find()
            ->where(['between', 'created_at', strtotime($from_date), strtotime($to_date)])
            ->andFilterWhere(['type' => $request->get('type')])
            ->andFilterWhere(['like', 'name', $request->get('keyword')])
            ->orderBy('created_at desc')
            ->with('fans')
            ->asArray()
            ->all();

        $header = [
            ['ID', 'id', 'text'],
            ['场景名称', 'name', 'text'],
            ['openid', 'fans.openid', 'text'],
            ['昵称', 'fans.nickname', 'text'],
            ['场景值', 'scene_str', 'text'],
            ['场景ID', 'scene_id', 'text'],
            ['关注/扫描', 'type', 'selectd', ['' => '全部','1' => '关注','2' => '扫描']],
            ['创建日期', 'field', 'date', 'Y-m-d H:i:s'],
        ];

        // 导出Excel
        ExcelHelper::exportData($dataList, $header, '扫描统计_' . time());
    }
}