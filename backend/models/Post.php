<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/22/16
 * Time: 6:02 PM
 */

namespace backend\models;

use backend\traits\StatusTrait;
use common\models\PostReport;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class Post extends \common\models\Post
{
    use StatusTrait;
    public $username;
    public $reports_count;

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['status', 'in', 'range' => [self::STATUS_BANNED, self::STATUS_ACTIVE], 'on' => 'update'],
            [
                ['status', 'description', 'is_nsfw', 'channel', 'is_retired', 'reports_count', 'username'],
                'safe',
                'on' => 'search'
            ]
        ]);
    }

    public function search($params)
    {
        $this->load($params);
        $query = static::find()
            ->alias('post')
            ->addSelect('post.*, COUNT(post_report.user_id) as reports_count')
            ->addSelect(['username' => User::tableName() . '.username'])
            ->joinWith('reports', false)
            ->innerJoinWith('author', false)
            ->andFilterWhere([
                'channel' => $this->channel,
                'is_retired' => $this->is_retired,
                'post.status' => $this->status,
                'is_nsfw' => $this->is_nsfw
            ])
            ->andFilterWhere(['like', User::tableName() . '.username', $this->username])
            ->groupBy('post.id')
            ->with(['photo']);

        if ($this->description) {
            $query->match($this->description);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $sort = $dataProvider->getSort();
        $sort->defaultOrder = ['created_at' => SORT_DESC];
        $sort->attributes['reports_count'] = [
            'asc' => ['reports_count' => SORT_ASC],
            'desc' => ['reports_count' => SORT_DESC]
        ];
        $sort->attributes['username'] = [
            'asc' => ['username' => SORT_ASC],
            'desc' => ['username' => SORT_DESC],
        ];
        return $dataProvider;
    }

    public static function count()
    {
        return static::find()->andWhere(['status' => self::STATUS_ACTIVE])->count();
    }

    public static function hot()
    {
        return static::find()->andWhere(['status' => self::STATUS_ACTIVE])->hot()->count();
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_BANNED => ['text' => 'Banned', 'class' => 'danger'],
            self::STATUS_ACTIVE => ['text' => 'Active', 'class' => 'success'],
        ];
    }

    public function getReports()
    {
        return $this->hasMany(PostReport::className(), ['post_id' => 'id']);
    }

    /**
     * @param int $limit
     * @return Post[]
     */
    public static function lastPublished($limit = 4)
    {
        return static::find()->orderBy(['created_at' => SORT_DESC])->limit($limit)->all();
    }
}
