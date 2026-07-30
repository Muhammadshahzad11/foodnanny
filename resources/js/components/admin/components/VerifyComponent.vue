<template>
    <span v-if="props.is_verified === enums.askEnum.YES">
        <i class="lab lab-fill-verify text-green-600"></i>
    </span>
    <span v-else-if="props.created_date === props.updated_date">
        <i class="lab lab-fill-new text-blue-600"></i>
    </span>
    <span v-else-if="props.is_read === enums.askEnum.NO">
        <i class="lab lab-fill-update text-yellow-600"></i>
    </span>
</template>
<script>
import askEnum from "../../../enums/modules/askEnum";

export default {
    name : "VerifyComponent",
    props: ['props'],
    data() {
        return {
            enums: {
                askEnum: askEnum
            },
        }
    }
}
</script>
