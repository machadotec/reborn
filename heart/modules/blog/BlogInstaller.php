<?php

namespace Blog;

class BlogInstaller extends \Reborn\Module\AbstractInstaller
{

    public function install($prefix = null)
    {
        \Schema::table($prefix.'blog', function ($table) {
            $table->create();
            $table->increments('id');
            $table->string('title');
            $table->string('slug');
            $table->integer('category_id');
            $table->text('body');
            $table->text('excerpt');
            // Add Post Type for Version 1.2 [NLH]
            $table->string('post_type', 50)->nullable()->default('standard');
            $table->string('attachment')->nullable();
            $table->integer('author_id');
            $table->enum('comment_status', array('open', 'close'))->default('open');
            $table->enum('status', array('draft', 'live'))->default('draft');
            $table->enum('editor_type', array('wysiwyg', 'markdown'))->default('wysiwyg');
            $table->integer('view_count')->default(0);
            $table->integer('lang_ref')->nullable();
            $table->string('lang', 20)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        \Schema::table($prefix.'blog_categories', function ($table) {
            $table->create();
            $table->increments('id');
            $table->string('name', 32);
            $table->string('slug', 32);
            $table->text('description');
            $table->integer('parent_id')->default(0);
            $table->integer('order')->default(0);
        });

        \DB::table($prefix.'blog_categories')->insert(array(
            'name' 			=> 'Default',
            'slug' 			=> 'default',
            'description' 	=> 'default category',
            'parent_id' 	=> 0,
            'order'			=> 0
            )
        );

        $data = array(
            'slug'		=> 'blog_per_page',
            'name'		=> 'Blog Posts per page',
            'desc'		=> 'Number of blog post to show at frontend',
            'value'		=> '',
            'default'	=> '5',
            'module'	=> 'Blog'
            );
        \Setting::add($data);

        $data = array(
            'slug' 		=> 'blog_rss_items',
            'name'		=> 'Blog RSS Items',
            'desc'		=> 'Number of blog post to show in RSS Feed',
            'value'		=> '',
            'default' 	=> '10',
            'module'	=> 'Blog'
        );
        \Setting::add($data);

        $data = array(
            'slug'		=> 'excerpt_length',
            'name'		=> 'Length of blog excerpt',
            'desc'		=> 'Blog excerpt word count',
            'value'		=> '',
            'default'	=> '50',
            'module'	=> 'Blog'
            );
        \Setting::add($data);

        $data = array(
            'slug'		=> 'blog_content_default_lang',
            'name'		=> 'Contents default language',
            'desc'		=> 'Default language for blog contents',
            'value'		=> '',
            'default'	=> 'en',
            'module'	=> 'Blog'
            );
        \Setting::add($data);

        //Have to move this setting as content setting
        $data = array(
           'slug'       => 'content_editor',
           'name'       => 'Contents editor',
           'desc'       => 'Text Editor for contents',
           'value'      => '',
           'default'    => 'wysiwyg',
           'module'     => 'Blog'
        );

        \Setting::add($data);
    }

    public function uninstall($prefix = null)
    {
        \Schema::drop($prefix.'blog');
        \Schema::drop($prefix.'blog_categories');

        \Setting::remove('blog_per_page');

        \Setting::remove('blog_rss_items');

        \Setting::remove('excerpt_length');

        \Setting::remove('blog_content_default_lang');

        \Setting::remove('blog_content_default_lang');

    }

    public function upgrade($v, $prefix = null)
    {
        if ($v == '1.0') {
            \Schema::table($prefix.'blog', function ($table) {
                $table->integer('lang_ref')->nullable();
                $table->string('lang', 20)->nullable();
                $table->softDeletes();
            });

            $data = array(
                'slug'		=> 'blog_content_default_lang',
                'name'		=> 'Contents default language',
                'desc'		=> 'Default language for blog contents',
                'value'		=> '',
                'default'	=> 'en',
                'module'	=> 'Blog'
            );
            \Setting::add($data);
        }

        if ($v < '1.2') {
            \Schema::table($prefix.'blog', function ($table) {
                $table->string('post_type', 50)->nullable()->default('standard');
            });
        }

        if ($v < '1.21') {
            \Schema::table($prefix.'blog', function($table) {
                $table->enum('editor_type', array('wysiwyg', 'markdown'))->default('wysiwyg');
            });
            //Have to move this setting as content setting
            $data = array(
               'slug'       => 'content_editor',
               'name'       => 'Contents editor',
               'desc'       => 'Text Editor for contents',
               'value'      => '',
               'default'    => 'wysiwyg',
               'module'     => 'Blog'
            );

            \Setting::add($data);
        }

    }

}
