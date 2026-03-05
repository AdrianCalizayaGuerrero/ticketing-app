<?php

namespace App\Enums;

enum TickedStatus:string
{
    case OPEN = 'OPEN';
    case IN_PROGRESS = 'IN_PROGRESS';
    case WAITING_FOR_USER = 'WAITING_FOR_USER';
    case RESOLVED = 'RESOLVED';
    case CLOSED = 'CLOSED';
}
