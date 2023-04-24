# Your first document

## Document migrations

To start the creation of your document you first need to create
a document layout. This is done via **document** migrations. To create one simply run

```
php artisan make:layout-migration fileName --model="your model"
```
This command creates a new file inside (and folder) inside `database/documents`.

Inside the file you will find an anonymous class which extends `use Illuminate\Database\Migrations\Migration`. The auto generated class has only one method:

```php
public function up()
{
    $blueprint = new LayoutBlueprint({{layoutName}}, {{modelClass}});

    // Add sections to your layout via blocks

    // $blueprint->addBlock({{htmlString}}, SectionBlock::class);

    // $blueprint->addBlock({{templateName}}, BladeBlock::class);

    $blueprint->upsert();
}
```

More on the blocks later.

## GenerateDocument-Service

To finally generate a document the package provides the `GenerateDocument` Service which we can inject in e.g. via a Controllers constructor.

```php
$this->generateDocument->generateDocument($layoutName, $model);
```

This generates a pdf which immediatly.
