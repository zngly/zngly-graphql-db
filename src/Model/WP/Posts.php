<?php

namespace Zngly\Graphql\Db\Model\WP;

use Zngly\Graphql\Db\Model\Field;
use Zngly\Graphql\Db\Model\FieldType;
use Zngly\Graphql\Db\Model\Table;

class Posts extends Table
{

    public static function table_single_name(): string
    {
        return "post";
    }
    public static function table_plural_name(): string
    {
        return "posts";
    }
    public static function description(): string
    {
        return "The Post Type";
    }
    public static function graphql_single_name(): string
    {
        return "Post";
    }
    public static function graphql_plural_name(): string
    {
        return "Posts";
    }
    public static function graphql_from_type(): string
    {
        return "RootQuery";
    }

    public static function fields(): array
    {
        return [
            Field::create()
                ->name('ID')
                ->graphql_name('databaseId')
                ->description('The ID of the post')
                ->type(FieldType::create()->BIGINT())
                ->not_null()
                ->primary_key()
                ->auto_increment()
                ->is_id(),
            Field::create()
                ->name('post_author')
                ->graphql_name('author')
                ->description('The ID of the author of the post')
                ->type(FieldType::create()->BIGINT())
                ->graphql_type('NodeWithAuthorToUserConnectionEdge')
                ->not_null()
                ->default('0')
                ->index(),
            Field::create()
                ->name('post_date')
                ->graphql_name('date')
                ->description('The date the post was published, in the site\'s timezone')
                ->type(FieldType::create()->DATETIME())
                ->not_null()
                ->default('0000-00-00 00:00:00'),
            Field::create()
                ->name('post_date_gmt')
                ->graphql_name('dateGmt')
                ->description('The date the post was published, as GMT')
                ->type(FieldType::create()->DATETIME())
                ->not_null()
                ->default('0000-00-00 00:00:00'),
            Field::create()
                ->name('post_content')
                ->graphql_name('content')
                ->description('The content of the post')
                ->type(FieldType::create()->LONGTEXT())
                ->not_null(),
            Field::create()
                ->name('post_title')
                ->graphql_name('title')
                ->description('The title of the post')
                ->type(FieldType::create()->TEXT())
                ->not_null(),
            Field::create()
                ->name('post_excerpt')
                ->graphql_name('excerpt')
                ->description('The excerpt of the post')
                ->type(FieldType::create()->TEXT())
                ->not_null(),
            Field::create()
                ->name('post_status')
                ->graphql_name('status')
                ->description('The status of the post')
                ->type(FieldType::create()->VARCHAR(20))
                ->not_null()
                ->default('publish'),
            Field::create()
                ->name('comment_status')
                ->graphql_name('commentStatus')
                ->description('Whether the post can accept comments')
                ->type(FieldType::create()->VARCHAR(20))
                ->not_null()
                ->default('open'),
            Field::create()
                ->name('ping_status')
                ->graphql_name('pingStatus')
                ->description('Whether the post can accept pings')
                ->type(FieldType::create()->VARCHAR(20))
                ->not_null()
                ->default('open'),
            Field::create()
                ->name('post_password')
                ->graphql_name('password')
                ->description('The password to access the post')
                ->type(FieldType::create()->VARCHAR(20))
                ->not_null()
                ->default(''),
            Field::create()
                ->name('post_name')
                ->graphql_name('slug')
                ->description('The name (slug) for the post')
                ->type(FieldType::create()->VARCHAR(200))
                ->not_null()
                ->index()
                ->default(''),
            Field::create()
                ->name('to_ping')
                ->graphql_name('toPing')
                ->description('Space-separated list of URLs to ping')
                ->type(FieldType::create()->TEXT())
                ->not_null(),
            Field::create()
                ->name('pinged')
                ->graphql_name('pinged')
                ->description('Space-separated list of URLs that have been pinged')
                ->type(FieldType::create()->TEXT())
                ->not_null(),
            Field::create()
                ->name('post_modified')
                ->graphql_name('modified')
                ->description('The date the post was last modified, in the site\'s timezone')
                ->type(FieldType::create()->DATETIME())
                ->not_null()
                ->default('0000-00-00 00:00:00'),
            Field::create()
                ->name('post_modified_gmt')
                ->graphql_name('modifiedGmt')
                ->description('The date the post was last modified, as GMT')
                ->type(FieldType::create()->DATETIME())
                ->not_null()
                ->default('0000-00-00 00:00:00'),
            Field::create()
                ->name('post_content_filtered')
                ->graphql_name('contentFiltered')
                ->description('The filtered content of the post')
                ->type(FieldType::create()->LONGTEXT())
                ->not_null(),
            // Field::create()
            //     ->name('post_parent')
            //     ->graphql_name('parent')
            //     ->description('The ID of the parent of the post')
            //     ->type(FieldType::create()->BIGINT())
            //     ->index()
            //     ->not_null()
            //     ->default('0'),
            Field::create()
                ->name('guid')
                ->graphql_name('guid')
                ->description('The GUID of the post')
                ->type(FieldType::create()->VARCHAR(255))
                ->not_null()
                ->default(''),
            Field::create()
                ->name('menu_order')
                ->graphql_name('menuOrder')
                ->description('The order of the post')
                ->type(FieldType::create()->INT())
                ->not_null()
                ->default('0'),
            Field::create()
                ->name('post_type')
                ->graphql_name('type')
                ->description('The type of post')
                ->type(FieldType::create()->VARCHAR(20))
                ->not_null()
                ->default('post'),
            Field::create()
                ->name('post_mime_type')
                ->graphql_name('mimeType')
                ->description('The mime type of the post')
                ->type(FieldType::create()->VARCHAR(100))
                ->not_null()
                ->default(''),
            Field::create()
                ->name('comment_count')
                ->graphql_name('commentCount')
                ->description('The number of comments for the post')
                ->type(FieldType::create()->BIGINT())
                ->not_null()
                ->default('0'),
            Field::_index('type_status_date', ['post_type', 'post_status', 'post_date', 'ID'])
        ];
    }
}
