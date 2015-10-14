<?php

/**
 * Special invite model
 *
 * @author Ajorjik
 */

class Special_invite extends DataMapper{
    var $auto_populate_has_one = TRUE;
    var $auto_populate_has_many = TRUE;

    var $table = 'special_invites';
    var $has_one = array(
        'plan',
        'user',
    );
    
    var $validation = array(
        'invite_code' => array(
            'rules' => array('required'),
        )

    );

    /**
     * Check if row with params exists
     *
     * @param $userId
     * @param $planId
     * @param null $inviteCode
     * @return DataMapper|null
     */
    public function check($planId, $inviteCode)
    {
        $specialInvite = $this->where(array(
                                            'plan_id' => $planId,
                                            'invite_code' => md5($inviteCode),
                                            'end_date > ' => time()
        ))->get(1);

        return ($specialInvite->id) ? $specialInvite : null;

    }
}

