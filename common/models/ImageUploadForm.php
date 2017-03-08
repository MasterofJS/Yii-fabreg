<?php
namespace common\models;

use common\components\UploadedFile;
use common\helpers\FileHelper;
use Yii;
use yii\base\Model;

/**
 * ImageUploadForm is the model behind the upload form.
 *
 */
class ImageUploadForm extends Model
{
    /**
     * @var UploadedFile file attribute
     */
    public $image;
    public $secureName;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [

            ['image', 'image',
                'mimeTypes' => 'image/jpeg, image/png, image/gif',
                'maxSize' => 2 * 1024 * 1024,
                'on' => 'avatar',
            ],

            ['image', 'image',
                'mimeTypes' => 'image/jpeg, image/png',
                'maxSize' => 2 * 1024 * 1024,
                'on' => 'cover',
            ],

            ['image', 'image',
                'mimeTypes' => 'image/jpeg, image/png, image/gif',
                'maxSize' => 8 * 1024 * 1024,
                'on' => 'post',
                'minWidth' => Media::FI_WIDTH,
                'minHeight' => Media::FI_HEIGHT,
            ],
        ];
    }

    /**
     * Change our filename to match our own naming convention
     * @return bool
     */
    public function beforeValidate()
    {
        if ($this->image instanceof UploadedFile) {
            $this->secureName = FileHelper::secureName();
        } else {
            $this->image = 'placeholder.png'; //Does not matter which value
        }
        return parent::beforeValidate();
    }

    public static function findGIFUrl($page_content) {
	$dom_obj = new \DOMDocument();
	@$dom_obj->loadHTML($page_content);
	$meta_val = null;
	foreach($dom_obj->getElementsByTagName('meta') as $meta) {
	if($meta->getAttribute('property')=='og:url' || $meta->getAttribute('name')=='og:url'){
		$url = $meta->getAttribute('content');
	}
	if ($meta->getAttribute('property')=='og:image' || $meta->getAttribute('name')=='og:image'){
	   $url2 = $meta->getAttribute('content');
	}
	}
	if (strpos($url,'.gif') !== false) {
			return $url;
	}
	if (strpos($url2,'.gif') !== false) {
			return $url2;
	}
	$regexp = '/<img[^>]*src="([^"]*?.gif)"[^>]*>/i';
	preg_match_all($regexp, $page_content, $matches);
	if (isset($matches[1][0])) {
			return $matches[1][0];
	}
    }
    /**
     * @param ImageUploadForm $model
     * @param string $preview
     * @return null|array
     * @throws \yii\base\InvalidConfigException
     */
    public static function upload($model, $preview = null)
    {
        if (!$model->image) {
            return ['error' => 'O ficheiro é grande demais. O tamanho não pode exceder 8.00 MiB.'];
        }
	if (mime_content_type($model->image->tempName) == 'text/html') {
		$gif = self::findGIFUrl(file_get_contents($model->image->tempName));
		if ($gif) $model->image = UploadedFile::getInstanceByUrl($gif);
	}
        if (!$model->validate()) {
            return ['error' => $model->getFirstError('image')];
        }

        $extension = $model->image->extension;
        $fileName = $model->secureName . '.' . $extension;
        $path = FileHelper::tmpPath($fileName);

        if (!$model->image->saveAs($path)) {
            return ['error' => 'Failed to save image for unknown reason.'];
        }

        $type = FileHelper::getMimeType($path);
        $size = filesize($path);

        if ($preview) {
            $previewPath = FileHelper::tmpPath($fileName, 'preview_');
            list($width, $height) = explode(',', $preview);
            FileHelper::thumbnail($path, $width, $height, $previewPath);
        }

        $response = [
            'secureName' => $model->secureName,
            //saves temp files in cloud storage
            'downloadUrl' => \Yii::$app->cloudStorage->uploadTmp($fileName),
            //saves temp files in locale server
            //'downloadUrl' => FileHelper::tmpUrl($fileName),
        ];

        if ($preview) {
            //saves temp files in cloud storage
            $response['previewUrl'] = \Yii::$app->cloudStorage->uploadTmp($fileName, 'preview_');
            //saves temp files in locale server
            //$response['previewUrl'] = FileHelper::tmpUrl($fileName, 'preview_');
        }

        $session = Yii::$app->session;
        $session->open();
        $session->set($model->secureName, [
            'name' => $model->secureName,
            'extension' => $extension,
            'type' => $type,
            'size' => $size,
            'scenario' => $model->scenario
        ]);
        $session->close();

        return $response;
    }
}
