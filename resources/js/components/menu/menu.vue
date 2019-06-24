<template>
    <div>
        <div class="row" style="margin-top: 10px">
            <div class="col s12 m3 l3 right">
                <a class="waves-effect waves-light btn" @click="createItem">Create item</a>
            </div>
        </div>
        <div class="row">
            <div class="col s12 l12 m12">
                <div id="form">
                    <div class="card" id="edit-form" v-if="editingItem || creatingItem">
                        <div class="card-content">
                            <h3>{{ title }}</h3>
                            <editcreate :item="currentItem" :operation="creatingItem ? 'create' : 'edit'" @save-item="saveItem" @cancel-item="cancelItem"></editcreate>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row flex" v-for="i in Math.ceil(items.length / 3)">
            <item v-for="item in items.slice((i - 1) * 3, i * 3)" :item="item" :key="item.id" @item-edit="editItem" @item-delete="deleteItem"></item>
        </div>
    </div>
</template>

<script>
    import item from "./item";
    import editcreate from "./editcreate";

    export default {
        components: {item,editcreate},
        data() {
            return {
                items: [],
                editingItem: false,
                creatingItem: false,
                currentItem: [],
                title: "",
                successMessage: "",
                showSuccess: false,
            }
        },
        methods: {
            getMenuItems() {
                axios.get('/api/menus')
                    .then((res) => {
                        this.items = res.data.data;
                    })
                    .catch((res) => {
                        console.log(res);
                    })
            },
            deleteItem(item){
                axios.delete('api/menus/'+item.id)
                    .then(res => {
                        console.log(res);
                        M.toast({html: '\"'+item.name+'\" deleted!'})
                        this.getMenuItems();
                    });
            },
            cancelItem(){
                if(this.creatingItem){
                    this.creatingItem = false;
                    this.currentItem = null;
                }else{
                    this.editingItem = false;
                    axios.get('/api/menus/'+this.currentItem.id)
                        .then(response=>{
                            Object.assign(this.currentItem, response.data.data);
                            this.currentItem = null;
                        });
                }
            },
            editItem(item) {
                this.currentItem = item;
                this.editingItem = true;
                this.title = "Edit";
            },
            createItem() {
                this.creatingItem = true;
                this.title = 'Create';
                this.currentItem = {};
            },
            saveItem(){
                this.getMenuItems();
                this.creatingItem = false;
                this.editingItem = false;
                this.currentItem = null;
            }

        },
        mounted: function () {
            this.getMenuItems();
        }
    }
</script>