<?php

use SilverStripe\ORM\DataObject;

class Contact extends DataObject
{

    private static $db = [
        'Name' => 'Varchar',
        'Email' => 'Varchar',
        'Company' => 'Varchar'
    ];
}