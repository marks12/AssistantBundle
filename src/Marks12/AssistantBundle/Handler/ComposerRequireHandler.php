<?php

namespace Marks12\AssistantBundle\Handler;

class ComposerRequireHandler
{
    public static function getRequire()
    {
        return [
            "doctrine/doctrine-migrations-bundle" => "^1.2",
            "nelmio/api-doc-bundle" => "^2.13",
            "doctrine/doctrine-fixtures-bundle" => "^2.3",
            "friendsofsymfony/rest-bundle" => "2.1.0",
            "jms/serializer-bundle" => "^1.1",
            "nelmio/cors-bundle" => "^1.4",
            "phpunit/phpunit" => "^5.6",
            "marks12/socketserverbundle" => ">=0.2",
        ];
    }
}
