[![Latest Stable Version](http://poser.pugx.org/zngly/zngly-graphql-db/v)](https://packagist.org/packages/zngly/zngly-graphql-db) [![Total Downloads](http://poser.pugx.org/zngly/zngly-graphql-db/downloads)](https://packagist.org/packages/zngly/zngly-graphql-db) [![Latest Unstable Version](http://poser.pugx.org/zngly/zngly-graphql-db/v/unstable)](https://packagist.org/packages/zngly/zngly-graphql-db) [![License](http://poser.pugx.org/zngly/zngly-graphql-db/license)](https://packagist.org/packages/zngly/zngly-graphql-db) [![PHP Version Require](http://poser.pugx.org/zngly/zngly-graphql-db/require/php)](https://packagist.org/packages/zngly/zngly-graphql-db)

# zngly-graphql-db

WPGraphQL custom database manager

### Installation Via Composer

<https://packagist.org/packages/zngly/zngly-graphql-db>

```bash
composer require zngly/zngly-graphql-db
```

### How To Use

```php
use Zngly\Graphql\Db\ZnglyDb;
use Zngly\Graphql\Db\Model\Field;
use Zngly\Graphql\Db\Model\FieldType;
use Zngly\Graphql\Db\Model\Table;

class NotificationsModel extends Table
{
    public static function table_single_name(): string
    {
        return 'notification';
    }

    public static function table_plural_name(): string
    {
        return "notifications";
    }

    public static function graphql_single_name(): string
    {
        return "Notification";
    }

    public static function graphql_plural_name(): string
    {
        return "Notifications";
    }

    public static function graphql_from_type(): string
    {
        return "RootQuery";
    }

    public static function description(): string
    {
        return "Notifications!";
    }

    public static function fields(): array
    {
        return [
            Field::create()
                ->name("id")
                ->is_id()
                ->primary_key()
                ->description("notification id")
                ->type(FieldType::create()->BIGINT())
                ->not_null()
                ->auto_increment(),
            Field::create()
                ->name('title')
                ->description('notification title')
                ->type(FieldType::create()->TEXT())
                ->not_null()
                ->collate(),
            Field::create()
                ->name('message')
                ->description('notification message')
                ->type(FieldType::create()->TEXT())
                ->not_null()
                ->collate(),
            Field::create()
                ->name('from')
                ->description('form who the notification is from')
                ->type(FieldType::create()->TEXT())
                ->not_null()
                ->collate(),
            Field::create()
                ->name('to')
                ->description('form who the notification is to')
                ->type(FieldType::create()->TEXT())
                ->not_null()
                ->collate(),
        ];
    }
}

new ZnglyDb("0.0.1", [
    new NotificationsModel(),
]);
```

### Graphql Queries & Mutations

```graphql
query notifications {
	notifications(first: 1100) {
		nodes {
			id
			from
			message
			title
			to
		}
	}
}

mutation create {
	createNotification(input: { title: "new test", to: "admin", message: "review this pls", from: "user" }) {
		notification {
			id
			title
			to
			message
			from
		}
	}
}

mutation delete {
	deleteNotification(input: { id: "16" }) {
		deletedId
		notification {
			title
			id
		}
	}
}

mutation update {
	updateNotification(input: { id: 16, message: "Can you Review this Please." }) {
		notification {
			id
			from
			message
			title
			to
		}
	}
}
```

### How To Access Your Models Manually

```php
// below is an example of how to get a model from the database manager
// you can query, add, update and delete entries for the model
use Zngly\Graphql\Db\Database\DatabaseManager;

$db = DatabaseManager::get_instance();

$notification_db = $db->get("notification");

$nr = rand(1, 100);

// below is the structure of how the notification table actions should be
$notification_db->insert([
    'message' => 'test nr: ' . $nr,
    'from' => 'test' . $nr,
    'to' => 'test' . $nr,
    'title' => 'test' . $nr,
]);

$notification_db->update(1, [
    'message' => 'This is my new updated test',
]);

$notification_db->delete(1);

$notification_db->query([
    'fields' => ['id', 'from', 'to', "message"],
    // 'number' => '1',
    // 'search' => 'hello',
    // 'search_columns' => ['message'],
]);
```
