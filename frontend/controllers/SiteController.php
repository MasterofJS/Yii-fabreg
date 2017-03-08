<?php
namespace frontend\controllers;

use rest\modules\v1\models\Post;
use rest\modules\v1\models\User;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @param null $level1
     * @param null $level2
     * @param null $level3
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionIndex($level1 = null, $level2 = null, $level3 = null)
    {
        $this->getView()->registerMetaTag(['property' => 'og:site_name', 'content' => Yii::$app->name]);
        $this->getView()->registerMetaTag(['property' => 'og:title', 'content' => Yii::$app->name]);
        switch ($level1) {
            case 'user':
                $user = User::findByUsername($level2);
                if (null === $user) {
                    throw new NotFoundHttpException("User not found: @$level2");
                }
                $this->getView()->title = $user->first_name . ' ' . $user->last_name . ' - Unicorno';
                break;
            case 'posts':
                $post = Post::findByHashId($level2);
                if (null === $post) {
                    throw new NotFoundHttpException("Post not found: $level2.");
                }
                $this->getView()->title = $post->description . ' - Unicorno';
                $this->getView()->registerMetaTag(['name' => 'description', 'content' => $post->description]);
                $this->getView()->registerMetaTag(
                    [
                        'property' => 'article:author',
                        'content' => 'https://www.facebook.com/Unicorno-1616356291963803/'
                    ]
                );
                $this->getView()->registerMetaTag(['property' => 'og:type', 'content' => 'article']);
                $this->getView()->registerMetaTag(['property' => 'og:description', 'content' => $post->description]);
                $postLinks = $post->getLinks();
		$url = Url::to(['site/index', 'level1' => 'posts', 'level2' => $post->hashId], true);
		$imageUrl = $post->getPhotoUrl(true);
		if ($postLinks && isset($postLinks['video']) && isset($postLinks['video']['gif'])) {
                	$url = $postLinks['video']['gif'];
			$imageUrl = $postLinks['video']['gif'];
		}
		$this->getView()->registerMetaTag(['property' => 'og:image','content' => $imageUrl]); // 'content' => $post->getPhotoUrl(true)]);
		/*$this->getView()->registerMetaTag(
                    [
                        'property' => 'og:url',
                        'content' => Url::to(['site/index', 'level1' => 'posts', 'level2' => $post->hashId], true)
                    ]
                );*/
		$this->getView()->registerMetaTag(['property'=>'og:url','content'=>$url]);
		$this->getView()->registerMetaTag(['property'=>'twitter:image','content'=>$imageUrl]);
		$this->getView()->registerMetaTag(['property'=>'twitter:card','content'=>'summary_large_image']);
                break;
            case 'terms':
                $this->getView()->title = 'Termos de Serviço - Unicorno';
                break;
            case 'privacy':
                $this->getView()->title = 'Termos de Privacidade - Unicorno';
                break;
            case 'contact':
                $this->getView()->title = 'Sugestões - Unicorno';
                break;
            case 'search':
                break;
            case 'fresh':
                $this->getView()->title = 'Em Alta - Unicorno';
                break;
            case 'trending':
                $this->getView()->title = 'Novos - Unicorno';
                break;
                break;
            case 'login':
                break;
            case 'signup':
                $this->getView()->title = 'Registre-se - Unicorno';
                break;
            case 'reset-password':
                $this->getView()->title = 'Resetar Senha - Unicorno';
                break;
            case 'confirm-email':
                $this->getView()->title = 'Verificar seu email - Unicorno';
                break;
            case 'forgot':
                $this->getView()->title = 'Esqueci minha senha - Unicorno';
                break;
            case null: //home page
                $this->getView()->registerMetaTag(['property' => 'og:type', 'content' => 'website']);
                $this->getView()->registerMetaTag(
                    [
                        'property' => 'og:description',
                        'content' => 'Unicorno, sem sentido, engraçado e estranho'
                    ]
                );
                $this->getView()->registerMetaTag(
                    [
                        'property' => 'og:image',
                        'content' => Url::to('dist/images/UnicornoPic.png', true)
                    ]
                );
                $this->getView()->registerMetaTag(
                    [
                        'property' => 'og:url',
                        'content' => Url::to(['site/index'], true)
                    ]
                );
                $this->getView()->title = 'Unicorno - O bom de ser idiota.';
                $this->getView()->registerMetaTag([
                    'name' => 'description',
                    'content' => 'Unicorno, o bom de ser idiota é rir dessas coisas.'
                        .' Site sem sentido, engraçado e estranho.'
                ]);
                break;
            default:
                if (Yii::$app->user->isGuest && $level1 && $level1 != 'terms' && $level1 != 'privacy') {
                    throw new NotFoundHttpException;
                }
        }
        if (empty($this->getView()->title)) {
            $this->getView()->title = 'Unicorno';
        }
        $this->getView()->registerMetaTag(
            [
                'property' => 'fb:app_id',
                'content' => Yii::$app->get('variables')->get('social', 'facebook.app_id')
            ]
        );
        $this->getView()->registerMetaTag(
            [
                'property' => 'google:client_id',
                'content' => Yii::$app->get('variables')->get('social', 'google.client_id')
            ]
        );
        if (Yii::$app->session->hasFlash('error')) {
            $this->getView()->registerMetaTag(
                [
                    'name' => 'alert:alert',
                    'content' => Yii::$app->session->getFlash('error')
                ]
            );
        }
        return $this->render('index');
    }
}
