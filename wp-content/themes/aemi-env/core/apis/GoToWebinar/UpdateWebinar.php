<?php


class UpdateWebinar
{
    public static function push($webinarKey, $post_id)
    {
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
            case 'series':
                $args['times'] = get_field($type_webinar.'_times', $post_id);
                break;
            default:
                $args['times'] = [get_field($type_webinar.'_times', $post_id)];
                break;
        }

        $api     = new ApiWebinars();
        $request = $api->newRequest('PUT', "organizers/{$organizer_key}/webinars/{$webinarKey}?notifyParticipants=false", json_encode($args));

        if ( is_wp_error($request) ) {
            return false;
        }

        return $api->getBodyResponse($request);
    }
}