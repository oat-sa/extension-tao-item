<?php

class taoItems_models_classes_preview_ItemNotFoundException extends Exception implements common_exception_UserReadableException
{

    public function getUserMessage()
    {
        return __('Item not found');
    }

}