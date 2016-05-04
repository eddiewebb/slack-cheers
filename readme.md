# Team Cheers for Slack

## Features
1. Web UI ('/') and RESTful  service API ('/api')
1. Runs locally against SQLite or MySQL.
2. Cloud Foundry compatible
1. Uses Redbeanphp as an ORM to abstract details
1. Uses the Slim framework for routing and helpers
1. Uses Twig templating for HTML presentation.

## Requirements
* Requires SQLite and PDO extensions to run locally
* Requires PHP Buildpack in Cloud Foundry (default)

#Integration with Slack
1. Deploy this somewhere
2. Point slack custom integration 'slash-command' to the /api/recognize endpoint as POST
3. use `/recognize @nameone @namemany |OPtional message` to start awarding folks.

# UI features
Pretty limited, hit /reports to see details for the team.


# Folders and FIles

## Folder Structure
```
   |-lib  (3rd party files you didn't write)
   |-public ( root WEBDIR folder locally and in CF, only files in here are visible in the browser )
   |---index.php
   |---style
   |-src (any custom non-UI code)
   |---routes (routing rules for SLim, files must be included in index.php)
   |---templates (UI templates for Twig.)
   |-----blocks  (Sub-templates that inherit from base and override distinct sections)
   |-vendor (managed by Composer, do not modify!)
```


## Special FIles

### .cfignore
make sure that .cfignore includes 'composer.*' - this prevents cloud foundry from moving the vendor directory and/or failing to download dependencies

### public/.htaccess 
This allows pretty routes like /report, /, /foo to be hit index.php where Slim can handle the logic.
http://docs.slimframework.com/routing/rewrite/

### .bp-config/options.json
Critical for CLoud Foundry!
This adds MySQL, PDO and other required extensions for common php apps to run.
https://docs.cloudfoundry.org/buildpacks/php/gsg-php-config.html#php-extensions


# populating

The tool will automatically create tables and populate bsed on use in Slack, but for demoing y0u can Load /nuke to populate with sample data.