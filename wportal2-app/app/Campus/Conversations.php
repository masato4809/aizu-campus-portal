<?php

declare(strict_types=1);

namespace App\Campus;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Conversations
{
    /** @return array{contacts: Collection<int, \stdClass>, peer: \stdClass|null, messages: Collection<int, \stdClass>} */
    public function get(\stdClass $me, int $peerId): array
    {
        $messages = collect();
        $contacts = DB::table('campus_students')->where('university', $me->university)->where('id', '!=', $me->id)->select('id', 'name', 'year')->get();
        $peer     = $contacts->firstWhere('id', $peerId);
        if ($peer) {
            $messages = DB::table('campus_messages')->where(function ($q) use ($me, $peer) {
                $q->where('sender_id', $me->id)->where('recipient_id', $peer->id);
            })->orWhere(function ($q) use ($me, $peer) {
                $q->where('sender_id', $peer->id)->where('recipient_id', $me->id);
            })->orderBy('id')->get();
        }

        return compact('contacts', 'peer', 'messages');
    }
}
