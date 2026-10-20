// Custom functions


window.addEventListener('load', function() {
    expandNewCard();
});

function expandNewCard() {
    let newCards = document.querySelectorAll('.AgendaNewsCard .newList');
    newCards.forEach(newCard => {
        newCard.addEventListener('click', function() {
            let distribution = newCard.querySelector('.newDistribution');
            if(distribution.classList.contains('closed')) {
                distribution.classList.remove('closed');
            } else {
                distribution.classList.add('closed');
            }
        });
    });
}