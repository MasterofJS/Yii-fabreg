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
use rest\modules\v1\actions\LikeAction;
use rest\modules\v1\models\Like;
use rest\modules\v1\models\User;
use rest\modules\v1\models\Comment;
use rest\modules\v1\models\CommentForm;
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

class CommentController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['view', 'index'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'view' => [
                'class' => 'yii\rest\ViewAction',
                'modelClass' => Comment::className(),
                'checkAccess' => [$this, 'checkAccess'],
                'findModel' => [$this, 'findComment'],
            ],
            'delete' => [
                'class' => 'yii\rest\DeleteAction',
                'modelClass' => Comment::className(),
                'checkAccess' => [$this, 'checkAccess'],
                'findModel' => [$this, 'findComment'],
            ],
            'like' => [
                'class' => LikeAction::className(),
                'entityClass' => Comment::className(),
                'value' => LikeAction::LIKE
            ],
            'dislike' => [
                'class' => LikeAction::className(),
                'entityClass' => Comment::className(),
                'value' => LikeAction::DISLIKE
            ],
        ];
    }

    public function checkAccess($action, $model = null)
    {
        /** @var Comment $model */
        if (!strcmp($action, 'delete') && $model->getAuthorId() != \Yii::$app->user->id) {
            throw new ForbiddenHttpException("Você não pode realizar esta ação.");
        }
        return true;
    }

    public function actionReport($id)
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        $comment = $this->findComment($id);
        if ($comment->getAuthorId() == \Yii::$app->user->id) {
            throw new ForbiddenHttpException("Você está tentando denunciar seu próprio comentário kkkk.");
        }
        if (!$user->hasReported($comment)) {
            $report = new CommentReport(['comment_id' => $comment->id]);
            if (!$report->save()) {
                throw new ServerErrorHttpException("Falha ao denunciar comentário(#$id) por razões desconhecidas.");
            }
        }
        \Yii::$app->getResponse()->setStatusCode(204);
    }

    public function actionCreate()
    {
        $model = new CommentForm();
        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->validate()) {
            return $model;
        }
        $comment = $model->comment();
        if ($comment) {
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(201);
            $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $comment->id], true));
        } else {
            throw new ServerErrorHttpException('Falha ao enviar comentário por razões desconhecidas.');
        }
        return $comment;
    }

    public function actionIndex($id, $filter = 'fresh')
    {
        //todo remove data provider to Comment::children()
        $comment = $this->findComment($id);
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
            ->orderBy(Comment::getOrder($filter))
            ->leftJoin([
                'like' => $like
            ], new Expression('`like`.[[comment_id]] = `comment`.[[id]]'))
            ->addSelect(['`comment`.*'])
            ->addSelect(['likes' => new Expression('COALESCE(`like`.[[likes]], 0)')])
            ->addSelect(['dislikes' => new Expression('COALESCE(`like`.[[dislikes]], 0)')])
            ->andWhere(['`comment`.parent_id' => $comment->id])
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
                'pageSizeLimit' => [3, 10],
            ]
        ]);
    }

    /**
     * @param $id
     * @return Comment
     * @throws NotFoundHttpException
     */
    public function findComment($id)
    {
        $comment = Comment::findOne(['id' => $id]);
        if (null === $comment) {
            throw new NotFoundHttpException("Comentário não encontrado: $id.");
        }
        return $comment;
    }
}
