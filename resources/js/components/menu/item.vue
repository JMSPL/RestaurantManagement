<template>
    <div class="col s12 m6 l4 flex">
        <div class="card">
            <div class="card-image">
                <img :src="'/storage/items/'+item.photo_url">
            </div>
            <div class="card-content">
                <span class="card-title">{{ item.name }} - {{ item.price }}€</span>
                <p>{{ item.description }}</p>
            </div>
            <div class="card-action" v-if="this.isUserLogged && user().type === 'manager'">
                <buttons :item="item" @item-edit="editItem" @item-delete="deleteItem"></buttons>
            </div>
        </div>
    </div>
</template>

<script>
    import buttons from './buttons';

    export default {
        data: function(){
            return {
                isUserLogged: false,
            }
        },
        props:['item'],
        components:{buttons},
        methods: {
            editItem: function () {
                this.$emit('item-edit', this.item);
            },
            deleteItem: function () {
                this.$emit('item-delete', this.item);
            },
            user(){
                return this.$store.state.user;
            }
        },
        mounted: function(){
            this.isUserLogged = this.$store.getters.loggedIn;
        }
    }
</script>