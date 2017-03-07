<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 2/25/16
 * Time: 12:52 PM
 */

namespace rest\modules\v1\components;


use yii\web\HttpException;

class LoginRequiredException extends HttpException
{

    /**
     * Constructor.
     * @param string $message error message
     * @param integer $code error code
     * @param \Exception $previous The previous exception used for the exception chaining.
     */
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(401, $message, $code, $previous);
    }
}