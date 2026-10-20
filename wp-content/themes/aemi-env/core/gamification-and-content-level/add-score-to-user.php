<?php

class AemiScoreLevelUsers extends AemiLevelUsers
{
    public $nonce_name    = 'aemi_score_level_user';
    public $nonce_action  = 'aemi_score_level_user_action';
    public $loadedOneTime = false;

    public function __construct()
    {
        add_action('template_redirect', [$this, 'addPunctuationToUserAccessPostAutomatically']);
        add_action('wp_footer', [$this, 'addAnimationWhenLevelingUp'], 9999);
        add_action('wp_footer', [$this, 'addScriptToReceivePointsByButton'], 9999);
        add_action('wp_ajax_aemi_mark_as_viewed_new_level_notification', [
            $this,
            'aemiMarkAsViewedNewLevelNotification'
        ]);
        add_action('wp_ajax_aemi_get_gamification_points', [$this, 'aemiGetGamificationPoints']);

        add_action('enqueue_block_editor_assets', [
            $this,
            'load_custom_gutemberg_block_skills_and_points_box'
        ]);
        add_shortcode('skills_and_points_box', [$this, 'shortcode_skills_and_points_box']);

        if (is_user_logged_in()) {
            $this->nonce_action .= '__' . get_current_user_id();
        }
    }

    public function load_custom_gutemberg_block_skills_and_points_box()
    {
        global $post_type;

        if (is_admin() && $post_type && $post_type == 'post') {
            wp_enqueue_script('block_skills_and_points_box', get_aemi_url_assets_js('skills_and_points_box_block_editor.js'), [
                'wp-blocks',
                'wp-editor'
            ], true);

            $templateElement = do_shortcode('[skills_and_points_box post_id="' . (isset($_GET['post']) && $_GET['post'] ? sanitize_text_field($_GET['post']) : '') . '"]');

            if (!$templateElement) {
                $templateElement = '
                <div class="card-content patientsGuid wrapperTabel">
                    <h4>Habilidades y puntos asociados al contenido.</h4>
                    <table class="skills-table">
                        <thead>
                            <tr>
                                <th>Capacidad</th>
                                <th>Puntos asociados</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <p>Sed tempus porta ex, vel faucibus tortor mattis ac.</p>
                                </td>
                                <td class="text-center"><span>50</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. </p>
                                </td>
                                <td class="text-center"><span>10</span></td>
                            </tr>
                            
                        </tbody>
                    </table>
                    <div> <button class="btn-secondary flex aemi-container-get-points__btn " id="addClassButton"> <img
                                src="' . get_aemi_url_assets_imgs('new-layout-v1/images/growicons.svg') . '">
                            Sumar competencias <span></span> </button> </div>
                            <div class="example-content-element-skills-aemi"><span>Esta es sólo una tabla de ejemplo. Para ver la tabla real, acceda al contenido del sitio web.</span></div>
                </div>
                ';
            }

            $templateElement = '<div class="element-box-container-preview--skills-and-points">
        <style>
        .element-box-container-preview--skills-and-points .btn-secondary {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin: auto;
            font-size: 16px;
            font-weight: 600;
            background: #ef8933;
            height: 40px;
            padding: 0;
            padding-left: 20px;
            padding-right: 20px;
            color: #fff;
            border-radius: 12px;
            cursor: pointer;
            border: 1px solid #ef8933;
        }
        .btn-secondary img {
            width: 20px;
            margin-right: 10px;
        }
        .patientsGuid h4 {
            color: #ef8933;
            margin-bottom: 10px;
            margin-top: 15px;
            margin-bottom: 25px;
            font-size: 24px;
            line-height: 1.4;
            font-weight: 600;
        }
        .skills-table{
            width: 100%
        }
        .skills-table thead th {
            color: #999999!important;
            font-size: 16px !important;
            font-weight: 600 !important;
            line-height: 12px !important;
            text-align: left !important;
        }
        .skills-table thead th:nth-child(2){
            width: 170px;
        }
        .skills-table thead tr {
            background-color: #f2f2f2 !important;
        }
        .skills-table thead th, .skills-table tbody td {
            padding: 1rem;
        }

        .skills-table tbody td {
            font-size: 16px;
            font-weight: 600;
            line-height: 21px;
        }
        .skills-table tbody td span {
            color: #ef8933;
        }
        .skills-table tbody td p {
            margin: 0;
        }
        .example-content-element-skills-aemi{
            background: #727272;
            color: #fff;
            text-align: center;
            font-size: 12px;
            display: table;
            margin: 23px auto 0;
            padding: 3px 19px;
            border-radius: 35px;
            opacity: 0.7;
        }
        </style>
            
            ' . $templateElement . '</div>';
            wp_localize_script('block_skills_and_points_box', 'template_skills_and_points_box', ['template' => $templateElement]);
        }
    }

    public function shortcode_skills_and_points_box($attr)
    {
        $elementPost = false;

        if (isset($attr['post_id']) && $attr['post_id']) {
            $elementPost = get_post($attr['post_id']);
        } else {
            global $post;
            $elementPost = $post;
        }

        $output = '';

        if ((is_single() || is_admin() && isset($_GET['post'])) && $elementPost) {
            if ($this->loadedOneTime && isset($attr['disable_multiple_loaded']) && $attr['disable_multiple_loaded']) {
            } else {
                $this->loadedOneTime = true;

                $contentTableInformation = '';
                $aemi_level_by_post = aemi_get_level_related_to_post_id($elementPost->ID);

                if ($aemi_level_by_post) {
                    foreach ($aemi_level_by_post as $level) {
                        if ($level->sub_categories) {
                            foreach ($level->sub_categories as $sub_categories) {
                                $contentTableInformation .= '<tr>';
                                $contentTableInformation .= '<td><p>' . $sub_categories->description . '</p></td>';
                                $contentTableInformation .= '<td class="text-center"><span>' . $sub_categories->level_points . '</span></td>';
                                $contentTableInformation .= '</tr>';
                            }
                        }
                    }
                }

                if ($contentTableInformation) {
                    $earned_points = check_user_has_already_earned_points_in_post(get_current_user_id(), $elementPost->ID);

                    $favorite_button = favorite_button($elementPost->ID);

                    $output .= '<div class="card-content patientsGuid wrapperTabel">';
                    $output .= '  <h4>Habilidades y puntos asociados al contenido.</h4>';
                    $output .= '  <table class="skills-table">';
                    $output .= '    <thead>';
                    $output .= '      <tr>';
                    $output .= '        <th>Capacidad</th>';
                    $output .= '        <th>Puntos asociados</th>';
                    $output .= '      </tr>';
                    $output .= '    </thead>';
                    $output .= '    <tbody>' . $contentTableInformation . '</tbody>';
                    $output .= '  </table>';
                    $output .= '  <div>';
                    $output .= '<div class="skills-actions">';
                    $output .= '    <button class="btn-secondary flex aemi-container-get-points__btn ' . ($earned_points ? 'aemi-container-get-points__btn-disabled-earned' : '') . '" id="addClassButton">';
                    $output .= '      <img src="' . get_aemi_url_assets_imgs('new-layout-v1/images/growicons.svg') . '">';
                    $output .= '      Sumar competencias';
                    $output .= '      <span></span>';
                    $output .= '    </button>';
                    $output .= '<button type="button" class="btn-secondary skills-add-to-favorite-btn flex ' . $favorite_button['class'] . '" data-id="' . $favorite_button['data-id'] . '">';
                    $output .= '<svg width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="mdi:heart-outline"><path id="Vector" d="M14.6207 22.4146L14.4998 22.5354L14.3669 22.4146C8.62734 17.2067 4.83317 13.7629 4.83317 10.2708C4.83317 7.85417 6.64567 6.04167 9.06234 6.04167C10.9232 6.04167 12.7357 7.25 13.3761 8.89333H15.6236C16.264 7.25 18.0765 6.04167 19.9373 6.04167C22.354 6.04167 24.1665 7.85417 24.1665 10.2708C24.1665 13.7629 20.3723 17.2067 14.6207 22.4146ZM19.9373 3.625C17.8348 3.625 15.8169 4.60375 14.4998 6.13833C13.1828 4.60375 11.1648 3.625 9.06234 3.625C5.34067 3.625 2.4165 6.53708 2.4165 10.2708C2.4165 14.8262 6.52484 18.56 12.7478 24.2029L14.4998 25.7979L16.2519 24.2029C22.4748 18.56 26.5832 14.8262 26.5832 10.2708C26.5832 6.53708 23.659 3.625 19.9373 3.625Z" fill="#FF3366"></path></g></svg>';
                    $output .= '</button>';
                    $output .= '</div>';
                    $output .= '  </div>';
                    $output .= '</div>';
                }
            }
        }

        return $output;
    }

    /**
     * When accessing the WikiMi page (Post) the system will try to automatically assign the score to the user.
     */
    public function addPunctuationToUserAccessPostAutomatically()
    {
        if (is_single() && !is_admin() && is_singular('post') && is_user_logged_in()) {
            global $post, $wp_query;

            $user_id = get_current_user_id();

            if ($post && $user_id) {
                /*
                 * Checks if the user has the correct level to access the content and receive the points
                 */
                if ($this->checkThatTheUserIsAtTheCorrectLevelToAccessContent($user_id, $post->ID)) {
                    // the customer asked for points to be received from a button on the frontend
                    // $this->addUserScorePerPost( $post->ID, $user_id );
                } else {
                    // $wp_query->set_404();
                    // status_header( 404 );
                    // get_template_part( 404 );
                    // exit();
                }
            }
        }
    }

    /**
     * Adds a score to the user based on the post and the user id.
     *
     * @return int - By default ira returns the user's total score
     */
    public function addUserScorePerPost($post_id, $user_id)
    {
        if ($user_id && $post_id) {
            // This method already validates if it has been viewed previously and adds points
            // only if the user has not yet viewed it.
            $user_view_pages = $this->insertInUserViewedPageIDAndSumScore($user_id, $post_id);
        }
    }

    /**
     * Ajax to mark new level notification as viewed so it doesn't appear again.
     */
    public function aemiMarkAsViewedNewLevelNotification()
    {
        if (is_user_logged_in()) {
            if (isset($_POST[$this->nonce_name]) && $_POST[$this->nonce_name]) {
                if (wp_verify_nonce($_POST[$this->nonce_name], $this->nonce_action)) {
                    $user_id = get_current_user_id();
                    if ($this->checkIfThereWasAnyLevelUpgradeOnTheUser($user_id)) {
                        $current_user_level = $this->getUserScoreCurrentLevel($user_id);
                        do_action('update_user_level_' . $current_user_level->term_id, $user_id);

                        $this->updateUserScoreCurrentOldLevelScoreForLevelUpdateAnimationComparisonWithCurrentLevel($user_id);
                    }
                }
            }
        }
    }

    /**
     * Add confetti animation to notify user that he has leveled up.
     */
    public function addAnimationWhenLevelingUp()
    {
        if ($this->checkIfThereWasAnyLevelUpgradeOnTheUser(get_current_user_id())) {
            $currentUserLevel = $this->getUserScoreCurrentLevel(get_current_user_id());
            $levelSkills = $this->get_info_from_previous_level_by_current_level_ID($currentUserLevel->term_id);
            ?>
            <div class="aemi-congratulations-new-level">
                <div class="aemi-congratulations-new-level__box-animation-a">
                    <div class="aemi-congratulations-new-level__box-animation">
                        <div class="aemi-congratulations-new-level__box-animation-unlocked">
                            <span><?php _e('New content has been unlocked.', AEMI_TEXT_DOMAIN); ?></span>
                        </div>

                        <div class="aemi-congratulations-new-level__box-animation-info">

                            <div class="aemi-congratulations-new-level__box-animation-info-circle">
                                <div class="aemi-congratulations-new-level__box-animation-info-circle-inner">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="426" height="426" viewBox="0 0 426 426">
                                        <defs>
                                            <filter id="circle-animation-gamification" x="0" y="0" width="426" height="426" filterUnits="userSpaceOnUse">
                                                <feOffset dy="3" input="SourceAlpha"/>
                                                <feGaussianBlur stdDeviation="23" result="blur"/>
                                                <feFlood flood-opacity="0.341"/>
                                                <feComposite operator="in" in2="blur"/>
                                                <feComposite in="SourceGraphic"/>
                                            </filter>
                                            <filter id="circle-animation-gamification_2" x="69" y="66" width="279" height="284" filterUnits="userSpaceOnUse">
                                                <feOffset dy="3" input="SourceAlpha"/>
                                                <feGaussianBlur stdDeviation="3" result="blur-2"/>
                                                <feFlood flood-color="#F3AF73" result="color"/>
                                                <feComposite operator="out" in="SourceGraphic" in2="blur-2"/>
                                                <feComposite operator="in" in="color"/>
                                                <feComposite operator="in" in2="SourceGraphic"/>
                                            </filter>
                                        </defs>
                                        <g transform="translate(-10906 -4824)">
                                            <g transform="matrix(1, 0, 0, 1, 10906, 4824)" filter="url(#circle-animation-gamification)">
                                                <circle id="circle-animation-gamification-2" data-name="Elipse 7" cx="144" cy="144" r="144" transform="translate(69 66)" fill="#F3AF73"/>
                                            </g>
                                            <g data-type="innerShadowGroup">
                                                <ellipse cx="139.5" cy="142" rx="139.5" ry="142" transform="translate(10975 4890)" fill="#FFBC81"/>
                                                <g transform="matrix(1, 0, 0, 1, 10906, 4824)" filter="url(#circle-animation-gamification_2)">
                                                    <ellipse cx="139.5" cy="142" rx="139.5" ry="142" transform="translate(69 66)" fill="#fff"/>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>

                                    <div class="aemi-congratulations-new-level__box-animation-info-circle__start">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="193" height="184" viewBox="0 0 193 184">
                                            <defs>
                                                <filter id="aemi-congratulations-new-leve__star" x="0" y="0" width="193" height="184" filterUnits="userSpaceOnUse">
                                                    <feOffset dy="3" input="SourceAlpha"/>
                                                    <feGaussianBlur stdDeviation="3" result="blur"/>
                                                    <feFlood flood-opacity="0.161"/>
                                                    <feComposite operator="in" in2="blur"/>
                                                    <feComposite in="SourceGraphic"/>
                                                </filter>
                                                <clipPath id="aemi-congratulations-new-leve-clip-path">
                                                    <path id="Polígono_2" data-name="Polígono 2" d="M78.453,19.205a10,10,0,0,1,18.093,0L111.406,50.75a10,10,0,0,0,7.8,5.661l34.627,4.342a10,10,0,0,1,5.587,17.225L134,101.758a10,10,0,0,0-2.989,9.187l6.557,34.168a10,10,0,0,1-14.634,10.65L92.313,138.948a10,10,0,0,0-9.626,0L52.064,155.763a10,10,0,0,1-14.634-10.65l6.557-34.168A10,10,0,0,0,41,101.758L15.577,77.978a10,10,0,0,1,5.587-17.225l34.627-4.342a10,10,0,0,0,7.8-5.661Z" transform="translate(0.039 0.371)" fill="#ffe600" stroke="#fff171" stroke-width="1"/>
                                                </clipPath>
                                            </defs>
                                            <g transform="translate(-11023 -4945)">
                                                <g transform="matrix(1, 0, 0, 1, 11023, 4945)" filter="url(#aemi-congratulations-new-leve__star)">
                                                    <g id="aemi-congratulations-new-leve__star-2" data-name="Polígono 1" transform="translate(9 6)" fill="#ffe600">
                                                        <path d="M 127.7479095458984 156.5225067138672 C 126.1616668701172 156.5225830078125 124.6236267089844 156.1197052001953 123.1765289306641 155.3250732421875 L 92.55388641357422 138.5097503662109 C 91.00950622558594 137.6617126464844 89.26188659667969 137.2134704589844 87.50000762939453 137.2134704589844 C 85.73810577392578 137.2134704589844 83.99048614501953 137.6617126464844 82.44610595703125 138.5097503662109 L 51.823486328125 155.3250732421875 C 50.37650680541992 156.1196441650391 48.83846664428711 156.5225067138672 47.2520866394043 156.5225067138672 C 45.85672760009766 156.5225067138672 44.44210815429688 156.1957855224609 43.16116714477539 155.5776519775391 C 41.90436935424805 154.9711761474609 40.77158737182617 154.0836944580078 39.88530731201172 153.0111541748047 C 38.07372665405273 150.8188934326172 37.37616729736328 148.0475158691406 37.92114639282227 145.2075653076172 L 44.47788619995117 111.0394668579102 C 45.16150665283203 107.4770736694336 43.98812866210938 103.8708114624023 41.33910751342773 101.3927307128906 L 15.91848754882812 77.61279296875 C 14.52344799041748 76.30779266357422 13.56374740600586 74.6705322265625 13.14316749572754 72.8780517578125 C 12.74868774414062 71.19673156738281 12.82776737213135 69.42137145996094 13.37190723419189 67.74388885498047 C 13.91604804992676 66.06642913818359 14.89412784576416 64.58267211914062 16.2004280090332 63.45307159423828 C 17.59310722351074 62.24874877929688 19.33110809326172 61.48656845092773 21.22654724121094 61.24893188476562 L 55.85386657714844 56.90740966796875 C 59.44258880615234 56.45746994018555 62.50526809692383 54.23540878295898 64.04652404785156 50.96340942382812 L 78.90573120117188 19.4183292388916 C 79.71820831298828 17.69351005554199 80.97928619384766 16.27955055236816 82.55265045166016 15.32929039001465 C 84.02867126464844 14.43784999847412 85.73942565917969 13.96665000915527 87.50000762939453 13.96665000915527 C 89.26056671142578 13.96665000915527 90.97132873535156 14.43784999847412 92.44734954833984 15.32929039001465 C 94.02070617675781 16.27955055236816 95.28178405761719 17.69351005554199 96.09426879882812 19.4183292388916 L 110.9534912109375 50.9633903503418 C 112.4947280883789 54.23538970947266 115.5574111938477 56.45745086669922 119.1461486816406 56.90740966796875 L 153.7734680175781 61.24893188476562 C 155.6689147949219 61.48656845092773 157.4069061279297 62.24874877929688 158.7995910644531 63.45307159423828 C 160.1058807373047 64.58267211914062 161.0839691162109 66.06642913818359 161.6281127929688 67.74388885498047 C 162.1722412109375 69.42137145996094 162.2513275146484 71.19673156738281 161.8568420410156 72.8780517578125 C 161.4362640380859 74.6705322265625 160.4765625 76.30779266357422 159.0815277099609 77.61279296875 L 133.6609344482422 101.3927307128906 C 131.0119018554688 103.8707733154297 129.8385009765625 107.4770278930664 130.5221099853516 111.0394668579102 L 137.0788726806641 145.2075653076172 C 137.6237640380859 148.0471496582031 136.9262084960938 150.8183288574219 135.1146545410156 153.0106353759766 C 134.2283325195312 154.083251953125 133.0955200195312 154.9708251953125 131.8387145996094 155.5774078369141 C 130.5577545166016 156.1956481933594 129.1431732177734 156.5224761962891 127.7479095458984 156.5225067138672 Z" stroke="none"/>
                                                        <path d="M 87.49998474121094 14.46662902832031 C 85.83052825927734 14.46662902832031 84.20914459228516 14.91293334960938 82.81112670898438 15.75729370117188 C 81.32244873046875 16.65638732910156 80.12838745117188 17.99604797363281 79.35807037353516 19.63140869140625 L 64.49887084960938 51.17649078369141 C 62.88420867919922 54.60427093505859 59.67566680908203 56.93212890625 55.91604614257812 57.40352630615234 L 21.28872680664062 61.74504852294922 C 19.49160766601562 61.97036743164062 17.84518432617188 62.6917724609375 16.5274658203125 63.83126831054688 C 15.29020690917969 64.90119171142578 14.36349487304688 66.30748748779297 13.84750366210938 67.89817047119141 C 13.33152770996094 69.48883056640625 13.25630187988281 71.17134857177734 13.62994384765625 72.76383209228516 C 14.02790832519531 74.45984649658203 14.93736267089844 76.01033020019531 16.26004028320312 77.24765014648438 L 41.68064880371094 101.027587890625 C 44.45584106445312 103.6236724853516 45.68510437011719 107.4016494750977 44.96894836425781 111.1337127685547 L 38.41218566894531 145.3017883300781 C 37.89582824707031 147.9926300048828 38.55586242675781 150.6174163818359 40.27072143554688 152.692626953125 C 41.99372863769531 154.7776947021484 44.60356140136719 156.0225067138672 47.25209045410156 156.0225067138672 C 48.75336456298828 156.0225067138672 50.21042633056641 155.6404113769531 51.58282470703125 154.8868103027344 L 82.2054443359375 138.0714874267578 C 83.8233642578125 137.1830749511719 85.65419006347656 136.7134704589844 87.49998474121094 136.7134704589844 C 89.34578704833984 136.7134704589844 91.17662811279297 137.1830749511719 92.79454803466797 138.0714874267578 L 123.4171905517578 154.8868103027344 C 124.7895660400391 155.6404113769531 126.2466430664062 156.0225067138672 127.7479248046875 156.0225067138672 C 130.3961791992188 156.0225067138672 133.0059509277344 154.7776184082031 134.7290649414062 152.6924133300781 C 136.4440002441406 150.6170959472656 137.1041259765625 147.9923706054688 136.5878295898438 145.3017883300781 L 130.0310668945312 111.1337127685547 C 129.3149261474609 107.401611328125 130.544189453125 103.6236267089844 133.3193664550781 101.027587890625 L 158.7399444580078 77.24765014648438 C 160.0626220703125 76.01033020019531 160.9721069335938 74.45984649658203 161.3700408935547 72.76383209228516 C 161.7437133789062 71.17134857177734 161.6684722900391 69.48885345458984 161.1525115966797 67.89817047119141 C 160.6365356445312 66.30751037597656 159.7098083496094 64.90119171142578 158.4725494384766 63.83126831054688 C 157.1548156738281 62.6917724609375 155.5083923339844 61.97036743164062 153.7112731933594 61.74504852294922 L 119.0839462280273 57.40352630615234 C 115.3243103027344 56.93212890625 112.1157836914062 54.60427093505859 110.5011444091797 51.17646789550781 L 95.64192962646484 19.63140869140625 C 94.87158966064453 17.99604797363281 93.67755126953125 16.65638732910156 92.18885040283203 15.75729370117188 C 90.79082489013672 14.91293334960938 89.16944885253906 14.46662902832031 87.49998474121094 14.46662902832031 M 87.5 13.46662902832031 C 91.12223815917969 13.46662902832031 94.74447631835938 15.3795166015625 96.54658508300781 19.20527648925781 L 111.4058074951172 50.75032806396484 C 112.8654632568359 53.84911346435547 115.8096084594727 55.98515319824219 119.2083435058594 56.41129302978516 L 153.8356628417969 60.7528076171875 C 162.2447814941406 61.80712890625 165.6121826171875 72.18829345703125 159.423095703125 77.97792816162109 L 134.0025024414062 101.7578735351562 C 131.4945983886719 104.103889465332 130.3659820556641 107.5726699829102 131.0131530761719 110.9452514648438 L 137.5699157714844 145.1133270263672 C 139.1650848388672 153.4260864257812 130.3552856445312 159.8374786376953 122.9358673095703 155.7633514404297 L 92.31322479248047 138.9480285644531 C 89.31550598144531 137.3019409179688 85.68448638916016 137.3019409179688 82.686767578125 138.9480285644531 L 52.06414794921875 155.7633514404297 C 44.64476013183594 159.8374633789062 35.83493041992188 153.4261016845703 37.43011474609375 145.1133270263672 L 43.98686218261719 110.9452514648438 C 44.63404846191406 107.5726470947266 43.50538635253906 104.103889465332 40.99751281738281 101.7578735351562 L 15.576904296875 77.97792816162109 C 9.387832641601562 72.18829345703125 12.75523376464844 61.80712890625 21.16432189941406 60.7528076171875 L 55.79164886474609 56.41129302978516 C 59.19038391113281 55.98515319824219 62.13452911376953 53.84911346435547 63.59420776367188 50.75035095214844 L 78.45340728759766 19.20527648925781 C 80.25551605224609 15.3795166015625 83.87775421142578 13.46662902832031 87.5 13.46662902832031 Z" stroke="none" fill="#fff171"/>
                                                    </g>
                                                </g>
                                                <g transform="translate(11031.961 4950.629)" clip-path="url(#aemi-congratulations-new-leve-clip-path)">
                                                    <path d="M11161.024,5005.363s21.682,6.988,7.561,20.345-28.05,19.082-22.707,29.386,20.608,43.506,14.312,44.079-37.782-17.746-43.888-16.792-42.744,25.761-42.744,25.761l83.579,9.922s23.661-34.919,24.425-35.683,29.768-72.893,29.768-75.183-39.69-19.082-39.69-19.082Z" transform="translate(-11028.577 -4947.25)" fill="#e1cb00"/>
                                                </g>
                                            </g>
                                        </svg>
                                    </div>
                                </div>

                                <div class="aemi-congratulations-new-level__box-animation-info-circle__bar-texts">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="536.808" height="106" viewBox="0 0 536.808 106">
                                        <defs>
                                            <filter id="gamification__animation-info-circle__bar" x="55.808" y="0" width="425" height="93" filterUnits="userSpaceOnUse">
                                                <feOffset dy="1" input="SourceAlpha"/>
                                                <feGaussianBlur stdDeviation="3" result="blur"/>
                                                <feFlood flood-opacity="0.188"/>
                                                <feComposite operator="in" in2="blur"/>
                                                <feComposite in="SourceGraphic"/>
                                            </filter>
                                        </defs>
                                        <g transform="translate(-10851.192 -5096)">
                                            <g>
                                                <path d="M0,0S28.94-2.239,53.642-2.239,98.808,0,98.808,0V57.524a230.39,230.39,0,0,0-45.166-3.187C28.94,55.08,0,60.5,0,60.5L28.289,30.44Z" transform="translate(10851.192 5141.5)" fill="#0060b0"/>
                                                <path d="M5.235,0H34V20.705S32.256,13.6,23.756,7.269A40.939,40.939,0,0,0,5.235,0Z" transform="translate(10916 5178)" fill="#055495"/>
                                            </g>
                                            <g transform="translate(11289.192 5139.261)">
                                                <path d="M98.808,0S69.868-2.239,45.166-2.239,0,0,0,0V57.524a230.39,230.39,0,0,1,45.166-3.187c24.7.744,53.642,6.164,53.642,6.164L70.519,30.44Z" transform="translate(0 2.239)" fill="#0060b0"/>
                                                <path d="M34,0H5.235V20.705S6.979,13.6,15.479,7.269A40.939,40.939,0,0,1,34,0Z" transform="translate(-5.235 38.739)" fill="#055495"/>
                                            </g>
                                            <g transform="matrix(1, 0, 0, 1, 10851.19, 5096)" filter="url(#gamification__animation-info-circle__bar)">
                                                <path d="M10,0,203.23-3,397,0a10,10,0,0,1,10,10V62a10,10,0,0,1-10,10L202.635,69,10,72A10,10,0,0,1,0,62V10A10,10,0,0,1,10,0Z" transform="translate(64.81 11)" fill="#0060b0"/>
                                            </g>
                                        </g>
                                    </svg>

                                    <div class="aemi-congratulations-new-level__box-animation-info-circle__bar-text-inner">
                                        <div class="aemi-congratulations-new-level__box-animation-info-circle__bar-text"><?php _e('Congratulations you have reached a new level', AEMI_TEXT_DOMAIN); ?></div>
                                        <div class="aemi-congratulations-new-level__box-animation-info-circle__bar-text-level">
                                            <span>
                                                <span><?php echo $currentUserLevel->name; ?></span>
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                        <div class="aemi-congratulations-new-level__box-animation__skills">
                            <div class="aemi-congratulations-new-level__box-animation__skills__title">
                                <p>Habilidades adquiridas</p>
                            </div>

                            <div class="aemi-congratulations-new-level__box-animation__skills__list">
                                <?php foreach ($levelSkills->classification as $classification) { ?>
                                    <div class="aemi-congratulations-new-level__box-animation__skills__list-box">
                                        <div class="aemi-congratulations-new-level__box-animation__skills__list-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                                                <!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                                <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/>
                                            </svg>
                                        </div>
                                        <div class="aemi-congratulations-new-level__box-animation__skills__list-text"><?php echo $classification->description; ?></div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="aemi-congratulations-new-level__box-animation-close-btn">
                            <button type="button" class="aemi-congratulations-new-level__box-animation-close-btn__button"><?php _e('Continue browsing', AEMI_TEXT_DOMAIN); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="aemi-congratulations-new-level__conffeti"></div>
            <script src="<?php echo get_aemi_url_assets_third_party_plugins('conffeti.js'); ?>"></script>
            <script src="<?php echo get_aemi_url_assets_js('gamification-new-level.js'); ?>"></script>
            <script>
                window.addEventListener('load', function () {
                    jQuery('.aemi-congratulations-new-level__box-animation-close-btn__button').click(function () {
                        jQuery.ajax({
                            url: '<?php echo get_admin_url(); ?>admin-ajax.php',
                            type: 'POST',
                            dataType: "json",
                            data: {
                                action: 'aemi_mark_as_viewed_new_level_notification',
                        <?php echo $this->nonce_name; ?>:
                        '<?php echo wp_create_nonce($this->nonce_action); ?>'
                    }
                    })
                        ;
                    });
                });
            </script>
            <?php
        }
    }

    public function aemiGetGamificationPoints()
    {
        $returnData = [
            'type' => 'error',
        ];

        if (is_user_logged_in()) {
            if (isset($_POST[$this->nonce_name]) && $_POST[$this->nonce_name] && isset($_POST['p']) && $_POST['p']) {
                if (wp_verify_nonce($_POST[$this->nonce_name], $this->nonce_action)) {
                    $user_id = get_current_user_id();
                    $this->addUserScorePerPost($_POST['p'], $user_id);

                    $returnData['type'] = 'success';

                    if ($this->checkIfThereWasAnyLevelUpgradeOnTheUser($user_id)) {
                        $returnData['type'] = 'lvl_up';
                    }
                }
            }
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($returnData);
        exit;
    }

    public function addScriptToReceivePointsByButton()
    {
        global $post;
        ?>
        <script>
            window.addEventListener('load', function () {
                var loadedGetPoints = false;
                jQuery('.aemi-container-get-points__btn:not(.aemi-container-get-points__btn-disabled-earned)').click(function () {
                    var currentBTNGetPoints = jQuery(this);

                    jQuery('.aemi-container-get-points__btn:not(.aemi-container-get-points__btn-disabled-earned)').each(function () {
                        jQuery(this).addClass('aemi-container-get-points__btn-disabled');
                    });

                    if (loadedGetPoints == false) {
                        jQuery.ajax({
                            url: '<?php echo get_admin_url(); ?>admin-ajax.php',
                            type: 'POST',
                            dataType: "json",
                            data: {
                                action: 'aemi_get_gamification_points',
                                p: <?php echo $post->ID; ?>,
                        <?php echo $this->nonce_name; ?>:
                        '<?php echo wp_create_nonce($this->nonce_action); ?>'
                    },
                        success: function (data) {
                            if (data['type']) {
                                switch (data['type']) {
                                    case "lvl_up":
                                        jQuery('body').append('<div class="get-points-gamification__notice" data-type="success"><?php _e('Points acquired successfully!', AEMI_TEXT_DOMAIN); ?></div>');
                                        window.location.reload();
                                        break;

                                    case "success":
                                        jQuery('body').append('<div class="get-points-gamification__notice" data-type="success"><?php _e('Points acquired successfully!', AEMI_TEXT_DOMAIN); ?></div>');
                                        break;

                                    case "error":
                                        jQuery('body').append('<div class="get-points-gamification__notice" data-type="error"><?php _e('Unable to acquire points, please try again later!', AEMI_TEXT_DOMAIN); ?></div>');
                                        break;
                                }

                                if (jQuery('.get-points-gamification__notice')) {
                                    setTimeout(() => {
                                        jQuery('.get-points-gamification__notice').addClass('started');

                                        setTimeout(() => {
                                            jQuery('.get-points-gamification__notice').addClass('remove');
                                        }, 3000);
                                    }, 100);
                                }
                            }

                            jQuery('.aemi-container-get-points__btn').each(function () {
                                jQuery(this).removeClass('aemi-container-get-points__btn-disabled');
                                jQuery(this).addClass('aemi-container-get-points__btn-disabled-earned');
                            });

                        }
                    })
                        ;
                    }
                });
            });
        </script>
        <?php
    }
}

$temp = new AemiScoreLevelUsers();
