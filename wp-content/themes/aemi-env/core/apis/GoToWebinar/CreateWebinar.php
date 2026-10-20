<?php


class CreateWebinar
{
    public static function push()
    {
        $post_id = get_the_ID();
        $organizer_key = FacadeGetTokenDatabase::get()->organizer_key;
        $type_webinar = get_field('type_webinar', $post_id);
        $experience_type = get_field('experience_type', $post_id);

        $args = [
            'subject' => get_the_title($post_id),
            'description' => get_field('descripcion', $post_id),
            'type' => $type_webinar,
            'experienceType' => $experience_type
        ];

        switch ($type_webinar) {
            case 'sequence':
            case 'series':
                $args['times'] = get_field($type_webinar.'_times', $post_id);
                break;
            default:
                $args['times'] = [get_field($type_webinar.'_times', $post_id)];
                break;
        }

        $api     = new ApiWebinars();
        $request = $api->newRequest('POST', "organizers/{$organizer_key}/webinars", json_encode($args));

        if ( is_wp_error($request) ) {
            return false;
        }

        return $api->getBodyResponse($request);
    }
}