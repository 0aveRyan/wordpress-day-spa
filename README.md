# wordpress-day-spa
Example of a Vue.js-powered SPA running in the WordPress Admin with vue-router and vue-loader.

# project structure

`package.json` - defines all dependencies
`webpack.config.js` - defines codesplitting and processing
`app/src/wds.core.js` - main app scaffolding with dependency-injection
`app/src/WDS.vue` - main app template injected onto page
`app/assets/js/wds.app.bundle.js` - main bundle containing CSS, JS and HTML to render
`app/assets/js/wds.lib.bundle.js` - main bundle containing vue, vue-router, axios and other libraries.

# using the project

`npm install` to bring down dependencies

`npm run dev` to generate the bundles plus watch files and recompile on change
`npm run build` to generate small, minified production assets

# spa dependency injection

babel and vue-loader allow us to use `import` and `export` within our `.vue` files to handle dependencies.

All third-party dependencies and custom components are managed in the `app/src/wds.core.js` file. 

### Adding a new component
1. Create your new `.vue` file with a `<template type="text/babel"></template>` and a `<script type="text/babel"><template>`
2. `import Vue from` from our `wds.core.js` file in the component. This makes all other dependencies available.
2. `export` with a [new component definition](https://vuejs.org/v2/guide/single-file-components.html). [Example](https://github.com/0aveRyan/wordpress-day-spa/blob/master/app/src/components/PostTable.vue#L22-L28).
2. `import YourNewComponent from` the location in your directory in the `wds.core.js` file.
3. Set a `Vue.component( 'your-component-elem', YourNewComponent )`

### Recommended Reading
* [Vue.js Template Syntax](https://vuejs.org/v2/guide/syntax.html)
* [Vue.js Computed Properties](https://vuejs.org/v2/guide/computed.html)
* [Vue.js Components](https://vuejs.org/v2/guide/components.html)
    * [Vue.js Single File Components](https://vuejs.org/v2/guide/single-file-components.html)
* [vue-router: Getting Started](https://router.vuejs.org/en/essentials/getting-started.html)

