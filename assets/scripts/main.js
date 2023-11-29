document.addEventListener("DOMContentLoaded", function() {
const generateStars = function() {
    const galaxy = document.querySelector(".galaxy");
    const numStars = 100;

    for (let i = 0; i < numStars; i++) {
        const xPosition = Math.random();
        const yPosition = Math.random();
        const starType = Math.floor(Math.random() * 3) + 1;
       const position = {
           x: galaxy.offsetWidth * xPosition,
           y: galaxy.offsetHeight * yPosition
       };
        const star = $('<div class="star star-type' + starType + '"></div>');
        star.appendTo(galaxy).css({
            top: position.y,
            left: position.x
        });
    }
};

generateStars();

});

function showFilms() {
  let mediaType = document.getElementById("type_media").value;
    let filmSelect = document.getElementById("film_select");
    if (mediaType === "film") {
        filmSelect.style.display = "block";
    } else {
        filmSelect.style.display = "none";
    }
}

function showMediaOptions() {
    let mediaType = document.getElementById("type_media").value;
    let serieSelect = document.getElementById("serie_select");
    let episodeSelect = document.getElementById("episode_select");

    serieSelect.style.display = mediaType === "serie" ? "block" : "none";
    episodeSelect.style.display = "none"; // Cacher à chaque changement de type de média
}

function showEpisodes() {
    let serieId = document.getElementById("serie").value;
    let episodeSelect = document.getElementById("episode");

    // Ici, tu peux ajouter une logique pour remplir les épisodes en fonction de la série choisie
    // Par exemple, en faisant une requête AJAX vers ton serveur pour récupérer les épisodes

    episodeSelect.style.display = serieId ? "block" : "none";
}

function checkForPublicite() {
    var mediaType = document.getElementById("type_media").value;
    if (mediaType === "publicite") {
        fetchRandomPublicite();
    } else {
        document.getElementById("bloc_publicitaire").style.display = "none";
    }
}

function fetchRandomPublicite() {
    fetch('/chemin_vers_script_php')
        .then(response => response.json())
        .then(data => {
            // Affiche les détails du bloc publicitaire
            var blocPublicitaire = document.getElementById("bloc_publicitaire");
            blocPublicitaire.innerHTML = 'Publicité sélectionnée : ' + data.titre; // Exemple
            blocPublicitaire.style.display = "block";
        })
        .catch(error => console.error('Erreur:', error));
}

function showIntermedes() {
    var mediaType = document.getElementById("type_media").value;
    var intermedeSelect = document.getElementById("intermede_select");
    intermedeSelect.style.display = mediaType === "intermede" ? "block" : "none";
}

window['__onGCastApiAvailable'] = function(isAvailable) {
    if (isAvailable) {
        cast.framework.CastContext.getInstance().setOptions({
            receiverApplicationId: chrome.cast.media.DEFAULT_MEDIA_RECEIVER_APP_ID,
            autoJoinPolicy: chrome.cast.AutoJoinPolicy.ORIGIN_SCOPED
        });
    }
};

function castVideo() {
    const castSession = cast.framework.CastContext.getInstance().getCurrentSession();
    
    if (castSession) {
        const mediaInfo = new chrome.cast.media.MediaInfo(videoPlayer.src, 'video/mp4');
        const request = new chrome.cast.media.LoadRequest(mediaInfo);
        castSession.loadMedia(request).then(
            function() { console.log('Casting a démarré'); },
            function(error) { console.log('Erreur de casting :', error); }
        );
    } else {
        console.log("Pas de session de casting disponible");
    }
}

const castButton = document.getElementById('castButton');
const videoPlayer = document.getElementById('videoPlayer');

castButton.addEventListener('click', function() {
    cast.framework.CastContext.getInstance().requestSession().then(
        function(session) {
            castVideo();
        },
        function(error) {
            console.log('Erreur lors de la demande de session :', error);
        }
    );
});

 window.addEventListener('DOMContentLoaded', (event) => {
        const urlParams = new URLSearchParams(window.location.search);
        const videoUrl = urlParams.get('video');
        if (videoUrl) {
            const videoPlayer = document.getElementById('videoPlayer');
            videoPlayer.src = videoUrl;
        }
    });
