<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\helpers\Html;

$this->title = '视频';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];
?>

<div class="wrapper wrapper-content animated fadeInRight">
    <?php echo $this->render('_nav', [
        'allMediaType' => $allMediaType,
        'mediaType' => $mediaType,
        'count' => $pages->totalCount
    ]); ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="inlineBlockContainer col3 vAlignTop">
                <?php foreach ($models as $model){ ?>
                    <div class="normalPaddingRight" style="width:20%;margin-top: 10px;">
                        <div class="borderColorGray separateChildrenWithLine whiteBG" style="margin-bottom: 30px;">
                            <div class="normalPadding">
                                <div style="height: 160px;text-align:center;" class="backgroundCover relativePosition mainPostCover">
                                    <i class="fa fa-play-circle-o" style="font-size: 50px;margin:0 auto;padding-top: 30px"></i>
                                    <div class="bottomBar"><?= $model['file_name'] ?></div>
                                </div>
                            </div>
                            <div class="flex-row hAlignCenter normalPadding postToolbar">
                                <div class="flex-col"><a href="<?= Url::to(['send','attach_id' => $model['id'], 'mediaType' => $mediaType])?>"  title="群发" data-toggle='modal' data-target='#ajaxModal'><i class="fa fa-send"></i></a></div>
                                <div class="flex-col"><a href="<?= Url::to(['preview','attach_id' => $model['id'], 'mediaType' => $mediaType])?>" title="手机预览" data-toggle='modal' data-target='#ajaxModal'><i class="fa fa-search"></i></a></div>
                                <div class="flex-col"><a href="<?= Url::to(['delete','attach_id' => $model['id'], 'mediaType' => $mediaType])?>" onclick="rfDelete(this);return false;" title="删除"><i class="fa fa-trash"></i></a></div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?= LinkPager::widget([
                'pagination'        => $pages,
                'maxButtonCount'    => 5,
                'firstPageLabel'    => "首页",
                'lastPageLabel'     => "尾页",
                'nextPageLabel'     => "下一页",
                'prevPageLabel'     => "上一页",
            ]);?>
        </div>
    </div>
</div>