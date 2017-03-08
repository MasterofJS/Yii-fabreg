<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/3/16
 * Time: 11:27 AM
 */

namespace common\interfaces;


interface Notifiable
{
    public function notify();

    public function getLastActorId();

    public function getLastCount();
}