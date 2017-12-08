<template type="text/babel">
  <div class="wrap" v-if="pewpew">
    <h1>Posts</h1>
    <post-table :posts="postData"></post-table>
  </div>
</template>

<script type="text/babel">
import Vue from './../wds.core.js';
export default {
    name: 'info',
    data: function() {
        return {
            pewpew: false,
            wdsData: wdsData,
            postData: false,
            wp: wp
        }
    },
    mounted: function () { 
        this.$nextTick(function () { // give it a millisecond to breathe, no need to rush
            this.fetchData();
        })
    },
    methods: {
        fetchData: function() {
            const resource = wdsData.restUrl + 'wp/v2/posts'; // nonce header set in wds.core.js
            this.axios.get(resource).then((response) => {
                this.postData = response.data;
                this.pewpew = true;
            })
        }
    }
}
</script>