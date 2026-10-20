<?php


class BodyCreateWebinars
{
    public static function getBody($post_id)
    {
        $type_webinar = get_field('type_webinar', $post_id);
        $times = get_field($type_webinar.'_times', $post_id);

        return $times;
    }
}