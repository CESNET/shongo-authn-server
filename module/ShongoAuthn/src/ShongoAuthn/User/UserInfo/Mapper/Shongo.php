<?php

namespace ShongoAuthn\User\UserInfo\Mapper;

use PhpIdServer\User\UserInterface;
use PhpIdServer\User\UserInfo\Mapper\AbstractMapper;


class Shongo extends AbstractMapper
{


    public function getUserInfoData (UserInterface $user)
    {
        $data = $user->toArray();
        /*
        $data['eduidcz_id'] = $data['id'];
        $data['id'] = $data['perun_id'];
        unset($data['perun_id']);
        */
        return $data;
    }
}