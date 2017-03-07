<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/24/16
 * Time: 3:22 PM
 */

namespace rest\modules\v1\controllers;


use common\models\Auth;
use common\models\Mute;
use common\models\PostReport;
use rest\modules\v1\models\AccountSettingsForm;
use rest\modules\v1\models\ChangePasswordForm;
use rest\modules\v1\models\Comment;
use rest\modules\v1\models\Like;
use rest\modules\v1\models\Post;
use rest\modules\v1\models\ProfileForm;
use rest\modules\v1\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class UserController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['view', 'feed', 'posts', 'likes', 'comments'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);
    }

    public function actionFeed($username)
    {
        $user = $this->findUser($username);
        $comment = (new Query)
            ->from(Comment::tableName())
            ->addSelect(['post_id'])
            ->addSelect(['date' => new Expression('MAX(IF([[user_id]] = :user_id, [[updated_at]], NULL))', ['user_id' => $user->id])])
            ->addSelect(['count' => new Expression('COUNT([[id]])')])
            ->groupBy(['post_id']);

        $like = (new Query)
            ->from(Like::tableName())
            ->addSelect(['post_id' => 'entity_id'])
            ->addSelect(['likes' => new Expression('SUM(IF([[value]] = 1, 1, 0))')])
            ->addSelect(['dislikes' => new Expression('SUM(IF([[value]] = -1, 1, 0))')])
            ->addSelect(['date' => new Expression('MAX(IF([[value]] = 1 AND [[user_id]] = :user_id, [[updated_at]], NULL))', ['user_id' => $user->id])])
            ->andWhere(['entity_type' => Post::TYPE])
            ->groupBy(['post_id']);

        if (!\Yii::$app->user->isGuest) {
            $like->addSelect(['liked' => new Expression('SUM(IF([[value]] = 1 AND [[user_id]] = :curr_user_id, 1, 0))', ['curr_user_id' => \Yii::$app->user->id])])
                ->addSelect(['disliked' => new Expression('SUM(IF([[value]] = -1 AND [[user_id]] = :curr_user_id, -1, 0))', ['curr_user_id' => \Yii::$app->user->id])]);
        }

        $query = Post::find()
            ->alias('post')
            ->orderBy(['date' => SORT_DESC])
            ->leftJoin([
                'comment' => $comment
            ], new Expression('`comment`.[[post_id]] = `post`.[[id]]'))
            ->leftJoin([
                'like' => $like
            ], new Expression('`like`.[[post_id]] = `post`.[[id]]'))
            ->addSelect(['`post`.*'])
            ->addSelect(['date' => new Expression('GREATEST(COALESCE(`comment`.[[date]], 0), COALESCE(`like`.[[date]], 0), `post`.[[created_at]])')])
            ->addSelect(['lastLikeDate' => '`like`.date'])
            ->addSelect(['lastCommentDate' => '`comment`.date'])
            ->addSelect(['comments' => new Expression('COALESCE(`comment`.[[count]], 0)')])
            ->addSelect(['likes' => new Expression('COALESCE(`like`.[[likes]], 0)')])
            ->addSelect(['dislikes' => new Expression('COALESCE(`like`.[[dislikes]], 0)')])
            ->with(['photo']);

        if (!\Yii::$app->user->isGuest) {
            $query->leftJoin(['report' => PostReport::tableName()], new Expression('`report`.[[post_id]] = `post`.[[id]] AND `report`.[[user_id]] = :curr_user_id', ['curr_user_id' => \Yii::$app->user->id]))
                ->andWhere('`report`.[[post_id]] IS NULL')
                ->addSelect(['canViewerReport' => new Expression('IF(`post`.[[user_id]] = :curr_user_id OR `report`.[[post_id]] IS NOT NULL, :no , :yes)', ['yes' => '1', 'no' => '0', 'curr_user_id' => \Yii::$app->user->id])])
                ->addSelect(['liked' => new Expression('COALESCE(`like`.[[liked]], 0)')])
                ->addSelect(['disliked' => new Expression('COALESCE(`like`.[[disliked]], 0)')]);
        }

        $query->andHaving(['OR', ['`post`.[[user_id]]' => $user->id], ['IS NOT', 'lastLikeDate', null], ['IS NOT', 'lastCommentDate', null]]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [10, 100],
            ]
        ]);
    }

    public function actionPosts($username)
    {
        $user = $this->findUser($username);
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
            $like->addSelect(['liked' => new Expression('SUM(IF([[value]] = 1 AND [[user_id]] = :curr_user_id, 1, 0))', ['curr_user_id' => \Yii::$app->user->id])])
                ->addSelect(['disliked' => new Expression('SUM(IF([[value]] = -1 AND [[user_id]] = :curr_user_id, -1, 0))', ['curr_user_id' => \Yii::$app->user->id])]);
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
            if (Yii::$app->user->id == $user->id) {
                $query->addSelect(['canViewerReport' => new Expression(':no', ['no' => '0'])]);
            } else {
                $query->leftJoin(['report' => PostReport::tableName()], new Expression('`report`.[[post_id]] = `post`.[[id]] AND `report`.[[user_id]] = :curr_user_id', ['curr_user_id' => \Yii::$app->user->id]))
                    ->addSelect(['canViewerReport' => new Expression('IF(`post`.[[user_id]] = :curr_user_id OR `report`.[[post_id]] IS NOT NULL, :no , :yes)', ['yes' => '1', 'no' => '0', 'curr_user_id' => \Yii::$app->user->id])]);

            }
            $query->addSelect(['liked' => new Expression('COALESCE(`like`.[[liked]], 0)')])
                ->addSelect(['disliked' => new Expression('COALESCE(`like`.[[disliked]], 0)')]);
        }
        $query->andWhere(['`post`.[[user_id]]' => $user->id]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [10, 100],
            ]
        ]);
    }

    public function actionLikes($username)
    {
        $user = $this->findUser($username);
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
            ->addSelect(['date' => new Expression('MAX(IF([[value]] = 1 AND [[user_id]] = :user_id, [[updated_at]], NULL))', ['user_id' => $user->id])])
            ->andWhere(['entity_type' => Post::TYPE])
            ->groupBy(['post_id']);

        if (!\Yii::$app->user->isGuest && Yii::$app->user->id != $user->id) {
            $like->addSelect(['liked' => new Expression('SUM(IF([[value]] = 1 AND [[user_id]] = :curr_user_id, 1, 0))', ['curr_user_id' => \Yii::$app->user->id])])
                ->addSelect(['disliked' => new Expression('SUM(IF([[value]] = -1 AND [[user_id]] = :curr_user_id, -1, 0))', ['curr_user_id' => \Yii::$app->user->id])]);
        }

        $query = Post::find()
            ->alias('post')
            ->orderBy(['date' => SORT_DESC])
            ->leftJoin([
                'comment' => $comment
            ], new Expression('`comment`.[[post_id]] = `post`.[[id]]'))
            ->leftJoin([
                'like' => $like
            ], new Expression('`like`.[[post_id]] = `post`.[[id]]'))
            ->addSelect(['`post`.*'])
            ->addSelect(['date' => '`like`.date'])
            ->addSelect(['comments' => new Expression('COALESCE(`comment`.[[count]], 0)')])
            ->addSelect(['likes' => new Expression('COALESCE(`like`.[[likes]], 0)')])
            ->addSelect(['dislikes' => new Expression('COALESCE(`like`.[[dislikes]], 0)')])
            ->with(['photo']);

        if (!\Yii::$app->user->isGuest) {
            if (Yii::$app->user->id == $user->id) {
                $query->addSelect(['liked' => new Expression(':yes', ['yes' => '1'])])
                    ->addSelect(['disliked' => new Expression(':no', ['no' => '0'])]);
            } else {
                $query->addSelect(['liked' => new Expression('COALESCE(`like`.[[liked]], 0)')])
                    ->addSelect(['disliked' => new Expression('COALESCE(`like`.[[disliked]], 0)')]);
            }
            $query->leftJoin(['report' => PostReport::tableName()], new Expression('`report`.[[post_id]] = `post`.[[id]] AND `report`.[[user_id]] = :curr_user_id', ['curr_user_id' => \Yii::$app->user->id]))
                ->andWhere('`report`.[[post_id]] IS NULL')
                ->addSelect(['canViewerReport' => new Expression('IF(`post`.[[user_id]] = :curr_user_id OR `report`.[[post_id]] IS NOT NULL, :no , :yes)', ['yes' => '1', 'no' => '0', 'curr_user_id' => \Yii::$app->user->id])]);
        }
        $query->andHaving(['IS NOT', 'date', null]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [10, 100],
            ]
        ]);
    }

    public function actionComments($username)
    {
        $user = $this->findUser($username);
        $comment = (new Query)
            ->from(Comment::tableName())
            ->addSelect(['post_id'])
            ->addSelect(['date' => new Expression('MAX(IF([[user_id]] = :user_id, [[updated_at]], NULL))', ['user_id' => $user->id])])
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
            $like->addSelect(['liked' => new Expression('SUM(IF([[value]] = 1 AND [[user_id]] = :curr_user_id, 1, 0))', ['curr_user_id' => \Yii::$app->user->id])])
                ->addSelect(['disliked' => new Expression('SUM(IF([[value]] = -1 AND [[user_id]] = :curr_user_id, -1, 0))', ['curr_user_id' => \Yii::$app->user->id])]);
        }

        $query = Post::find()
            ->alias('post')
            ->orderBy(['date' => SORT_DESC])
            ->leftJoin([
                'comment' => $comment
            ], new Expression('`comment`.[[post_id]] = `post`.[[id]]'))
            ->leftJoin([
                'like' => $like
            ], new Expression('`like`.[[post_id]] = `post`.[[id]]'))
            ->addSelect(['`post`.*'])
            ->addSelect(['date' => '`comment`.date'])
            ->addSelect(['comments' => new Expression('COALESCE(`comment`.[[count]], 0)')])
            ->addSelect(['likes' => new Expression('COALESCE(`like`.[[likes]], 0)')])
            ->addSelect(['dislikes' => new Expression('COALESCE(`like`.[[dislikes]], 0)')])
            ->with(['photo']);

        if (!\Yii::$app->user->isGuest) {
            $query->leftJoin(['report' => PostReport::tableName()], new Expression('`report`.[[post_id]] = `post`.[[id]] AND `report`.[[user_id]] = :curr_user_id', ['curr_user_id' => \Yii::$app->user->id]))
                ->andWhere('`report`.[[post_id]] IS NULL')
                ->addSelect(['canViewerReport' => new Expression('IF(`post`.[[user_id]] = :curr_user_id OR `report`.[[post_id]] IS NOT NULL, :no , :yes)', ['yes' => '1', 'no' => '0', 'curr_user_id' => \Yii::$app->user->id])])
                ->addSelect(['liked' => new Expression('COALESCE(`like`.[[liked]], 0)')])
                ->addSelect(['disliked' => new Expression('COALESCE(`like`.[[disliked]], 0)')]);
        }
        $query->andHaving(['IS NOT', 'date', null]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [10, 100],
            ]
        ]);
    }

    public function actionView($username)
    {
        return $this->findUser($username);
    }

    public function actionMute($username)
    {
        $sender = $this->findUser($username);
        /** @var User $receiver */
        $receiver = Yii::$app->user->identity;
        if ($sender->id == $receiver->id) {
            throw new ForbiddenHttpException("Você tentou colocar a sí mesmo no mudo kkkk.");
        }
        if (!$receiver->hasTurnedOff($sender->id)) {
            $mute = new Mute(['sender_id' => $sender->id]);
            if (!$mute->save()) {
                throw new ServerErrorHttpException("Falha ao colocar @$username no mudo por razões deconhecidas.");
            }
        }
        Yii::$app->getResponse()->setStatusCode(204);
    }

    public function actionUnmute($username)
    {
        $sender = $this->findUser($username);
        /** @var User $receiver */
        $receiver = Yii::$app->user->identity;
        if ($sender->id != $receiver->id) {
            Mute::deleteAll(['sender_id' => $sender->id, 'receiver_id' => $receiver->id]);
        }
        Yii::$app->getResponse()->setStatusCode(204);
    }

    public function actionMe()
    {
        /** @var $user User */
        $user = Yii::$app->user->identity;
        return $user->full();
    }

    public function actionConfirmEmail()
    {
        /** @var $user User */
        $user = Yii::$app->user->identity;
        if (!$user->sendEmailConfirmationLink()) {
            throw new ServerErrorHttpException('Falha a enviar token de email de confirmação.');
        }
        Yii::$app->getResponse()->setStatusCode(204);
    }

    public function actionDelete()
    {
        /** @var $user User */
        $user = Yii::$app->user->identity;
        $user->deletion_reason = Yii::$app->getRequest()->getBodyParam('deletion_reason');
        $user->status = User::STATUS_DELETED;
        if (!$user->validate(['deletion_reason'])) {
            return $user;
        }
        if (!$user->save()) {
            throw new ServerErrorHttpException('Falha ao deletar sua conta por razões desconhecidas.');
        }
        Yii::$app->getResponse()->setStatusCode(204);
    }

    public function actionSettings()
    {
        $model = new AccountSettingsForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->validate()) {
            return $model;
        }
        /** @var $user User */
        $user = $model->save();
        if (!$user) {
            throw new ServerErrorHttpException('Falha ao atualizar suas configurações por razões desconhecidas.');
        }
        return $user->full();
    }

    public function actionProfile()
    {
        $model = new ProfileForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->validate()) {
            return $model;
        }
        /** @var $user User */
        $user = $model->save();
        if (!$user) {
            throw new ServerErrorHttpException('Falha ao atualizar seu perfil por razões desconhecidas.');
        }
        return $user->full();
    }

    public function actionUpdate()
    {
        $model = new ProfileForm(['scenario' => 'media']);
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->validate()) {
            return $model;
        }
        /** @var $user User */
        $user = $model->save();
        if (!$user) {
            throw new ServerErrorHttpException('Falha ao atualizar seu perfil por razões desconhecidas.');
        }
        return $user->full();
    }

    public function actionChangePassword()
    {
        $model = new ChangePasswordForm();
        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->validate()) {
            return $model;
        }
        if (!$model->changePassword()) {
            throw new ServerErrorHttpException('Falha ao mudar password por razões desconhecidas.');
        }
        Yii::$app->getResponse()->setStatusCode(204);
    }

    /**
     * @param $username
     * @return User
     * @throws NotFoundHttpException
     */
    public function findUser($username)
    {
        $user = User::findByUsername($username);
        if (null === $user) {
            throw new NotFoundHttpException("Usuário não encontrado: @$username");
        }
        return $user;
    }
}