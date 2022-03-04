<template>
	<!-- Global #app template with router-view tag to
route other application components -->
	<div id="app">
		<router-view />
	</div>
</template>

<script>
// Global declaration of fontawesome kit for all application icons
export default {
	// When Vue instance is mounted
	mounted() {
		let fontawesome = document.createElement('script')
		fontawesome.setAttribute(
			'src',
			'https://kit.fontawesome.com/90cfa59e2e.js'
		)
		document.head.appendChild(fontawesome)
	}
}
</script>

<!-- Global SCSS applying Be Com' Different standard fonts, colors... -->
<style lang="scss">
$bcd_color: #ea5b13;
#app {
	font-family: Avenir, Helvetica, Arial, sans-serif;
	-webkit-font-smoothing: antialiased;
	-moz-osx-font-smoothing: grayscale;
	color: #2c3e50;
}

.btn {
	&.btn-primary {
		background-color: $bcd_color;
		border-color: $bcd_color;
		color: #fff;
		&:hover {
			color: $bcd_color;
			background: #fff;
			border-color: $bcd_color;
		}
	}
}
</style>
