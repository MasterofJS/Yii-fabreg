<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/24/16
 * Time: 12:54 PM
 */

namespace backend\models;


use backend\traits\ApprovableStatusTrait;
use common\models\PostReport;
use yii\data\ActiveDataProvider;

class Report extends PostReport
{
    use ApprovableStatusTrait;

    public $username;
    public $postDesc;


    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['update'] = ['status'];
        $scenarios['search'] = ['status', 'username', 'postDesc'];
        return $scenarios;
    }


    public function search($params)
    {
        $this->load($params);
        $query = static::find()
            ->addSelect(static::tableName() . '.*')
            ->addSelect(['username' => User::tableName() . '.username'])
            ->addSelect(['postDesc' => Post::tableName() . '.description'])
            ->innerJoinWith('post.photo')
            ->innerJoinWith('user', false);

        $query->andFilterWhere([static::tableName() . '.status' => $this->status]);
        $query->andFilterWhere(['like', User::tableName() . '.username', $this->username]);
        $query->andFilterWhere(['or like', Post::tableName() . '.description', preg_split('/\P{L}+/u', $this->postDesc, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE)]);
        if (isset($params['post_id'])) {
            $query->andFilterWhere([static::tableName() . '.post_id' => $params['post_id']]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $sort = $dataProvider->getSort();
        $sort->attributes['username'] = [
            'asc' => ['username' => SORT_ASC],
            'desc' => ['username' => SORT_DESC],
        ];
        $sort->attributes['postDesc'] = [
            'asc' => ['postDesc' => SORT_ASC],
            'desc' => ['postDesc' => SORT_DESC],
        ];
        $sort->defaultOrder = ['created_at' => SORT_DESC];
        return $dataProvider;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (array_key_exists('status', $changedAttributes)) {
            if (self::STATUS_APPROVED == $this->status) {
                $this->post->ban();
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public static function multiUpdate($reports, $status)
    {
        $updateRows = 0;
        $posts = [];
        foreach ($reports as $report) {
            $result = static::updateAll([
                'status' => $status
            ], [
                'user_id' => $report->user_id,
                'post_id' => $report->post_id
            ]);
            if (!in_array($report->post_id, $posts) && $result) {
                $posts[] = $report->post_id;
            }
            $updateRows += $result;

        }
        if ($status == static::STATUS_APPROVED) {
            foreach (Post::findAll($posts) as $post) {
                $post->ban();
            }
        }
        return $updateRows == (count($reports) + count($posts));
    }

    public static function count()
    {
        return static::find()->andWhere(['status' => self::STATUS_PENDING])->count();
    }


}