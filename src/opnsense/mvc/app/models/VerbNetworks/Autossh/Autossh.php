<?php

/*
    Copyright (c) 2018 Verb Networks Pty Ltd <contact@verbnetworks.com>
    Copyright (c) 2018 Nicholas de Jong <me@nicholasdejong.com>
    All rights reserved.

    Redistribution and use in source and binary forms, with or without modification,
    are permitted provided that the following conditions are met:

    1. Redistributions of source code must retain the above copyright notice, this
       list of conditions and the following disclaimer.

    2. Redistributions in binary form must reproduce the above copyright notice,
       this list of conditions and the following disclaimer in the documentation
       and/or other materials provided with the distribution.

    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
    ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
    WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
    DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
    ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
    (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
    LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
    ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
    (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
    SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

namespace VerbNetworks\Autossh;

use OPNsense\Base\BaseModel;
use \OPNsense\Core\Backend;


class Autossh extends BaseModel
{
    public $config_status_filename = '/var/run/autossh.reload_required';
    
    public function setConfigChangeOn()
    {
        touch($this->config_status_filename);
        return;
    }
    
    public function setConfigChangeOff()
    {
        if (file_exists($this->config_status_filename)) {
            unlink($this->config_status_filename);
        }
        return;
    }
    
    public function getConfigChangeStatus()
    {
        return file_exists($this->config_status_filename);
    }
    
    public function performConfigHelper()
    {
        $backend = new Backend();
        $backend_response = @json_decode(trim($backend->configdRun('autossh config_helper')), true);
        
        if(empty($backend_response) || !isset($backend_response['status'])) {
            return array('status'=>'fail',
                'message'=>'Unknown error occured while performing autossh config_helper, '
                . 'review configd logs for more information');
        }
        return $backend_response;
    }
}
