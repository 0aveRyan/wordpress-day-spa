<template type="text/babel">
	<div id="start-page">
        <div class="welcome-panel">
            <div class="welcome-panel-content">
                <h2>{{ greeting }}</h2>
                <p class="about-description">{{ wdsData.siteUrl }}</p>
                <div class="welcome-panel-column-container">
                    <div class="welcome-panel-column welcome-panel-last">
                        <h3>Custom Launcher</h3>
                        <a class="button button-primary button-hero" 
                           :href="wdsData.adminUrl + 'post-new.php'">New Post</a>
                        <p>or, <a :href="editPostUrl">another option</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/babel">
import Vue from './../wds.core.js';
export default {
    name: 'start',
    data: function() {
        return {
            wdsData: window.wdsData
        }
    },
    computed: {
        greeting: function() {
            return 'Welcome to ' + wdsData.siteName;
        },
         editPostUrl: function() {
             return wdsData.adminUrl + 'edit.php';
         }
    },
   
}
</script>