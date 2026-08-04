<template>
    <VueElementLoading spinner="bar-fade-scale" color="#148A3C" :active="props.isActive" :is-full-screen="true"/>
</template>

<script>


import VueElementLoading from "vue-element-loading";

export default {
    name: "LoadingComponent",
    components: {VueElementLoading},
    props: ['props']
}
</script>
