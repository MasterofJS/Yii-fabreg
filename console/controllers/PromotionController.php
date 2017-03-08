<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/14/16
 * Time: 11:30 AM
 */

namespace console\controllers;

use common\models\Comment;
use common\models\Like;
use common\models\Post;
use common\models\Share;
use common\models\Variable;
use yii\console\Controller;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

class PromotionController extends Controller
{
    /**
     * frequency(/5 min) yii promotion/process fresh
     * frequency(/5 min) yii promotion/process trending
     *
     * @param $channel
     * @return int
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionProcess($channel)
    {
        $commentWeight = \Yii::$app->get('variables')->get('promotion', 'weight.comment');
        $likeWeight = \Yii::$app->get('variables')->get('promotion', 'weight.like');
        $shareWeight = \Yii::$app->get('variables')->get('promotion', 'weight.share');
        $limit = \Yii::$app->get('variables')->get('promotion', 'limit.default');
        $intervalType = null;

        if (!strcmp($channel, 'fresh')) {
            $interval = \Yii::$app->get('variables')->get('promotion', 'interval.trending', $intervalType);
            $percentage = \Yii::$app->get('variables')->get('promotion', 'percentage.trending');
            $score = \Yii::$app->get('variables')->get('promotion', 'score.trending');
            $upstream = 'trending';
        } elseif (!strcmp($channel, 'trending')) {
            $interval = \Yii::$app->get('variables')->get('promotion', 'interval.hot', $intervalType);
            $percentage = \Yii::$app->get('variables')->get('promotion', 'percentage.hot');
            $score = \Yii::$app->get('variables')->get('promotion', 'score.hot');
            $upstream = 'hot';
        } else {
            $this->stdout("Wrong channel\n", Console::FG_RED);
            return self::EXIT_CODE_ERROR;
        }

        $interval = Variable::toSeconds($interval, $intervalType);
        
        if (!$this->beforeProcess($upstream, $interval, $lastPromotionTime)) {
            return self::EXIT_CODE_NORMAL;
        }
        
        $this->stdout("Processing promotion...\n", Console::BOLD);
        $date = new \DateTime();
        $date->setTimestamp($lastPromotionTime);
        $nppi = Post::find()
            ->fresh()
            ->andWhere([
                'between',
                'created_at',
                $date->format(Post::DATETIME_FORMAT),
                $date->add(new \DateInterval("PT{$interval}S"))->format(Post::DATETIME_FORMAT)
            ])
            ->count();

        if ($nppi > 10) {
            $limit = round($nppi * $percentage / 100);
        }

        $posts = Post::find()
            ->alias('post')
            ->active($channel)
            ->leftJoin([
                'comment' => (new Query())
                    ->from(Comment::tableName())
                    ->addSelect(['post_id'])
                    ->addSelect(['count' => new Expression('COUNT(DISTINCT [[user_id]])')])
                    ->groupBy(['post_id'])
            ], new Expression('`comment`.[[post_id]] = `post`.[[id]]'))
            ->leftJoin([
                'share' => (new Query())
                    ->from(Share::tableName())
                    ->addSelect(['post_id'])
                    ->addSelect(['count' => new Expression('COUNT(*)')])
                    ->groupBy(['post_id'])
            ], new Expression('`share`.[[post_id]] = `post`.[[id]]'))
            ->leftJoin([
                'like' => (new Query())
                    ->from(Like::tableName())
                    ->addSelect(['post_id' => 'entity_id'])
                    ->addSelect(['count' => new Expression('COUNT(*)')])
                    ->andWhere(['entity_type' => Post::TYPE, 'value' => 1])
                    ->groupBy(['post_id'])
            ], new Expression('`like`.[[post_id]] = `post`.[[id]]'))
            ->addSelect('`post`.*')
            ->addSelect([
                'score' => new Expression(
                    '(:CWeight * COALESCE(`comment`.[[count]], 0) '
                    . '+ :LWeight * COALESCE(`like`.[[count]], 0) '
                    . '+ :SWeight * COALESCE(`share`.[[count]], 0))',
                    [
                        ':CWeight' => $commentWeight,
                        ':LWeight' => $likeWeight,
                        ':SWeight' => $shareWeight
                    ]
                )
            ])
            ->limit($limit)
            ->orderBy(['score' => SORT_DESC])
            ->having(['>=', 'score', $score])
            ->all();

        //Original posting date order
        ArrayHelper::multisort($posts, ['created_at'], [SORT_ASC]);
        $count = count($posts);
        if ($count) {
            $sec = floor($interval / $count);
            /** @var Post $post */
            foreach ($posts as $post) {
                $post->channel = $post->channel + 1;
                $post->released_at = (new \DateTime())
                    ->add(new \DateInterval("PT{$sec}S"))
                    ->format(Post::DATETIME_FORMAT);
                if ($post->update()) {
                    $this->stdout("{$post->hashId} is successfully promoted to $upstream channel\n", Console::FG_GREEN);
                } else {
                    $this->stdout("Failed to promote {$post->hashId} to $upstream channel\n", Console::FG_RED);
                }
                $sec += $sec;
            }
            $this->stdout("Promotion to $upstream channel has been completed\n", Console::FG_GREEN);
        } else {
            $this->stdout("No post found to promote to $upstream channel\n", Console::FG_YELLOW);
        }
        $this->afterProcess($upstream, $interval);
        return self::EXIT_CODE_NORMAL;
    }

    /**
     * frequency(/3 min) yii promotion/release
     *
     * @throws \Exception
     */
    public function actionRelease()
    {
        /** @var Post $post */
        foreach (Post::find()->rc()->each() as $post) {
            $post->channel = $post->channel + 1;
            $upstream = $post->getChannelText();
            if ($post->update()) {
                $this->stdout($post->hashId . " is successfully released to $upstream channel\n", Console::FG_GREEN);
            } else {
                $this->stdout("Failed to release {$post->hashId} to $upstream channel\n", Console::FG_RED);
            }
        }
    }

    /**
     * frequency(x/1 hour) yii promotion/retire
     *
     * @throws \Exception
     */
    public function actionRetire()
    {
        $delay = \Yii::$app->get('variables')->get('retirement', 'interval');
        /** @var Post $post */
        foreach (Post::find()->retired(false)->olderThan($delay)->each() as $post) {
            $post->is_retired = true;
            $upstream = $post->getChannelText();
            if ($post->update()) {
                $this->stdout($post->hashId . "has been retired from $upstream channel\n", Console::FG_YELLOW);
            } else {
                $this->stdout("Failed to retire {$post->hashId} from $upstream channel\n", Console::FG_RED);
            }
        }

    }

    /**
     * starts the promotion process. Initializes variables
     * @return int
     */
    public function actionStart()
    {
        $namespace = 'system';
        $variables = ['promotion.trending', 'promotion.hot'];
        foreach ($variables as $key) {
            $model = Variable::find()->andWhere(['namespace' => $namespace, 'key' => $key])->one();
            if (!$model) {
                $model = new Variable();
                $model->key = $key;
                $model->namespace = $namespace;
                $model->type = Variable::TYPE_INTEGER;
            }
            $model->value = time();
            if (!$model->save()) {
                $this->stdout("Failed to start promotion process correctly\n", Console::FG_RED);
                return self::EXIT_CODE_ERROR;
            }
        }
        $this->stdout("promotion process started successfully.\n", Console::FG_GREEN);
        return self::EXIT_CODE_NORMAL;
    }

    /**
     * @param string $upstream upstream channel
     * @param int $interval promotion interval
     * @param int $lastPromotionTime
     * @return bool
     */
    protected function beforeProcess($upstream, $interval, &$lastPromotionTime)
    {
        $namespace = 'system';
        $variable = Variable::find()->andWhere(['namespace' => $namespace, 'key' => "promotion.$upstream"])->one();
        if (is_null($variable)) {
            return false;
        }
        $lastPromotionTime = intval($variable->value);
        if (time() - $lastPromotionTime < $interval) {
            return false;
        }
        return true;
    }

    protected function afterProcess($upstream, $interval)
    {
        $namespace = 'system';
        $variable = Variable::find()->andWhere(['namespace' => $namespace, 'key' => "promotion.$upstream"])->one();
        if (is_null($variable)) {
            return;
        }
        $variable->value += $interval;
        $variable->save();
    }
}
