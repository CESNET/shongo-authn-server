<?php

namespace ShongoAuthn\User;


/**
 * User entity specific to the Shongo application.
 * 
 * @method string getOriginalId()
 * @method void setOriginalId(string $originalId)
 * @method integer getPerunId()
 * @method void setPerunId(integer $perunId)
 * @method string getPerunUrl()
 * @method void setPerunUrl(string $perunUrl)
 */
class User extends \InoOicServer\User\User
{

    const FIELD_ORIGINAL_ID = 'original_id';

    const FIELD_REGISTER_TIME = 'register_time';

    const FIELD_PERUN_ID = 'perun_id';

    const FIELD_PERUN_URL = 'perun_url';

    protected $_fields = array(
        self::FIELD_ID,
        self::FIELD_NAME,
        self::FIELD_GIVEN_NAME,
        self::FIELD_FAMILY_NAME,
        self::FIELD_NICKNAME,
        self::FIELD_EMAIL,
        self::FIELD_ORIGINAL_ID,
        self::FIELD_REGISTER_TIME,
        self::FIELD_PERUN_ID,
        self::FIELD_PERUN_URL
    );
}