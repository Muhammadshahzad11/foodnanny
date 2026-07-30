<template>
    <VueDatePicker v-model="timeValue" :inputClassName="className" menuClassName="foodnanny-menu" format="hh:mma"
        :placeholder="inputStyle === 'filter' ? '' : $t('label.select_date_range')" :time-picker="true"
        :time-picker-only="true" :is24="false" :range="range" utc="false"
        :teleport="inputStyle === 'filter'">
        <template #input-icon>
            <i class="lab-line-time-slots"></i>
            <i class="lab-line-chevron-down"></i>
        </template>
        <template #clear-icon="{ clear }"><i @click="clear" class="lab-line-cross"></i></template>
    </VueDatePicker>
</template>
<script>
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

export default {
    name: "TimePickerComponent",
    props: {
        inputStyle: {
            type: String,
            required: true,
            validator: (propValue) => ['box', 'read', 'filter'].includes(propValue)
        },
        range: {
            type: Boolean
        }
    },
    components: {
        VueDatePicker
    },
    data() {
        return {
            timeValue: null,
        }
    },
    computed: {
        className(state) {
            if (state.inputStyle === 'box') {
                return 'foodnanny-input box'
            }
            else if (state.inputStyle === 'read') {
                return 'foodnanny-input read'
            }
            else if (state.inputStyle === 'filter') {
                return 'foodnanny-input filter'
            }
            else return 'foodnanny-input'
        }
    },
}
</script>
