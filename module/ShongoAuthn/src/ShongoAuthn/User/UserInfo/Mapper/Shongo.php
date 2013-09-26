<?php

namespace ShongoAuthn\User\UserInfo\Mapper;

use InoOicServer\User\UserInterface;
use InoOicServer\User\UserInfo\Mapper\AbstractMapper;


class Shongo extends AbstractMapper
{


    public function getUserInfoData (UserInterface $user)
    {
        $data = $user->toArray();
        
        $data['original_id'] = $data['id'];
        $data['id'] = $data['perun_id'];
        unset($data['perun_id']);
        unset($data['perun_vos']);
        
        return $data;
    }
}