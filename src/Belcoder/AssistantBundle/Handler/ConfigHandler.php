<?php

namespace Belcoder\AssistantBundle\Handler;

class ConfigHandler
{
    public static function getBody()
    {
        return '' .
'# Imports
imports:
    - { resource: parameters.yml }
    - { resource: security.yml }
    - { resource: services.yml }

# Parameters
parameters:
    locale: ru

# Framework
framework:
    secret:          "%secret%"
    router:
        resource: "%kernel.root_dir%/config/routing.yml"
        strict_requirements: ~
    form:            ~
    csrf_protection: false #only use for public API
    validation:      { enable_annotations: true }
    templating:
        engines: [\'twig\']
    default_locale:  "%locale%"
    trusted_hosts:   ~
    trusted_proxies: ~
    session:
        handler_id:  session.handler.native_file
        save_path:   "%kernel.root_dir%/../var/sessions/%kernel.environment%"
    fragments:       ~
    http_method_override: true
    assets: ~

# Twig
twig:
    debug:            "%kernel.debug%"
    strict_variables: "%kernel.debug%"

# Doctrine
doctrine:
    dbal:
        driver:   pdo_pgsql
        host:     "%database_host%"
        port:     "%database_port%"
        dbname:   "%database_name%"
        user:     "%database_user%"
        password: "%database_password%"
        charset:  UTF8
    orm:
        auto_generate_proxy_classes: "%kernel.debug%"
        naming_strategy: doctrine.orm.naming_strategy.underscore
        auto_mapping: true

# Swiftmailer
swiftmailer:
    transport: "%mailer_transport%"
    host:      "%mailer_host%"
    username:  "%mailer_user%"
    password:  "%mailer_password%"
    spool:     { type: memory }

# Nelmio CORS
nelmio_cors:
    defaults:
        allow_origin:  ["*"]
        allow_methods: ["POST", "PUT", "GET", "DELETE", "OPTIONS"]
        allow_headers: ["content-type", "authorization"]
        max_age:       3600
    paths:
        \'^/\': ~

# Nelmio API Doc
nelmio_api_doc:
    sandbox:
        accept_type:        "application/json"
        body_format:
            formats:        [ "json" ]
            default_format: "json"
        request_format:
            formats:
                json:       "application/json"


# FOS REST Bundle
fos_rest:
    body_listener: true
    format_listener:  true
    param_fetcher_listener: true
    view:
        view_response_listener: \'force\'
        formats:
            jsonp: true
            json: true
            xml: false
            rss: false
        mime_types:
            json: [\'application/json\', \'application/x-json\']
        jsonp_handler: ~
    routing_loader:
        default_format:  json
        include_format:  false
    format_listener:
        rules:
            - { path: ^/, priorities: [ json, jsonp ], fallback_format: ~, prefer_extension: true }
    exception:
        enabled: true
    serializer:
        serialize_null: true

# Marks12 Socket Server
marks12_socket_server:
    class:      \'AppBundle\Handler\SocketApiHandler\'
    address:    \'0.0.0.0\'
    port:       \'10000\'' .
            '';
    }
}
