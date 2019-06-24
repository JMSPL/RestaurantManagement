<template>
    
</template>

<script>
    export default {
        created(){
            this.$store.dispatch('logout')
                .then(res => {
                    this.$root.user = null;
                    this.$router.push('/');
                })
        }
    }
</script>

<style scoped>

</style>