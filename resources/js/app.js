require('./bootstrap');
window.Vue = require('vue').default;
require("./components/subscribe-button").default;
require("./components/channel-uploads").default;
const app = new Vue({
    el: '#app',
});
