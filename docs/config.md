# Configuration

If you execute the command `php artisan vendor:publish documents` this package copies its own configuration file to your configuration directory. You can find it inside the folder under **documents.php**.

Inside the configuration allows you to specify the follwing values.

## Migrations

Path where the plugin will look for the document migrations.

- key: "migrations_path"
- default: `database_path('documents')`

## Placeholder

Inside the **placeholder** section you can configure how the package handles *placeholders* and their *resolving*.

- key: "placeholder"

### Auto discover

Specifies an array where the package searches for classes that extend `mindtwo\DocumentGenerator\Document\Placeholder`

- key: "placeholder.auto_discover"
- default: `['Documents/Placeholders']`

## Blocks

Inside the **blocks** section you can configure where your templates live.

- key: "blocks"

### Template roots

Specifies an array of paths where the package searches for your block templates.

- key: "blocks.template_roots"
- default: `['Documents/templates']`
