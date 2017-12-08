<template type="text/babel">
	<main id="wds-app">
		<wordtabs :tabs="wdsTabs"></wordtabs>
    <router-view></router-view>
	</main>
</template>
<script type="text/babel">
// Pulls Vue via instance in main js file to inherit all registered components and config exported there
import Vue from './wds.core.js'
// Sets default configuration array for file
export default {
  name: 'app',
  data: function() {
    return {
      wdsTabs: {
        "start": {
          icon:"dashicons-yes"
        },
        "info": {
          icon:"dashicons-info"
        }
      },
      wdsData: window.wdsData
    }
  }
}
</script>