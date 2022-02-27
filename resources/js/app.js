require('./bootstrap');
window.Vue = require('vue').default;
Vue.config.ignoredElements = ["video-js"];
Vue.component("subscribe-button", require("./components/subscribe-button.vue").default);
require("./components/channel-uploads").default;
const app = new Vue({
    el: '#app',
});
