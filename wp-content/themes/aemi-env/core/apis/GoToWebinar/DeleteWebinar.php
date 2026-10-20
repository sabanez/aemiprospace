<?php


class DeleteWebinar
{
    public static function delete()
    {
        $post_id = get_the_ID();
        $organizer_key = FacadeGetTokenDatabase::get()->organizer_key;
        $webinar_key = get_post_meta($post_id, 'webinar_meta')[0]->webinarKey;

        $args = [
            'sendCancellationEmails' => true
        ];

        $api     = new ApiWebinars();
        $request = $api->newRequest('DELETE', "organizers/{$organizer_key}/webinars/{$webinar_key}", json_encode($args));

        if ( is_wp_error($request) ) {
            return false;
        }
    }
}