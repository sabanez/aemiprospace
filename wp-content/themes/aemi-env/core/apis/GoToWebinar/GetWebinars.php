<?php


class GetWebinars
{
    public static function pull()
    {
        $organizers_key = FacadeGetTokenDatabase::get()->organizer_key;

        $body = array(
            'fromTime' => '2022-08-01T00:00:00Z',
            'toTime'   => '2022-09-30T00:00:00Z'
        );

        $api     = new ApiWebinars();
        $request = $api->newRequest("GET", "organizers/{$organizers_key}/webinars", $body);

        if (is_wp_error($request)) {
            return false;
        }

        return $api->getBodyResponse($request);
    }
}