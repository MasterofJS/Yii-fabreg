<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/22/16
 * Time: 12:58 PM
 */

namespace backend\models;


use backend\traits\StatusTrait;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class User extends \common\models\User
{
    use StatusTrait;

    public $name;

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['status', 'in', 'range' => [self::STATUS_BANNED, self::STATUS_ACTIVE, self::STATUS_DELETED], 'on' => 'update'],
            [['status', 'name', 'username', 'email', 'gender', 'country'], 'safe', 'on' => 'search']
        ]);
    }

    public function search($params)
    {
        $this->load($params);

        $query = static::find()
            ->addSelect(static::tableName() . '.*')
            ->addSelect(['name' => new Expression('CONCAT_WS(\' \', [[first_name]], [[last_name]])')]);

        $query->andFilterWhere(['gender' => $this->gender, 'status' => $this->status, 'country' => $this->country]);
        $query->andFilterWhere(['like', 'email', $this->email]);
        $query->andFilterWhere(['like', 'username', $this->username]);
        if (!empty($this->name)) {
            $names = preg_split('/\P{L}+/u', $this->name, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
            $query->andFilterWhere(['or',
                ['or like', 'first_name', $names],
                ['or like', 'last_name', $names],
            ]);
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $sort = $dataProvider->getSort();
        $sort->attributes['name'] = [
            'asc' => ['first_name' => SORT_ASC, 'last_name' => SORT_ASC],
            'desc' => ['first_name' => SORT_DESC, 'last_name' => SORT_DESC],
        ];
        $sort->defaultOrder = ['created_at' => SORT_DESC];

        return $dataProvider;
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_BANNED => ['text' => 'Banned', 'class' => 'default'],
            self::STATUS_ACTIVE => ['text' => 'Active', 'class' => 'success'],
            self::STATUS_DELETED => ['text' => 'Deleted', 'class' => 'danger'],
        ];
    }

    public static function byCountry()
    {
        return static::find()->select(['count' => new Expression('COUNT(*)'), 'country'])
            ->andWhere(['status' => self::STATUS_ACTIVE])
            ->andWhere(['is not', 'country', null])
            ->groupBy(['country'])
            ->indexBy('country')
            ->column();
    }

    public static function byGender()
    {
        $query = static::find();
        $count = $query->andWhere(['status' => self::STATUS_ACTIVE])->count();

        if ($count) {
            $male = $query->where(['gender' => self::GENDER_MALE])
                ->andWhere(['status' => self::STATUS_ACTIVE])
                ->count();
            $male = round(100 * $male / $count, 2);
            $female = $query->where(['gender' => self::GENDER_FEMALE])
                ->andWhere(['status' => self::STATUS_ACTIVE])
                ->count();
            $female = round(100 * $female / $count, 2);
        } else {
            $male = $female = 0;
        }

        return [
            ['label' => 'Male', 'value' => $male],
            ['label' => 'Female', 'value' => $female],
            ['label' => 'Other', 'value' => 100 - $male - $female],
        ];
    }

    public static function byAge()
    {
        $query = static::find();
        $count = $query->andWhere(['status' => self::STATUS_ACTIVE])->count();

        if ($count) {
            $range0 = $query->where(['between', 'birthday', date(self::DATE_FORMAT, strtotime('-17 year')), date(self::DATE_FORMAT)])
                ->andWhere(['status' => self::STATUS_ACTIVE])
                ->count();
            $range0 = round(100 * $range0 / $count, 2);
            $range1 = $query->where(['between', 'birthday', date(self::DATE_FORMAT, strtotime('-24 year')), date(self::DATE_FORMAT, strtotime('-18 year'))])
                ->andWhere(['status' => self::STATUS_ACTIVE])
                ->count();
            $range1 = round(100 * $range1 / $count, 2);
            $range2 = $query->where(['between', 'birthday', date(self::DATE_FORMAT, strtotime('-34 year')), date(self::DATE_FORMAT, strtotime('-25 year'))])
                ->andWhere(['status' => self::STATUS_ACTIVE])
                ->count();
            $range2 = round(100 * $range2 / $count, 2);
            $range3 = $query->where(['between', 'birthday', date(self::DATE_FORMAT, strtotime('-49 year')), date(self::DATE_FORMAT, strtotime('-35 year'))])
                ->andWhere(['status' => self::STATUS_ACTIVE])
                ->count();
            $range3 = round(100 * $range3 / $count, 2);
            $range4 = $query->where(['<=', 'birthday', date(self::DATE_FORMAT, strtotime('-50 year'))])
                ->andWhere(['status' => self::STATUS_ACTIVE])
                ->count();
            $range4 = round(100 * $range4 / $count, 2);
        } else {
            $range0 = $range1 = $range2 = $range3 = $range4 = 0;
        }


        return [
            ['label' => '18-', 'value' => $range0],
            ['label' => '18-24', 'value' => $range1],
            ['label' => '25-34', 'value' => $range2],
            ['label' => '35-49', 'value' => $range3],
            ['label' => '50+', 'value' => $range4],
        ];
    }

    public static function count()
    {
        return static::find()->andWhere(['status' => self::STATUS_ACTIVE])->count();
    }

    /**
     * @param int $limit
     * @return User[]
     */
    public static function lastRegistered($limit = 8)
    {
        return static::find()
            ->addSelect(static::tableName() . '.*')
            ->addSelect(['name' => new Expression('CONCAT_WS(\' \', [[first_name]], [[last_name]])')])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit)
            ->all();
    }
}