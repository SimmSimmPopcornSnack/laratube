// const { default: axios } = require("axios");

var viewLogged = false;
var player = videojs("video");
player.on("timeupdate", function() {
    var percentagePlayed = Math.ceil(player.currentTime() / player.duration() * 100);
    if(!viewLogged && percentagePlayed > 10) {
        axios.put("/videos/" + window.CURRENT_VIDEO);
        viewLogged = true;
    }
});
