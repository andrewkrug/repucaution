<?php

class Influencers_whitelist extends DataMapper {

    var $table = 'influencers_whitelist';

    /**
     * Add to list
     *
     * @param int $userId
     * @param int $creatorId
     * @return bool
     */
    public function add($userId, $creatorId, $social)
    {
        $result = false;
        $row = $this->where('user_id', $userId)->where('creator_id', $creatorId)->where('social', $social)->get();
        if (!$row->exists()) {
            $this->user_id = $userId;
            $this->creator_id = $creatorId;
            $this->social = $social;
            $result = $this->save();
        }

        return $result;
    }

    /**
     * Remove from list
     *
     * @param int $userId
     * @param int $creatorId
     * @return bool
     */
    public function remove($userId, $creatorId, $social)
    {
        $result = false;
        $row = $this->where('user_id', $userId)->where('creator_id', $creatorId)->where('social', $social)->get();
        if ($row->exists()) {
            $result = $row->delete();
        }

        return $result;
    }

    /**
     * Get id of creators in list
     *
     * @param int $userId
     * @return array
     */
    public function getByUser($userId)
    {
        $result = $this->where('user_id', $userId)->get();
        $creators = array();
        if ($result->exists()) {
            foreach($result as $row) {
                $creators[$row->creator_id] = $row->social;
            }
        }

        return $creators;
    }
    
}