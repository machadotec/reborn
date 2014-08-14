<?php 

namespace Blog\Extensions;

use League\Fractal\TransformerAbstract;

use Blog\Model\Blog;

class BlogTransformer extends TransformerAbstract
{

	public function transform(Blog $blog)
    {

        $custom_fields = ($blog->custom_field) ? $blog->custom_field->toArray() : array();

        return array(
        	'id' 			=> (int)$blog->id,
        	'title' 		=> $blog->title,
        	'slug' 			=> $blog->slug,
        	'url'			=> $blog->url,
            'body'          => $blog->content,
            'excerpt'       => $blog->excerpt,
            'post_date'     => $blog->created_at->timestamp,
            'featured_img'  => $blog->feature_image_no_size,
            'post_type'     => $blog->post_type,
            'author'        => array(
                'id'        => (int)$blog->author_id,
                'name'      => $blog->author_name,
                'url'       => $blog->author_url,
                'avatar'    => $blog->author_avatar_link
            ),
            'category'      => array(
                'id'        => (int)$blog->category_id,
                'name'      => $blog->category_name,
                'url'       => $blog->category_url,
            ),
	        'tags'			=> $blog->tags_arr_with_links,
	        'comment_count'	=> $blog->comment_count,
            'custom_fields' => $custom_fields
        );

    }

}
