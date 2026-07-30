<template>
    <component :is="tag" v-bind="$attrs" type="button" class="group relative">
        <slot/>
        <span
            class="inline-block absolute -top-1 left-1/2 -translate-x-1/2 -translate-y-full first-letter:capitalize text-xs rounded-md py-1 px-2 bg-gray-800 text-white before:absolute before:w-2 before:h-2 before:bg-gray-800 before:rotate-45 before:left-1/2 before:-bottom-1 before:-translate-x-1/2 group-hover:opacity-100 group-hover:visible group-hover:-top-2 opacity-0 invisible transition-all duration-300"
            :class="tooltipClasses">
            {{ text }}
        </span>
    </component>
</template>


<script>
export default {
    name: "TooltipComponent",
    props: {
        tag: {
            type: String,
            default: 'div',
        },
        text: {
            type: String,
            required: true,
            default: 'tooltip',
        },
        line: {
            type: String,
            default: 'single',
            validator: value => ['single', 'multiple'].includes(value),
        },
        width: {
            type: String,
            default: 'w-40'
        }
    },
    computed: {
        tooltipClasses: function () {
            return [
                this.$props.width, this.$props.line === 'single' ? 'whitespace-nowrap' : 'whitespace-normal',
            ]
        }
    }
}
</script>
