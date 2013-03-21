<?php

namespace Shongo\User;


/**
 * User entity specific to the Shongo application.
 * 
 * @method string getOriginalId()
 * @method void setOriginalId() setOriginalId(string $originalId)
 */
class User extends \PhpIdServer\User\User
{

    const FIELD_ORIGINAL_ID = 'original_id';

    const FIELD_REGISTER_TIME = 'register_time';

    protected $_fields = array(
        self::FIELD_ID, 
        self::FIELD_NAME, 
        self::FIELD_GIVEN_NAME, 
        self::FIELD_FAMILY_NAME, 
        self::FIELD_NICKNAME, 
        self::FIELD_EMAIL, 
        self::FIELD_ORIGINAL_ID, 
        self::FIELD_REGISTER_TIME
    );
}