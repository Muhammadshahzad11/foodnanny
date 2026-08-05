<template>
    <div class="paper-group" v-if="page.total > 10">
        <button @click="handleDropDown($event)" class="paper-button h-9 px-3 py-4 text-sm tracking-wide font-medium capitalize rounded-md shadow flex items-center justify-center gap-1 border border-primary text-primary bg-white">
            <span>{{ search.per_page }} </span>
            <i class="lab lab-line-chevron-down font-semibold transition-all duration-300"></i>
        </button>
        <ul class="paper-content absolute top-9 right-1/2 translate-x-1/2 z-30 min-w-[80px] w-fit py-2 rounded-md shadow-paper bg-white">
            <li v-for="limit in limits" @click="limitChange(limit)" class="cursor-pointer px-2 py-0.5 w-full hover:text-primary">
                {{ limit }}
            </li>
        </ul>
    </div>
</template>

<script>
import { usePaper } from "../../../composables/paper";
export default {
    name: "TableLimitComponent",
    props: {
        page: { type: Object },
        search: { type: Object },
        method: { type: Function }
    },
    data() {
        return {
            limits: [10, 25, 50, 100, 500, 1000]
        }
    },
    setup() {
        const { handlePaper } = usePaper()
        return {
            handlePaper
        }
    },
    methods: {
        handleDropDown: function (event) {
            this.handlePaper(event)
        },
        limitChange: function (number) {
            this.search.per_page = number;
            this.method();
        }
    }
}
</script>
