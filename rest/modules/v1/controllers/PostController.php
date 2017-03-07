<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/25/16
 * Time: 11:50 AM
 */

namespace rest\modules\v1\controllers;

use common\models\CommentReport;
use common\models\Mute;
use common\models\PostReport;
use common\models\Share;
use rest\modules\v1\actions\LikeAction;
use rest\modules\v1\components\RateLimiter;
use rest\modules\v1\models\Comment;
use rest\modules\v1\models\Like;
use rest\modules\v1\models\Post;
use rest\modules\v1\models\PostForm;
use rest\modules\v1\models\User;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class PostController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['view', 'index', 'comments', 'search', 'featured'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'rateLimiter' => [
                'class' => RateLimiter::className(),
                'only' => ['create']
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'like' => [
                'class' => LikeAction::className(),
                'entityClass' => Post::className(),
                'value' => LikeAction::LIKE
            ],
            'dislike' => [
                'class' => LikeAction::className(),
                'entityClass' => Post::className(),
                'value' => LikeAction::DISLIKE
            ],
        ];
    }

    public function actionShare($id)
    {
        $post = $this->findPost($id);
        if ($post->getAuthorId() != \Yii::$app->user->id &&
            !Share::find()->andWhere(
                ['post_id' => $post->id, 'network' => \Yii::$app->request->getBodyParam('network')]
            )->exists()
        ) {
            $share = new Share(['post_id' => $post->id, 'network' => \Yii::$app->request->getBodyParam('network')]);
            if (!$share->validate()) {
                \Yii::$app->getResponse()->setStatusCode(422, 'Data Validation Failed.');
                return;
            }
            if (!$share->save()) {
                throw new ServerErrorHttpException("Falha ao compartilhar post(#$id) por razões desconhecidas.");
            }
        }
        \Yii::$app->getResponse()->setStatusCode(204);
    }

    public function actionReport($id)
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        $post = $this->findPost($id);
        if ($post->getAuthorId() == \Yii::$app->user->id) {
            throw new ForbiddenHttpException("Você tento denunciar seu próprio post kkkk.");
        }
        if (!$user->hasReported($post)) {
            $report = new PostReport(['post_id' => $post->id, 'type' => \Yii::$app->request->getBodyParam('type')]);
            if (!$report->validate()) {
                \Yii::$app->getResponse()->setStatusCode(422, 'Data Validation Failed.');
                return;
            }
            if (!$report->save()) {
                throw new ServerErrorHttpException("Falha ao denunciar post(#$id) por razões desconhedicas.");
            }
        }
        \Yii::$app->getResponse()->setStatusCode(204);
    }

    public function actionView($id)
    {
        return $this->findPost($id);
    }

    public function actionUpdate($id)
		{
			$post = $this->findPost($id);
			if ($post->getAuthorId() != \Yii::$app->user->id) {
				throw new ForbiddenHttpException("Você não pode realizar esta ação.");
			}
			$post->load(\Yii::$app->getRequest()->getBodyParams(), '');
			if (!$post->save()) {
				return $post;
			}
			\Yii::$app->getResponse()->setStatusCode(204);
		}

    public function actionDelete($id)
    {
        $post = $this->findPost($id);
        if ($post->getAuthorId() != \Yii::$app->user->id) {
            throw new ForbiddenHttpException("Você não pode realizar esta ação.");
        }
        if (false === $post->delete()) {
            throw new ServerErrorHttpException("Falha ao deletar o post(#$id) por razões desconhecidas. ");
        }
        \Yii::$app->getResponse()->setStatusCode(204);
    }

    public function actionCreate()
    {
        $model = new PostForm();
        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->validate()) {
            return $model;
        }
        $post = $model->post();
        if ($post) {
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(201);
            $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $post->id], true));
        } else {
            throw new ServerErrorHttpException('Falha ao criar post por razões desconhecidas.');
        }
        return $post;
    }

    /**
     * @return ActiveQuery
     */
    private function getBaseQuery()
    {
        //todo remove data provider to Post::list()
        $comment = (new Query)
            ->from(Comment::tableName())
            ->addSelect(['post_id'])
            ->addSelect(['count' => new Expression('COUNT([[id]])')])
            ->groupBy(['post_id']);

        $like = (new Query)
            ->from(Like::tableName())
            ->addSelect(['post_id' => 'entity_id'])
            ->addSelect(['likes' => new Expression('SUM(IF([[value]] = 1, 1, 0))')])
            ->addSelect(['dislikes' => new Expression('SUM(IF([[value]] = -1, 1, 0))')])
            ->andWhere(['entity_type' => Post::TYPE])
            ->groupBy(['post_id']);

        if (!\Yii::$app->user->isGuest) {
            $like->addSelect(
                ['liked' => new Expression(
                    'SUM( IF([[value]] = 1 AND [[user_id]] = :curr_user_id, 1, 0))',
                    ['curr_user_id' => \Yii::$app->user->id]
                )]
            )
                ->addSelect(
                    ['disliked' => new Expression(
                        'SUM( IF([[value]] = -1 AND [[user_id]] = :curr_user_id, -1, 0))',
                        ['curr_user_id' => \Yii::$app->user->id]
                    )]
                );
        }

        $query = Post::find()
            ->alias('post')
            ->orderBy(['created_at' => SORT_DESC])
            ->leftJoin([
                'comment' => $comment
            ], new Expression('`comment`.[[post_id]] = `post`.[[id]]'))
            ->leftJoin([
                'like' => $like
            ], new Expression('`like`.[[post_id]] = `post`.[[id]]'))
            ->addSelect(['`post`.*'])
            ->addSelect(['comments' => new Expression('COALESCE(`comment`.[[count]], 0)')])
            ->addSelect(['likes' => new Expression('COALESCE(`like`.[[likes]], 0)')])
            ->addSelect(['dislikes' => new Expression('COALESCE(`like`.[[dislikes]], 0)')])
            ->with(['photo']);

        if (!\Yii::$app->user->isGuest) {
            $query->leftJoin(
                ['report' => PostReport::tableName()],
                new Expression(
                    '`report`.[[post_id]] = `post`.[[id]] AND `report`.[[user_id]] = :curr_user_id',
                    ['curr_user_id' => \Yii::$app->user->id]
                )
            )
                ->andWhere('`report`.[[post_id]] IS NULL')
                ->addSelect(
                    ['canViewerReport' => new Expression(
                        'IF(`post`.[[user_id]] = :curr_user_id OR `report`.[[post_id]] IS NOT NULL, :no , :yes)',
                        ['yes' => '1', 'no' => '0', 'curr_user_id' => \Yii::$app->user->id]
                    )]
                )
                ->addSelect(['liked' => new Expression('COALESCE(`like`.[[liked]], 0)')])
                ->addSelect(['disliked' => new Expression('COALESCE(`like`.[[disliked]], 0)')]);
        }
        return $query;
    }

    public function actionSearch($query)
    {
        $query = $this->getBaseQuery()->match($query, count(explode(' ', $query)));
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [10, 100],
            ]
        ]);
    }

    public function actionIndex($filter = 'fresh')
    {
        $query = $this->getBaseQuery()->channel($filter);
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [10, 100],
            ]
        ]);
    }

    public function actionFeatured()
    {
        $query = Post::find()
            ->alias('post')
            ->featured()
            ->leftJoin(
                ['report' => PostReport::tableName()],
                new Expression(
                    '`report`.[[post_id]] = `post`.[[id]] AND `report`.[[user_id]] = :curr_user_id',
                    ['curr_user_id' => \Yii::$app->user->id]
                )
            )
            ->andWhere('`report`.[[post_id]] IS NULL')
            ->addSelect(['`post`.*'])
            ->addSelect(['isFeatured' => new Expression(':yes', [':yes' => '1'])])
            //we don't need all these fields, that's why we won't process their
            ->addSelect(['comments' => new Expression(':no', [':no' => '0'])])
            ->addSelect(['likes' => new Expression(':no', [':no' => '0'])])
            ->addSelect(['dislikes' => new Expression(':no', [':no' => '0'])])
            ->addSelect(['canViewerReport' => new Expression(':no', [':no' => '0'])])
            ->addSelect(['canViewerDelete' => new Expression(':no', [':no' => '0'])])
            ->addSelect(['liked' => new Expression(':no', [':no' => '0'])])
            ->addSelect(['disliked' => new Expression(':no', [':no' => '0'])])
            ->limit(\Yii::$app->get('variables')->get('pageSize', 'featured.posts'))
            ->with(['photo']);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
    }


    public function actionComments($id, $filter = 'fresh')
    {
        $post = $this->findPost($id);

        //todo remove data provider to Post::comments()
        $children = (new Query)
            ->from(Comment::tableName())
            ->addSelect(['parent_id'])
            ->addSelect(['count' => new Expression('COUNT([[id]])')])
            ->andWhere(['IS NOT', 'parent_id', null])
            ->groupBy(['parent_id']);

        $like = (new Query)
            ->from(Like::tableName())
            ->addSelect(['comment_id' => 'entity_id'])
            ->addSelect(['likes' => new Expression('SUM(IF([[value]] = 1, 1, 0))')])
            ->addSelect(['dislikes' => new Expression('SUM(IF([[value]] = -1, 1, 0))')])
            ->andWhere(['entity_type' => Comment::TYPE])
            ->groupBy(['comment_id']);

        if (!\Yii::$app->user->isGuest) {
            $like->addSelect(
                ['liked' => new Expression(
                    'SUM( IF([[value]] = 1 AND [[user_id]] = :curr_user_id, 1, 0))',
                    ['curr_user_id' => \Yii::$app->user->id]
                )]
            )
                ->addSelect(
                    ['disliked' => new Expression(
                        'SUM( IF([[value]] = -1 AND [[user_id]] = :curr_user_id, -1, 0))',
                        ['curr_user_id' => \Yii::$app->user->id]
                    )]
                );
        }

        $query = Comment::find()
            ->alias('comment')
            ->orderBy(Comment::getOrder($filter, SORT_DESC))
            ->leftJoin([
                'children' => $children
            ], new Expression('`children`.[[parent_id]] = `comment`.[[id]]'))
            ->leftJoin([
                'like' => $like
            ], new Expression('`like`.[[comment_id]] = `comment`.[[id]]'))
            ->addSelect(['`comment`.*'])
            ->addSelect(['comments' => new Expression('COALESCE(`children`.[[count]], 0)')])
            ->addSelect(['likes' => new Expression('COALESCE(`like`.[[likes]], 0)')])
            ->addSelect(['dislikes' => new Expression('COALESCE(`like`.[[dislikes]], 0)')])
            ->andWhere(['`comment`.post_id' => $post->id, '`comment`.parent_id' => null])
            ->with(['author' => function ($query) {
                /** @var ActiveQuery $query */
                $query->with('avatar');
                if (!\Yii::$app->user->isGuest) {
                    $query->alias('user')
                        ->leftJoin(
                            ['m' => Mute::tableName()],
                            'receiver_id = id AND sender_id = :curr_user_id',
                            ['curr_user_id' => \Yii::$app->user->id]
                        )
                        ->groupBy(['`user`.[[id]]'])
                        ->addSelect(['`user`.*'])
                        ->addSelect(['mute' => new Expression('COUNT(`m`.receiver_id)')]);
                }

            }]);

        if (!\Yii::$app->user->isGuest) {
            $query->leftJoin(
                ['report' => CommentReport::tableName()],
                new Expression(
                    '`report`.[[comment_id]] = `comment`.[[id]] AND `report`.[[user_id]] = :curr_user_id',
                    ['curr_user_id' => \Yii::$app->user->id]
                )
            )
                ->addSelect(
                    ['canViewerReport' => new Expression(
                        'IF(`comment`.[[user_id]] = :curr_user_id, :no , :yes)',
                        ['yes' => '1', 'no' => '0', 'curr_user_id' => \Yii::$app->user->id]
                    )]
                )
                ->addSelect(['liked' => new Expression('COALESCE(`like`.[[liked]], 0)')])
                ->addSelect(['disliked' => new Expression('COALESCE(`like`.[[disliked]], 0)')])
                ->andWhere('`report`.[[comment_id]] IS NULL');
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [10, 40],
            ]
        ]);
    }

    /**
     * @param $id
     * @return Post
     * @throws NotFoundHttpException
     */
    public function findPost($id)
    {
        $post = Post::findByHashId($id);
        if (null === $post) {
            throw new NotFoundHttpException("Post não encontrado: $id.");
        }
        return $post;
    }
}
