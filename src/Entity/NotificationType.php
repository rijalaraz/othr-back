<?php

namespace App\Entity;

final class NotificationType 
{
    public const JOIN_NETWORK = 'join_network';
    public const SWAAPE_REQUEST = 'swaape_request';
    public const RECOMMAND_USER = 'recommand_user';
    public const TYPES = [self::JOIN_NETWORK, self::SWAAPE_REQUEST, self::RECOMMAND_USER];
}