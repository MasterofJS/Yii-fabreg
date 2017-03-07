<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/23/16
 * Time: 5:54 PM
 */

namespace rest\modules\v1\models;

use yii\filters\RateLimitInterface;
use yii\helpers\ArrayHelper;
use yii\web\Link;
use yii\web\Linkable;

class User extends \common\models\User implements Linkable, RateLimitInterface
{

    /**
     * @inheritdoc
     */
    public function getLinks()
    {
        return [];
    }

    public function full()
    {
        return $this->toArray([], ['settings', 'profile', 'cover', 'avatar', 'social_links']);
    }

    public function with($expand)
    {
        return $this->toArray([], $expand);
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['has_confirmed_email'] = 'hasConfirmedEmail';
        $sensitive = ['id', 'status', 'avatar_id', 'cover_id', 'auth_key', 'password_hash', 'password_reset_token', 'api_usage', 'email_confirmation_token', 'created_at', 'updated_at'];
        return array_diff_key($fields, array_combine($sensitive, $sensitive));
    }

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return [];
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $data = [];

        foreach ($this->resolveFields($fields, $expand) as $field => $definition) {
            $data[$field] = is_string($definition) ? $this->$definition : call_user_func($definition, $this, $field);
        }
        if ($this instanceof Linkable) {
            $links = $this->getLinks();

            if (in_array('avatar', $expand)) {
                if (!empty($this->avatar_id)) {
                    $links[static::REL_AVATAR] = $this->avatar->getUrl();
                }
            }
            if (in_array('cover', $expand)) {
                if (!empty($this->cover_id)) {
                    $links[static::REL_COVER] = $this->cover->getUrl();
                }
            }
            if (in_array('social_links', $expand)) {
                $socialLinks = [];
                if ($this->socialLinks) {
                    foreach ($this->socialLinks as $socialLink) {
                        $source = ($socialLink->source == 'google') ? 'plus.' . $socialLink->source : $socialLink->source;
                        $socialLinks[$socialLink->source] = 'http://' . $source . '.com/' . $socialLink->source_id;
                    }
                    $links['social_links'] = $socialLinks;
                }
            }
            $data['_links'] = Link::serialize($links);
        }

        return $recursive ? static::convertToArray($data, $fields, $expand) : $data;
    }


    protected function resolveFields(array $fields, array $expand)
    {
        $result = [];
        foreach ($this->fields() as $field => $definition) {
            if (is_int($field)) {
                $field = $definition;
            }
            if (empty($fields) || in_array($field, $fields, true)) {
                $result[$field] = $definition;
            }
        }

        if (in_array('permissions', $expand) && \Yii::$app->user->id != $this->id) {
            $result['can_viewer_mute'] = 'canViewerMute';
            $result['can_viewer_unmute'] = 'canViewerUnmute';
        }
        if (!empty($expand)) {
            foreach ($this->extraFields() as $field => $definition) {
                if (is_int($field)) {
                    $field = $definition;
                }
                if (in_array($field, $expand, true)) {
                    $result[$field] = $definition;
                }
            }
        };
        if ((\Yii::$app->user->id == $this->id && !in_array('settings', $expand)) || \Yii::$app->user->id != $this->id) {
            $settings = ['email', 'gender', 'country', 'birthday', 'has_confirmed_email', 'show_nswf', 'notify_post_upvote', 'notify_post_comment', 'notify_post_share', 'notify_comment_upvote', 'notify_comment_reply'];
            $result = array_diff_key($result, array_combine($settings, $settings));
        }

        if (!in_array('profile', $expand)) {
            $profile = ['first_name', 'last_name', 'about', 'hide_upvotes'];
            $result = array_diff_key($result, array_combine($profile, $profile));
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getRateLimit($request, $action)
    {
        $rateLimit = \Yii::$app->get('variables')->get('api.usage', str_replace('/', '.', $action->uniqueId));
        if (!$rateLimit) {
            //Rate limit skipped
            return [];
        }
        return explode('/', $rateLimit);
    }

    /**
     * @inheritdoc
     */
    public function loadAllowance($request, $action)
    {
        $allowance = ArrayHelper::getValue($this->api_usage, "{$action->uniqueId}.allowance", true);
        $allowance_updated_at = ArrayHelper::getValue($this->api_usage, "{$action->uniqueId}.allowance_updated_at", 0);
        return [$allowance, $allowance_updated_at];
    }

    /**
     * @inheritdoc
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $apiUsage = $this->api_usage;
        if (!isset($apiUsage[$action->uniqueId])) {
            $apiUsage[$action->uniqueId] = [];
        }
        $apiUsage[$action->uniqueId]['allowance'] = $allowance;
        $apiUsage[$action->uniqueId]['allowance_updated_at'] = $timestamp;
        $this->api_usage = $apiUsage;
        $this->save();
    }
}