<?php

namespace PhpPact\Config\Enum;

enum WriteMode: string
{
    /**
     * The entire file is overwritten
     */
    case OVERWRITE = 'overwrite';
    /**
     * Interactions are added
     */
    case MERGE = 'merge';
}
