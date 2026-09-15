<?php

declare(strict_types=1);

namespace App\Pipe\App\Input;

use App\Enum\App\ENotificationType;
use App\Trait\GraphQLVariablesHelper;

final class InputNotification
{
    use GraphQLVariablesHelper;

    public int $id                              = 0;

    public int $trnProjectId                    = 0;

    public ENotificationType $eNotificationType = ENotificationType::INVALID;

    public string $notificationValue            = '';

    /**
     * @param  object{id: int, trnProjectId: int, notificationType: int, notificationValue: string}  $object
     */
    public static function createFromObject(object $object): InputNotification
    {
        $new                    = new InputNotification;

        $new->id                = $object->id;
        $new->trnProjectId      = $object->trnProjectId;
        $new->eNotificationType = ENotificationType::tryFrom(
            $object->notificationType
        ) ?? ENotificationType::INVALID;
        $new->notificationValue = $object->notificationValue;

        return $new;
    }
}
