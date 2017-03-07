<?php

namespace rest\modules\v1\models;


class Avatar extends \common\models\Avatar
{
    public function fields()
    {
        $fields = parent::fields();
        $sensitive = ['id', 'extension', 'type', 'is_default', 'is_over_max_height', 'size', 'created_at', 'updated_at'];
        return array_diff_key($fields, array_combine($sensitive, $sensitive));
    }
}