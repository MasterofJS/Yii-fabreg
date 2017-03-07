<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/26/16
 * Time: 11:39 AM
 */

namespace rest\modules\v1\actions;


use common\models\ActiveRecord;
use rest\modules\v1\models\Like;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecordInterface;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class LikeAction extends Action
{
    const DISLIKE = -1;
    const LIKE = 1;

    public $entityClass;
    public $value;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->entityClass === null) {
            throw new InvalidConfigException(get_class($this) . '::$entityClass must be set.');
        }
    }

    /**
     * Displays a model.
     * @param string $id the primary key of the model.
     * @return ActiveRecordInterface the model being displayed
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function run($id)
    {
        /* @var $modelClass ActiveRecord */
        $modelClass = $this->entityClass;
        $entity_id = $modelClass::decodeId($id);
        $exist = $modelClass::find()->andWhere(['id' => $entity_id])->exists();
        if (!$exist) {
            throw new NotFoundHttpException("Entity not found: $id");
        }
        $model = Like::getInstance($entity_id, $this->entityClass, $this->value);

        if (!$model->save()) {
            throw new ServerErrorHttpException('Failed to like (dislike) the entity for unknown reason.');
        }
        \Yii::$app->getResponse()->setStatusCode(204);
    }
}