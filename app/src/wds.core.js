// IMPORT CORE APP
import Vue from 'vue'
import VueRouter from 'vue-router'
import axios from 'axios'
import _ from 'lodash'
import VueLodash from 'vue-lodash/dist/vue-lodash.min'
import VueAxios from 'vue-axios'

import WDS from './WDS.vue'

// IMPORT PAGE TEMPLATES FOR REGISTRATION IN ROUTER
import Start from './components/Start.vue'
import Info from './components/Info.vue'
import WordTabs from './components/WordTabs.vue';
import PostTable from './components/PostTable.vue';

// SETUP ROUTER AND COMPONENTS
Vue.use( VueRouter )
Vue.use( VueAxios, axios )
Vue.component( 'wordtabs', WordTabs )
Vue.component( 'post-table', PostTable )

// Preset Nonce for all axios calls
Vue.axios.defaults.headers.common['X-WP-Nonce'] = wdsData.nonce;

export default Vue; // export this as Vue from now on so we have everything

// ROUTER CONFIG
const routes = [
	{ path: '/', redirect: '/start' },
	{ path: '/start', name: "start", component: Start },
	{ path: '/info', name: "info", component: Info }
];

// ROUTER INSTANTIATION
const router = new VueRouter( {
	routes: routes,
	linkActiveClass: "nav-tab-active", // active class for non-exact links.
	linkExactActiveClass: "nav-tab-current-route" // active class for *exact* links.
} );

// EnterPressApp APP INSTANTIATION
var wdsAppVue = Vue.extend( {
	router: router,
	render: function( createElement ) {
		return createElement( WDS );
	}
} );

// FINALLY, MOUNT APP ON DIV, REPLACING DIV ENTIRELY
new wdsAppVue().$mount('#app');