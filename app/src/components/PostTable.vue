<template type="text/babel">
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" class="column-primary">Post</th>
                <th scope="col">Date</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="post in this.$props.posts">
                <td class="column-username has-row-actions column-primary" data-colname="Post">
                    <strong><a :href="post.guid.rendered">{{ post.title.rendered }}</a></strong><br>
                </td>
                <td data-colname="Date">{{ post.date }}</td>
            </tr>
        </tbody>
    </table>
</template>

<script type="text/babel">
import Vue from './../wds.core.js';
export default {
    name: 'wordtable',
    props: [ 'posts' ],
    data: function() {
        return {}
    }
}
</script>