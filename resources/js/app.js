require('./bootstrap');
window.Vue = require('vue').default;
require("./components/subscribe-button").default;
const app = new Vue({
    el: '#app',
});
