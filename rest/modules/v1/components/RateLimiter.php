<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 4/21/16
 * Time: 11:41 AM
 */

namespace rest\modules\v1\components;


use yii\web\TooManyRequestsHttpException;

class RateLimiter extends \yii\filters\RateLimiter
{
    /**
     * @var boolean whether to include rate limit headers in the response
     */
    public $enableRateLimitHeaders = false;

    /**
     * @inheritdoc
     */
    public function checkRateLimit($user, $request, $response, $action)
    {
        $current = time();
        $rateLimit = $user->getRateLimit($request, $action);
        if (!$rateLimit) {
            return;
        }

        list ($limit, $window) = $rateLimit;
        list (, $timestamp) = $user->loadAllowance($request, $action);
        $per = ceil($window / $limit);
        $wait = $per - ($current - $timestamp);
        if ($wait > 0) {
            $time = date('H:i:s', $wait);
            $this->errorMessage = "Por favor, espere {$time} para postar novamente";
            throw new TooManyRequestsHttpException($this->errorMessage);
        } else {
            $user->saveAllowance($request, $action, true, $current);
        }
    }
}