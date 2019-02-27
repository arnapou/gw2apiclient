<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api\Model;

/**
 *
 * @method int getId()
 * @method string getType()
 * @method string getUser()
 * @method string getInvitedBy() for type = invited
 * @method string getDeclinedBy() for type = invite_declined
 * @method string getKickedBy() for type = kick
 * @method string getChangedBy() for type = rank_change
 * @method string getOldRank() for type = rank_change
 * @method string getNewRank() for type = rank_change
 * @method string getOperation() for type = stash
 * @method string getItemId() for type = stash, treasury
 * @method string getCount() for type = stash, treasury
 * @method string getCoins() for type = stash
 * @method string getMotd() for type = motd
 * @method string getAction() for type = upgrade
 * @method string getUpgradeId() for type = upgrade
 * @method string getActivity() for type = influence
 * @method string getTotalParticipants() for type = influence
 * @method string getParticipants() for type = influence
 */
class GuildLog extends AbstractObject
{
    const TYPE_JOINED          = 'joined';
    const TYPE_INVITED         = 'invited';
    const TYPE_INVITE_DECLINED = 'invite_declined';
    const TYPE_KICK            = 'kick';
    const TYPE_                = 'rank_change';
    const TYPE_TREASURY        = 'treasury';
    const TYPE_STASH           = 'stash';
    const TYPE_MOTD            = 'motd';
    const TYPE_UPGRADE         = 'upgrade';
    const TYPE_INFLUENCE       = 'influence';
    const OPERATION_DEPOSIT    = 'deposit';
    const OPERATION_WITHDRAW   = 'withdraw';

    /**
     *
     * @var Item
     */
    protected $item;

    /**
     *
     * @var GuildUpgrade
     */
    protected $upgrade;

    /**
     *
     * @param array $data
     */
    protected function setData($data)
    {
        parent::setData($data);

        if (isset($data['count'], $data['item_id'])) {
            $this->item = new InventorySlot($this->getEnvironment(), [
                'id'    => $data['item_id'],
                'count' => $data['count'],
            ]);
        }

        if (isset($data['upgrade_id'])) {
            $this->upgrade = new GuildUpgrade($this->getEnvironment(), $data['upgrade_id']);
        }
    }

    /**
     *
     * @return string YYYY-MM-DD HH:MM:SS UTC format
     */
    public function getTime()
    {
        $date = $this->getData('time');
        return $date ? gmdate('Y-m-d H:i:s', strtotime($date)) : null;
    }

    /**
     *
     * @return GuildUpgrade
     */
    public function getUpgrade()
    {
        return $this->upgrade;
    }

    /**
     *
     * @return InventorySlot
     */
    public function getItem()
    {
        return $this->item;
    }
}
