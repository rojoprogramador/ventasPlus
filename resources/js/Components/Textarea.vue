<template>
    <textarea
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
    ></textarea>
</template>

<script setup>
import { onMounted, ref } from 'vue';

const input = ref(null);

defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    }
});

defineEmits(['update:modelValue']);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>
