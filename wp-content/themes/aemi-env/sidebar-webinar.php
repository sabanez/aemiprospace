<div class="sidebar-webinar mt-3">

    <div class="sidebar-action">
        <div class="video">
            <video src=""></video>
            <i class="fa-solid fa-circle-play" id="play-video"></i>
        </div>

        <div class="text-center p-4">
            <button class="button text-uppercase w-100"><?=esc_html_e('VER WEBINAR', AEMI_TEXT_DOMAIN)?></button>
        </div>

        <ul class="webinar-informations">
            <li>
                <span class="icon"><i class="fa-solid fa-user"></i> <?=esc_html_e('Oradores:')?></span>
                <span class="text"><?= 'Dra. Josepa y, Dra. Pascal' ?></span>
            </li>

            <li>
                <span class="icon"><i class="fa-solid fa-stopwatch"></i> <?=esc_html_e('Duración:')?></span>
                <span class="text"><?= '2 horas' ?></span>
            </li>

            <li>
                <span class="icon"><i class="fa-solid fa-calendar-days"></i> <?=esc_html_e('Fecha del evento:')?></span>
                <span class="text"><?= '15 de enero de 2023 10:15' ?></span>
            </li>

            <li>
                <span class="icon"><i class="fa-solid fa-clock-rotate-left"></i> <?=esc_html_e('Última actualización:')?></span>
                <span class="text"><?= '15 de enero de 2023' ?></span>
            </li>
        </ul>
    </div>

    <div class="skills mt-5">
        <p class="title-skills"><?=esc_html_e('Habilidades que aprenderás', AEMI_TEXT_DOMAIN)?></p>

        <ul>
            <?php for ($i = 1; $i <= 6; $i++): ?>
            <li><i class="fa-solid fa-circle-check"></i> Lorem ipsum dolor sit amet, consectetur.</li>
            <?php endfor; ?>
        </ul>
    </div>

</div>
