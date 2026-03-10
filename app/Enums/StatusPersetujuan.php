<?php

namespace App\Enums;

enum StatusPersetujuan: string
{
    case DIPROSES = 'diproses';
    case DISETEJUI = 'disetujui';
    case DITOLAK = 'ditolak';
}
