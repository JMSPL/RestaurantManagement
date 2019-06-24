<template>
    <div>
        <a href="#form" @click="editItem">Edit</a>
        <a @click="deleteItem">Delete</a>
    </div>
</template>

<script>
    export default {
        name: "buttons",
        props: ['item'],
        methods: {
            editItem: function () {
                this.$emit('item-edit', this.item);
            },
            deleteItem: function () {
                this.$emit('item-delete', this.item);
            }
        }
    }
</script>

<style scoped>

</style>