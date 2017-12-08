<template type="text/babel">
	<div class="wrap">
        <h1 v-if="pageTitle">{{ this.$props.title }}</h1>
        <h2 class="nav-tab-wrapper wordtabs">
            <router-link :to="tab" :key="tab" v-for="(data, tab) in tabs" class="nav-tab">{{ tab }}</router-link>
        </h2>
    </div>
</template>

<script type="text/babel">
import Vue from './../wds.core.js';
export default {
    name: 'wordtabs',
    props: [ 'tabs', 'title' ],
    data: function() {
        return {}
    },
    computed: {
        pageTitle: function() {
            if ( '' !== this.$props.title ) {
                return this.$props.title;
            }

            return false;
        }
    }
}
</script>

<style type="text/css">
.wordtabs a.nav-tab:focus {
        box-shadow: none !important;
}
</style>