var gamificationAnimation = document.querySelector('.aemi-congratulations-new-level');

if( gamificationAnimation ){
    setTimeout(function(){
        gamificationAnimation.classList.add('aemi-congratulations-new-level__started-animation');

        
        setTimeout(function(){
            let elementBoxSkills = document.querySelectorAll('.aemi-congratulations-new-level__box-animation__skills__list-box');

            if( elementBoxSkills.length ){
                iloop = 0;
                elementBoxSkills.forEach( function(indexElement){
                    setTimeout(function(){ indexElement.classList.add('aemi-congratulations-new-level__box-animation__skills__list-box-loop' ); }, (700*iloop));
                    iloop++;
                });
            }
        }, 2500);

    }, 100);

    var gamificationAnimationBtn = document.querySelector('.aemi-congratulations-new-level__box-animation-close-btn__button');
    
    if( gamificationAnimationBtn ){
        gamificationAnimationBtn.addEventListener('click', function(){
            gamificationAnimation.classList.add('aemi-congratulations-new-level__close-animation');
            
            setTimeout(function(){
                gamificationAnimation.remove();
                gamificationConffetiDisable();
            }, 1000);
        });
    }
}